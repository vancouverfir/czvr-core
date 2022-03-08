<?php

namespace App\Models\ControllerBookings;

use App\Models\Network\VatsimPosition;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class ControllerBooking extends Model
{
    protected $fillable = [
        'user_id', 'position_id', 'start_time', 'end_time', 'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(VatsimPosition::class, 'position_id');
    }
}
