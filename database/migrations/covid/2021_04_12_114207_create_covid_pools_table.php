<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('covid_worksheets', function (Blueprint $table) {
            $table->integer('pool_id')->nullable()->default(null)->unsigned()->index()->after('national_worksheet_id');
        });  

        Schema::table('covid_samples', function (Blueprint $table) {
            $table->integer('pool_sample_id')->nullable()->default(null)->unsigned()->index()->after('national_sample_id');
        });   


        Schema::create('covid_pools', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('machine_type')->unsigned();
            $table->tinyInteger('lab_id')->unsigned();
            $table->tinyInteger('status_id')->unsigned()->default(1)->index();

            $table->tinyInteger('neg_control_result')->unsigned()->nullable();
            $table->tinyInteger('pos_control_result')->unsigned()->nullable();

            $table->string('neg_control_interpretation', 50)->nullable();
            $table->string('pos_control_interpretation', 50)->nullable();

            $table->integer('createdby')->unsigned()->nullable();

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
        Schema::dropIfExists('covid_pools');
    }
}
