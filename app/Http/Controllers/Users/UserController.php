<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\ControllerBookings\ControllerBookingsBan;
use App\Models\Network\SessionLog;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Settings\AuditLogEntry;
use App\Models\Users\User;
use App\Models\Users\UserNote;
use App\Models\Users\UserNotification;
use App\Models\Users\UserPreferences;
use App\Notifications\DiscordWelcome;
use App\Notifications\WelcomeNewUser;
use Auth;
use Carbon\Carbon;
use Exception;
use function GuzzleHttp\Psr7\str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use mofodojodino\ProfanityFilter\Check;
use NotificationChannels\Discord\Discord;
use NotificationChannels\Discord\Exceptions\CouldNotSendNotification;
use RestCord\DiscordClient;
use SocialiteProviders\Manager\Config;

class UserController extends Controller
{
    public function privacyAccept()
    {
        $user = Auth::user();
        if ($user->init == 1) {
            return redirect()->route('index');
        }
        $user->init = 1;
        $user->save();
        $user->notify(new WelcomeNewUser($user));

        return redirect('/dashboard')->with('success', 'Welcome to Winnipeg, '.$user->fname.'! We are glad to have you on board.');
    }

    public function privacyDeny()
    {
        $user = Auth::user();
        $preferences = UserPreferences::where('user_id', $user->id);
        if ($user->init == 1) {
            return redirect()->route('index');
        }
        Auth::logout($user);

        AuditLogEntry::insert(User::find(1), 'User '.$user->fullName('FLC').' denied privacy policy - account deleted', User::find(1), 0);
        $preferences->delete();
        $user->delete();

        return redirect()->route('index')->with('info', 'Your account has been removed as you have not accepted the privacy policy.');
    }

    public function viewAllUsers()
    {
        $users = User::all()->sortBy('id');

        return view('admin.users.index', compact('users'));
    }

    public function viewProfile($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $rosterMember = RosterMember::where('user_id', $id)->first();
        if ($rosterMember) {
            $logs = SessionLog::where('cid', $id)->get();
            $monthlyHours = decimal_to_hm(RosterMember::where('cid', $id)->firstOrFail()->currency);

            //Start our array
            $time = [
                'del' => 0,
                'gnd' => 0,
                'twr' => 0,
                'dep' => 0,
                'app' => 0,
                'ctr' => 0,
            ];

            //Get our times per position for this month
            foreach ($logs as $l) {
                if (Str::endsWith($l->callsign, 'DEL')) {
                    $time['del'] += $l->duration;
                } elseif (Str::endsWith($l->callsign, 'GND')) {
                    $time['gnd'] += $l->duration;
                } elseif (Str::endsWith($l->callsign, 'TWR')) {
                    $time['twr'] += $l->duration;
                } elseif (Str::endsWith($l->callsign, 'DEP')) {
                    $time['dep'] += $l->duration;
                } elseif (Str::endsWith($l->callsign, 'APP')) {
                    $time['app'] += $l->duration;
                } elseif (Str::endsWith($l->callsign, 'CTR')) {
                    $time['ctr'] += $l->duration;
                }
            }

            //Make the time's readable
            $time['del'] = decimal_to_hm($time['del']);
            $time['gnd'] = decimal_to_hm($time['gnd']);
            $time['twr'] = decimal_to_hm($time['twr']);
            $time['dep'] = decimal_to_hm($time['dep']);
            $time['app'] = decimal_to_hm($time['app']);
            $time['ctr'] = decimal_to_hm($time['ctr']);

            $connections = SessionLog::where('cid', $id)->get()->sortByDesc('session_end');

            foreach ($connections as $c) {
                $c['duration'] = decimal_to_hm($c['duration']);
            }
        } else {
            $monthlyHours = 'N/A';
            $rosterMember = null;
            $connections = [];
        }

        return view('profile', compact('id', 'user', 'monthlyHours', 'rosterMember', 'time', 'connections'));
    }

    public function viewConnections($id)
    {
        function clockalize($in)
        {
            $h = intval($in);
            $m = round((((($in - $h) / 100.0) * 60.0) * 100), 0);
            if ($m == 60) {
                $h++;
                $m = 0;
            }
            $retval = sprintf('%02d:%02d', $h, $m);

            return $retval;
        }

        $connections = SessionLog::where('cid', $id)->get()->sortByDesc('session_start');
        $user = User::where('id', $id)->first();

        foreach ($connections as $c) {
            $c['date'] = substr($c['session_start'], 5, 2).'-'.substr($c['session_start'], 8, 2).'-'.substr($c['session_start'], 0, 4);
            $c['session_start'] = substr($c['session_start'], 11, 5);
            $c['session_end'] = substr($c['session_end'], 11, 5);
            $c['duration'] = clockalize($c['duration']);
        }

        return view('connections', compact('connections', 'user', 'id'));
    }

    public function adminViewUserProfile($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        $certification = null;
        $active = null;
        $potentialRosterMember = RosterMember::where('user_id', $user->id)->first();
        if ($potentialRosterMember !== null) {
            $certification = $potentialRosterMember->status;
            $active = $potentialRosterMember->active;
        }

        $xml = [];
        $userNotes = UserNote::where('user_id', $user->id)->orderBy('timestamp', 'desc')->get();
        //$xml['return'] = file_get_contents('https://cert.vatsim.net/cert/vatsimnet/idstatus.php?cid=' . $user->id);
        $auditLog = AuditLogEntry::where('affected_id', $id)->get();
        $allroles = Role::all();
        $roles = $user->getRoleNames();

        return view('admin.users.profile', compact('user', 'xml', 'certification', 'active', 'auditLog', 'userNotes', 'roles', 'allroles'));
    }

    public function addRole(Request $request)
    {
        $u = User::whereId($request->input('id'))->first();
        $r = $request->input('role');
        $role = Role::where('name', $r)->first();
        if ($u->hasRole($role->name)) {
            return back()->withError('This user is already assigned the '.$role->name.' role!');
        }
        $u->assignRole($role->name);
        $audit = new AuditLogEntry();
        $audit->user_id = Auth::user()->id;
        $audit->action = 'Added the '.$role->name.' Role.';
        $audit->affected_id = $u->id;
        $audit->time = Carbon::now()->toDateTimeString();
        $audit->private = '0';
        $audit->save();

        return back()->withSuccess('Added the '.$role->name.' role!');
    }

    public function deleteRole($id, $user)
    {
        $role = Role::where('name', $id)->first();
        $m = $role->name;
        $u = User::whereId($user)->first();
        if($role->protected == '2' && !Auth::user()->hasRole('Administrator')) {
                return back()->withError('You do not have the permissions to delete this role!');
        }
        if($role->protected == '1' && !Auth::user()->hasAnyRole('Administrator|Staff')) {
                return back()->withError('You do not have the permissions to delete this role!');

        }
        $u->removeRole($id);
        $audit = new AuditLogEntry();
        $audit->user_id = Auth::user()->id;
        $audit->action = 'Removed the '.$m.' Role.';
        $audit->affected_id = $u->id;
        $audit->time = Carbon::now()->toDateTimeString();
        $audit->private = '0';
        $audit->save();

        return back()->withSuccess('Deleted the '.$m.' role!');
    }

    public function editPermissions(Request $request, $id)
    {
        $user = User::where('id', $id)->firstorFail();
        $roster = RosterMember::where('cid', $id)->first();
        $user->permissions = $request->input('permissions');
        $user->save();
        if ($roster != null) {
            $roster->status = $request->input('certification');
            $roster->save();
        }

        return redirect()->back()->withSuccess('User Permissions Changed!');
    }

    public function deleteUser($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        if ($user->id == Auth::user()->id) {
            abort(403, 'You cannot delete yourself!');
        }

        $entry = new AuditLogEntry([
            'user_id' => Auth::user()->id,
            'affected_id' => $user->id,
            'action' => 'DELETE USER',
            'time' => date('Y-m-d H:i:s'),
            'private' => 0,
        ]);
        $entry->save();
        $user->fname = 'Deleted';
        $user->lname = 'User';
        $user->email = 'no-reply@czqo.vatcan.ca';
        $user->rating = 'Deleted';
        $user->division = 'Deleted';
        $user->permissions = 0;
        $user->deleted = 1;
        $user->save();

        return redirect()->route('users.viewall')->with('info', 'User deleted.');
    }

    public function editUser($id)
    {
        $user = User::where('id', $id)->firstOrFail();

        //return view('admin.users.edituser', compact('user'));
        abort(404, 'Not implemented');
    }

    public function changeUsersAvatar(Request $request)
    {
        $this->validate($request, [
            'file' => 'required',
            'user_id' => 'required',
        ]);
        $editUser = Auth::user();
        $user = User::whereId($request->get('user_id'))->firstOrFail();
        $uploadedFile = $request->file('file');
        $filename = $uploadedFile->getClientOriginalName();
        Storage::disk('local')->putFileAs(
            'public/files/avatars/'.$user->id.'/'.$editUser->id,
            $uploadedFile,
            $filename
        );
        $user->avatar = Storage::url('public/files/avatars/'.$user->id.'/'.$editUser->id.'/'.$filename);
        $user->avatar_mode = 1;
        $user->save();
        AuditLogEntry::insert($editUser, 'Changed user avatar', $user, 0);

        return redirect()->back()->with('success', 'Avatar changed!');
    }

    public function resetUsersAvatar(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
        ]);
        $editUser = Auth::user();
        $user = User::whereId($request->get('user_id'))->firstOrFail();
        if ($user->isAvatarDefault()) {
            abort(403, 'The avatar is already the default avatar.');
        }

        $user->avatar = '/img/default-profile-img.jpg';
        $user->avatar_mode = 0;
        $user->save();
        AuditLogEntry::insert($editUser, 'Reset user avatar', $user, 0);

        return redirect()->back()->with('success', 'Avatar reset!');
    }

    public function resetUsersBio(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
        ]);

        $editUser = Auth::user();
        $user = User::whereId($request->get('user_id'))->firstOrFail();

        $user->bio = null;
        $user->save();

        AuditLogEntry::insert($editUser, 'Reset user bio', $user, 0);

        //Redirect
        return redirect()->back()->with('success', 'Biography reset!');
    }

    public function storeEditUser(Request $request, $id)
    {
        $user = User::find($id);
        $prevPermissions = $user->permissions;
        $user->permissions = $request->get('permissions');
        $user->save();
        $entry = new AuditLogEntry([
            'user_id' => Auth::user()->id,
            'action' => 'EDIT USER',
            'affected_id' => $user->id,
            'time' => date('Y-m-d H:i:s'),
            'private' => 0,
        ]);
        $entry->save();
        if ($prevPermissions != $user->permissions) {
            $notification = new UserNotification([
                'user_id' => $user->id,
                'content' => 'Your permissions have been updated.',
                'link' => '/dashboard',
                'dateTime' => date('Y-m-d H:i:s'),
            ]);
            $notification->save();
        }

        //return redirect()->route('users.viewprofile', $user->id)->with('success', 'User edited!');
        abort(404, 'Not implemented');
    }

    public function emailCreate($id)
    {
        $user = User::where('id', $id)->firstOrFail();

        //return view('dashboard.users.email', compact('user'));
        abort(404, 'Not implemented');
    }

    public function emailStore(Request $request)
    {
    }

    public function createUserNote(Request $request, $id)
    {
        $this->validate($request, [
            'content' => 'required',
        ]);

        $user = User::where('id', $id)->firstOrFail();
        $instructor = Auth::user();
        $content = $request->get('content');
        $note = new UserNote([
            'user_id' => $user->id,
            'author' => Auth::user()->id,
            'author_name' => $instructor->fullName('FLC'),
            'content' => $content,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);

        if ($request->get('confidential') == 'on') {
            $note->confidential = 1;
        }

        $note->save();

        return redirect()->route('users.viewprofile', $user->id)->with('success', 'User note saved!');
    }

    public function deleteUserNote($user_id, $note_id)
    {
        $user = User::where('id', $user_id)->firstOrFail();
        $note = UserNote::where('id', $note_id)->where('user_id', $user->id)->firstOrFail();

        $entry = new AuditLogEntry([
            'user_id' => Auth::user()->id,
            'action' => 'DELETE USER NOTE '.$note->id,
            'affected_id' => $user->id,
            'time' => date('Y-m-d H:i:s'),
            'private' => 0,
        ]);
        if ($note->confidential == 1) {
            $entry->private = 1;
        }
        $entry->save();

        $note->delete();

        return redirect()->route('users.viewprofile', $user->id)->with('success', 'User note deleted.');
    }

    public function changeAvatar(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $user = Auth::user();
        $uploadedFile = $request->file('file');
        $filename = $uploadedFile->getClientOriginalName();
        Storage::disk('local')->putFileAs(
            'public/files/avatars/'.$user->id,
            $uploadedFile,
            $filename
        );
        $user->avatar = Storage::url('public/files/avatars/'.$user->id.'/'.$filename);
        $user->avatar_mode = 1;
        $user->save();

        return redirect()->route('dashboard.index')->with('success', 'Avatar changed!');
    }

    public function changeAvatarDiscord()
    {
        $user = Auth::user();
        $user->avatar_mode = 2;
        $user->save();

        return redirect()->route('dashboard.index')->with('success', 'Avatar changed!');
    }

    public function resetAvatar()
    {
        $user = Auth::user();
        if ($user->isAvatarDefault()) {
            abort(403, 'Your avatar is already the default avatar.');
        }

        $user->avatar_mode = 0;
        $user->save();

        return redirect()->back()->with('success', 'Avatar reset!');
    }

    public function searchUsers(Request $request)
    {
        if ($request->ajax != false) {
            abort(400, 'AJAX requests only');
        }
        $query = strtolower($request->get('query'));
        $users = User::
            where('id', 'LIKE', "%{$query}%")->
            orWhere('display_fname', 'LIKE', "%{$query}")->
            orWhere('lname', 'LIKE', "%{$query}%")->get();
        if (count($users) > 0) {
            return Response($users);
        } else {
            return Response('n/a');
        }
    }

    public function editBioIndex()
    {
        return view('dashboard.me.editbio');
    }

    public function editBio(Request $request)
    {
        $this->validate($request, [
            'bio' => 'sometimes|max:5000',
        ]);

        //Get user
        $user = Auth::user();

        //Get input
        $input = $request->get('bio');

        //Run through profanity filter
        $check = new Check();
        if ($check->hasProfanity($input)) {
            return redirect()->back()->withInput()->with('error-modal', 'Profanity was detected in your input, please remove it.');
        }

        //No swear words.. give them the new bio
        $user->bio = $input;
        $user->save();

        //Redirect
        return redirect()->back()->with('success', 'Biography saved!');
    }

    public function changeDisplayName(Request $request)
    {
        $this->validate($request, [
            'display_fname' => 'required',
            'format' => 'required',
        ]);

        //Get user
        $user = Auth::user();

        //Run through profanity filter
        $check = new Check();
        if ($check->hasProfanity($request->get('display_fname'))) {
            return redirect()->back()->withInput()->with('error', 'Profanity was detected in your display name. Please use a more appropriate name, if you believe this is in error, please contact our <a href="/dashboard/tickets?create=yes">Webmaster.</a>');
        }

        //No swear words... give them the new name!
        $user->display_fname = $request->get('display_fname');
        if ($request->get('format') == 'showall') {
            $user->display_last_name = true;
            $user->display_cid_only = false;
        } elseif ($request->get('format') == 'showfirstcid') {
            $user->display_last_name = false;
            $user->display_cid_only = false;
        } else {
            $user->display_last_name = false;
            $user->display_cid_only = true;
        }
        $user->save();

        //Redirect
        return redirect()->back()->with('success', 'Display name saved!');
    }

    public function viewUserProfilePublic($id)
    {
        $user = User::whereId($id)->firstOrFail();

        return view('dashboard.me.publicuserprofile', compact('user'));
    }

    public function createBookingBan(Request $request, $id)
    {
        //Validate and get user
        $this->validate($request, [
            'reason' => 'required',
        ]);
        $user = User::whereId($id)->firstOrFail();

        //Is the user banned?
        if ($user->bookingBan()) {
            abort(403, 'This user is already banned.');
        }

        //No... let's create a ban
        $ban = new ControllerBookingsBan;
        $ban->reason = $request->get(Auth::user()->fullName('FLC').' at '.date('Y-m-d H:i:s').' '.$request->get('reason'));
        $ban->user_id = $user->id;
        $ban->timestamp = date('Y-m-d H:i:s');
        $ban->save();

        //Notify them
    }

    public function removeBookingBan(Request $request, $id)
    {
    }

    public function linkDiscord()
    {
        Log::info('Linking Discord for '.Auth::id());

        return Socialite::with('discord')->setScopes(['identify'])->redirect();
    }

    public function linkDiscordRedirect()
    {
        $discordUser = Socialite::driver('discord')->stateless()->user();
        if (! $discordUser) {
            abort(403, 'Discord OAuth failed.');
        }
        $user = Auth::user();
        if (User::where('discord_user_id', $discordUser->id)->first()) {
            return redirect()->route('dashboard.index')->with('error-modal', 'This Discord account has already been linked by another user.');
        }
        $user->discord_user_id = $discordUser->id;
        $user->discord_dm_channel_id = app(Discord::class)->getPrivateChannel($discordUser->id);
        $user->save();

        return redirect()->route('dashboard.index')->with('success', 'Linked with account '.$discordUser->nickname.'!');
    }

    public function joinDiscordServerRedirect()
    {
        $config = new Config(config('services.discord.client_id'), config('services.discord.client_secret'), config('services.discord.redirect_join'));

        return Socialite::with('discord')->setConfig($config)->setScopes(['identify', 'guilds.join'])->redirect();
    }

    public function joinDiscordServer()
    {
        $discord = new DiscordClient(['token' => config('services.discord.token')]);
        $config = new Config(config('services.discord.client_id'), config('services.discord.client_secret'), config('services.discord.redirect_join'));
        $discordUser = Socialite::driver('discord')->setConfig($config)->user();
        $args = [
            'guild.id' => 598023748741758976,
            'user.id' => intval($discordUser->id),
            'access_token' => $discordUser->token,
            'nick' => Auth::user()->fullName('FL'),
        ];
        if (Auth::user()->rosterProfile) {
            if (Auth::user()->rosterProfile->status == 'training') {
                $args['roles'] = [717155319981146182];
            } elseif (Auth::user()->rosterProfile->status == 'home') {
                $args['roles'] = [713914598750683157];
            } elseif (Auth::id() == '1427371') {
                $args['roles'] = [673725707259609093];
            }
        } else {
            $args['roles'] = [482835389640343562];
        }
        $discord->guild->addGuildMember($args);

        try {
            Auth::user()->notify(new DiscordWelcome());
        } catch (CouldNotSendNotification $e) {
            // do nothing
        }

        $discord->channel->createMessage(['channel.id' => 695849973585149962, 'content' => '<@'.$discordUser->id.'> ('.Auth::id().') has joined.']);

        return redirect()->route('dashboard.index')->with('success', 'You have joined the Winnipeg Discord server!');
    }

    public function unlinkDiscord()
    {
        $discord = new DiscordClient(['token' => config('services.discord.token')]);
        $user = Auth::user();
        if ($user->memberOfCZWGGuild()) {
            try {
                $discord->guild->removeGuildMember(['guild.id' => 598023748741758976, 'user.id' => $user->discord_user_id]);
                $discord->channel->createMessage(['channel.id' => 695849973585149962, 'content' => '<@'.$user->discord_user_id.'> ('.Auth::id().') has unlinked their account and has been kicked.']);
            } catch (Exception $ex) {
                Log::error($ex->getMessage());
            }
        }
        $user->discord_user_id = null;
        $user->discord_dm_channel_id = null;
        if ($user->avatar_mode == 2) {
            $user->avatar_mode = 0;
        }
        $user->save();

        return redirect()->route('dashboard.index')->with('info', 'Account unlinked.');
    }

    public function preferences()
    {
        $preferences = Auth::user()->preferences;

        return view('dashboard.me.preferences', compact('preferences'));
    }
}
