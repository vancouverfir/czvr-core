<?php

namespace App\Models\Users;

use App\Classes\DiscordClient;
use App\Models\AtcTraining;
use App\Models\ControllerBookings;
use App\Models\Events;
use App\Models\News;
use App\Models\Tickets;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'fname', 'lname', 'email', 'rating_id', 'rating_short', 'rating_long', 'rating_GRP',
        'reg_date', 'permissions', 'init', 'gdpr_subscribed_emails', 'avatar', 'bio', 'display_cid_only', 'display_fname', 'display_last_name',
        'discord_user_id', 'discord_dm_channel_id', 'avatar_mode',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Return articles that the user has written.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function news()
    {
        return $this->hasMany(News\News::class);
    }

    public function applications()
    {
        return $this->hasMany(AtcTraining\Application::class);
    }

    public function eventApplications()
    {
        return $this->hasMany(Events\ControllerApplication::class);
    }

    public function eventConfirms()
    {
        return $this->hasMany(Events\EventConfirm::class);
    }

    public function instructorProfile()
    {
        return $this->hasOne(AtcTraining\Instructor::class);
    }

    public function studentProfile()
    {
        return $this->hasOne(AtcTraining\Student::class);
    }

    public function tickets()
    {
        return $this->hasMany(Tickets\Ticket::class);
    }

    public function ticketReplies()
    {
        return $this->hasMany(Tickets\TicketReply::class);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffMember::class);
    }

    public function rosterProfile()
    {
        return $this->hasOne(AtcTraining\RosterMember::class);
    }

    public function notes()
    {
        return $this->hasMany(UserNote::class);
    }

    public function userSinceInDays()
    {
        $created = $this->created_at;
        $now = Carbon::now();
        $difference = $created->diff($now)->days;

        return $difference;
    }

    public function permissions()
    {
        return match ($this->permissions) {
            0 => 'Guest',
            1 => 'Controller/Trainee',
            2 => 'Mentor',
            3 => 'Instructor',
            4 => 'Staff Member',
            5 => 'Administrator',
            default => 'Unknown',
        };
    }

    public function fullName($format)
    {
        //display name check
        if ($this->display_cid_only == true) {
            return strval($this->id);
        }

        if ($format == 'FLC') {
            if ($this->display_last_name == true) {
                return $this->display_fname.' '.$this->lname.' '.$this->id;
            } else {
                return $this->display_fname.' '.$this->id;
            }
        } elseif ($format === 'FL') {
            if ($this->display_last_name == true) {
                return $this->display_fname.' '.$this->lname;
            } else {
                return $this->display_fname;
            }
        } elseif ($format === 'F') {
            return $this->display_fname;
        }

        return null;
    }

    public function isAvatarDefault()
    {
        if ($this->avatar_mode == 0) {
            return true;
        }

        return false;
    }

    public function certified()
    {
        if (! $this->rosterProfile()) {
            return false;
        }

        return true;
    }

    public function bookingBanned()
    {
        if (! ControllerBookings\ControllerBookingsBan::where('user_id', $this->id)->first()) {
            return false;
        }

        return true;
    }

    public function routeNotificationForDiscord()
    {
        return $this->discord_dm_channel_id;
    }

    public function hasDiscord()
    {
        if (! $this->discord_user_id) {
            return false;
        }

        return true;
    }

    public function getDiscordUser()
    {
        return Cache::remember('users.discorduserdata.'.$this->id, 84600, function () {
            if (! $this->discord_user_id) {
                return null;
            }

            $discord = new DiscordClient(config('services.discord.token'));

            return $discord->GetDiscordUser($this->discord_user_id);
        });
    }

    public function getDiscordAvatar()
    {
        return Cache::remember('users.discorduserdata.'.$this->id.'.avatar', 21600, function () {
            $user = $this->getDiscordUser();
            $url = 'https://cdn.discordapp.com/avatars/'.$user->id.'/'.$user->avatar.'.png';

            return $url;
        });
    }

    public function memberOfCZVRGuild()
    {
        $discord = new DiscordClient(config('services.discord.token'));
        try {
            if ($discord->GetGuildMember($this->discord_user_id)) {
                return true;
            }
        } catch (Throwable $ex) {
            return false;
        }

        return false;
    }

    public function currentDiscordBan()
    {
        $ban = DiscordBan::whereDate('ban_end_timestamp', '>', Carbon::now())->where('user_id', $this->id)->first();
        if ($ban) {
            return $ban;
        } else {
            return null;
        }
    }

    public function discordBans()
    {
        return $this->hasMany(DiscordBan::class);
    }

    public function avatar()
    {
        if ($this->avatar_mode == 0) {
            return Cache::remember('users.'.$this->id.'.initialsavatar', 172800, function () {
                $avatar = new InitialAvatar();
                $image = $avatar
                    ->name($this->fullName('FL'))
                    ->size(125)
                    ->background('#3A6F26')
                    ->color('#6CC24A')
                    ->generate();
                Storage::put('public/files/avatars/'.$this->id.'/initials.png', (string) $image->encode('png'));

                return Storage::url('public/files/avatars/'.$this->id.'/initials.png');
                imagedestroy($image);
            });
        } elseif ($this->avatar_mode == 1) {
            return $this->avatar;
        } else {
            return $this->getDiscordAvatar();
        }
    }

    public function preferences()
    {
        return $this->hasOne(UserPreferences::class);
    }
}
