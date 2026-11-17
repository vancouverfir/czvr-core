<?php

namespace App\Models\Publications;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingMinutes extends Model
{
    protected $fillable = [
        'user_id', 'title', 'link',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
