<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrSampleFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_sample_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sample_id')->unsigned()->index();
            $table->bigInteger('exatype_file_id')->unsigned()->index()->nullable();
            $table->string('primer', 5);
            $table->string('contig', 10);
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
        Schema::dropIfExists('dr_sample_files');
    }
}
