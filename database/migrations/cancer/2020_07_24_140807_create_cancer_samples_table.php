<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancerSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cancer_samples', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('national_sample_id')->nullable();
            $table->integer('patient_id');
            $table->tinyInteger('sample_type')->nullable();
            $table->tinyInteger('justification')->nullable();

            $table->tinyInteger('receivedstatus')->unsigned()->nullable();
            $table->integer('sample_received_by')->unsigned()->nullable();
            $table->float('age', 7, 4)->unsigned()->nullable();

            $table->string('comments', 30)->nullable();
            $table->string('labcomment', 50)->nullable();
            $table->integer('parentid')->unsigned()->default(0)->nullable();
            $table->tinyInteger('rejectedreason')->unsigned()->nullable();
            $table->string('reason_for_repeat', 50)->nullable();
            $table->string('interpretation', 100)->nullable();
            $table->tinyInteger('result')->unsigned()->nullable();
            $table->tinyInteger('action')->unsigned()->nullable();

            $table->integer('facility_id')->unsigned();

            $table->tinyInteger('flag')->unsigned()->default(1)->nullable();
            $table->tinyInteger('run')->unsigned()->default(1)->nullable();
            $table->tinyInteger('repeatt')->unsigned()->default(0)->nullable();
            $table->tinyInteger('eqa')->unsigned()->default(0)->nullable();

            $table->integer('approvedby')->unsigned()->nullable();
            $table->integer('approvedby2')->unsigned()->nullable();

            $table->date('datecollected')->nullable();
            $table->date('datereceived')->nullable();
            $table->date('datetested')->nullable();
            $table->date('datemodified')->nullable();
            $table->date('dateapproved')->nullable();
            $table->date('dateapproved2')->nullable();
            $table->date('datedispatched')->nullable();
            $table->dateTime('time_result_sms_sent')->nullable();

            $table->tinyInteger('tat1')->unsigned()->nullable();
            $table->tinyInteger('tat2')->unsigned()->nullable();
            $table->tinyInteger('tat3')->unsigned()->nullable();
            $table->tinyInteger('tat4')->unsigned()->nullable();

            $table->tinyInteger('synched')->default(0)->nullable();
            $table->date('datesynched')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('cancer_samples');
    }
}
