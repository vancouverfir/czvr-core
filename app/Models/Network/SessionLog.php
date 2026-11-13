<?php

namespace App\Models\Network;

use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Log of all sessions
class SessionLog extends Model
{
    // session_start and session_end are in format 'Y-m-d H:i:s'
    protected $fillable = [
        'id', 'roster_member_id', 'cid', 'session_start', 'session_end', 'monitored_position_id', 'callsign', 'duration', 'emails_sent',
    ];

    protected function casts(): array
    {
        return [
            'session_start' => 'datetime',
            'session_end' => 'datetime',
            'duration' => 'float',
            'emails_sent' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cid');
    }

    public function userLogs(): BelongsTo
    {
        return $this->user();
    }

    public function rosterMember(): BelongsTo
    {
        return $this->belongsTo(RosterMember::class, 'roster_member_id');
    }

    public function monitoredPosition(): BelongsTo
    {
        return $this->belongsTo(MonitoredPosition::class, 'monitored_position_id');
    }
}
