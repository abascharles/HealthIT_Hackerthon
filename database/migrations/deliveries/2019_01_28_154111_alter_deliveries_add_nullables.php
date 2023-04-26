<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDeliveriesAddNullables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deliveries', function(Blueprint $table){
            $table->integer('receivedby')->nullable()->change();
            $table->date('datereceived')->nullable()->change();
            $table->integer('enteredby')->nullable()->change();
            $table->date('datesynched')->nullable()->change();
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
