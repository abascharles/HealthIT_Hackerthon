<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidPoolSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_pool_samples', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pool_id')->unsigned()->index();
            $table->tinyInteger('position')->unsigned();


            $table->string('target1', 30)->nullable();
            $table->string('target2', 30)->nullable();
            $table->string('error', 20)->nullable();
            $table->string('interpretation', 40)->nullable();
            $table->tinyInteger('result')->unsigned()->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('covid_pool_samples');
    }
}
