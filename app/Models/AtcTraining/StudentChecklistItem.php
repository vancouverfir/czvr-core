<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class StudentChecklistItem extends Model
{
    protected $fillable = ['student_id', 'checklist_item_id', 'completed'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function checklistItem()
    {
        return $this->belongsTo(ChecklistItem::class);
    }
}
