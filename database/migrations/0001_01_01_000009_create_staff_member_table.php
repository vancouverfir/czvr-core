<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_member', function (Blueprint $table) {
            $table->increments('id');
            $table->text('position');
            $table->text('group');
            $table->text('description')->nullable();
            $table->text('email')->nullable();
            $table->text('shortform');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('staff_groups');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_member');
    }
};
