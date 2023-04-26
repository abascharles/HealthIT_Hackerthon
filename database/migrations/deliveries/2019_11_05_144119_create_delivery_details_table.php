<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('national_id')->nullable();
            $table->bigInteger('delivery_id');
            $table->integer('kit_id');
            $table->string('kit_type', 100);
            $table->string('lotno')->nullable();
            $table->date('expiry')->nullable();
            $table->float('received')->default(0);
            $table->float('damaged')->default(0);
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
        Schema::dropIfExists('delivery_details');
    }
}
