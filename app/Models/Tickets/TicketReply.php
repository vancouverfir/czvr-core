<?php

namespace App\Models\Tickets;

use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'user_id', 'ticket_id', 'message', 'submission_time',
    ];

    /**
     * Retrieve the list of fields that should be processed as Markdown.
     * Required for HasMarkdownFields.
     *
     * @return array An array of field names that are treated as Markdown.
     */
    protected function getMarkdownFields(): array
    {
        return ['message'];
    }

    protected $table = 'ticket_reply';

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submission_time_pretty()
    {
        return Carbon::create($this->submission_time)->toDayDateTimeString().' Zulu';
    }

    public function statusEvent(): ?array
    {
        $message = trim((string) $this->message);

        if (preg_match('/^(?:Reopened this ticket\.|Ticket has been reopened by )/', $message)) {
            return ['action' => 'reopened this ticket', 'comment' => null];
        }

        if (preg_match('/^(?:Placed this ticket on hold\.|Ticket has been placed on hold by )/', $message)) {
            return ['action' => 'put this ticket on hold', 'comment' => null];
        }

        if (preg_match('/^Closed this ticket\.\s*(.*)$/s', $message, $m)) {
            return ['action' => 'closed this ticket', 'comment' => trim($m[1]) ?: null];
        }

        if (preg_match('/^Ticket closed by .+?(?: with comment: (.*?))?\. Closed at /s', $message, $m)) {
            return ['action' => 'closed this ticket', 'comment' => isset($m[1]) ? trim($m[1]) : null];
        }

        return null;
    }
}
