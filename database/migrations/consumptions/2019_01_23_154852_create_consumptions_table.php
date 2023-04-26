<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsumptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('national_id')->nullable();
            $table->tinyInteger('month')->nullable();
            $table->integer('year')->nullable();
            $table->tinyInteger('type')->nullable()->comment("Indicates if this is an EID, VL, Consumable delivery");
            $table->tinyInteger('machine')->nullable();
            $table->integer('tests')->default(0);
            $table->date('datesubmitted')->nullable();
            $table->integer('submittedby')->nullable();
            $table->tinyInteger('lab_id')->nullable();
            $table->text('comments')->nullable();
            $table->text('issuedcomments')->nullable();
            $table->tinyInteger('approve')->default(0);
            $table->text('disapprovereason')->nullable();
            $table->tinyInteger('synched')->default(0);
            $table->date('datesynched')->nullable();
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
        Schema::dropIfExists('consumptions');
    }
}
