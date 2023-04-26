<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateViraljustificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('viraljustifications')->where('id','<=',3)->update(['flag'=>0]);
        DB::table('viraljustifications')->where('id','>=',6)->where('id','<',12)->update(['flag'=>0]);
        $updates = [
            [
                'displaylabel' => '1 1ST VL',
                'name' => '1ST VL',
                'flag' => 1,
                'rank_id' => 1
            ],
            [
                'displaylabel' => '2 Follow up',
                'name' => 'Follow up',
                'flag' => 1,
                'rank_id' => 2
            ],

            [
                'displaylabel' => '5 PMTCT NP',
                'name' => 'PMTCT NP',
                'flag' => 1,
                'rank_id' => 5
            ],
            [
                'displaylabel' => '6 PMTCT KP',
                'name' => 'PMTCT KP',
                'flag' => 1,
                'rank_id' => 6
            ]

        ];

        if (!(DB::table('viraljustifications')->where('displaylabel','=',"2 Follow up")->get()->first())) {
            DB::table('viraljustifications')->insert($updates);
        }
        DB::table('viraljustifications')->where('name','Single Drug Substitution')->update(['displaylabel'=>'3 Single Drug Substitution', 'rank_id' => 3]);
        DB::table('viraljustifications')->where('name','Recency Testing')->update(['displaylabel'=>'4 Recency','name'=>'Recency', 'rank_id' => 4]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
