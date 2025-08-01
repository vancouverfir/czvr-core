<?php

namespace App\Models\Tickets;

use App\Models\Users\StaffMember;
use App\Models\Users\User;
use App\Traits\HasMarkdownFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasMarkdownFields;

    protected $fillable = [
        'user_id', 'ticket_id', 'staff_member_id', 'staff_member_cid', 'title', 'message', 'status', 'submission_time',
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

    public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id', 'ticket_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function staff_member()
    {
        return $this->belongsTo(StaffMember::class);
    }

    public function updated_at_pretty()
    {
        return Carbon::create($this->updated_at->toDateTimeString())->diffForHumans();
    }

    public function submission_time_pretty()
    {
        return Carbon::create($this->submission_time)->toDayDateTimeString().' Zulu';
    }
}
