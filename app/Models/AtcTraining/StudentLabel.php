<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class StudentLabel extends Model
{
    protected $table = 'student_label';

    protected $fillable = [
        'name', 'fa_icon', 'color',
    ];

    public function students()
    {
        return $this->hasMany(StudentInteractiveLabels::class, 'student_label_id');
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
