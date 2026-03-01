<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_info', function (Blueprint $table) {
            $table->increments('id');
            $table->text('sys_name');
            $table->text('release');
            $table->text('sys_build');
            $table->text('copyright_year');
            $table->text('banner')->nullable();
            $table->text('bannerMode')->nullable();
            $table->text('bannerLink')->nullable();
            $table->text('emailfirchief');
            $table->text('emaildepfirchief');
            $table->text('emailcinstructor');
            $table->text('emaileventc');
            $table->text('emailfacilitye');
            $table->text('emailwebmaster');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_info');
    }
};
