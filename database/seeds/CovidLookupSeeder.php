<?php

use Illuminate\Database\Seeder;

class CovidLookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	//  INSERT INTO results (id, `name`, `alias`, `name_colour`) VALUES (8, 'Presumed Positive', '', "<strong><div style='color: #ffff00;'>Presumed Positive</div></strong>");

		// return;

		DB::statement("ALTER TABLE covid_samples ADD COLUMN `vaccination_status` tinyint(3) unsigned DEFAULT 1 AFTER `sample_type` ");

		DB::statement("DROP TABLE IF EXISTS `covid_vaccination_statuses`;");
		DB::statement("
			CREATE TABLE `covid_vaccination_statuses` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(150) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");
		DB::table('covid_vaccination_statuses')->insert([
			['id' => 1, 'name' => 'Not Vaccinated', ],
			['id' => 2, 'name' => 'Partially Vaccinated (One Dose)', ],
			['id' => 3, 'name' => 'Fully Vaccinated', ],
		]);

		// return;

		DB::statement("DROP TABLE IF EXISTS `covid_sample_types`;");
		DB::statement("
			CREATE TABLE `covid_sample_types` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				`nphl_name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('covid_sample_types')->insert([
			['id' => 1, 'name' => 'Nasopharygneal & Oropharygneal swabs', 'nphl_name' => 'NP/OP Swab'],
			['id' => 2, 'name' => 'Nasopharygneal swab in UTM', 'nphl_name' => 'NP Swab'],
			['id' => 3, 'name' => 'Oropharygneal swab in UTM', 'nphl_name' => 'OP Swab'],
			['id' => 4, 'name' => 'Serum', 'nphl_name' => 'Serum'],
			['id' => 5, 'name' => 'Sputum', 'nphl_name' => 'Sputum'],
			['id' => 6, 'name' => 'Tracheal Aspirate', 'nphl_name' => 'Tracheal Aspirate'],
			['id' => 7, 'name' => 'Other', 'nphl_name' => 'Other'],
		]);


		DB::statement("DROP TABLE IF EXISTS `covid_justifications`;");
		DB::statement("
			CREATE TABLE `covid_justifications` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(150) DEFAULT NULL,
				`nphl_code` tinyint DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('covid_justifications')->insert([
			['id' => 1, 'name' => 'Contact with confirmed case', 'nphl_code' => 2],
			['id' => 2, 'name' => 'Presented at health facility', 'nphl_code' => 4],
			['id' => 3, 'name' => 'Surveillance', 'nphl_code' => 4],
			['id' => 4, 'name' => 'Point of entry detection', 'nphl_code' => 4],
			['id' => 5, 'name' => 'Repatriation', 'nphl_code' => 4],
			['id' => 6, 'name' => 'Other', 'nphl_code' => 4],
			['id' => 7, 'name' => 'Surveillance and Quarantine', 'nphl_code' => 4],
			['id' => 8, 'name' => 'Recent travel', 'nphl_code' => 4],
			['id' => 9, 'name' => 'Health Care Worker', 'nphl_code' => 3],
			['id' => 10, 'name' => 'Truck Driver', 'nphl_code' => 4],
			['id' => 11, 'name' => 'Food Handlers', 'nphl_code' => 4],
			['id' => 12, 'name' => 'Before medical/surgical procedure', 'nphl_code' => 6],
			['id' => 13, 'name' => 'Travel to Country/County with presumed widespread COVID-19', 'nphl_code' => 2],
			['id' => 14, 'name' => 'Meets case definition', 'nphl_code' => 1],
			['id' => 15, 'name' => 'Acute severe respiratory illness', 'nphl_code' => 1],
			['id' => 16, 'name' => 'Air Travel', 'nphl_code' => 4],
			['id' => 17, 'name' => 'Outbreak Investigation', 'nphl_code' => 4],
			['id' => 18, 'name' => 'Prison/Remand', 'nphl_code' => 5],
		]);

		return;

		DB::statement("DROP TABLE IF EXISTS `nationalities`;");
		DB::statement("
			CREATE TABLE `nationalities` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('nationalities')->insert([
			['id' => 1, 'name' => 'Kenyan'],
			['id' => 2, 'name' => 'African'],
			['id' => 3, 'name' => 'European'],
			['id' => 4, 'name' => 'Asian'],
			['id' => 5, 'name' => 'American'],
			['id' => 6, 'name' => 'Other'],
			['id' => 7, 'name' => 'Ugandan'],
			['id' => 8, 'name' => 'Tanzanian'],
			['id' => 9, 'name' => 'Rwandese'],
			['id' => 10, 'name' => 'DRC National'],
			['id' => 11, 'name' => 'South Sudanese'],
			['id' => 12, 'name' => 'Somalian'],
			['id' => 13, 'name' => 'Ethiopian'],
			['id' => 14, 'name' => 'Sudanese'],
			['id' => 15, 'name' => 'Burundian'],
			['id' => 16, 'name' => 'Chinese'],
		]);

    	
		DB::statement("DROP TABLE IF EXISTS `quarantine_sites`;");
		DB::statement("CREATE TABLE `quarantine_sites` (
				`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(100) DEFAULT NULL,
				`email` varchar(100) DEFAULT NULL,
				`synched` tinyint(1) DEFAULT 0,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('quarantine_sites')->insert([
			['id' => 1, 'synched' => 1, 'name' => 'Infectious Disease Unit-KNH IDU', 'email' => 'mwangimilkahke@gmail.com,idu@knh.or.ke'],
			['id' => 2, 'synched' => 1, 'name' => 'Kenyatta University - (KU)', 'email' => null],
			['id' => 3, 'synched' => 1, 'name' => 'Kenya School of Government - (KSG)', 'email' => null],
			['id' => 4, 'synched' => 1, 'name' => 'Boma Hotel', 'email' => 'omondij2005@yahoo.com,pwwanjohi@gmail.com'],
			['id' => 5, 'synched' => 1, 'name' => 'KMTC Karen', 'email' => 'jeprosekip@gmail.com'],
			['id' => 6, 'synched' => 1, 'name' => 'Mandera - (MCRH)', 'email' => 'abukardug@gmail.com'],
			['id' => 7, 'synched' => 1, 'name' => "Nairobi Women's", 'email' => 'alex.muruga@nwch.co.ke'],
			['id' => 8, 'synched' => 1, 'name' => 'LANCET', 'email' => null],
			['id' => 9, 'synched' => 1, 'name' => 'KQ medical centre pride', 'email' => null],
			['id' => 10, 'synched' => 1, 'name' => 'Nairobi hospital', 'email' => 'alicekanyua@nbihosp.org'],
			['id' => 11, 'synched' => 1, 'name' => 'Nairobi West', 'email' => null],
			['id' => 12, 'synched' => 1, 'name' => 'Kisii teaching and referral hospital', 'email' => 'cliffmomanyimogeni@gmail.com'],
			['id' => 13, 'synched' => 1, 'name' => 'NHPLS', 'email' => null],
			['id' => 14, 'synched' => 1, 'name' => 'Meru TRH', 'email' => null],
			['id' => 15, 'synched' => 1, 'name' => 'MP Shah hospital', 'email' => 'vpatel@mpshahhospital.org'],
			['id' => 16, 'synched' => 1, 'name' => 'KEMRI clinic', 'email' => null],
			['id' => 17, 'synched' => 1, 'name' => 'Lenana School', 'email' => null],
			['id' => 18, 'synched' => 1, 'name' => 'Moi Girls Kibra', 'email' => null],
			['id' => 19, 'synched' => 1, 'name' => 'MTRH', 'email' => null],
			['id' => 20, 'synched' => 1, 'name' => 'KMTC', 'email' => null],
			['id' => 21, 'synched' => 1, 'name' => 'Monarch Hotel', 'email' => null],
			['id' => 22, 'synched' => 1, 'name' => 'Trademark Hotel', 'email' => null],
			['id' => 23, 'synched' => 1, 'name' => 'Panari Hotel', 'email' => null],
			['id' => 24, 'synched' => 1, 'name' => 'KEWI', 'email' => null],
			['id' => 25, 'synched' => 1, 'name' => 'Kauwi subcounty hospital', 'email' => 'alowino@gmail.com,mutisya45@yahoo.com,annvera06@gmail.com'],
			['id' => 26, 'synched' => 1, 'name' => 'Kings Premier Inn', 'email' => 'neemandege@gmail.com'],
			['id' => 27, 'synched' => 1, 'name' => 'Land Mark Suites', 'email' => null],
			['id' => 28, 'synched' => 1, 'name' => 'Nairobi School', 'email' => null],
			['id' => 29, 'synched' => 1, 'name' => 'MASH', 'email' => null],
			['id' => 30, 'synched' => 1, 'name' => '67 Hotel Syokimau', 'email' => null],
			['id' => 31, 'synched' => 1, 'name' => 'Kenya Comfort', 'email' => null],
			['id' => 32, 'synched' => 1, 'name' => 'NIC', 'email' => null],
			['id' => 33, 'synched' => 1, 'name' => "Nairobi Women's, Kitengela", 'email' => null],
			['id' => 34, 'synched' => 1, 'name' => 'Makindu Sub-County Hospital', 'email' => null],
			['id' => 35, 'synched' => 1, 'name' => 'Kambu Sub County Hospital', 'email' => null],
			['id' => 36, 'synched' => 1, 'name' => 'Iten County referal hospital', 'email' => null],
			['id' => 37, 'synched' => 1, 'name' => 'Coptic Hospital', 'email' => null],
			// ['id' => 38, 'synched' => 1, 'name' => 'Kisii Teaching and Referral hospital', 'email' => null],
			['id' => 39, 'synched' => 1, 'name' => 'Karen - KEMRI', 'email' => null],
			['id' => 40, 'synched' => 1, 'name' => 'EID - KEMRI', 'email' => null],
			['id' => 41, 'synched' => 1, 'name' => 'Mtito Andei Subcounty hospital', 'email' => null],
			['id' => 42, 'synched' => 1, 'name' => 'Kibwezi subcounty hospital', 'email' => null],
			['id' => 43, 'synched' => 1, 'name' => 'Makueni county referral hospital', 'email' => 'daviekiuluku@yahoo.com'],
			['id' => 44, 'synched' => 1, 'name' => 'Elgeyo Marakwet', 'email' => null],
			['id' => 45, 'synched' => 1, 'name' => 'Kitui Referral Hospital', 'email' => 'alowino@gmail.com,mutisya45@yahoo.com,annvera06@gmail.com'],
			['id' => 46, 'synched' => 1, 'name' => 'Kitui Nursing home', 'email' => null],
			['id' => 47, 'synched' => 1, 'name' => 'Longisa County referral hospital', 'email' => null],
			['id' => 48, 'synched' => 1, 'name' => 'Siha Hospital Mpeketoni', 'email' => null],
			['id' => 49, 'synched' => 1, 'name' => 'Tenwek Hospital', 'email' => null],
			['id' => 50, 'synched' => 1, 'name' => 'Migori county referral hospital', 'email' => null],
			['id' => 51, 'synched' => 1, 'name' => 'Rapta / Raotha / Raphta', 'email' => null], 
			['id' => 52, 'synched' => 1, 'name' => 'Crown Plaza', 'email' => null],
			['id' => 53, 'synched' => 1, 'name' => 'Mater Hospital (MMH)', 'email' => 'martin.makanga@materkenya.com,mmuhati@materkenya.com'],
			['id' => 54, 'synched' => 1, 'name' => 'Sultan Hamud', 'email' => 'daviekiuluku@yahoo.com'],
			['id' => 55, 'synched' => 1, 'name' => 'Sheraton', 'email' => null],
			['id' => 56, 'synched' => 1, 'name' => 'Four Points JKIA', 'email' => 'neemandege@gmail.com'],
			['id' => 57, 'synched' => 1, 'name' => 'Pride Inn Azure', 'email' => null],
			['id' => 58, 'synched' => 1, 'name' => 'Aga Khan Kisii', 'email' => null],
			['id' => 59, 'synched' => 1, 'name' => 'Migwani Hospital', 'email' => 'alowino@gmail.com,mutisya45@yahoo.com,annvera06@gmail.com'],
			['id' => 60, 'synched' => 1, 'name' => 'Hilton Garden Inn', 'email' => 'neemandege@gmail.com'],
			['id' => 61, 'synched' => 1, 'name' => 'Kawangware', 'email' => null],
			['id' => 62, 'synched' => 1, 'name' => 'Ngara', 'email' => 'nmmarebe@gmail.com,koyiolucina@yahoo.co.uk,ngunucarol@yahoo.com'],
			['id' => 63, 'synched' => 1, 'name' => 'Mwingi Level 4', 'email' => 'alowino@gmail.com,mutisya45@yahoo.com,annvera06@gmail.com'],
			['id' => 64, 'synched' => 1, 'name' => 'New Life Home Trust', 'email' => 'janet.mutinda@newlifehometrust.org'],
			['id' => 65, 'synched' => 1, 'name' => 'KMTC- Port Reitz', 'email' => null],
			['id' => 66, 'synched' => 1, 'name' => 'KMTC- Mombasa Campus', 'email' => null],
			['id' => 67, 'synched' => 1, 'name' => 'Mombasa Beach Hotel', 'email' => null],
			['id' => 68, 'synched' => 1, 'name' => 'Likoni Approved School', 'email' => null],
			['id' => 69, 'synched' => 1, 'name' => 'Kamalel', 'email' => null],
			['id' => 70, 'synched' => 1, 'name' => 'CJ Bar & Restaurant', 'email' => null],
			['id' => 71, 'synched' => 1, 'name' => 'Kajiado Central', 'email' => null],
			['id' => 72, 'synched' => 1, 'name' => 'Embassy Hotel', 'email' => null],
			['id' => 73, 'synched' => 1, 'name' => 'Eco Foods Resort', 'email' => null],
			['id' => 74, 'synched' => 1, 'name' => 'Whiskey stream', 'email' => null],
			['id' => 75, 'synched' => 1, 'name' => 'Lisbon Cafe', 'email' => null],
			['id' => 76, 'synched' => 1, 'name' => 'Havana', 'email' => null],
			['id' => 77, 'synched' => 1, 'name' => 'Bluu Nile Hotel Kisii', 'email' => null],
			['id' => 78, 'synched' => 1, 'name' => 'Scoops & Smiles Ice Cream', 'email' => null],
			['id' => 79, 'synched' => 1, 'name' => 'Coffee Shop Kisii', 'email' => null],
			['id' => 80, 'synched' => 1, 'name' => 'Befries', 'email' => null],
			['id' => 81, 'synched' => 1, 'name' => 'Diplozz Resort', 'email' => null],
			['id' => 82, 'synched' => 1, 'name' => 'Wind Park', 'email' => null],
			['id' => 83, 'synched' => 1, 'name' => 'Bellavista', 'email' => null],
			['id' => 84, 'synched' => 1, 'name' => 'Kajiado County Referral Hospital', 'email' => 'sheyue@gmail.com,eligach2017@gmail.com,michirakelvin@gmail.com'],
			['id' => 85, 'synched' => 1, 'name' => 'Narok County Referral Hospital', 'email' => 'bundi.lilah@gmail.com'],
			['id' => 86, 'synched' => 1, 'name' => 'Vigilance', 'email' => 'covid-19lab@nphl.go.ke'],
			['id' => 87, 'synched' => 1, 'name' => 'Nanyuki TRH', 'email' => 'laikipiacountyhealth@gmail.com,joelmaino7@yahoo.com'],
			['id' => 88, 'synched' => 1, 'name' => 'KMTC Uasin Gishu', 'email' => null],
			['id' => 89, 'synched' => 1, 'name' => 'Eastleigh', 'email' => null],
			['id' => 90, 'synched' => 1, 'name' => 'Somalia', 'email' => 'raimundP@bacroftglobal.org'],
			['id' => 91, 'synched' => 1, 'name' => 'Dandora', 'email' => null],
			['id' => 92, 'synched' => 1, 'name' => 'Kasarani', 'email' => 'florencewangari09@yahoo.com'],
			['id' => 93, 'synched' => 1, 'name' => 'Aga Khan Mombasa', 'email' => null],
			['id' => 94, 'synched' => 1, 'name' => 'Naivasha hospital', 'email' => 'naivashahospital@gmail.com,lizkiptoo@gmail.com'],
			['id' => 95, 'synched' => 1, 'name' => 'Kiambu Level 5', 'email' => 'scmohkiambu@gmail.com'],
			['id' => 96, 'synched' => 1, 'name' => 'Kwibancha', 'email' => null],
			['id' => 97, 'synched' => 1, 'name' => 'Red Cross', 'email' => null],
			['id' => 98, 'synched' => 1, 'name' => 'Namanga Port', 'email' => null],
			['id' => 99, 'synched' => 1, 'name' => 'Busia border point', 'email' => 'kimelijoshua@gmail.com'],
			['id' => 100, 'synched' => 1, 'name' => 'Malaba border point', 'email' => 'walelaeve@gmail.com'],
			['id' => 101, 'synched' => 1, 'name' => 'Nairobi Remand Prison Health Centre', 'email' => 'kisivuliazech@gmail.com,petvich@yahoo.com'],
			['id' => 102, 'synched' => 1, 'name' => 'Langata Women\'s Prison', 'email' => 'kisivuliazech@gmail.com,petvich@yahoo.com'],
			['id' => 103, 'synched' => 1, 'name' => 'National Youth Service', 'email' => null],
			['id' => 104, 'synched' => 1, 'name' => 'Embu KSG', 'email' => 'kanginjiru11@gmail.com,patnjuki08@gmail.com'],
			['id' => 105, 'synched' => 1, 'name' => 'Ongata Rongai', 'email' => 'eligach2017@gmail.com,jacksonsitoya@gmail.com,ericjmose@gmail.com'],
			['id' => 106, 'synched' => 1, 'name' => 'Dagoretti', 'email' => 'rirutalab2015@gmail.com'],
			['id' => 107, 'synched' => 1, 'name' => 'Pearl Hotel', 'email' => 'erastonyabugah@gmail.com,shilaho74@gmail.com'],
			['id' => 108, 'synched' => 1, 'name' => 'Glory Palace Hotel', 'email' => 'erastonyabugah@gmail.com,shilaho74@gmail.com'],
			['id' => 109, 'synched' => 1, 'name' => 'KMTC Ngong', 'email' => 'tiaratish@gmail.com'],
			['id' => 110, 'synched' => 1, 'name' => 'Lamada Hotel', 'email' => 'jnyamweru@gmail.com'],
			['id' => 111, 'synched' => 1, 'name' => 'JKUAT', 'email' => 'jnyamweru@gmail.com'],
			['id' => 112, 'synched' => 1, 'name' => 'Batian Peak Apartments', 'email' => 'gichanajk@yahoo.com'],
			['id' => 113, 'synched' => 1, 'name' => 'Korinda Prison', 'email' => 'otirehilary@yahoo.com'],
			['id' => 114, 'synched' => 1, 'name' => 'Corat Africa', 'email' => null],
			['id' => 115, 'synched' => 1, 'name' => 'Kamkunji', 'email' => null],
			['id' => 116, 'synched' => 1, 'name' => 'Mathare', 'email' => null],
			['id' => 117, 'synched' => 1, 'name' => 'Garden Estate', 'email' => null],
			['id' => 118, 'synched' => 1, 'name' => 'Ngara Health Centre', 'email' => null],
			['id' => 119, 'synched' => 1, 'name' => 'Port Health Kitengela', 'email' => null],
			['id' => 120, 'synched' => 1, 'name' => 'Garbatula', 'email' => null],
			['id' => 121, 'synched' => 1, 'name' => 'Nakuru Prison', 'email' => null],
			['id' => 122, 'synched' => 1, 'name' => 'Kenya Institute of Special Education', 'email' => null],
			['id' => 123, 'synched' => 1, 'name' => 'St. Teresia', 'email' => null],
			['id' => 124, 'synched' => 1, 'name' => 'St. Vincent', 'email' => null],
			['id' => 125, 'synched' => 1, 'name' => 'Kitengela', 'email' => null],
			['id' => 149, 'synched' => 1, 'name' => 'Enoomatasiani', 'email' => null],
			['id' => 150, 'synched' => 1, 'name' => 'Ole Kasasi', 'email' => null],
			// ['id' => , 'synched' => 1, 'name' => '', 'email' => null],
		]);

		// osbp/bus - busia - id 99
		// bus/tn - malaba - 100
		// bus/mty/pris - prison - 113
		// bus/fh - food handlers clmt
		// bus/ts - alupe



		DB::statement("DROP TABLE IF EXISTS `covid_test_types`;");
		DB::statement("
			CREATE TABLE `covid_test_types` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('covid_test_types')->insert([
			['id' => 1, 'name' => 'Initial Test'],
			['id' => 2, 'name' => '1st Follow Up'],
			['id' => 3, 'name' => '2nd Follow Up'],
			['id' => 4, 'name' => '3rd Follow Up'],
			['id' => 5, 'name' => '4th Follow Up'],
			['id' => 6, 'name' => '5th Follow Up'],
			['id' => 7, 'name' => 'Not Specified'],
			['id' => 8, 'name' => 'Repeat'],
		]);

		DB::statement("DROP TABLE IF EXISTS `identifier_types`;");
		DB::statement("
			CREATE TABLE `identifier_types` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('identifier_types')->insert([
			['id' => 1, 'name' => 'National ID'],
			['id' => 2, 'name' => 'Passport No'],
			['id' => 3, 'name' => 'Foreign ID'],
			['id' => 4, 'name' => 'National ID of parent'],
			['id' => 5, 'name' => 'Passport No of parent'],
			['id' => 6, 'name' => 'Foreign ID of parent'],
			['id' => 7, 'name' => 'Other'],
		]);


		DB::statement("DROP TABLE IF EXISTS `health_statuses`;");
		DB::statement("
			CREATE TABLE `health_statuses` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('health_statuses')->insert([
			['id' => 1, 'name' => 'Stable'],
			['id' => 2, 'name' => 'Severely ill'],
			['id' => 3, 'name' => 'Dead'],
			['id' => 4, 'name' => 'Unknown'],
		]);



		DB::statement("DROP TABLE IF EXISTS `covid_test_types`;");
		DB::statement("
			CREATE TABLE `covid_test_types` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('covid_test_types')->insert([
			['id' => 1, 'name' => 'Initial Test'],
			['id' => 2, 'name' => '1st Follow Up'],
			['id' => 3, 'name' => '2nd Follow Up'],
			['id' => 4, 'name' => '3rd Follow Up'],
			['id' => 5, 'name' => '4th Follow Up'],
			['id' => 6, 'name' => '5th Follow Up'],
		]);



		DB::statement("DROP TABLE IF EXISTS `covid_symptoms`;");
		DB::statement("
			CREATE TABLE `covid_symptoms` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('covid_symptoms')->insert([
			['id' => 1, 'name' => 'Fever / Chills'],
			['id' => 2, 'name' => 'Shortness of Breath'],
			['id' => 3, 'name' => 'Muscular Pain'],

			['id' => 4, 'name' => 'General Weakness'],
			['id' => 5, 'name' => 'Diarrhoea'],
			['id' => 6, 'name' => 'Chest Pain'],

			['id' => 7, 'name' => 'Cough'],
			['id' => 8, 'name' => 'Nausea / vomiting'],
			['id' => 9, 'name' => 'Abdominal Pain'],

			['id' => 10, 'name' => 'Sore Throat'],
			['id' => 11, 'name' => 'Headache'],
			['id' => 12, 'name' => 'Joint Pain'],

			['id' => 13, 'name' => 'Runny Nose'],
			['id' => 14, 'name' => 'Irritability / Confusion'],
			// ['id' => , 'name' => ''],
		]);



		DB::statement("DROP TABLE IF EXISTS `observed_signs`;");
		DB::statement("
			CREATE TABLE `observed_signs` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('observed_signs')->insert([
			['id' => 1, 'name' => 'Pharyngeal exudate'],
			['id' => 2, 'name' => 'Coma'],
			['id' => 3, 'name' => 'Abnormal lung X-Ray findings'],
			['id' => 4, 'name' => 'Conjunctival injection'],
			['id' => 5, 'name' => 'Dyspnea / tachypnea'],
			['id' => 6, 'name' => 'Seizure'],
			['id' => 7, 'name' => 'Abnormal lung auscultation'],
		]);



		DB::statement("DROP TABLE IF EXISTS `underlying_conditions`;");
		DB::statement("
			CREATE TABLE `underlying_conditions` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		DB::table('underlying_conditions')->insert([
			['id' => 1, 'name' => 'Pregnancy (trimester 1)'],
			['id' => 2, 'name' => 'Pregnancy (trimester 2)'],
			['id' => 3, 'name' => 'Pregnancy (trimester 3)'],
			['id' => 4, 'name' => 'Post-partum (&lt; 6 weeks)'],
			['id' => 5, 'name' => 'Cardiovascular disease, including hypertension'],
			['id' => 6, 'name' => 'Immunodeficiency, including HIV'],
			['id' => 7, 'name' => 'Diabetes'],
			['id' => 8, 'name' => 'Renal disease'],
			['id' => 9, 'name' => 'Liver disease'],
			['id' => 10, 'name' => 'Chronic lung disease'],
			['id' => 11, 'name' => 'Chronic neurological or neuromuscular disease'],
			['id' => 12, 'name' => 'Malignancy'],
			['id' => 13, 'name' => 'Smoking'],
		]);



		DB::statement("DROP TABLE IF EXISTS `covid_isolations`;");
		DB::statement("
			CREATE TABLE `covid_isolations` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
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


		DB::statement("DROP TABLE IF EXISTS `cities`;");
		DB::statement("
			CREATE TABLE `cities` (
				`id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
				`name` varchar(50) DEFAULT NULL,
				`subcountry` varchar(150) DEFAULT NULL,
				`country` varchar(50) DEFAULT NULL,
				`subcounty_id` smallint(5) unsigned DEFAULT NULL,
				PRIMARY KEY (`id`),
				KEY `name` (`name`),
				KEY `country` (`country`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
		");

		$a = file_get_contents(public_path('world-cities_json.json'));
		$b = json_decode($a);

		foreach ($b as $key => $row) {
			DB::table('cities')->insert([
				['id' => $key+1, 'name' => $row->name, 'subcountry' => $row->subcountry, 'country' => $row->country]
			]);
		}
    }
}
