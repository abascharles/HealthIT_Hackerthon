<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewLabEquipmentTrackers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_equipment_trackers', function (Blueprint $table) {
            // $table->softDeletes()->after('datesynched');
        });
        // Schema::create('lab_equipment_trackers', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->tinyInteger('month')->unsigned();
        //     $table->integer('year')->unsigned();
        //     $table->tinyInteger('lab_id')->unsigned()->index();
        //     $table->tinyInteger('equipment_id')->unsigned()->index();
        //     $table->date('datesubmitted');
        //     $table->integer('submittedBy')->unsigned();
        //     $table->date('dateemailsent')->nullable();
        //     $table->date('datebrokendown')->nullable();
        //     $table->date('datereported')->nullable();
        //     $table->date('datefixed')->nullable();
        //     $table->integer('downtime')->unsigned()->nullable();
        //     $table->integer('samplesnorun')->unsigned()->nullable();
        //     $table->integer('failedruns')->unsigned()->nullable();
        //     $table->string('reagentswasted', 300)->nullable();
        //     $table->string('breakdownreason', 300)->nullable();
        //     $table->string('othercomments', 300)->nullable();
        //     $table->tinyInteger('synched')->default(0);
        //     $table->date('datesynched')->nullable();
        //     $table->timestamps();

        //     $table->index(['year', 'month'], 'year_month');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
