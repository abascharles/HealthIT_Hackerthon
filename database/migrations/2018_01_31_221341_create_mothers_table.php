<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMothersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mothers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('national_mother_id')->unsigned()->nullable()->index(); 

            // The id on viralpatients table
            $table->integer('patient_id')->unsigned()->nullable()->index();
            $table->string('ccc_no', 25)->nullable()->index();
            $table->date('mother_dob')->nullable();
            // $table->integer('fcode')->unsigned()->nullable();
            $table->integer('facility_id')->unsigned()->index();
            $table->tinyInteger('hiv_status')->unsigned()->nullable();
            $table->tinyInteger('synched')->default(0);
            $table->date('datesynched')->nullable();
            // $table->date('created_at')->nullable();
            // $table->date('updated_at')->nullable();
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
        Schema::dropIfExists('mothers');
    }
}
