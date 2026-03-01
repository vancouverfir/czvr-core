<?php

namespace App\Models\Events;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ControllerApplication extends Model
{
    protected $table = 'event_controller_applications';

    protected $fillable = [
        'id', 'event_id', 'user_id', 'start_availability_timestamp', 'end_availability_timestamp', 'airport', 'comments', 'submission_timestamp',
    ];

    protected function casts(): array
    {
        return [
            'start_availability_timestamp' => 'datetime',
            'end_availability_timestamp' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function flatpickr_limits()
    {
        $start = Carbon::create($this->start_availability_timestamp);
        $end = Carbon::create($this->end_availability_timestamp);

        return [
            $start->format('H:i'),
            $end->format('H:i'),
        ];
    }
}
