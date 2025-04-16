<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class Checklist extends Model {
    protected $fillable = ['name'];

    public function items() {
        return $this->hasMany(ChecklistItem::class);
    }
}
