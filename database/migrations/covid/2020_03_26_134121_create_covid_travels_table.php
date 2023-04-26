<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidTravelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_travels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('national_travel_id')->index()->nullable();
            $table->integer('patient_id')->index();
            $table->date('travel_date')->nullable();
            $table->string('city', 35)->nullable();
            $table->string('country', 35)->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('duration_visited')->nullable();


            $table->tinyInteger('synched')->default(0)->nullable();
            $table->date('datesynched')->nullable();
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
        Schema::dropIfExists('covid_travels');
    }
}
