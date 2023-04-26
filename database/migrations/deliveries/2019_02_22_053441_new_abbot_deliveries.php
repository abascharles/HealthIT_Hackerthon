<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewAbbotDeliveries extends Migration
{
    public $main = ['qualkit','calibration','control','buffer','preparation','adhesive','deepplate','mixtube','reactionvessels','reagent','reactionplate','1000disposable','200disposable'];
    public $sub = ['received','damaged'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('abbotdeliveries', function(Blueprint $table){
            // $table->softDeletes()->after('datesynched');
        });
        // Schema::create('abbotdeliveries', function (Blueprint $table) {
            // $table->increments('id');
            // $table->tinyInteger('testtype')->unsigned();
            // $table->tinyInteger('lab_id')->unsigned()->index();
            // $table->tinyInteger('quarter')->unsigned();
            // $table->integer('year')->unsigned();
            // $table->tinyInteger('source')->unsigned()->nullable();
            // $table->tinyInteger('labfrom')->unsigned()->nullable();
            // foreach ($this->main as $key => $value) {
            //     if ($key < 5)
            //         $table->string($value.'lotno', 12)->nullable();
            // }
            // foreach ($this->main as $key => $value) {
            //     if ($key < 5)
            //         $table->date($value.'expiry')->nullable();
            // }
            // foreach ($this->main as $key => $value) {
            //     foreach ($this->sub as $subkey => $subvalue) {
            //         $table->integer($value.$subvalue)->unsigned()->default(0)->nullable();
            //     }
            // }
            // $table->integer('receivedby')->unsigned()->index()->nullable();
            // $table->date('datereceived')->nullable();
            // $table->tinyInteger('status')->unsigned()->default(0);
            // $table->integer('enteredby')->unsigned()->index()->nullable();
            // $table->date('dateentered')->nullable();
            // $table->tinyInteger('flag')->unsigned()->default(1);
            // // $table->date('datesynchronized')->nullable();
            // $table->tinyInteger('synched')->default(0);
            // $table->date('datesynched')->nullable();
            // $table->softDeletes();
            // $table->timestamps();

            // $table->index(['year', 'quarter'], 'year_quarter');
        // });
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
