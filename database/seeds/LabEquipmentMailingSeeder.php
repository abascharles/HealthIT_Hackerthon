<?php

use Illuminate\Database\Seeder;

class LabEquipmentMailingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\EquipmentMailingList::truncate();
    	if (env('APP_LAB') == 1) //nairobi
			$labemails = [['email' => 'emmahmaina1@gmail.com', 'type' => 2],
        					['email' => 'nafisa.lillian@gmail.com', 'type' => 2]];
		elseif (env('APP_LAB') == 2) //cdc
			$labemails = [['email' => 'olootob@gmail.com', 'type' => 2]];
		elseif (env('APP_LAB') == 3) //alupe
			$labemails = [['email' => 'lucy.okubis@gmail.com', 'type' => 2],
        					['email' => 'adhysmaureen2@yahoo.com', 'type' => 2]];
		elseif (env('APP_LAB') == 4) //wrp
			$labemails = [['email' => 'Alex.Kasembeli@usamru-k.org', 'type' => 2]];
		elseif (env('APP_LAB') == 5) //ampath
			$labemails = [['email' => 'skadima@ampathplus.or.ke', 'type' => 2],
        					['email' => 'maryron2002@gmail.com', 'type' => 2]];
		elseif (env('APP_LAB') == 6) //cpgh
			$labemails = [['email' => 'kenga.dickson@yahoo.com', 'type' => 2],
        					['email' => 'wakeshot@yahoo.com', 'type' => 2]];
		elseif (env('APP_LAB') == 8) //nyumbani
			$labemails = [['email' => 'labquality@nyumbani.org', 'type' => 2],
        					['email' => 'diagnosis@nyumbani.org', 'type' => 2]];
		elseif (env('APP_LAB') == 9) //knh
			$labemails = [['email' => 'eokapesi@gmail.com', 'type' => 2],
        					['email' => 'kibewaweru@gmail.com', 'type' => 2]];


        $emails = [['email' => 'njebungeibowen@gmail.com', 'type' => 1],
        ['email' => 'mld8@cdc.gov', 'type' => 2], ['email' => 'uys3@cdc.gov', 'type' => 2], 
        ['email' => 'EKirui@mgickenya.org', 'type' => 2], ['email' => 'ootieno@usaid.gov', 'type' => 2], 
        ['email' => 'kouma@mgickenya.org', 'type' => 2], ['email' => 'skipkerich@gmail.com', 'type' => 2], 
        ['email' => 'jbatuka@usaid.gov', 'type' => 2],
        ['email' => 'jlusike@clintonhealthaccess.org', 'type' => 2], ['email' => 'solwande@clintonhealthaccess.org', 'type' => 2], 
        ['email' => 'tngugi@clintonhealthaccess.org', 'type' => 2], ['email' => 'joshua.bakasa@dataposit.co.ke', 'type' => 2],
        ['email' => 'joel.kithinji@dataposit.co.ke', 'type' => 2]];
        
        // Merge the lab records with the universal recipients
        $emails = array_merge($emails, $labemails);
        
        foreach ($emails as $key => $value) {
        	$insert = \App\EquipmentMailingList::create($value);
        }
    }
}
