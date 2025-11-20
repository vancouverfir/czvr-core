<?php

namespace App\Models\Events;

use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use Auth;
use Illuminate\Database\Eloquent\Model;

class EventConfirm extends Model
{
    protected $fillable = [
        'id', 'event_id', 'user_id', 'start_timestamp', 'end_timestamp', 'airport',
    ];

    protected $casts = [
        'start_timestamp' => 'datetime',
        'end_timestamp' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userHasApplied()
    {
        if (EventConfirm::where('event_id', $this->id)->where('user_id', Auth::id())->first()) {
            return true;
        }

        return false;
    }

    public function rostermember()
    {
        return $this->belongsTo(RosterMember::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
