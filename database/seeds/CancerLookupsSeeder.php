<?php

use Illuminate\Database\Seeder;

class CancerLookupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	// Sample types
        $sampletypes = [
        	['name' => 'Clinician Collected', 'displaylabel' => '1. Clinician Collected'],
            ['name' => 'Self Collected', 'displaylabel' => '2. Self Collected'],
        ];
        DB::table('cancersampletypes')->truncate();
        DB::table('cancersampletypes')->insert($sampletypes);

        // Justifications
        $justifications = [
        	['name' => 'Initial Screening', 'displaylabel' => '1. Initial Screening'],
        	['name' => 'Routine screening', 'displaylabel' => '2. Routine screening'],
        	['name' => 'Post Rx Screening', 'displaylabel' => '3. Post Rx Screening'],
        	['name' => 'Repeat Screening', 'displaylabel' => '4. Repeat Screening'],
        ];
        DB::table('cancerjustifications')->truncate();
        DB::table('cancerjustifications')->insert($justifications);

        // HIV Statuses
        $hivstatuses = [
        	['name' => 'Reactive ', 'displaylabel' => '1. Reactive '],
        	['name' => 'Non Reactive', 'displaylabel' => '2. Non Reactive'],
        	['name' => 'Unknown', 'displaylabel' => '3. Unknown'],
        ];
        DB::table('cancerhivstatuses')->truncate();
        DB::table('cancerhivstatuses')->insert($hivstatuses);

        // Entry Points
        $entrypoints = [
        	['name' => 'MCH', 'displaylabel' => '1. MCH'],
        	['name' => 'CCC', 'displaylabel' => '2. CCC'],
        	['name' => 'OPD', 'displaylabel' => '3. OPD'],
        ];
        DB::table('cancerentrypoint')->truncate();
        DB::table('cancerentrypoint')->insert($entrypoints);

        // Rejected Reasons
        $rejectedreasons = [
        	['name' => 'Missing Sample', 'displaylabel' => '1. Missing Sample'],
	        ['name' => 'Missing patient Number', 'displaylabel' => '2. Missing patient Number'],
	        ['name' => 'Sample request form & Sample mismatch', 'displaylabel' => '3. Sample request form & Sample mismatch'],
	        ['name' => 'Delayed delivery', 'displaylabel' => '4. Delayed delivery'],
	        ['name' => 'Specimen processing delay', 'displaylabel' => '5. Specimen processing delay'],
	        ['name' => 'No request form', 'displaylabel' => '6. No request form'],
	        ['name' => 'Improper packaging', 'displaylabel' => '7. Improper packaging'],
	        ['name' => 'Insufficient volume', 'displaylabel' => '8. Insufficient volume'],
	        ['name' => 'Poorly Collected Sample', 'displaylabel' => '9. Poorly Collected Sample'],
	        ['name' => 'Incorrect container', 'displaylabel' => '10. Incorrect container'],
	        ['name' => 'Other (Specify)', 'displaylabel' => '11. Other (Specify)'],
        ];
        DB::table('cancerrejectedreasons')->truncate();
        DB::table('cancerrejectedreasons')->insert($rejectedreasons);

        // Actions
        $actions = [
        	['name' => 'Client given follow up date', 'displaylabel' => '1. Client given follow up date'],
	        ['name' => 'Referred for VIA', 'displaylabel' => '2. Referred for VIA'],
	        ['name' => 'Referred for Rx (Cryotherapy, LEEP or Thermocoagulation)', 'displaylabel' => '3. Referred for Rx (Cryotherapy, LEEP or Thermocoagulation)'],
	        ['name' => 'Client pregnant therefore follow up after delivery.', 'displaylabel' => '4. Client pregnant therefore follow up after delivery.'],
            ['name'=> 'Re-screen after 1 year', 'displaylabel' => '5. Re-screen after 1 year'],
            ['name'=> 'Re-screen after 3 years', 'displaylabel' => '6. Re-screen after 3 years'],
        ];
        DB::table('canceractions')->truncate();
        DB::table('canceractions')->insert($actions);
        
    }
}
