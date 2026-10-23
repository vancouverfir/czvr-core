<?php

namespace App\Models\Users;

use App\Support\Authorization\SystemRoleCatalog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'secure'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'users_roles');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'roles_permissions');
    }

    public function scopeOrdered(Builder $query): Builder
    {
        $ranks = array_reverse(array_keys(SystemRoleCatalog::all()));
        $cases = collect($ranks)->map(fn (string $slug, int $rank) => "WHEN ? THEN $rank")->implode(' ');

        return $query->orderByRaw("CASE slug $cases ELSE ".count($ranks).' END', $ranks)->orderBy('name');
    }

    public static function findByName(string $name): self
    {
        return static::where('name', $name)->firstOrFail();
    }

    public function getProtectedAttribute(): ?int
    {
        return $this->secure;
    }

    public function setProtectedAttribute(mixed $value): void
    {
        $this->attributes['secure'] = $value;
    }
}
