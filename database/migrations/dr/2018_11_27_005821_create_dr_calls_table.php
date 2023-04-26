<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_calls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sample_id')->unsigned()->index();
            $table->string('drug_class', 50)->nullable(); 
            $table->tinyInteger('drug_class_id')->nullable()->unsigned()->index(); 
            $table->string('mutations')->nullable(); 
            // $table->string('other_mutations', 250)->nullable(); 
            // $table->string('major_mutations', 250)->nullable(); 
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
        Schema::dropIfExists('dr_calls');
    }
}
