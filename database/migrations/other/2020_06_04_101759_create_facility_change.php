<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityChange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facility_changes', function (Blueprint $table) {
            $table->increments('id');            
            $table->integer('old_facility_id')->unsigned()->index();
            $table->integer('new_facility_id')->unsigned()->index();
            $table->integer('temp_facility_id')->unsigned()->index();

            $table->tinyInteger('implemented')->unsigned()->default(0);
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
        Schema::dropIfExists('facility_changes');
    }
}
