<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controller_feedback', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamp('deleted_at')->nullable();
            $table->integer('controller_cid')->unsigned();
            $table->foreign('controller_cid')->references('id')->on('users');
            $table->text('position');
            $table->longText('content');
            $table->tinyInteger('approval')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controller_feedback');
    }
};
