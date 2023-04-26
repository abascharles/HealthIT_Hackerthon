<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateViralrejectedreasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('viralrejectedreasons')->where('id', '=', 17)->update(['name' => 'Missing Sample', 'alias' => 'Missing Sample']);
        DB::table('viralrejectedreasons')->where('id', '=', 20)->update(['name' => 'Hemolysed sample', 'alias' => 'Hemolysed sample']);
        DB::table('viralrejectedreasons')->where('id', '=', 3)->update(['name' => 'Missing patient ID', 'alias' => 'Missing patient ID']);

        $updates = [
            [
                'name' => 'Sample request form & sample mismatch',
                'alias' => 'Sample request form & sample mismatch',
                'originalid' => 0
            ],
            [
                'name' => 'Sample not under cold chain',
                'alias' => 'Sample not under cold chain',
                'originalid' => 0
            ],

            [
                'name' => 'Expired PPT tubes',
                'alias' => 'Expired PPT tubes',
                'originalid' => 0
            ],
            [

                'name' => 'No request form',
                'alias' => 'No request form',
                'originalid' => 0
            ],
            [
                'name' => 'Improper packaging',
                'alias' => 'Improper packaging',
                'originalid' => 0
            ]

        ];


        DB::table('viralrejectedreasons')->insert($updates);

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

