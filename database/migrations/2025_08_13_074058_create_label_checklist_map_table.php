<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabelChecklistMapTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('label_checklist_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('label_id')
                  ->constrained('student_label');
            $table->foreignId('checklist_id')
                  ->constrained('checklists');
            $table->string('tier_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_checklist_map');
    }
};
