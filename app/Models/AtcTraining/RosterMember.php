<?php

namespace App\Models\AtcTraining;

use App\Models\Events\EventConfirm;
use App\Models\Network\SessionLog;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RosterMember extends Model

    // Vancouver CONTROLLERS
{
    protected $table = 'roster';

    protected $fillable = [
        'cid', 'user_id', 'full_name', 'status', 'active', 'currency', 'rating_hours',
        'fss', 'delgnd', 'delgnd_t2', 'twr', 'twr_t2', 'dep', 'app', 'app_t2',
        'ctr', 'remarks', 'visit',
    ];

    public const CERTIFICATION_FIELDS = ['fss', 'delgnd', 'delgnd_t2', 'twr', 'twr_t2', 'dep', 'app', 'app_t2', 'ctr'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasAnyCertification(): bool
    {
        foreach (self::CERTIFICATION_FIELDS as $field) {
            if ((int) $this->{$field} > 0) {
                return true;
            }
        }

        return false;
    }

    public function canResetRatingHours(): bool
    {
        return (bool) $this->user?->studentProfile?->consolidationIsPending();
    }

    public function getLeaderboardHours()
    { // Get hours from leaderboard
        return $this->monthly_hours;
    }

    // Every active roster member needs the same quarterly hours for their status (config/currency.php)
    public function meetsActivityRequirement(): bool
    {
        return $this->active == '1' && (float) $this->currency >= (float) config('currency.'.$this->status, 3);
    }

    public function eventconfirm(): HasMany
    {
        return $this->hasMany(EventConfirm::class, 'user_id', 'user_id');
    }

    public function sessionLogs(): HasMany
    {
        return $this->hasMany(SessionLog::class, 'roster_member_id');
    }
}
