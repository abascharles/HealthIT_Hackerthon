<?php

use Illuminate\Database\Seeder;
use App\ManualMachine;

class ManualMachineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $machines = [
        	['machine' => 'Manual - DAAN Kit'],
        	['machine' => 'Manual - SD Biosensor'],
        	['machine' => 'Manual - Sun Sure'],
        ];
        foreach ($machines as $key => $machine) {
        	ManualMachine::create($machine);
        }
    }
}
