<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies_sections', function (Blueprint $table) {
            $table->increments('id');
            $table->text('section_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies_sections');
    }
};
