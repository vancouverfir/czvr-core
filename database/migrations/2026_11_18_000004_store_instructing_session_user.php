<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructing_sessions', function (Blueprint $table): void {
            $table->unsignedInteger('instructor_user_id')->nullable();
            $table->foreign('instructor_user_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::table('instructing_sessions', function (Blueprint $table): void {
            $table->dropForeign(['instructor_user_id']);
            $table->dropColumn('instructor_user_id');
        });
    }
};
