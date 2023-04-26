<?php

use Illuminate\Database\Seeder;

class DrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	\App\DrExtractionWorksheet::create(['lab_id' => env('APP_LAB'), 'createdby' => 1, 'date_gel_documentation' => date('Y-m-d')]);

    	\App\DrWorksheet::create(['extraction_worksheet_id' => 1])->create();

    	DB::table('dr_samples')->insert([
    		['id' => 1, 'control' => 1, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 2, 'control' => 2, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    	]);

    	DB::table('dr_samples')->insert([
    		['id' => 6, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 10, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 14, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 17, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 20, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 22, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 99, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 2009695759, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 2012693909, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 2012693911, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 2012693943, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 3005052934, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    		['id' => 3005052959, 'patient_id' => 1, 'worksheet_id' => 1, 'extraction_worksheet_id' => 1],
    	]);

    }
}
