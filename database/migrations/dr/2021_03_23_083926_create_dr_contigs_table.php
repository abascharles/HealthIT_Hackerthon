<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrContigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_contigs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sample_id')->unsigned()->index();
            $table->bigInteger('exatype_id')->unsigned()->index()->nullable();
            $table->tinyInteger('exatype_status_id')->nullable()->unsigned()->index()->default(4);
            $table->string('contig', 10)->nullable();
            $table->string('chromatogram_id')->nullable();
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
        Schema::dropIfExists('dr_contigs');
    }
}
