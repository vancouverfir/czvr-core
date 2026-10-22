<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_cid');
            $table->foreign('user_cid')->references('id')->on('users');
            $table->boolean('is_instructor')->nullable();
            $table->bigInteger('is_local');
            $table->bigInteger('is_radar');
            $table->bigInteger('is_enroute');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
