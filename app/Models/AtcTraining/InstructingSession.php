<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructingSession extends Model
{
    protected $fillable = [
        'instructor_id', 'instructor_user_id', 'student_id', 'title', 'start_time', 'end_time', 'instructor_comment',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_user_id');
    }

    public function instructorUser(): ?User
    {
        if ($this->instructor_user_id !== null) {
            return $this->assignedUser;
        }

        return $this->instructor?->user ?? User::find($this->instructor_id);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
