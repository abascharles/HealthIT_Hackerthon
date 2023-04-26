<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrWorksheetWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_worksheet_warnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('worksheet_id')->unsigned()->index();
            $table->integer('warning_id')->unsigned()->index();
            // $table->boolean('error')->default(0);
            // $table->string('title', 20)->nullable();
            $table->string('system_field', 20)->nullable();
            $table->string('detail', 100)->nullable();
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
        Schema::dropIfExists('dr_worksheet_warnings');
    }
}
