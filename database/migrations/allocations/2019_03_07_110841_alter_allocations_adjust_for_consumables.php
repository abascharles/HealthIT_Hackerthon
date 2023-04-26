<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAllocationsAdjustForConsumables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('allocations');
        Schema::create('allocations', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('national_id')->nullable();
            $table->integer('year');
            $table->tinyInteger('month');
            $table->date('datesubmitted')->nullable();
            $table->string('submittedby', 100)->nullable();
            $table->tinyInteger('lab_id')->nullable();
            $table->tinyInteger('synched')->default(0)->comment("0:Awaiting synching; 1:Synched; 2:Update awaiting synching;");
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
        //
    }
}
