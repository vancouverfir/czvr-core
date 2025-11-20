<?php

namespace App\Models\Events;

use Illuminate\Database\Eloquent\Model;

class EventPosition extends Model
{
    protected $fillable = [
        'id', 'event_id', 'position',
    ];

    public function hasControllers($position, $event_id)
    {
        $controllers = EventConfirm::where('event_id', $event_id)->get();

        foreach ($controllers as $c) {
            if ($c->position == $position) {
                return true;
            }
        }

        return false;
    }
}
