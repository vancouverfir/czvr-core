<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentoring_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('time');
            $table->text('position');
            $table->longText('notes');
            $table->unsignedInteger('student');
            $table->foreign('student')->references('id')->on('users');
            $table->unsignedInteger('instructor');
            $table->foreign('instructor')->references('id')->on('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentoring_sessions');
    }
};
