<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('county_id')->unsigned()->nullable();
            $table->string('name', 25);
            $table->string('subject', 100);
            $table->string('from_name', 50)->nullable();
            $table->string('cc_list')->nullable();
            $table->string('bcc_list')->nullable();
            $table->boolean('lab_signature')->default(true);
            $table->dateTime('time_to_be_sent')->nullable();
            $table->boolean('sent')->default(false);
            $table->integer('user_id')->unsigned()->nullable()->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
    }
}
