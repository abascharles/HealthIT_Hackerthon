<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsumptionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumption_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('consumption_id');
            $table->integer('kit_id');
            $table->float('begining_balance')->default(0);
            $table->float('used')->default(0);
            $table->float('positive_adjustment')->default(0)->comment("Received from other sources eg. other labs");
            $table->float('wasted')->default(0);
            $table->float('negative_adjustment')->default(0)->comment("Given to other labs");
            $table->float('ending_balance')->default(0);
            $table->float('request')->default(0);
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
        Schema::dropIfExists('consumption_details');
    }
}
