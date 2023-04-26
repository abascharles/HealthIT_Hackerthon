<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_samples', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned()->index();
            $table->integer('facility_id')->unsigned()->index();
            $table->tinyInteger('lab_id')->unsigned()->index();

            $table->string('clinician_name', 50)->nullable();

            // 0 is for normal sample
            // 1 is negative control
            // 2 is positive control
            $table->tinyInteger('control')->unsigned()->nullable()->default(0);
            $table->bigInteger('exatype_id')->unsigned()->index()->nullable();

            $table->tinyInteger('prev_prophylaxis')->unsigned()->nullable();
            $table->tinyInteger('prophylaxis')->unsigned()->nullable();
            $table->tinyInteger('receivedstatus')->unsigned()->nullable(); 
            $table->tinyInteger('rejectedreason')->unsigned()->nullable();

            // Public,Survelliance etc
            $table->tinyInteger('project')->unsigned()->nullable();

            // Specimen Type
            $table->tinyInteger('sampletype')->unsigned()->nullable();
            $table->tinyInteger('container_type')->unsigned()->nullable();
            $table->tinyInteger('amount_unit')->unsigned()->nullable();
            $table->smallInteger('sample_amount')->unsigned()->nullable();
            $table->tinyInteger('age')->unsigned()->nullable();
            $table->tinyInteger('age_category')->unsigned()->default(0)->nullable();
            
            // Multiple
            $table->string('clinical_indications', 50)->nullable(); 


            $table->boolean('has_opportunistic_infections')->default(0)->nullable();
            $table->string('opportunistic_infections', 100)->nullable();

            $table->boolean('has_tb')->default(0)->nullable();
            $table->tinyInteger('tb_treatment_phase_id')->unsigned()->nullable();

            $table->boolean('has_arv_toxicity')->default(0)->nullable();
            // Multiple
            $table->string('arv_toxicities', 50)->nullable();

            $table->string('cd4_result', 30)->nullable();

            // Adherence
            $table->boolean('has_missed_pills')->default(0)->nullable();
            $table->smallInteger('missed_pills')->unsigned()->nullable();
            $table->boolean('has_missed_visits')->default(0)->nullable();
            $table->smallInteger('missed_visits')->unsigned()->nullable();
            $table->boolean('has_missed_pills_because_missed_visits')->default(0)->nullable();

            // Multiple and mixed
            $table->string('other_medications', 100)->nullable();

            $table->string('vl_result1', 20)->nullable();
            $table->string('vl_result2', 20)->nullable();
            $table->string('vl_result3', 20)->nullable();

            $table->date('vl_date_result1')->nullable(); 
            $table->date('vl_date_result2')->nullable(); 
            $table->date('vl_date_result3')->nullable(); 


            $table->boolean('repeatt')->default(0);
            $table->tinyInteger('run')->default(1)->unsigned();
            $table->integer('parentid')->unsigned()->default(0)->nullable()->index();
            // Used for when the result is a collect new sample
            $table->boolean('collect_new_sample')->default(0);

            // startartdate
            $table->date('date_prev_regimen')->nullable(); 
            $table->date('date_current_regimen')->nullable(); 

            $table->integer('bulk_registration_id')->nullable()->unsigned()->index();
            $table->integer('extraction_worksheet_id')->nullable()->unsigned()->index();
            $table->integer('worksheet_id')->nullable()->unsigned()->index();

            $table->date('datecollected')->nullable();
            $table->date('datereceived')->nullable();
            $table->date('datetested')->nullable();
            $table->date('datedispatched')->nullable();


            $table->integer('approvedby')->unsigned()->nullable();
            $table->integer('approvedby2')->unsigned()->nullable();
            
            $table->date('dateapproved')->nullable();
            $table->date('dateapproved2')->nullable();

            $table->tinyInteger('dr_reason_id')->nullable()->unsigned()->index();

            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->integer('received_by')->unsigned()->nullable();

            $table->boolean('passed_gel_documentation')->nullable();

            $table->tinyInteger('status_id')->nullable()->unsigned()->index();

            $table->boolean('qc_pass')->default(0)->nullable();

            $table->boolean('qc_stop_codon_pass')->default(0)->nullable();
            $table->boolean('qc_plate_contamination_pass')->default(0)->nullable();
            $table->boolean('qc_frameshift_codon_pass')->default(0)->nullable();

            $table->smallInteger('qc_distance_to_sample')->nullable()->unsigned();
            $table->smallInteger('qc_distance_from_sample')->nullable()->unsigned();
            $table->float('qc_distance_difference', 4, 3)->unsigned()->nullable();
            $table->string('qc_distance_strain_name', 50)->nullable();
            $table->string('qc_distance_compare_to_name', 50)->nullable();
            $table->string('qc_distance_sample_name', 20)->nullable();


            $table->boolean('has_errors')->default(0);
            $table->boolean('has_warnings')->default(0);
            $table->boolean('has_mutations')->default(0);

            // $table->boolean('has_calls')->default(0);
            // $table->boolean('has_genotypes')->default(0);

            // PendChromatogramManualIntervention
            $table->boolean('pending_manual_intervention')->default(0);
            $table->boolean('had_manual_intervention')->default(0);


            $table->text('assembled_sequence')->nullable(); 
            $table->string('chromatogram_url', 50)->nullable(); 
            $table->string('pdf_download_link')->nullable(); 
            $table->string('exatype_version', 30)->nullable(); 
            $table->string('algorithm', 20)->nullable(); 
            

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
        Schema::dropIfExists('dr_samples');
    }
}
