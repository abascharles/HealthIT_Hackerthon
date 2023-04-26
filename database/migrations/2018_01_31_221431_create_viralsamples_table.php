<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViralsamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('viralsamples', function (Blueprint $table) {
            $table->increments('id'); 
            $table->integer('national_sample_id')->unsigned()->nullable()->index();
            $table->integer('patient_id')->unsigned()->index();
            // $table->bigInteger('batch_id')->unsigned()->index();
            $table->double('batch_id', 14, 2)->unsigned()->index();
            $table->tinyInteger('amrs_location')->nullable();
            $table->string('provider_identifier', 50)->nullable();
            $table->string('order_no', 30)->nullable()->index();
            $table->tinyInteger('vl_test_request_no')->nullable();
            $table->tinyInteger('receivedstatus')->unsigned()->nullable(); 
            $table->integer('sample_received_by')->unsigned()->nullable();           

            // This will be used instead
            $table->float('age', 6, 3)->unsigned()->nullable()->index();
            $table->tinyInteger('age_category')->unsigned()->default(0)->nullable();
            $table->tinyInteger('justification')->unsigned()->nullable();
            $table->string('other_justification', 50)->nullable();
            $table->string('recency_number', 30)->nullable();
            $table->tinyInteger('sampletype')->unsigned()->nullable();
            $table->tinyInteger('prophylaxis')->unsigned()->nullable();
            $table->tinyInteger('regimenline')->unsigned()->nullable();
            $table->tinyInteger('pmtct')->unsigned()->default(3)->nullable();

            $table->tinyInteger('dilutionfactor')->unsigned()->nullable();
            $table->tinyInteger('dilutiontype')->unsigned()->nullable();

            $table->string('comments', 30)->nullable();
            $table->string('labcomment', 50)->nullable();
            $table->integer('parentid')->unsigned()->default(0)->index()->nullable();
            // $table->tinyInteger('spots')->unsigned()->nullable();
            $table->tinyInteger('rejectedreason')->unsigned()->nullable();
            $table->string('reason_for_repeat', 50)->nullable();
            $table->tinyInteger('rcategory')->unsigned()->default(0)->nullable()->index();

            $table->string('result', 20)->nullable();
            $table->string('units', 20)->nullable();
            $table->string('interpretation', 100)->nullable();

            $table->integer('worksheet_id')->unsigned()->nullable()->index();
            // $table->boolean('inworksheet')->default(false);

            $table->tinyInteger('flag')->unsigned()->default(1)->nullable();
            $table->tinyInteger('run')->unsigned()->default(1)->nullable();
            $table->tinyInteger('repeatt')->unsigned()->default(0)->nullable();
            // $table->tinyInteger('eqa')->unsigned()->default(0)->nullable();

            $table->integer('approvedby')->unsigned()->nullable();
            $table->integer('approvedby2')->unsigned()->nullable();

            // startartdate
            $table->date('dateinitiatedonregimen')->nullable();

            $table->date('datecollected')->nullable();
            $table->date('dateseparated')->nullable();
            $table->date('datetested')->nullable();
            $table->date('datemodified')->nullable();
            $table->date('dateapproved')->nullable();
            $table->date('dateapproved2')->nullable();
            $table->dateTime('time_result_sms_sent')->nullable();

            $table->tinyInteger('tat1')->unsigned()->nullable();
            $table->tinyInteger('tat2')->unsigned()->nullable();
            $table->tinyInteger('tat3')->unsigned()->nullable();
            $table->tinyInteger('tat4')->unsigned()->nullable();

            $table->tinyInteger('synched')->default(0)->nullable();
            $table->date('datesynched')->nullable();
            // $table->date('created_at')->nullable();
            // $table->date('updated_at')->nullable();
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
        Schema::dropIfExists('viralsamples');
    }
}
