<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewLabPerformanceTrackers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_performance_trackers', function (Blueprint $table) {
            // $table->softDeletes()->after('datesynched');
        });
        // Schema::create('lab_performance_trackers', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->integer('lab_id')->unsigned()->index();
        //     $table->tinyInteger('month')->unsigned();
        //     $table->integer('year')->unsigned();
        //     // $table->tinyInteger('submitted')->unsigned()->index();
        //     // $table->tinyInteger('eamilsent')->unsigned()->index();
        //     $table->date('dateemailsent')->nullable();
        //     $table->tinyInteger('testtype')->unsigned()->nullable();
        //     $table->tinyInteger('sampletype')->unsigned()->nullable();
        //     $table->integer('received')->unsigned()->nullable();
        //     $table->integer('rejected')->unsigned()->nullable();
        //     $table->integer('loggedin')->unsigned()->nullable();
        //     $table->integer('notlogged')->unsigned()->nullable();
        //     $table->integer('tested')->unsigned()->nullable();
        //     $table->string('reasonforbacklog', 100)->nullable();
        //     $table->date('datesubmitted')->nullable();
        //     $table->integer('submittedBy')->unsigned()->nullable();
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
