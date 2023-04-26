<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidAllocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('covid_allocations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('allocation_date');
            $table->string('allocation_type');
            $table->integer('lab_id');
            $table->integer('machine_id')->nullable();
            $table->text('comments')->nullable();
            $table->enum('received', ['YES', 'NO'])->default('NO');
            $table->enum('responded', ['YES', 'NO', 'POSTPONED'])->default('NO');
            $table->integer('respond_count')->default(0);
            $table->date('date_responded')->nullable();
            $table->date('date_received')->nullable();
            $table->bigInteger('consumption_id')->nullable();
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
        Schema::dropIfExists('covid_allocations');
    }
}
