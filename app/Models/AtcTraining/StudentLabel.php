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
                ->orWhere('new_status', '!=', Student::STATUS_WAITLIST));
        }

        return $query->where('visitor', false)
            ->whereNotIn('name', Student::MANAGED_TRAINING_LABELS);
    }

    public function labelHtml()
    {
        $html = "<span style='font-weight: 400' class='badge rounded shadow-none ";

        if ($this->color) {
            $html .= $this->color." text-white'>";
        } else {
            $html .= "grey lighten-3 text-black'>";
        }

        if ($this->fa_icon) {
            $html .= "<i class='".$this->fa_icon." fa-fw'></i>&nbsp;";
        }

        $html .= $this->name.'</span>';

        return new HtmlString($html);
    }
}
