<?php

namespace App\Models\Users;

use App\Classes\DiscordClient;
use App\Models\AtcTraining;
use App\Models\Events;
use App\Models\News;
use App\Models\Tickets;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 'fname', 'lname', 'email', 'rating_id', 'rating_short', 'rating_long', 'rating_GRP',
        'reg_date', 'permissions', 'init', 'gdpr_subscribed_emails', 'avatar', 'bio', 'display_cid_only', 'display_fname', 'display_last_name',
        'discord_user_id', 'discord_dm_channel_id', 'avatar_mode',
        'region_code', 'region_name', 'division_code', 'division_name', 'subdivision_code', 'subdivision_name', 'used_connect',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Return articles that the user has written.
     */
    public function news(): HasMany
    {
        return $this->hasMany(News\News::class);
    }

    public function eventApplications(): HasMany
    {
        return $this->hasMany(Events\ControllerApplication::class);
    }

    public function eventConfirms(): HasMany
    {
        return $this->hasMany(Events\EventConfirm::class);
    }

    public function instructorProfile(): HasOne
    {
        return $this->hasOne(AtcTraining\Instructor::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(AtcTraining\Student::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Tickets\Ticket::class);
    }

    public function ticketReplies(): HasMany
    {
        return $this->hasMany(Tickets\TicketReply::class);
    }

    public function staffProfile(): HasOne
    {
        return $this->hasOne(StaffMember::class);
    }

    public function rosterProfile(): HasOne
    {
        return $this->hasOne(AtcTraining\RosterMember::class);
    }

    public function notes(): HasMany
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

    public function discordBans(): HasMany
    {
        return $this->hasMany(DiscordBan::class);
    }

    public function avatar()
    {
        if ($this->avatar_mode == 0) {
            return Cache::remember('users.'.$this->id.'.initialsavatar', 172800, function () {
                $name = $this->fullName('FL');
                $initials = collect(explode(' ', $name))
                    ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
                    ->take(2)
                    ->implode('');

                $image = \Intervention\Image\ImageManager::gd()->create(125, 125);
                $image->fill('#3A6F26');
                $image->text($initials, 62, 62, function ($font) {
                    $font->color('#6CC24A');
                    $font->size(48);
                    $font->align('center');
                    $font->valign('middle');
                });

                $path = 'public/files/avatars/'.$this->id.'/initials.png';
                Storage::put($path, $image->toPng());

                return Storage::url($path);
            });
        } elseif ($this->avatar_mode == 1) {
            return $this->avatar;
        } else {
            return $this->getDiscordAvatar();
        }
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreferences::class);
    }
}
