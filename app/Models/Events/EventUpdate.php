<?php

namespace App\Models\Events;

use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Parsedown;

class EventUpdate extends Model
{
    protected $fillable = [
        'event_id', 'user_id', 'title', 'content', 'created_timestamp', 'slug',
    ];

    public function event()
    {
        return $this->belongTo(Event::class);
    }

    public function html()
    {
        return new HtmlString(app(Parsedown::class)->text($this->content));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function created_pretty()
    {
        $t = Carbon::create($this->created_timestamp);

        return $t->day.' '.$t->monthName.' '.$t->year.' '.$t->format('H:i').' Zulu';
    }

    public function author_pretty()
    {
        return $this->user->fullName('FLC');
    }
}
