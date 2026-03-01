<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('ticket_id')->unique();
            $table->text('title');
            $table->text('message');
            $table->integer('status')->default(0);
            $table->dateTime('submission_time');
            $table->integer('staff_member_id')->unsigned();
            $table->foreign('staff_member_id')->references('id')->on('staff_member');
            $table->integer('staff_member_cid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
