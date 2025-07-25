<?php

namespace App\Models\Tickets;

use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class TicketReply extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'user_id', 'ticket_id', 'message', 'submission_time',
    ];

    /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields
     *
     * @return array An array of field names that are treated as Markdown.
     */
    protected function getMarkdownFields(): array
    {
        return ['message'];
    }

    protected $table = 'ticket_reply';

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function submission_time_pretty()
    {
        return Carbon::create($this->submission_time)->toDayDateTimeString().' Zulu';
    }
}
