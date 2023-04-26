<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidKitTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_kit_types', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('machine_id')->index()->nullable();
            $table->string('covid_kit_type', 100)->nullable();
            $table->string('id_column', 100)->nullable();
            $table->string('target_column', 100)->nullable();
            $table->string('ct_column', 100)->nullable();
            $table->string('target1', 100)->nullable();
            $table->string('target2', 100)->nullable();
            $table->string('control_gene', 100)->nullable();
            $table->double('threshhold', 14, 4)->unsigned()->nullable();
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
        Schema::dropIfExists('covid_kit_types');
    }
}
