<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExamToCbtModulesTable extends Migration
{
    public function up()
    {
        Schema::table('cbt_modules', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cbt_exams', function (Blueprint $table) {
            //
        });
    }
}
