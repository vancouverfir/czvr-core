<?php

namespace Database\Seeders;

use App\Models\Users\Permission;
use App\Models\Users\Role;
use App\Support\Authorization\PermissionCatalog;
use App\Support\Authorization\SystemRoleCatalog;
use Illuminate\Database\Seeder;

class AuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PermissionCatalog::all() as $slug => $definition) {
            Permission::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $definition['label']],
            );
        }

        foreach (SystemRoleCatalog::all() as $slug => $name) {
            $role = Role::query()->firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'secure' => 1],
            );
            $role->permissions()->syncWithoutDetaching(
                Permission::query()->whereIn('slug', SystemRoleCatalog::defaultPermissions()[$slug])->pluck('id'),
            );
        }
    }
}
