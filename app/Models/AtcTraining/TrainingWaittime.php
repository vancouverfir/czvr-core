<?php

namespace App\Models\AtcTraining;

use Illuminate\Database\Eloquent\Model;

class TrainingWaittime extends Model
{
    protected $fillable = [
        'colour', 'wait_length',
    ];
}
