<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabelChecklistVisitorMap extends Model
{
    use HasFactory;

    protected $table = 'label_checklist_visitor_map';

    protected $fillable = [
        'label_id',
        'checklist_id',
        'tier_type',
    ];

    public function label(): BelongsTo
    {
        return $this->belongsTo(StudentLabel::class, 'label_id');
    }

    public function checklist(): BelongsTo
    {
        return $this->belongsTo(Checklist::class, 'checklist_id');
    }
}
