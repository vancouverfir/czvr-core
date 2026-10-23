<?php

namespace App\Models\Users;

use App\Classes\DiscordClient;
use App\Models\AtcTraining;
use App\Models\Events;
use App\Models\News;
use App\Models\Tickets;
use App\Models\Users\Concerns\HasRolesAndPermissions;
use App\Support\Authorization\SystemRoleCatalog;
use App\Support\VatcanFacilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager;
use Throwable;

class User extends Authenticatable
{
    use HasFactory, HasRolesAndPermissions, Notifiable;

    public $incrementing = false;

    protected $keyType = 'int';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id', 'fname', 'lname', 'email', 'rating_id', 'rating_short', 'rating_long', 'rating_GRP',
        'reg_date', 'init', 'gdpr_subscribed_emails', 'avatar', 'bio', 'display_cid_only', 'display_fname', 'display_last_name',
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

    public function activeInstructorProfile(): HasOne
    {
        return $this->hasOne(AtcTraining\Instructor::class)->where('active', true);
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

    public function canWaiveTrainingRequirements(): bool
    {
        return $this->hasRole(SystemRoleCatalog::ADMINISTRATOR)
            && $this->staffProfile()->whereIn('position', ['FIR Chief', 'Deputy FIR Chief', 'Chief Instructor'])->exists();
    }

    /** The Staff and Administrator roles can only be given, removed or edited by an administrator */
    public function canAssignRole(Role $role): bool
    {
        return $this->hasRole(SystemRoleCatalog::ADMINISTRATOR)
            || ! in_array($role->slug, SystemRoleCatalog::administratorOnlySlugs(), true);
    }

    /** Non-administrators can only hand out permissions they hold themselves */
    public function isSystemAccount(): bool
    {
        return in_array((int) $this->id, [1, 2], true);
    }

    public function canGrantPermission(string $slug): bool
    {
        return $this->hasRole(SystemRoleCatalog::ADMINISTRATOR) || $this->hasPermissionTo($slug);
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

    public function isController(): bool
    {
        return $this->hasRole('controller');
    }

    public function accessLabel(): string
    {
        if ($this->isAdministrator()) {
            return 'Administrator';
        }

        if ($this->hasRole('instructor')) {
            return 'Instructor';
        }

        if ($this->hasRole('mentor')) {
            return 'Mentor';
        }

        if ($this->hasRole('staff')) {
            return 'Staff';
        }

        return $this->isController() ? 'Controller/Trainee' : 'Guest';
    }

    public function homeFacilityName(): string
    {
        return VatcanFacilities::homeFacilityName((int) $this->id)
            ?: ($this->subdivision_name ?: ($this->division_name ?: ($this->region_name ?: 'Unknown')));
    }

    public function emailUnsubscribeUrl(): string
    {
        return URL::signedRoute('emails.unsubscribe', ['user' => $this->id]);
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

    public function certified(): bool
    {
        return $this->rosterProfile()->exists();
    }

    /** Event controller applications are open to roster members holding at least one position certification. */
    public function canApplyForEvents(): bool
    {
        return (bool) $this->rosterProfile?->hasAnyCertification();
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

            $token = config('services.discord.token');
            if (! is_string($token) || $token === '') {
                return null;
            }

            return (new DiscordClient($token))->GetDiscordUser($this->discord_user_id);
        });
    }

    public function getDiscordAvatar()
    {
        return Cache::remember('users.discorduserdata.'.$this->id.'.avatar', 21600, function () {
            $user = $this->getDiscordUser();

            if (! $user?->id || ! $user?->avatar) {
                return '/img/default-profile-img.jpg';
            }

            $url = 'https://cdn.discordapp.com/avatars/'.$user->id.'/'.$user->avatar.'.png';

            return $url;
        });
    }

    public function memberOfCZVRGuild(): bool
    {
        $token = config('services.discord.token');
        if (! $this->discord_user_id || ! is_string($token) || $token === '') {
            return false;
        }

        $discord = new DiscordClient($token);
        try {
            if ($discord->GetGuildMember($this->discord_user_id)) {
                return true;
            }
        } catch (Throwable $ex) {
            return false;
        }

        return false;
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

                $width = 125;
                $height = 125;
                $image = ImageManager::usingDriver(Driver::class)->createImage($width, $height);

                $centerX = $image->width() / 2;
                $centerY = $image->height() / 2;

                $image->fill('#3A6F26');

                $image->text($initials, $centerX, $centerY, function ($font) {
                    $font->file(public_path('webfonts/static/Roboto_SemiCondensed-Regular.ttf'));
                    $font->size(65);
                    $font->color('#6CC24A');

                    $font->align('center', 'center');
                });

                $path = 'files/avatars/'.$this->id.'/initials.png';
                Storage::disk('public')->put($path, (string) $image->encodeUsingFormat(Format::PNG));

                return Storage::disk('public')->url($path);
            });
        } elseif ($this->avatar_mode == 1) {
            return $this->avatar;
        } else {
            return $this->getDiscordAvatar();
        }
    }

    public function forgetAvatarCache(): void
    {
        Cache::forget('users.discorduserdata.'.$this->id);
        Cache::forget('users.discorduserdata.'.$this->id.'.avatar');
        Cache::forget('users.'.$this->id.'.initialsavatar');
    }

    public function deleteUploadedAvatar(): void
    {
        $path = $this->uploadedAvatarPath();

        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }
    }

    private function uploadedAvatarPath(): ?string
    {
        $urlPath = (string) parse_url((string) ($this->getAttributes()['avatar'] ?? ''), PHP_URL_PATH);

        if (! Str::startsWith($urlPath, '/storage/')) {
            return null;
        }

        $path = Str::after($urlPath, '/storage/');

        if (! Str::startsWith($path, 'files/avatars/'.$this->id.'/')
            || Str::contains($path, '..')
            || basename($path) === 'initials.png') {
            return null;
        }

        return $path;
    }
}
