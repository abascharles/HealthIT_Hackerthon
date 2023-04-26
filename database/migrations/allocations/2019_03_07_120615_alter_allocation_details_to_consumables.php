<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAllocationDetailsToConsumables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('allocation_details');
        Schema::create('allocation_details', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('national_id')->nullable();
            $table->bigInteger('allocation_id');
            $table->integer('machine_id')->nullable();
            $table->tinyInteger('testtype')->nullable();
            $table->text('allocationcomments')->nullable();
            $table->text('issuedcomments')->nullable();
            $table->tinyInteger('approve')->default(0);
            $table->text('disapprovereason')->nullable();
            $table->integer('submissions')->default(1);
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
