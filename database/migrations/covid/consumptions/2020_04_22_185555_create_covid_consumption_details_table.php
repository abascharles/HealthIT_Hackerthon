<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCovidConsumptionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_consumption_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('consumption_id');
            $table->integer('kit_id');
            $table->integer('begining_balance')->default(0);
            $table->integer('received')->default(0);
            $table->integer('kits_used')->default(0);
            $table->integer('positive')->default(0);
            $table->integer('negative')->default(0);
            $table->integer('wastage')->default(0);
            $table->integer('ending')->default(0);
            $table->integer('requested')->default(0);
            $table->tinyInteger('synced')->default(0);
            $table->date('datesynced')->nullable();
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
        Schema::dropIfExists('covid_consumption_details');
    }
}