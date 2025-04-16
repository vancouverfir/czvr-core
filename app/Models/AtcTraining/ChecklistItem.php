<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class ChecklistItem extends Model {
    protected $fillable = ['checklist_id', 'item'];

    public function checklist() {
        return $this->belongsTo(Checklist::class);
    }
}
