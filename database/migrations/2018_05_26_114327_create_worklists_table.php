<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worklists', function (Blueprint $table) {
            $table->increments('id');

            // 1 for eid
            // 2 for vl
            $table->tinyInteger('testtype')->unsigned()->nullable();

            // 1 is in process
            // 2 is tested, results uploaded awaiting approval
            // 3 is results uploaded and approved
            // 4 is cancelled
            $table->tinyInteger('status_id')->unsigned()->default(1)->index();
            
            $table->integer('facility_id')->unsigned()->nullable();
            // $table->integer('createdby')->unsigned()->nullable();

            $table->tinyInteger('synched')->default(0);
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
        Schema::dropIfExists('worklists');
    }
}
