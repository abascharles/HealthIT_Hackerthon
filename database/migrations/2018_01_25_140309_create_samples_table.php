<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('samples', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('national_sample_id')->unsigned()->nullable()->index();
            $table->integer('patient_id')->unsigned()->index(); 
            // $table->bigInteger('batch_id')->unsigned()->index();
            $table->double('batch_id', 14, 2)->unsigned()->index();
            $table->tinyInteger('amrs_location')->nullable();
            $table->string('provider_identifier', 50)->nullable();
            $table->string('order_no', 30)->nullable()->index();
            $table->string('sample_type', 30)->nullable();


            $table->tinyInteger('mother_age')->unsigned()->nullable();
            $table->string('mother_last_result', 30)->nullable();
            $table->tinyInteger('mother_last_rcategory')->unsigned()->nullable();

            $table->tinyInteger('receivedstatus')->unsigned()->nullable();
            $table->integer('sample_received_by')->unsigned()->nullable();
            $table->float('age', 7, 4)->unsigned()->nullable();

            $table->boolean('redraw')->default(false)->nullable();
            $table->tinyInteger('pcrtype')->unsigned()->nullable();
            $table->tinyInteger('regimen')->unsigned()->nullable();
            $table->tinyInteger('mother_prophylaxis')->unsigned()->nullable();
            $table->tinyInteger('feeding')->unsigned()->nullable();
            $table->tinyInteger('spots')->unsigned()->nullable();
            $table->string('comments', 30)->nullable();
            $table->string('labcomment', 50)->nullable();
            $table->integer('parentid')->unsigned()->default(0)->nullable()->index();
            $table->tinyInteger('rejectedreason')->unsigned()->nullable();
            $table->string('reason_for_repeat', 50)->nullable();
            $table->string('interpretation', 100)->nullable();
            $table->tinyInteger('result')->unsigned()->nullable();

            $table->integer('worksheet_id')->unsigned()->nullable()->index();

            $table->tinyInteger('flag')->unsigned()->default(1)->nullable();
            $table->tinyInteger('run')->unsigned()->default(1)->nullable();
            $table->tinyInteger('repeatt')->unsigned()->default(0)->nullable();
            $table->tinyInteger('eqa')->unsigned()->default(0)->nullable();

            $table->integer('approvedby')->unsigned()->nullable();
            $table->integer('approvedby2')->unsigned()->nullable();

            $table->date('datecollected')->nullable();
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
        Schema::dropIfExists('samples');
    }
}
