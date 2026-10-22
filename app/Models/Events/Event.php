<?php

namespace App\Models\Events;

use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'id', 'name', 'start_timestamp', 'end_timestamp', 'user_id', 'description', 'image_url', 'controller_applications_open', 'departure_icao', 'arrival_icao', 'slug',
    ];

    protected function casts(): array
    {
        return [
            'start_timestamp' => 'datetime',
            'end_timestamp' => 'datetime',
            'controller_applications_open' => 'boolean',
        ];
    }

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getConfirmedAttribute()
    {
        return $this->eventconfirm()
            ->where('user_id', Auth::id())
            ->first();
    }

    public function updates(): HasMany
    {
        return $this->hasMany(EventUpdate::class);
    }

    public function controllerApplications(): HasMany
    {
        return $this->hasMany(ControllerApplication::class);
    }

    public function starts_in_pretty()
    {
        return $this->start_timestamp->diffForHumans();
    }

    public function start_timestamp_pretty()
    {
        $t = $this->start_timestamp;

        return $t->monthName.' '.$t->day.', '.$t->year.' '.$t->format('H:i').'z';
    }

    public function flatpickr_limits()
    {
        $start = $this->start_timestamp;
        $end = $this->end_timestamp;

        return [
            $start->format('H:i'),
            $end->format('H:i'),
        ];
    }

    public function end_timestamp_pretty()
    {
        $t = $this->end_timestamp;

        return $t->monthName.' '.$t->day.', '.$t->year.' '.$t->format('H:i').'z';
    }

    public function event_in_past()
    {
        return $this->end_timestamp->isPast();
    }

    public function userHasApplied()
    {
        if (ControllerApplication::where('event_id', $this->id)->where('user_id', Auth::id())->first()) {
            return true;
        }

        return false;
    }

    public function eventconfirm(): HasMany
    {
        return $this->hasMany(EventConfirm::class);
    }
}
