<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancerPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancer_patients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('national_patient_id')->unsigned()->nullable()->index();
            $table->string('patient', 25);

            // This is when the patient is enrolled into ccc program
            $table->string('ccc_no', 25)->nullable();
            $table->string('patient_name', 30)->nullable();
            $table->integer('facility_id')->unsigned();

            $table->tinyInteger('hiv_status')->unsigned()->nullable();
            $table->string('entry_point', 25)->nullable();

            // 1 for English
            // 2 for Kiswahili
            $table->tinyInteger('preferred_language')->nullable();
            $table->string('patient_phone_no', 15)->nullable();
            $table->string('caregiver_phone', 15)->nullable();
            $table->tinyInteger('sex')->unsigned();
            $table->date('dob')->nullable();
            $table->date('dateinitiatedontreatment')->nullable();
            $table->tinyInteger('synched')->default(0);
            $table->date('datesynched')->nullable();
            $table->timestamps();
            $table->index(['facility_id', 'patient'], 'cancer_patient_unq_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cancer_patients');
    }
}
