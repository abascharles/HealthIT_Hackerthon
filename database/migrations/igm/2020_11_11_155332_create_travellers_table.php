<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travellers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('id_passport', 50)->nullable();
            $table->string('patient_name', 50)->nullable();
            $table->string('marriage_status', 50)->nullable();
            $table->string('phone_no', 20)->nullable();
            $table->string('county', 20)->nullable();
            $table->string('residence', 40)->nullable();
            $table->string('citizenship', 40)->nullable();
            $table->tinyInteger('sex')->nullable();
            $table->tinyInteger('age')->unsigned()->nullable();



            $table->tinyInteger('result')->unsigned()->nullable();
            $table->tinyInteger('igm_result')->unsigned()->nullable();
            $table->tinyInteger('igg_igm_result')->unsigned()->nullable();
            
            $table->date('datecollected')->nullable();
            $table->date('datereceived')->nullable();
            $table->date('datetested')->nullable();
            $table->date('datedispatched')->nullable();

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
        Schema::dropIfExists('travellers');
    }
}
