<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTaqmanDeliveries extends Migration
{
    public $main = ['qualkit','spexagent','ampinput','ampflapless','ampktips','ampwash','ktubes','consumables'];
    public $sub = ['received','damaged'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('taqmandeliveries', function (Blueprint $table) {
            // $table->softDeletes()->after('datesynched');
        });
        // Schema::create('taqmandeliveries', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->tinyInteger('testtype')->unsigned()->index();
        //     $table->tinyInteger('lab_id')->unsigned()->index();
        //     $table->tinyInteger('quarter')->unsigned();
        //     $table->integer('year')->unsigned();
        //     $table->tinyInteger('source')->unsigned()->nullable();
        //     $table->tinyInteger('labfrom')->unsigned()->nullable();
        //     $table->string('kitlotno', 12)->nullable();
        //     $table->date('expirydate')->nullable();
        //     foreach ($this->main as $key => $value) {
        //         foreach ($this->sub as $subkey => $subvalue) {
        //             $table->integer($value.$subvalue)->unsigned()->nullable();
        //         }
        //     }
        //     $table->integer('receivedby')->unsigned()->index()->nullable();
        //     $table->date('datereceived')->nullable();
        //     $table->tinyInteger('status')->unsigned()->default(0);
        //     $table->integer('enteredby')->unsigned()->index()->nullable();
        //     $table->date('dateentered')->nullable();
        //     $table->tinyInteger('flag')->unsigned()->default(1);
        //     // $table->date('datesynchronized')->nullable();
        //     $table->tinyInteger('synched')->default(0);
        //     $table->date('datesynched')->nullable();
        //     $table->softDeletes();
        //     $table->timestamps();

        //     $table->index(['year', 'quarter'], 'year_quarter');
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
