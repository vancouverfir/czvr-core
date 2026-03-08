<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitored_positions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identifier');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitored_positions');
    }
};
