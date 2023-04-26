<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatesSampleLogTrailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sample_log_trail', function (Blueprint $table) {
            $table->uuid('id')->autoIncrement();
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('sample_id');
            $table->unsignedInteger('patient_id');
            $table->unsignedInteger('batch_id');
            $table->string('action');
            $table->timestamps();
            $table->primary('id');
            $table->foreign('user_id')->references('id')->on('users');

        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
