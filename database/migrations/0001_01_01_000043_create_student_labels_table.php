<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_label', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('fa_icon');
            $table->string('color');
            $table->boolean('visible_home');
            $table->boolean('exclusive');
            $table->boolean('visitor');
            $table->unsignedTinyInteger('new_status')->nullable();
            $table->timestamps();
        });

        Schema::create('student_interactive_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_label_id');
            $table->unsignedInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_interactive_labels');
        Schema::dropIfExists('student_label');
    }
};
