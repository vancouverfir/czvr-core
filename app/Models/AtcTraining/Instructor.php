<?php

namespace App\Models\AtcTraining;

use App\Models\Users\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instructor extends Model
{
    protected $fillable = [
        'user_id', 'qualification', 'email',
    ];

    /*
     * * Return who posted the article
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function studentnotes(): HasMany
    {
        return $this->hasMany(StudentNote::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(InstructingSession::class);
    }
}
