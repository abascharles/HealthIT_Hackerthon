<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllocationDetailsBreakdownsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_details_breakdowns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('national_id')->nullable();
            $table->bigInteger('allocation_detail_id');
            $table->integer('breakdown_id');
            $table->string('breakdown_type');
            $table->float('allocated');
            $table->tinyInteger('synched')->default(0)->comment("0:Awaiting synching; 1:Synched; 2:Update awaiting synching;");
            $table->date('datesynched')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('allocation_details_breakdowns');
    }
}
