<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Support\Authorization\SystemRoleCatalog;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $legacyRoleIds = DB::table('roles')
                ->where('slug', SystemRoleCatalog::LEGACY_ADMIN)
                ->pluck('id');
            $administratorRoleId = DB::table('roles')
                ->where('slug', SystemRoleCatalog::ADMINISTRATOR)
                ->value('id');

            if ($legacyRoleIds->isNotEmpty() && $administratorRoleId !== null) {
                $userIds = DB::table('users_roles')
                    ->whereIn('role_id', $legacyRoleIds)
                    ->pluck('user_id');

                if ($userIds->isNotEmpty()) {
                    DB::table('users_roles')->insertOrIgnore(
                        $userIds->unique()->map(fn (int $userId): array => [
                            'user_id' => $userId,
                            'role_id' => $administratorRoleId,
                        ])->all(),
                    );
                }

                DB::table('roles_permissions')
                    ->whereIn('role_id', $legacyRoleIds)
                    ->delete();
            }
        });
    }

    public function down(): void
    {
        // Revoked permissions cannot be re-created safely.
    }
};
