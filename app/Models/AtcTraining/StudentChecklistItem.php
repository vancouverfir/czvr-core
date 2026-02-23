<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentChecklistItem extends Model
{
    protected $fillable = ['student_id', 'checklist_item_id', 'completed'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class);
    }
}
