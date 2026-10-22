<?php

namespace App\Models\Events;

use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class EventConfirm extends Model
{
    protected $fillable = [
        'id', 'event_id', 'user_id', 'start_timestamp', 'end_timestamp', 'airport',
    ];

    protected function casts(): array
    {
        return [
            'start_timestamp' => 'datetime',
            'end_timestamp' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userHasApplied()
    {
        return EventConfirm::where('event_id', $this->event_id)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function rostermember(): BelongsTo
    {
        return $this->belongsTo(RosterMember::class, 'user_id', 'user_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
