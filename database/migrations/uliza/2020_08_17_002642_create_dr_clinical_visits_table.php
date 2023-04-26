<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrClinicalVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uliza_clinical_visits', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uliza_clinical_form_id')->unsigned()->index();
            $table->integer('reviewer_id')->unsigned()->index()->nullable();
            $table->date('clinicvisitdate');
            $table->string('cd4')->nullable();
            $table->string('hb')->nullable();
            $table->string('crclegfr')->nullable();
            $table->string('viral_load')->nullable();
            $table->string('weight_bmi')->nullable();
            $table->string('arv_regimen')->nullable();
            $table->string('arv_regimen_other')->nullable();
            $table->string('reason_switch')->nullable();
            $table->string('new_oi')->nullable();
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
        Schema::dropIfExists('dr_clinical_visits');
    }
}
