<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrExtractionWorksheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_extraction_worksheets', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('lab_id')->unsigned();
            $table->tinyInteger('status_id')->unsigned()->default(1)->index();
            $table->integer('createdby')->unsigned()->nullable();
            $table->date('date_gel_documentation')->nullable(); 
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
        Schema::dropIfExists('dr_extraction_worksheets');
    }
}
