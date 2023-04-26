<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUlizaAdditionalInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uliza_additional_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uliza_clinical_form_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index()->nullable();
            $table->text('requested_info')->nullable();
            $table->text('additional_info')->nullable();
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
        Schema::dropIfExists('uliza_additional_infos');
    }
}
