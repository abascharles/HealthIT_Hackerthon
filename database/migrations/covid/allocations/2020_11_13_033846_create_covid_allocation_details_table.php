<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidAllocationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_allocation_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('covid_allocation_detail_id');
            $table->string('material_number');
            $table->float('allocated_kits');
            $table->float('received_kits')->nullable();
            $table->bigInteger('consumption_detail_id')->nullable();
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
        Schema::dropIfExists('covid_allocation_details');
    }
}
