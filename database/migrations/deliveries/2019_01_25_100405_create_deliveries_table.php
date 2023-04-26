<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function(Blueprint $table){
            $table->bigIncrements('id');
            $table->bigInteger('national_id')->nullable();
            $table->tinyInteger('quarter')->comment('This is the quarter in which the delivery was done')->nullable();
            $table->tinyInteger('month')->comment('This is the month in which the delivery was done. Replaced the quarterly since most of the deliveris were done in monthly basis')->nullable();
            $table->integer('year')->comment('This is the quarter in which the delivery was done');
            $table->tinyInteger('type')->nullable()->comment("Indicates if this is an EID, VL, Consumable delivery");
            $table->tinyInteger('machine')->nullable();
            $table->tinyInteger('lab_id');
            $table->bigInteger('receivedby')->nullable();
            $table->date('datereceived')->nullable();
            $table->bigInteger('enteredby')->nullable();
            $table->date('dateentered')->nullable();
            $table->tinyInteger('synched')->default(0);
            $table->dateTime('datesynched')->nullable();
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
        Schema::dropIfExists('deliveries');
    }
}
