<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCovidConsumptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_consumptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('start_of_week');
            $table->date('end_of_week');
            $table->integer('week');
            $table->integer('lab_id');
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
        Schema::dropIfExists('covid_consumptions');
    }
}
