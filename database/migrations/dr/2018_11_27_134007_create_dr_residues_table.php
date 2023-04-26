<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrResiduesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_residues', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('genotype_id')->unsigned()->index();
            $table->string('residue', 10)->nullable(); 
            $table->smallInteger('residue_id')->nullable()->unsigned()->index();
            $table->smallInteger('position')->nullable()->unsigned();
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
        Schema::dropIfExists('dr_residues');
    }
}
