<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
    protected $table = 'roster';

    protected $fillable = [
        'cid', 'user_id', 'full_name', 'status', 'active', 'currency', 'rating_hours', 'fss', 'delgnd', 'delgnd_t2', 'twr', 'twr_t2', 'dep', 'app', 'app_t2', 'ctr', 'remarks', 'visit', 'staff',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
