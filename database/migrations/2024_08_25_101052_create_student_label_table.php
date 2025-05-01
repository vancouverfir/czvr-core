<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentLabelTable extends Migration
{
    public function up()
    {
        Schema::create('student_label', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('fa_icon')->nullable();
            $table->string('color')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_label');
    }
}
