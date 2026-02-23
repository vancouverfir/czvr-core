<?php

namespace App\Models\Events;

use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventUpdate extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'id', 'event_id', 'user_id', 'title', 'content', 'created_timestamp', 'slug',
    ];

    /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields.
     *
     * @return array An array of field names that are treated as Markdown.
     */
    protected function getMarkdownFields(): array
    {
        return ['content'];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function user(): BelongsTo
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
