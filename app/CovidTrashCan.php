<?php

namespace App;

use DB;

use Excel;

class CovidTrashCan
{
    public static function add_data()
    {    	
        config(['excel.import.heading' => true]);

        $data = Excel::load(public_path('nic_data.xlsx'), function($reader){
            $reader->toArray();
        })->get();

        foreach ($data as $key => $row) {
        	CovidSample::create([
        		'sample_number' => $row->sample_number,
        		'created_at' => $row->date_entered,
        		'datecollected' => $row->date_collected,
        		'datereceived' => $row->date_received,
        		'identifier' => $row->lab_id,
        		'patient' => $row->patient_id,
        		'county' => $row->county,
        		'age' => $row->age,
        		'sex' => $row->gender,
        		'datetested' => $row->date_tested,
        		'result' => $row->result,
        		'residence' => $row->area_of_residence,
        	]);
        }
    }

    public static function add_data_two()
    {    	
    	DB::statement(' delete from covid_samples where result=2');
        config(['excel.import.heading' => true]);

        $data = Excel::load(public_path('covid_cases.csv'), function($reader){
            $reader->toArray();
        })->get();

        // dd($data);

        foreach ($data as $key => $row) {
        	CovidSample::create([
        		'patient' => $row->case_id,
        		'county_id' => $row->county,
        		'age' => $row->age,
        		'sex' => $row->sex,
        		'result' => 2,
        	]);
        }
    }

    public static function create_nat_table()
    {
    	DB::statement("DROP TABLE IF EXISTS `covid_samples`;");
		DB::statement("
			CREATE TABLE `covid_samples` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`national_sample_id` int(10) unsigned DEFAULT NULL,
				`county_id` tinyint(3) unsigned DEFAULT NULL,
				`lab_id` tinyint(3) unsigned DEFAULT NULL,
				`facility_id` int(10) unsigned DEFAULT NULL,
				`patient` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`patient_name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`amrs_location` tinyint(4) DEFAULT NULL,
				`provider_identifier` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
				`order_no` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`site_entry` tinyint(3) unsigned DEFAULT NULL,


				`identifier` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`sample_number` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`county` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,

				`dob` date DEFAULT NULL,
				`age` tinyint unsigned DEFAULT NULL,
				`sex` tinyint unsigned DEFAULT NULL,
				`residence` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
				`phone_no` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`symptoms_date` date DEFAULT NULL,
				`isolation_status` tinyint(3) unsigned DEFAULT NULL,
				`symptoms` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,

				`suspected` tinyint(3) unsigned DEFAULT NULL,
				`exposure` tinyint(3) unsigned DEFAULT NULL,
				`exposure_details` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
				`sample_type` tinyint(3) unsigned DEFAULT NULL,

				`receivedstatus` tinyint(3) unsigned DEFAULT NULL,
				`user_id` int(10) unsigned DEFAULT NULL,
				`received_by` int(10) unsigned DEFAULT NULL,
				`entered_by` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`comments` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`labcomment` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
				`parentid` int(10) unsigned DEFAULT '0',
				`rejectedreason` tinyint(3) unsigned DEFAULT NULL,
				`reason_for_repeat` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,

				`interpretation` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
				`result` tinyint(3) unsigned DEFAULT NULL,
				`worksheet_id` int(10) unsigned DEFAULT NULL,
				`run` tinyint(3) unsigned DEFAULT '1',
				`repeatt` tinyint(3) unsigned DEFAULT '0',
				`approvedby` int(10) unsigned DEFAULT NULL,
				`approvedby2` int(10) unsigned DEFAULT NULL,

				`datecollected` date DEFAULT NULL,
				`datereceived` date DEFAULT NULL,
				`datetested` date DEFAULT NULL,
				`datedispatched` date DEFAULT NULL,

				`datemodified` date DEFAULT NULL,
				`dateapproved` date DEFAULT NULL,
				`dateapproved2` date DEFAULT NULL,

				`tat1` tinyint(3) unsigned DEFAULT '0',
				`tat2` tinyint(3) unsigned DEFAULT '0',
				`tat3` tinyint(3) unsigned DEFAULT '0',
				`tat4` tinyint(3) unsigned DEFAULT '0',

				`synched` tinyint(4) DEFAULT '0',
				`datesynched` date DEFAULT NULL,
				`created_at` timestamp NULL DEFAULT NULL,
				`updated_at` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `covid_national_sample_id_index` (`national_sample_id`),
				KEY `covid_patient_index` (`patient`),
				KEY `covid_order_no_index` (`order_no`),
				KEY `covid_parentid_index` (`parentid`),
				KEY `covid_worksheet_id_index` (`worksheet_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

    }

    


	public static function create_tables(){

		DB::statement("
			CREATE TABLE `covid_worksheets` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`national_worksheet_id` int(10) unsigned DEFAULT NULL,
				`machine_type` tinyint(3) unsigned NOT NULL,
				`lab_id` tinyint(3) unsigned NOT NULL,
				`status_id` tinyint(3) unsigned NOT NULL DEFAULT '1',
				`runby` int(10) unsigned DEFAULT NULL,
				`uploadedby` int(10) unsigned DEFAULT NULL,
				`sortedby` int(10) unsigned DEFAULT NULL,
				`alliquotedby` int(10) unsigned DEFAULT NULL,
				`bulkedby` int(10) unsigned DEFAULT NULL,
				`reviewedby` int(10) unsigned DEFAULT NULL,
				`reviewedby2` int(10) unsigned DEFAULT NULL,
				`createdby` int(10) unsigned DEFAULT NULL,
				`cancelledby` int(10) unsigned DEFAULT NULL,
				`hiqcap_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`spekkit_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`rack_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`lot_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`sample_prep_lot_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`bulklysis_lot_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`control_lot_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`calibrator_lot_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`amplification_kit_lot_no` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`neg_control_result` tinyint(3) unsigned DEFAULT NULL,
				`pos_control_result` tinyint(3) unsigned DEFAULT NULL,
				`neg_control_interpretation` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
				`pos_control_interpretation` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
				`cdcworksheetno` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
				`kitexpirydate` date DEFAULT NULL,
				`sampleprepexpirydate` date DEFAULT NULL,
				`bulklysisexpirydate` date DEFAULT NULL,
				`controlexpirydate` date DEFAULT NULL,
				`calibratorexpirydate` date DEFAULT NULL,
				`amplificationexpirydate` date DEFAULT NULL,
				`datecut` date DEFAULT NULL,
				`datereviewed` date DEFAULT NULL,
				`datereviewed2` date DEFAULT NULL,
				`dateuploaded` date DEFAULT NULL,
				`datecancelled` date DEFAULT NULL,
				`daterun` date DEFAULT NULL,
				`synched` tinyint(4) NOT NULL DEFAULT '0',
				`datesynched` date DEFAULT NULL,
				`created_at` timestamp NULL DEFAULT NULL,
				`updated_at` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `covid_national_worksheet_id_index` (`national_worksheet_id`),
				KEY `covid_status_id_index` (`status_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");


		DB::statement("
			CREATE TABLE `covid_samples` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`national_sample_id` int(10) unsigned DEFAULT NULL,
				`lab_id` tinyint(3) unsigned NOT NULL,
				`facility_id` int(10) unsigned NOT NULL,
				`patient` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
				`patient_name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`amrs_location` tinyint(4) DEFAULT NULL,
				`provider_identifier` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
				`order_no` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`site_entry` tinyint(3) unsigned DEFAULT NULL,

				`dob` date DEFAULT NULL,
				`age` tinyint unsigned DEFAULT NULL,
				`sex` tinyint unsigned DEFAULT NULL,
				`residence` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
				`phone_no` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`symptoms_date` date DEFAULT NULL,
				`isolation_status` tinyint(3) unsigned DEFAULT NULL,
				`symptoms` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,

				`suspected` tinyint(3) unsigned DEFAULT NULL,
				`exposure` tinyint(3) unsigned DEFAULT NULL,
				`exposure_details` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
				`sample_type` tinyint(3) unsigned DEFAULT NULL,

				`receivedstatus` tinyint(3) unsigned DEFAULT NULL,
				`user_id` int(10) unsigned DEFAULT NULL,
				`received_by` int(10) unsigned DEFAULT NULL,
				`entered_by` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`comments` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
				`labcomment` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
				`parentid` int(10) unsigned DEFAULT '0',
				`rejectedreason` tinyint(3) unsigned DEFAULT NULL,
				`reason_for_repeat` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,

				`interpretation` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
				`result` tinyint(3) unsigned DEFAULT NULL,
				`worksheet_id` int(10) unsigned DEFAULT NULL,
				`run` tinyint(3) unsigned DEFAULT '1',
				`repeatt` tinyint(3) unsigned DEFAULT '0',
				`approvedby` int(10) unsigned DEFAULT NULL,
				`approvedby2` int(10) unsigned DEFAULT NULL,

				`datecollected` date DEFAULT NULL,
				`datereceived` date DEFAULT NULL,
				`datetested` date DEFAULT NULL,
				`datedispatched` date DEFAULT NULL,

				`datemodified` date DEFAULT NULL,
				`dateapproved` date DEFAULT NULL,
				`dateapproved2` date DEFAULT NULL,

				`tat1` tinyint(3) unsigned DEFAULT '0',
				`tat2` tinyint(3) unsigned DEFAULT '0',
				`tat3` tinyint(3) unsigned DEFAULT '0',
				`tat4` tinyint(3) unsigned DEFAULT '0',

				`synched` tinyint(4) DEFAULT '0',
				`datesynched` date DEFAULT NULL,
				`created_at` timestamp NULL DEFAULT NULL,
				`updated_at` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `covid_national_sample_id_index` (`national_sample_id`),
				KEY `covid_patient_index` (`patient`),
				KEY `covid_order_no_index` (`order_no`),
				KEY `covid_parentid_index` (`parentid`),
				KEY `covid_worksheet_id_index` (`worksheet_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");


		DB::statement("
			CREATE TABLE `covid_travels` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`national_travel_id` int(10) unsigned DEFAULT NULL,
				`sample_id` int(10) unsigned DEFAULT NULL,

				`travel_date` date DEFAULT NULL,
				`return_date` date DEFAULT NULL,
				`city_visited` varchar(50) DEFAULT NULL,
				`city_id` smallint(5) unsigned DEFAULT NULL,
				`duration_visited` smallint unsigned DEFAULT NULL,

				`synched` tinyint(4) NOT NULL DEFAULT '0',
				`datesynched` date DEFAULT NULL,
				`created_at` timestamp NULL DEFAULT NULL,
				`updated_at` timestamp NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `covid_national_travel_id_index` (`national_travel_id`),
				KEY `covid_sample_id_index` (`sample_id`),
				KEY `city_id` (`city_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
	}

	public static function lookups()
	{
		DB::statement("DROP TABLE IF EXISTS `covid_symptoms`;");
		DB::statement("
			CREATE TABLE `covid_symptoms` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('covid_symptoms')->insert([
			['id' => 1, 'name' => 'Fever'],
			['id' => 2, 'name' => 'Dry Cough'],
			['id' => 3, 'name' => 'Sore Throat'],
			['id' => 4, 'name' => 'Shortness of Breath'],
		]);

		DB::statement("DROP TABLE IF EXISTS `covid_isolations`;");
		DB::statement("
			CREATE TABLE `covid_isolations` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('covid_isolations')->insert([
			['id' => 1, 'name' => 'Admitted and Isolation'],
			['id' => 2, 'name' => 'In Patient Ward'],
			['id' => 3, 'name' => 'Self Quarantine'],
			['id' => 4, 'name' => 'ICU - critical condition'],
		]);

		DB::statement("DROP TABLE IF EXISTS `covid_sample_types`;");
		DB::statement("
			CREATE TABLE `covid_sample_types` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('covid_sample_types')->insert([
			['id' => 1, 'name' => 'Nasopharygneal swab in UTM'],
			['id' => 2, 'name' => 'Oropharygneal swab in UTM'],
		]);
	}

	public static function drop_tables()
	{
		DB::statement("DROP TABLE IF EXISTS `covid_worksheets`;");
		DB::statement("DROP TABLE IF EXISTS `covid_samples`;");
		DB::statement("DROP TABLE IF EXISTS `covid_travels`;");

	}

	public static function alter_tables()
	{		
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `patient_name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL after patient;');
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `isolation_status` tinyint(3) unsigned DEFAULT NULL after symptoms_date;');
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `site_entry` tinyint(3) unsigned DEFAULT NULL after order_no;');
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `phone_no` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL after residence;');
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `symptoms` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL after isolation_status;');
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `exposure` tinyint(3) unsigned DEFAULT NULL after symptoms;');
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `suspected` tinyint(3) unsigned DEFAULT NULL after symptoms;');
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `exposure_details` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL after exposure;');
		DB::statement('ALTER TABLE covid_samples ADD COLUMN `sample_type` tinyint(3) unsigned DEFAULT NULL after exposure_details;');


		DB::statement('ALTER TABLE covid_travels ADD COLUMN `return_date` date DEFAULT NULL after travel_date;');
		DB::statement('ALTER TABLE covid_travels ADD COLUMN `city_id` smallint(5) unsigned DEFAULT NULL after city_visited;');
	}

	public static function cities()
	{
		DB::statement("DROP TABLE IF EXISTS `cities`;");
		DB::statement("
			CREATE TABLE `cities` (
				`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				`subcountry` varchar(50) DEFAULT NULL,
				`country` varchar(50) DEFAULT NULL,
				`subcounty_id` smallint(5) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `name` (`name`),
				KEY `country` (`country`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		$a = file_get_contents(public_path('cities/world-cities_json.json'));
		$b = json_decode($a);

		foreach ($b as $key => $row) {
			DB::table('cities')->insert([
				['id' => $key+1, 'name' => $row->name, 'subcountry' => $row->subcountry, 'country' => $row->country]
			]);
		}
	}
}
