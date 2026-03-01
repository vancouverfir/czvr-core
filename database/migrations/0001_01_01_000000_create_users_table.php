<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->string('fname');
            $table->string('lname');
            $table->string('email');
            $table->integer('rating_id')->nullable();
            $table->string('rating_short')->nullable();
            $table->string('rating_long')->nullable();
            $table->string('rating_GRP')->nullable();
            $table->dateTime('reg_date')->nullable();
            $table->string('region_code')->nullable();
            $table->string('region_name')->nullable();
            $table->string('division_code')->nullable();
            $table->string('division_name')->nullable();
            $table->string('subdivision_code')->nullable();
            $table->string('subdivision_name')->nullable();
            $table->unsignedInteger('permissions')->default(0);
            $table->integer('gdpr_subscribed_emails')->default(0);
            $table->boolean('deleted')->default(false);
            $table->integer('init')->default(0);
            $table->string('avatar')->default('/img/default-profile-img.jpg');
            $table->longText('bio')->nullable();
            $table->rememberToken();
            $table->tinyInteger('display_cid_only')->default(0);
            $table->string('display_fname')->nullable();
            $table->tinyInteger('display_last_name')->default(1);
            $table->bigInteger('discord_user_id')->nullable();
            $table->bigInteger('discord_dm_channel_id')->nullable();
            $table->integer('avatar_mode')->default(0);
            $table->tinyInteger('used_connect')->default(0);
            $table->integer('visitor')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
