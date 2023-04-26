<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sample_id')->unsigned()->index();
            $table->tinyInteger('dr_primer_id')->unsigned()->index();
            $table->string('result_file')->nullable();
            $table->string('intepreted_result')->nullable();
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
        Schema::dropIfExists('dr_results');
    }
}
