<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class StudentInteractiveLabels extends Model
{
    protected $table = 'student_interactive_labels';

    protected $fillable = [
        'student_label_id', 'student_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function label()
    {
        return $this->belongsTo(StudentLabel::class, 'student_label_id');
    }
}
