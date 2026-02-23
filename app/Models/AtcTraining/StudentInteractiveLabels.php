<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentInteractiveLabels extends Model
{
    protected $table = 'student_interactive_labels';

    protected $fillable = [
        'student_label_id', 'student_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(StudentLabel::class, 'student_label_id');
    }
}
