<?php

namespace App\Models\Events;

use App\Models\AtcTraining\RosterMember;
use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'id', 'name', 'start_timestamp', 'end_timestamp', 'user_id', 'description', 'image_url', 'controller_applications_open', 'departure_icao', 'arrival_icao', 'slug',
    ];

    /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields.
     *
     * @return array An array of field names that are treated as Markdown.
     */
    protected function getMarkdownFields(): array
    {
        return ['description'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getConfirmedAttribute()
    {
        $check = RosterMember::where('user_id', Auth::id())->first();

        return EventConfirm::where('event_id', $this->id)->where('roster_id', $check->id)->first();
    }

    public function updates()
    {
        return $this->hasMany(EventUpdate::class);
    }

    public function controllerApplications()
    {
        return $this->hasMany(ControllerApplication::class);
    }

    public function starts_in_pretty()
    {
        $t = Carbon::create($this->start_timestamp);

        return $t->diffForHumans();
    }

    public function start_timestamp_pretty()
    {
        $t = Carbon::create($this->start_timestamp);

        return $t->monthName.' '.$t->day.', '.$t->year.' '.$t->format('H:i').'z';
    }

    public function flatpickr_limits()
    {
        $start = Carbon::create($this->start_timestamp);
        $end = Carbon::create($this->end_timestamp);

        return [
            $start->format('H:i'),
            $end->format('H:i'),
        ];
    }

    public function end_timestamp_pretty()
    {
        $t = Carbon::create($this->end_timestamp);

        return $t->monthName.' '.$t->day.', '.$t->year.' '.$t->format('H:i').'z';
    }

    public function event_in_past()
    {
        $end = Carbon::create($this->end_timestamp);
        if (! $end->isPast()) {
            return false;
        }

        return true;
    }

    public function userHasApplied()
    {
        if (ControllerApplication::where('event_id', $this->id)->where('user_id', Auth::id())->first()) {
            return true;
        }

        return false;
    }

    public function eventconfirm()
    {
        return $this->hasMany(EventConfirm::class);
    }
}
