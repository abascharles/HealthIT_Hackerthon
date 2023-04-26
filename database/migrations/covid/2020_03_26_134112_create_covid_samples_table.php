<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidSamplesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_samples', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('national_sample_id')->index()->nullable();
            $table->integer('pool_sample_id')->nullable()->default(null)->unsigned()->index();
            $table->integer('cif_sample_id')->index()->nullable();
            $table->integer('patient_id')->index()->nullable();
            $table->tinyInteger('lab_id')->nullable();

            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->integer('received_by')->unsigned()->nullable();
            $table->string('entered_by', 30)->nullable()->index();

            $table->tinyInteger('test_type')->nullable();

            // 1 for facility
            // 2 for poc
            // 5 for other system
            $table->tinyInteger('site_entry')->nullable()->default(5);
            $table->tinyInteger('highpriority')->nullable()->default(0);


            $table->tinyInteger('amrs_location')->nullable();
            $table->string('provider_identifier', 50)->nullable();
            $table->string('order_no', 30)->nullable()->index();

            
            $table->tinyInteger('age')->unsigned()->nullable();
            $table->tinyInteger('health_status')->nullable();
            $table->string('symptoms')->nullable();
            $table->tinyInteger('temperature')->nullable();
            $table->string('observed_signs')->nullable();
            $table->string('underlying_conditions')->nullable();


            $table->string('comments', 80)->nullable();
            $table->string('labcomment', 100)->nullable();
            $table->tinyInteger('sample_type')->nullable();
            $table->tinyInteger('receivedstatus')->unsigned()->nullable();
            $table->tinyInteger('rejectedreason')->unsigned()->nullable();

            $table->integer('worksheet_id')->nullable()->unsigned()->index();


            $table->string('target1', 30)->nullable();
            $table->string('target2', 30)->nullable();
            $table->string('error', 20)->nullable();
            $table->string('interpretation', 40)->nullable();
            $table->tinyInteger('result')->unsigned()->nullable();

            $table->boolean('repeatt')->default(0);
            $table->tinyInteger('run')->default(1)->unsigned();
            $table->integer('parentid')->unsigned()->default(0)->nullable()->index();
            

            $table->date('datecollected')->nullable();
            $table->date('datereceived')->nullable();
            $table->date('datetested')->nullable();
            $table->date('datedispatched')->nullable();

            $table->date('dateapproved')->nullable();
            $table->date('dateapproved2')->nullable();
            $table->integer('approvedby')->unsigned()->nullable();
            $table->integer('approvedby2')->unsigned()->nullable();

            $table->tinyInteger('tat1')->unsigned()->nullable();
            $table->tinyInteger('tat2')->unsigned()->nullable();
            $table->tinyInteger('tat3')->unsigned()->nullable();
            $table->tinyInteger('tat4')->unsigned()->nullable();

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
        Schema::dropIfExists('covid_samples');
    }
}
