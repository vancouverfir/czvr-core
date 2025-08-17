<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class LabelChecklistMap extends Model
{
    protected $table = 'label_checklist_map';

    protected $fillable = [
        'label_id',
        'checklist_id',
        'tier_type',
    ];

    public function label()
    {
        return $this->belongsTo(StudentLabel::class, 'label_id');
    }

    public function checklist()
    {
        return $this->belongsTo(Checklist::class, 'checklist_id');
    }
}
