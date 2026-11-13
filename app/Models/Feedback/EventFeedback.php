<?php

namespace App\Models\Feedback;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventFeedback extends Model
{
    use SoftDeletes;

    protected $table = 'event_feedback';

    protected $hidden = ['id'];

    protected $fillable = [
        'user_id', 'subject', 'content',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
