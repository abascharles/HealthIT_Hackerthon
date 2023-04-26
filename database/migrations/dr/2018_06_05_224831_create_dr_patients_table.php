<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrPatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_patients', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned()->index();
            $table->date('datereceived')->nullable();
            $table->string('result', 20)->nullable();
            $table->integer('worksheet_id')->unsigned()->nullable()->index();
            $table->tinyInteger('rcategory')->unsigned()->index();
            $table->tinyInteger('dr_reason_id')->unsigned()->index();
            $table->tinyInteger('status_id')->unsigned()->default(1)->index();  

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
        Schema::dropIfExists('dr_patients');
    }
}
