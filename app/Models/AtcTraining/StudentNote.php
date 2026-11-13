<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentNote extends Model
{
    protected $fillable = [
        'student_id', 'author_cid', 'title', 'content', 'created_at',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_cid');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
