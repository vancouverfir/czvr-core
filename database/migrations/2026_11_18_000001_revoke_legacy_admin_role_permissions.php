<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles_permissions')
            ->whereIn('role_id', DB::table('roles')->select('id')->where('slug', 'admin'))
            ->delete();
    }

    public function down(): void
    {
        // Revoked permissions cannot be re-created safely.
    }
};
