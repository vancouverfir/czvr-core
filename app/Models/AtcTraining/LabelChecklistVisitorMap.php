<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabelChecklistVisitorMap extends Model
{
    use HasFactory;

    protected $table = 'label_checklist_visitor_map';

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
