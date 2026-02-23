<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = ['checklist_id', 'item'];

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class);
    }
}
