<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class update_viraljustifications_table extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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
        //if
        if (!(DB::table('viraljustifications')->where('displaylabel','=',"2 Follow up"))) {
            DB::table('viraljustifications')->insert($updates);
        }
        DB::table('viraljustifications')->where('name','Single Drug Substitution')->update(['displaylabel'=>'3 Single Drug Substitution']);
        DB::table('viraljustifications')->where('name','Recency Testing')->update(['displaylabel'=>'4 Recency','name'=>'Recency']);

    }
}
