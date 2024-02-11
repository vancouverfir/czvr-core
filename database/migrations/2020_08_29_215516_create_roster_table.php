<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRosterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roster', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cid')->unsigned();
            $table->foreign('cid')->references('id')->on('users');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->text('full_name');
            $table->string('status');
            $table->tinyInteger('active')->default(1);
            $table->timestamps();
            $table->double('currency', 8, 2)->nullable();
            $table->double('rating_hours', 8, 2)->nullable();
            $table->integer('del')->default(0);
            $table->integer('gnd')->default(0);
            $table->integer('twr')->default(0);
            $table->integer('twr_t2')->default(0);
            $table->integer('dep')->default(0);
            $table->integer('app')->default(0);
            $table->integer('app_t2')->default(0);
            $table->integer('ctr')->default(0);
            $table->text('remarks')->nullable();
            $table->integer('visit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roster');
    }
}
