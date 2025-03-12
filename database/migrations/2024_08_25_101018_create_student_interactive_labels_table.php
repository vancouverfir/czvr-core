<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentInteractiveLabelsTable extends Migration
{
    public function up()
    {
        Schema::create('student_interactive_labels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('student_label_id');
            $table->unsignedInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_interactive_labels');
    }
}
