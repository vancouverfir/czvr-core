<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffGroup extends Model
{
    protected $fillable = [
        'id', 'name', 'slug', 'description', 'can_receive_tickets',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(StaffMember::class, 'group_id');
    }
}
