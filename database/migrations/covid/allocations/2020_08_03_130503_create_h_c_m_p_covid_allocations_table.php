<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHCMPCovidAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hcmp_covid_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('allocation_date');
            $table->string('allocation_type');
            $table->integer('lab_id');
            $table->string('material_number');
            $table->float('allocated_kits');
            $table->float('received_kits')->nullable();
            $table->bigInteger('consumption_detail_id')->nullable();
            $table->text('comments')->nullable();
            $table->enum('received', ['YES', 'NO'])->default('NO');
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
        Schema::dropIfExists('hcmp_covid_allocations');
    }
}
