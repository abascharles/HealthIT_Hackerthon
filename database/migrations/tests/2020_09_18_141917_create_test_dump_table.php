<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestDumpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_dump', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('date')->nullable();
            $table->string('tests')->nullable();
            $table->string('platform')->nullable();
            $table->string('begining_balance')->nullable();
            $table->string('received')->nullable();
            $table->string('kits_used')->nullable();
            $table->string('positive')->nullable();
            $table->string('negative')->nullable();
            $table->string('wastage')->nullable();
            $table->string('ending')->nullable();
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
        Schema::dropIfExists('test_dump');
    }
}
