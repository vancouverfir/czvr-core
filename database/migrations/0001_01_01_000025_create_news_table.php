<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->tinyInteger('show_author')->default(0);
            $table->text('image')->nullable();
            $table->longText('content')->nullable();
            $table->text('summary')->nullable();
            $table->dateTime('published');
            $table->dateTime('edited')->nullable();
            $table->tinyInteger('visible')->default(1);
            $table->integer('email_level')->default(0);
            $table->tinyInteger('certification')->default(0);
            $table->string('slug');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
