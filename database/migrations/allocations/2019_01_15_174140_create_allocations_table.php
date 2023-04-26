<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('allocations', function(Blueprint $table){
        //     $table->bigIncrements('id');
        //     $table->bigInteger('national_id')->nullable();
        //     $table->integer('machine_id');
        //     $table->tinyInteger('testtype');
        //     $table->integer('year');
        //     $table->tinyInteger('month');
        //     $table->string('order_num', 20)->after('month')->nullable();
        //     $table->date('datesubmitted')->nullable();
        //     $table->string('submittedby', 100)->nullable();
        //     $table->tinyInteger('lab_id')->nullable();
        //     $table->text('allocationcomments')->comment("Comment section filled at the Lab");
        //     $table->text('issuedcomments')->comment("Comment section filed at National");
        //     $table->tinyInteger('approve')->default(0);
        //     $table->text('disapprovereason')->nullable();
        //     $table->tinyInteger('synched')->default(0)->comment("0:Awaiting synching; 1:Synched; 2:Update awaiting synching;");
        //     $table->date('datesynched')->nullable();
        //     $table->softDeletes();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('allocations');
    }
}
