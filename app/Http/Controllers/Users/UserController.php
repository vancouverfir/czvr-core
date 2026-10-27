<?php

namespace App\Http\Controllers\Users;

use App\Classes\DiscordClient;
use App\Http\Controllers\Controller;
use App\Models\AtcTraining\RosterMember;
use App\Models\Network\SessionLog;
use App\Models\Settings\AuditLogEntry;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Notifications\DiscordWelcome;
use App\Notifications\WelcomeNewUser;
use App\Support\Authorization\PermissionCatalog;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;
use mofodojodino\ProfanityFilter\Check;
use NotificationChannels\Discord\Discord;
use NotificationChannels\Discord\Exceptions\CouldNotSendNotification;
use SocialiteProviders\Manager\Config;

class UserController extends Controller
{
    public function privacyAccept(): RedirectResponse
    {
        $user = Auth::user();
        if ($user->init == 1) {
            return redirect()->route('index');
        }
        $user->init = 1;
        $user->save();
        $user->notify(new WelcomeNewUser($user));

        return redirect('/dashboard')->with('success', 'Welcome to Vancouver, '.$user->fname.'! We are glad to have you on board!');
    }

    public function privacyDeny(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user->init == 1) {
            return redirect()->route('index');
        }
        Auth::logout();

        AuditLogEntry::insert(User::find(1), 'User '.$user->fullName('FLC').' denied privacy policy - account deleted', User::find(1), 0);
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('index')->with('info', 'Your account has been removed as you have not accepted the privacy policy!');
    }

    public function viewAllUsers(): View
    {
        $users = User::with('roles')->withCount('directPermissions')->orderBy('id')->get();
        $individualCount = $users->where('direct_permissions_count', '>', 0)->count();

        return view('admin.users.index', compact('users', 'individualCount'));
    }

    public function viewProfile($id): View
    {
        $user = User::where('id', $id)->firstOrFail();
        $rosterMember = RosterMember::where('user_id', $id)->first();
        if ($rosterMember) {
            $logs = SessionLog::where('cid', $id)->get();
            $quarterlyHours = decimal_to_hm(RosterMember::where('cid', $id)->firstOrFail()->currency);

            // Start our array
            $time = [
                'del' => 0,
                'gnd' => 0,
                'twr' => 0,
                'dep' => 0,
                'app' => 0,
                'ctr' => 0,
            ];

            // Get our times per position for this month
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

            // Make the time's readable
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
            $quarterlyHours = 'N/A';
            $rosterMember = null;
            $connections = [];
            $time = [];
        }

        return view('roster.profile', compact('id', 'user', 'quarterlyHours', 'rosterMember', 'time', 'connections'));
    }

    public function viewConnections($id): View
    {
        $connections = SessionLog::where('cid', $id)->get()->sortByDesc('session_start');
        $user = User::where('id', $id)->firstOrFail();

        foreach ($connections as $c) {
            $c['date'] = substr($c['session_start'], 5, 2).'-'.substr($c['session_start'], 8, 2).'-'.substr($c['session_start'], 0, 4);
            $c['session_start'] = substr($c['session_start'], 11, 5);
            $c['session_end'] = substr($c['session_end'], 11, 5);
            $c['duration'] = decimal_to_hm($c['duration']);
        }

        return view('roster.connections', compact('connections', 'user', 'id'));
    }

    public function adminViewUserProfile($id): View
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
        $auditLog = AuditLogEntry::where('affected_id', $id)->get();
        $allRoles = Role::query()->with('permissions')->ordered()->get();
        $permissionGroups = PermissionCatalog::grouped();
        $user->load(['roles.permissions', 'directPermissions']);

        return view('admin.users.profile', compact('user', 'xml', 'certification', 'active', 'auditLog', 'allRoles', 'permissionGroups'));
    }

    public function updateAccess(Request $request, $id): RedirectResponse
    {
        $validated = $request->validate([
            'roles' => ['sometimes', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ]);

        $user = User::with(['roles', 'directPermissions'])->findOrFail($id);
        $editor = Auth::user();
        abort_if($user->isSystemAccount(), 403, 'System users cannot be modified!');

        // Roles and permissions the editor may not hand out keep their current state
        $requestedRoles = collect($validated['roles'] ?? [])->map(fn ($roleId) => (int) $roleId);
        $roleIds = Role::query()->get()
            ->filter(fn (Role $role) => $editor->canAssignRole($role)
                ? $requestedRoles->contains($role->id)
                : $user->roles->contains($role))
            ->modelKeys();

        $requestedPermissions = $validated['permissions'] ?? [];
        $directPermissions = collect(PermissionCatalog::slugs())
            ->filter(fn (string $slug) => $editor->canGrantPermission($slug)
                ? in_array($slug, $requestedPermissions, true)
                : $user->directPermissions->contains('slug', $slug))
            ->values()
            ->all();

        $user->roles()->sync($roleIds);
        $user->syncDirectPermissions($directPermissions);

        AuditLogEntry::insert(
            Auth::user(),
            'Updated roles and direct permissions',
            $user,
            0,
        );

        return back()->withSuccess('Updated roles and permissions for '.$user->fullName('FLC').'!');
    }

    public function deleteUser($id): RedirectResponse
    {
        $actor = Auth::user();
        $user = User::findOrFail($id);

        abort_if($user->is($actor), 403, 'You cannot delete yourself!');
        abort_if($user->isSystemAccount(), 403, 'System users cannot be deleted!');
        abort_if(
            $user->hasPermissionTo(PermissionCatalog::ROLES_MANAGE)
                && ! $actor->hasPermissionTo(PermissionCatalog::ROLES_MANAGE),
            403,
            'You cannot delete an access administrator!',
        );

        AuditLogEntry::insert($actor, 'Deleted user account', $user, 0);

        $user->forceFill([
            'fname' => 'Deleted',
            'lname' => 'User',
            'email' => 'deleted-'.$user->id.'@invalid.local',
            'rating_id' => null,
            'rating_short' => null,
            'rating_long' => null,
            'rating_GRP' => null,
            'region_code' => null,
            'region_name' => null,
            'division_code' => null,
            'division_name' => null,
            'subdivision_code' => null,
            'subdivision_name' => null,
            'display_fname' => 'Deleted',
            'bio' => null,
            'discord_user_id' => null,
            'discord_dm_channel_id' => null,
            'avatar' => '/img/default-profile-img.jpg',
            'avatar_mode' => 0,
            'deleted' => true,
        ])->save();
        $user->roles()->detach();
        $user->directPermissions()->detach();

        return redirect()->route('users.viewall')->with('info', 'User deleted!');
    }

    public function editUser($id): RedirectResponse
    {
        User::where('id', $id)->firstOrFail();

        return redirect()->route('users.viewprofile', $id);
    }

    public function changeUsersAvatar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $editUser = Auth::user();
        $user = User::findOrFail($validated['user_id']);
        $uploadedFile = $request->file('file');
        $filename = Str::uuid().'.'.$uploadedFile->extension();
        $path = $uploadedFile->storeAs('files/avatars/'.$user->id.'/'.$editUser->id, $filename, 'public');
        abort_unless($path, 500, 'The avatar could not be saved!');

        // The new file is stored, so the replaced upload can go.
        $user->deleteUploadedAvatar();
        $user->avatar = Storage::disk('public')->url($path);
        $user->avatar_mode = 1;
        $user->save();
        $user->forgetAvatarCache();
        AuditLogEntry::insert($editUser, 'Changed user avatar', $user, 0);

        return back()->with('success', 'Avatar changed!');
    }

    public function resetUsersAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        $editUser = Auth::user();
        $user = User::whereId($request->get('user_id'))->firstOrFail();
        if ($user->isAvatarDefault()) {
            abort(403, 'The avatar is already the default avatar.');
        }

        $user->deleteUploadedAvatar();
        $user->avatar = '/img/default-profile-img.jpg';
        $user->avatar_mode = 0;
        $user->save();
        $user->forgetAvatarCache();
        AuditLogEntry::insert($editUser, 'Reset user avatar', $user, 0);

        return redirect()->back()->with('success', 'Avatar reset!');
    }

    public function resetUsersBio(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $editUser = Auth::user();
        $user = User::whereId($request->get('user_id'))->firstOrFail();

        $user->bio = null;
        $user->save();

        AuditLogEntry::insert($editUser, 'Reset user bio', $user, 0);

        // Redirect
        return redirect()->back()->with('success', 'Biography reset!');
    }

    public function emailCreate($id): View
    {
        $user = User::where('id', $id)->firstOrFail();

        // return view('dashboard.users.email', compact('user'));
        abort(404, 'Not implemented');
    }

    public function emailStore(Request $request): void
    {
    }

    public function changeAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $user = Auth::user();
        $uploadedFile = $request->file('file');
        $filename = Str::uuid().'.'.$uploadedFile->extension();
        $path = $uploadedFile->storeAs('files/avatars/'.$user->id, $filename, 'public');
        abort_unless($path, 500, 'The avatar could not be saved!');

        // The new file is stored, so the replaced upload can go.
        $user->deleteUploadedAvatar();
        $user->avatar = Storage::disk('public')->url($path);
        $user->avatar_mode = 1;
        $user->save();
        $user->forgetAvatarCache();

        return redirect()->route('dashboard.index')->with('success', 'Avatar changed!');
    }

    public function changeAvatarDiscord(): RedirectResponse
    {
        $user = Auth::user();
        abort_unless($user->hasDiscord(), 422, 'Link a Discord account before using its avatar!');

        $user->deleteUploadedAvatar();
        $user->avatar = '/img/default-profile-img.jpg';
        $user->avatar_mode = 2;
        $user->save();
        $user->forgetAvatarCache();

        return redirect()->route('dashboard.index')->with('success', 'Avatar changed!');
    }

    public function resetAvatar(): RedirectResponse
    {
        $user = Auth::user();
        if ($user->isAvatarDefault()) {
            abort(403, 'Your avatar is already the default avatar!');
        }

        $user->deleteUploadedAvatar();
        $user->avatar = '/img/default-profile-img.jpg';
        $user->avatar_mode = 0;
        $user->save();
        $user->forgetAvatarCache();

        return redirect()->back()->with('success', 'Avatar reset!');
    }

    public function searchUsers(Request $request): mixed
    {
        abort_unless($request->ajax(), 400, 'AJAX requests only');

        $validated = $request->validate([
            'query' => ['required', 'string', 'max:100'],
        ]);
        $query = mb_strtolower($validated['query']);
        $users = User::query()
            ->where('id', 'like', '%'.$query.'%')
            ->orWhere('display_fname', 'like', '%'.$query.'%')
            ->orWhere('lname', 'like', '%'.$query.'%')
            ->limit(25)
            ->get();

        return response()->json($users);
    }

    public function editBioIndex(): View
    {
        return view('dashboard.me.editbio');
    }

    public function editBio(Request $request): RedirectResponse
    {
        $request->validate([
            'bio' => ['nullable', 'string', 'max:5000'],
        ]);

        // Get user
        $user = Auth::user();

        // Get input
        $input = $request->get('bio');

        // Run through profanity filter
        $check = new Check;
        if ($input && $check->hasProfanity($input)) {
            return redirect()->back()->withInput()->with('error-modal', 'Profanity was detected in your input, please remove it!');
        }

        // No swear words.. give them the new bio
        $user->bio = $input;
        $user->save();

        // Redirect
        return redirect()->back()->with('success', 'Biography saved!');
    }

    public function changeDisplayName(Request $request): RedirectResponse
    {
        $request->validate([
            'display_fname' => ['required', 'string', 'max:100'],
            'format' => ['required', 'in:showall,showfirstcid,showcid'],
        ]);

        // Get user
        $user = Auth::user();

        // Run through profanity filter
        $check = new Check;
        if ($check->hasProfanity($request->get('display_fname'))) {
            return redirect()->back()->withInput()->with('error', 'Profanity was detected in your display name! Please use a more appropriate name, if you believe this is in error, please contact our <a href="/dashboard/tickets?create=yes">Webmaster!</a>');
        }

        // No swear words... give them the new name!
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
        $user->forgetAvatarCache();

        // Redirect
        return redirect()->back()->with('success', 'Display name saved!');
    }

    public function viewUserProfilePublic($id): View
    {
        $user = User::whereId($id)->firstOrFail();

        return view('dashboard.me.publicuserprofile', compact('user'));
    }

    public function linkDiscord(): mixed
    {
        if (Auth::user()->hasDiscord()) {
            return redirect()->route('dashboard.index')->with('info', 'A Discord account is already linked!');
        }

        return $this->discordDriver('redirect')
            ->setScopes(['identify', 'guilds.join'])
            ->redirect();
    }

    public function linkDiscordRedirect(): RedirectResponse
    {
        try {
            $discordUser = $this->discordDriver('redirect')->user();
            $user = Auth::user();

            $existingUser = User::where('discord_user_id', $discordUser->id)->first();
            if ($existingUser && ! $existingUser->is($user)) {
                return redirect()->route('dashboard.index')->with('error-modal', 'This Discord account has already been linked!');
            }

            $user->discord_user_id = $discordUser->id;
            $user->discord_dm_channel_id = app(Discord::class)->getPrivateChannel($discordUser->id);
            $user->save();
            $user->forgetAvatarCache();

            $discord = new DiscordClient(config('services.discord.token'));
            $joined = $discord->AddGuildMember($discordUser->id, $discordUser->token, $user->fullName('FL'), []);

            try {
                $user->notify(new DiscordWelcome);
            } catch (CouldNotSendNotification) {
                // Linking succeeded even when a welcome direct message is unavailable
            }

            return $joined
                ? redirect()->route('dashboard.index')->with('success', 'Joined the Discord with account '.$discordUser->nickname.'!')
                : redirect()->route('dashboard.index')->with('error-modal', 'Linked Discord account '.$discordUser->nickname.', but you could not be added to the Vancouver Discord server! Please contact staff if this keeps happening.');
        } catch (Exception $exception) {
            Log::warning('Discord OAuth callback failed.', ['exception' => $exception::class]);

            return redirect()->route('dashboard.index')
                ->with('error-modal', 'Discord authentication was canceled or failed!');
        }
    }

    public function joinDiscordServerRedirect(): mixed
    {
        if (! Auth::user()->hasDiscord()) {
            return redirect()->route('me.discord.link');
        }

        return $this->discordDriver('redirect_join')
            ->setScopes(['identify', 'guilds.join'])
            ->redirect();
    }

    public function joinDiscordServer(): RedirectResponse
    {
        try {
            $discordUser = $this->discordDriver('redirect_join')->user();
            $user = Auth::user();
            abort_unless((string) $discordUser->id === (string) $user->discord_user_id, 403, 'Discord account mismatch!');

            $discord = new DiscordClient(config('services.discord.token'));

            return $discord->AddGuildMember($discordUser->id, $discordUser->token, $user->fullName('FL'), [])
                ? redirect()->route('dashboard.index')->with('success', 'Rejoined the Vancouver Discord server!')
                : redirect()->route('dashboard.index')->with('error-modal', 'You could not be added to the Vancouver Discord server! Please contact staff if this keeps happening.');
        } catch (Exception $exception) {
            Log::warning('Discord server join callback failed.', ['exception' => $exception::class]);

            return redirect()->route('dashboard.index')
                ->with('error-modal', 'Discord authentication was canceled or failed!');
        }
    }

    private function discordDriver(string $redirectKey): mixed
    {
        $config = new Config(
            (string) config('services.discord.client_id'),
            (string) config('services.discord.client_secret'),
            (string) config('services.discord.'.$redirectKey),
        );

        return Socialite::driver('discord')->setConfig($config);
    }

    public function unlinkDiscord(): RedirectResponse
    {
        $user = Auth::user();
        // memberOfCZVRGuild() is false without a bot token, so the client is only built when it can be used
        if ($user->memberOfCZVRGuild()) {
            $discord = new DiscordClient(config('services.discord.token'));
            try {
                $discord->RemoveGuildMember($user->discord_user_id);
                $discord->SendAuditMessage('<@'.$user->discord_user_id.'> ('.Auth::id().') has unlinked their account and has been kicked!');
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
        $user->forgetAvatarCache();

        return redirect()->route('dashboard.index')->with('info', 'Account successfully unlinked!');
    }

    public function preferences(): View
    {
        return view('dashboard.me.preferences');
    }
}
