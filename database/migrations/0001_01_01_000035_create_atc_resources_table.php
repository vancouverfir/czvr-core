<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atc_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('title');
            $table->string('font_awesome');
            $table->text('description')->nullable();
            $table->string('url');
            $table->boolean('atc_only');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atc_resources');
    }
};
