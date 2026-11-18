<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\HtmlString;

class StudentLabel extends Model
{
    protected $table = 'student_label';

    protected $fillable = [
        'name', 'fa_icon', 'color', 'visible_home', 'exclusive', 'visitor', 'new_status',
    ];

    protected function casts(): array
    {
        return [
            'visible_home' => 'boolean',
            'exclusive' => 'boolean',
            'visitor' => 'boolean',
            'new_status' => 'integer',
        ];
    }

    public function students(): HasMany
    {
        return $this->hasMany(StudentInteractiveLabels::class, 'student_label_id');
    }

    public function scopeAssignableTo(Builder $query, Student $student): Builder
    {
        if ($student->isVisitor()) {
            return $query->where(fn (Builder $query) => $query
                ->whereNull('new_status')
                ->orWhereIn('new_status', [Student::STATUS_VISITOR_WAITLIST, Student::STATUS_VISITOR_ACTIVE, Student::STATUS_MARKED_FOR_REMOVAL]));
        }

        $student->loadMissing('labels.label');
        $recoveringPausedStage = $student->labels->contains(fn ($link) => $link->label?->name === Student::PAUSED_LABEL)
            && ! $student->labels->contains(fn ($link) => $link->label?->exclusive);

        return $query->where('visitor', false)
            ->whereNotIn('name', $recoveringPausedStage ? [Student::RECERTIFICATION_LABEL] : Student::MANAGED_TRAINING_LABELS);
    }

    public function labelHtml()
    {
        $html = "<span style='font-weight: 400' class='badge rounded shadow-none ";

        if ($this->color) {
            $html .= e($this->color)." text-white'>";
        } else {
            $html .= "grey lighten-3 text-black'>";
        }

        if ($this->fa_icon) {
            $html .= "<i class='".e($this->fa_icon)." fa-fw'></i>&nbsp;";
        }

        $html .= e($this->name).'</span>';

        return new HtmlString($html);
    }
}
