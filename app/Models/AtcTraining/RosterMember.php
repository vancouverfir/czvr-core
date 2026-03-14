<?php

namespace App\Models\AtcTraining;

use App\Models\Events\EventConfirm;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RosterMember extends Model

    // Vancouver CONTROLLERS
{
    protected $table = 'roster';

    protected $fillable = [
        'cid', 'user_id', 'status', 'full_name', 'rating', 'del', 'gnd', 'twr', 'twr_t2', 'dep', 'app', 'app_t2', 'ctr', 'currency', 'rating_hours', 'remarks', 'active', 'home_fir', 'visit', 'staff',

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLeaderboardHours()
    { // Get hours from leaderboard
        return $this->monthly_hours;
    }

    public function meetsActivityRequirement()
    {
        if ($this->status == 'visit' && $this->active == '1' && $this->currency >= 3.0) {
            return true;
        } elseif ($this->status == 'home' && $this->staff == 'mentor' && $this->active == '1' && $this->currency >= 3.0) {
            return true;
        } elseif ($this->status == 'home' && $this->staff == 'staff' && $this->active == '1' && $this->currency >= 3.0) {
            return true;
        } elseif ($this->status == 'home' && $this->staff == 'exec' && $this->active == '1' && $this->currency >= 5.0) {
            return true;
        } elseif ($this->status == 'home' && $this->active == '1' && $this->currency >= 3.0) {
            return true;
        } elseif ($this->status == 'instructor' && $this->staff == 'exec' && $this->active == '1' && $this->currency >= 5.0) {
            return true;
        } elseif ($this->status == 'instructor' && $this->active == '1' && $this->currency >= 3.0) {
            return true;
        } elseif ($this->status == 'instructor' && $this->active == '1' && $this->currency >= 3.0) {
            return true;
        } else {
            return $IsActive = false;
        }

        return false;
    }

    public function eventconfirm(): HasMany
    {
        return $this->hasMany(EventConfirm::class);
    }
}
