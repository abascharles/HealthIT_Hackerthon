<?php

use Illuminate\Database\Seeder;
use App\TestType;

class TestTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
        		['name' => 'EID', 'type' => 'Qualitative'],
        		['name' => 'VL', 'type' => 'Quantitative']
        	];

        TestType::truncate();
        foreach ($types as $key => $type) {
        	TestType::create($type);
        }
    }
}
