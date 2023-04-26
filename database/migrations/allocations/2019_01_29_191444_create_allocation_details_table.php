<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllocationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('national_id')->nullable();
            $table->bigInteger('allocation_id');
            $table->bigInteger('kit_id');
            $table->integer('allocated')->default(0);
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
        Schema::dropIfExists('allocation_details');
    }
}
