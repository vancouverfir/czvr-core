<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('label_checklist_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('student_label');
            $table->foreignId('checklist_id')->constrained('checklists');
            $table->string('tier_type');
            $table->timestamps();
        });

        Schema::create('label_checklist_visitor_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')->constrained('student_label');
            $table->foreignId('checklist_id')->constrained('checklists');
            $table->string('tier_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('label_checklist_visitor_map');
        Schema::dropIfExists('label_checklist_map');
    }
};
