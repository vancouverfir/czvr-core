<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class StaffGroup extends Model
{
    protected $fillable = [
        'id', 'name', 'slug', 'description', 'can_recieve_tickets',
    ];

    public function members()
    {
        return $this->hasMany(StaffMember::class, 'group_id');
    }
}
