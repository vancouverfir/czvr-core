<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use App\Notifications\CreateStudent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'times', 'position', 'status', 'instructor_id', 'renewal_token', 'renewed_at', 'renewal_notified_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    public function solorequest(): HasMany
    {
        return $this->hasMany(SoloRequest::class);
    }

    public function instructingSessions(): HasMany
    {
        return $this->hasMany(InstructingSession::class);
    }

    public function trainingNotes(): HasMany
    {
        return $this->hasMany(StudentNote::class)->orderBy('created_at', 'desc');
    }

    protected function casts(): array
    {
        return [
        'renewed_at' => 'datetime',
        'renewal_notified_at' => 'datetime',
        ];
    }

    public function labels(): HasMany
    {
        return $this->hasMany(StudentInteractiveLabels::class);
    }

    public function assignLabel(StudentLabel $label)
    {
        $link = new StudentInteractiveLabels([
            'student_id' => $this->id,
            'student_label_id' => $label->id,
        ]);
        $link->save();
    }

    public function hasLabel($label_text)
    {
        if (! StudentLabel::whereName($label_text)->first()) {
            return false;
        }
        if ($label = StudentInteractiveLabels::where('student_id', $this->id)
            ->where(
                'student_label_id',
                StudentLabel::whereName($label_text)->first()->id
            )->first()
        ) {
            return true;
        }

        return false;
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(StudentChecklistItem::class);
    }

    protected static function booted()
    {
        static::created(function ($student) {
            $student->renewal_token = Str::random(31);
            $student->renewed_at = now();
            $student->save();

            if ($student->user && ! empty($student->user->email)) {
                $student->user->notify(new CreateStudent($student));
            } else {
                \Log::warning('New student notification skipped! Missing User or Email!!!', [
                    'student_id' => $student->id,
                ]);
            }
        });
    }
}
