<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Load the following SQL files
        // complete.sql
        // facilitys.sql

        // dd('What the hell do you think you are doing!! We see you!!!');
        // $this->call(UsersTableSeeder::class);
        // $this->call(KitsSeeder::class);    
        // $this->call(LabEquipmentMailingSeeder::class);
        // $this->call(GeneralConsumablesSeeder::class);
        $this->call(CovidLookupSeeder::class);
        // $this->call(CovidKitsSeeder::class);
        // $this->call(TestTypeSeeder::class);
        // $this->call(ManualMachineSeeder::class);
        // $this->call(CancerLookupsSeeder::class);
    }
}
