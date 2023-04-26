<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrBulkRegistrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_bulk_registrations', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('lab_id')->unsigned();

            // This is the exatype id
            $table->bigInteger('plate_id')->unsigned()->nullable()->index();
            $table->integer('extraction_worksheet_id')->nullable()->unsigned()->index();

            // 1 is in process
            // 2 is tested, results uploaded
            // 3 is results from exatype approved
            // 4 is cancelled
            // 5 is sent to exatype, awaiting response
            // 6 is result sent back by exatype
            $table->tinyInteger('status_id')->unsigned()->default(1)->index();

            // Exatype status
            $table->tinyInteger('exatype_status_id')->unsigned()->default(4)->index();

            $table->date('dateuploaded')->nullable();
            $table->date('datecancelled')->nullable();
            $table->date('datereviewed')->nullable();
            $table->date('datereviewed2')->nullable();

            $table->integer('createdby')->unsigned()->nullable();
            $table->integer('runby')->unsigned()->nullable();
            $table->integer('uploadedby')->unsigned()->nullable();
            $table->integer('cancelledby')->unsigned()->nullable();
            $table->integer('reviewedby')->unsigned()->nullable();
            $table->integer('reviewedby2')->unsigned()->nullable();

            $table->dateTime('time_sent_to_exatype')->nullable();

            $table->boolean('qc_run')->default(0);
            $table->boolean('qc_pass')->default(0);
            $table->integer('qc_distance_pass')->nullable();
            $table->boolean('plate_controls_pass')->default(0);



            $table->boolean('has_errors')->default(0);
            $table->boolean('has_warnings')->default(0);
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
        Schema::dropIfExists('dr_bulk_registrations');
    }
}
