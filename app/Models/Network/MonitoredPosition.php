<?php

namespace App\Models\Network;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

// A position monitored by the activity bot
class MonitoredPosition extends Model
{
    protected $fillable = ['identifier'];

    public function sessions(): HasMany
    {
        return $this->hasMany(SessionLog::class, 'monitored_position_id');
    }

    public function latestSession(): HasOne
    {
        return $this->hasOne(SessionLog::class, 'monitored_position_id')->latestOfMany('session_start');
    }

    public function lastOnlinePretty(): string
    {
        $session = $this->latestSession;
        if (! $session) {
            return 'Never used';
        }

        return ($session->session_end ?? $session->session_start)->diffForHumans();
    }
}
