<?php

namespace App\Models\Network;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Log of all sessions
class SessionLog extends Model
{
    // session_start and session_end are in format 'Y-m-d H:i:s'
    protected $fillable = [
        'id', 'roster_member_id', 'cid', 'session_start', 'session_end', 'callsign', 'duration', 'emails_sent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userLogs(): HasMany
    {
        return $this->hasMany(User::class, 'cid');
    }
}
