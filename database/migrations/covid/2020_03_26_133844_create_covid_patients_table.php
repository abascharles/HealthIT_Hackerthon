<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_patients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('national_patient_id')->index()->nullable();
            $table->integer('cif_patient_id')->index()->nullable();
            $table->integer('facility_id')->nullable();
            $table->smallInteger('quarantine_site_id')->nullable();
            $table->tinyInteger('county_id')->nullable();
            $table->smallInteger('subcounty_id')->nullable();
            $table->integer('case_id')->nullable();
            $table->tinyInteger('nationality')->nullable();
            $table->tinyInteger('identifier_type')->nullable();
            $table->string('identifier', 30)->index();
            $table->string('national_id', 20)->nullable()->index();
            $table->string('occupation', 80)->nullable();


            $table->string('patient_name', 50)->nullable();
            $table->string('email_address', 40)->nullable();
            $table->string('phone_no', 20)->nullable();

            $table->string('contact_email_address', 40)->nullable();
            $table->string('contact_phone_no', 20)->nullable();

            $table->tinyInteger('justification')->nullable();

            $table->string('county', 20)->nullable();
            $table->string('subcounty', 30)->nullable();
            $table->string('ward', 30)->nullable();
            $table->string('residence', 40)->nullable();

            $table->string('hospital_admitted', 40)->nullable();

            $table->date('dob')->nullable();
            $table->tinyInteger('sex')->nullable();

            $table->tinyInteger('current_health_status')->nullable();

            $table->date('date_symptoms')->nullable();
            $table->date('date_admission')->nullable();
            $table->date('date_isolation')->nullable();
            $table->date('date_death')->nullable();

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
        Schema::dropIfExists('covid_patients');
    }
}
