<?php

namespace App\Models\Users\Concerns;

use App\Models\Users\Permission;
use App\Models\Users\Role;
use App\Support\Authorization\PermissionCatalog;
use App\Support\Authorization\SystemRoleCatalog;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRolesAndPermissions
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'users_permissions');
    }

    public function hasRole(Role|string $role): bool
    {
        return $role instanceof Role
            ? $this->roles()->whereKey($role->getKey())->exists()
            : $this->roles()->where('slug', $role)->exists();
    }

    public function hasAnyRole(array|string $roles): bool
    {
        $roles = is_string($roles) ? preg_split('/[|,]/', $roles) : $roles;
        return collect($roles)->contains(
            fn (Role|string $role): bool => $this->hasRole(is_string($role) ? trim($role) : $role),
        );
    }

    public function isAdministrator(): bool
    {
        $this->loadMissing('roles');

        return $this->roles->contains('slug', SystemRoleCatalog::ADMINISTRATOR);
    }

    public function assignRole(Role|string $role): void
    {
        $this->roles()->syncWithoutDetaching([$this->resolveRole($role)->getKey()]);
        $this->unsetRelation('roles');
    }

    public function removeRole(Role|string $role): void
    {
        $this->roles()->detach($this->resolveRole($role)->getKey());
        $this->unsetRelation('roles');
    }

    public function hasPermissionTo(Permission|string $permission): bool
    {
        $slug = $permission instanceof Permission ? $permission->slug : $permission;

        $this->loadMissing(['directPermissions', 'roles.permissions']);

        if ($this->isAdministrator() && in_array($slug, PermissionCatalog::slugs(), true)) {
            return true;
        }

        if ($this->directPermissions->contains('slug', $slug)) {
            return true;
        }

        return $this->roles->contains(
            fn (Role $role): bool => $role->slug !== SystemRoleCatalog::LEGACY_ADMIN
                && $role->permissions->contains('slug', $slug),
        );
    }

    public function hasAnyPermission(array|string $permissions): bool
    {
        $permissions = is_string($permissions) ? preg_split('/[|,]/', $permissions) : $permissions;

        return collect($permissions)->contains(
            fn (Permission|string $permission): bool => $this->hasPermissionTo($permission),
        );
    }

    public function givePermissionTo(Permission|string $permission): void
    {
        $this->directPermissions()->syncWithoutDetaching([$this->resolvePermission($permission)->getKey()]);
        $this->unsetRelation('directPermissions');
    }

    public function revokePermissionTo(Permission|string $permission): void
    {
        $this->directPermissions()->detach($this->resolvePermission($permission)->getKey());
        $this->unsetRelation('directPermissions');
    }

    /** @param array<int, int|string|Permission> $permissions */
    public function syncDirectPermissions(array $permissions): void
    {
        $ids = collect($permissions)->map(fn (int|string|Permission $permission): int => match (true) {
            $permission instanceof Permission => $permission->getKey(),
            is_int($permission), ctype_digit($permission) => Permission::query()->findOrFail($permission)->getKey(),
            default => $this->resolvePermission($permission)->getKey(),
        });

        $this->directPermissions()->sync($ids);
        $this->unsetRelation('directPermissions');
    }

    private function resolveRole(Role|string $role): Role
    {
        if ($role instanceof Role) {
            return $role;
        }

        return Role::where('slug', $role)->firstOrFail();
    }

    private function resolvePermission(Permission|string $permission): Permission
    {
        if ($permission instanceof Permission) {
            return $permission;
        }

        return Permission::query()->where('slug', $permission)->orWhere('name', $permission)->firstOrFail();
    }
}
