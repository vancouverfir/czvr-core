<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructingSession extends Model
{
    protected $fillable = [
        'instructor_id', 'student_id', 'title', 'start_time', 'end_time', 'instructor_comment',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function instructorUser()
    {
        if ($this->instructor) {
            return $this->instructor->user;
        }

        return User::find($this->instructor_id);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
