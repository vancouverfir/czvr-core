<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentNote extends Model
{
    protected $fillable = [
        'student_id', 'author_id', 'title', 'content', 'created_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studentnote(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function getInstructorAttribute()
    {
        return Instructor::whereId($this->author_id)->firstOrFail();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
