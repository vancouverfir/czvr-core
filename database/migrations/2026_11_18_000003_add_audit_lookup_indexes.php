<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_logs', function (Blueprint $table): void {
            $table->index(['cid', 'session_end']);
            $table->index('session_start');
            $table->index('session_end');
        });
        Schema::table('students', fn (Blueprint $table) => $table->index(['status', 'position']));
        Schema::table('events', fn (Blueprint $table) => $table->index('slug'));
        Schema::table('news', fn (Blueprint $table) => $table->index('slug'));
    }

    public function down(): void
    {
        Schema::table('session_logs', function (Blueprint $table): void {
            $table->dropIndex(['cid', 'session_end']);
            $table->dropIndex(['session_start']);
            $table->dropIndex(['session_end']);
        });
        Schema::table('students', fn (Blueprint $table) => $table->dropIndex(['status', 'position']));
        Schema::table('events', fn (Blueprint $table) => $table->dropIndex(['slug']));
        Schema::table('news', fn (Blueprint $table) => $table->dropIndex(['slug']));
    }
};
