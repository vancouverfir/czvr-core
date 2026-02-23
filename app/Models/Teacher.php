<?php

namespace App\Models;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    public $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_cid');
    }
}
