<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrContigWarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dr_contig_warnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('contig_id')->unsigned()->index();
            $table->smallInteger('warning_id')->unsigned()->index();
            // $table->boolean('error')->default(0);
            // $table->string('title', 20)->nullable();
            $table->string('system_field', 20)->nullable();
            $table->string('detail', 100)->nullable();
            $table->string('type', 100)->nullable();
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
        Schema::dropIfExists('dr_contig_warnings');
    }
}
