<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('student_label')->where('name', 'Training Paused')->update(['exclusive' => false, 'new_status' => null]);
    }

    public function down(): void
    {
        DB::table('student_label')->where('name', 'Training Paused')->update(['exclusive' => true, 'new_status' => 1]);
    }
};
