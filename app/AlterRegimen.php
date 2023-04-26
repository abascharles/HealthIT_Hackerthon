<?php

namespace App;

use DB;
use App\Viralsample;
use App\ViralsampleView;

class AlterRegimen
{

	public static function mysql8_update()
	{
		DB::statement('ALTER TABLE viraljustifications CHANGE `rank` `rank_id` TINYINT;');
		DB::statement('ALTER TABLE prophylaxis CHANGE `rank` `rank_id` TINYINT;');
		if(env('APP_LAB') != 1){
			DB::statement("DROP TABLE IF EXISTS dr_warnings;");
			DB::statement("DROP TABLE IF EXISTS dr_worksheet_warnings;");
		}
		Lookup::cacher();

		DB::statement("INSERT INTO `viralregimen` (`id`, `name`, `code`, `age`, `line`, `regimen1`, `regimen2`, `regimen3`, `regimen4`, `regimen5`, `regimen1_class_id`, `regimen2_class_id`, `regimen3_class_id`, `regimen4_class_id`, `regimen5_class_id`) VALUES
			(47,	'ABC+3TC+DTG',	'CF2G',	2,	1,	'ABC',	'3TC',	'DTG',	NULL,	NULL,	11,	10,	2,	NULL,	NULL),
			(48,	'TDF+3TC+DTG',	'CF4E',	2,	1,	'TDF',	'3TC',	'DTG',	NULL,	NULL,	16,	10,	2,	NULL,	NULL);
		");
	}

	public static function work()
	{
		self::alter_sampletype();
		self::alter_justification();
		self::create_new_viralprophylaxis();
		self::alter_columns();
		if(env('NATIONAL_SYSTEM')) self::recreate_views_national();
		else{
			self::recreate_views();
			self::alter_facilitys();
		}
		self::alter_regimen();
	}

	public static function alter_sampletype()
	{
		DB::table('viralsampletype')->where('id', 4)->delete();	
		DB::table('viralsampletype')->where('id', 3)->update(['name' => 'DBS', 'sampletype' => 3]);	
		DB::table('viralsampletype')->where('id', 2)->update(['name' => 'Whole Blood', 'sampletype' => 2]);	

		Viralsample::where(['sampletype' => 4])->update(['sampletype' => 3]);
	}

	public static function alter_justification()
	{
		DB::table('viraljustifications')->insert(['id' => 11, 'rank' => 6, 'name' => 'Confirmation of Persistent Low Level Viremia (PLLV)']);
		DB::table('viraljustifications')->where('id', 7)->update(['rank' => 8]);		
	}

	public static function alter_facilitys()
	{		
		DB::statement('ALTER TABLE facilitys ADD COLUMN `clinician_phone` VARCHAR(15) DEFAULT NULL after `telephone3`;');
		DB::statement('ALTER TABLE facilitys ADD COLUMN `clinician_name` VARCHAR(25) DEFAULT NULL after `clinician_phone`;');
		DB::statement('ALTER TABLE facilitys ADD COLUMN `hubcontacttelephone` VARCHAR(25) DEFAULT NULL after `PostalAddress`;');

		DB::statement("
			CREATE OR REPLACE
			VIEW view_facilitys AS
			SELECT  

			fac.id, fac.facilitycode, fac.name as name, dis.name as subcounty, dis.id as subcounty_id, countys.name as county, countys.id as county_id, dis.province as province_id,
			labs.name as lab, partners.name as partner, partners.id as partner_id, fac.poc,  fac.smsprinter, fac.clinician_phone, fac.clinician_name,
			fac.hubcontacttelephone, fc.physicaladdress, fc.PostalAddress, fc.telephone, fc.telephone2, fc.fax, 
			fc.email, fc.contactperson, fc.ContactEmail, fc.contacttelephone, fc.contacttelephone2, 
			fc.sms_printer_phoneno, fc.G4Sbranchname, fc.G4Slocation, fc.G4Sphone1, fc.G4Sphone2, fc.G4Sphone3, fc.G4Sfax

			FROM facilitys fac
			LEFT JOIN facility_contacts fc ON fac.id=fc.facility_id
			LEFT JOIN districts dis ON fac.district=dis.id
			LEFT JOIN countys ON dis.county=countys.id
			LEFT JOIN partners ON fac.partner=partners.id
			LEFT JOIN labs ON fac.lab=labs.id
			WHERE fac.Flag=1;
		");
	}

	public static function create_new_viralprophylaxis()
	{
		DB::statement("DROP TABLE IF EXISTS `viralregimen` ;");
		DB::statement("
			CREATE TABLE IF NOT EXISTS `viralregimen` (
				`id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
				`name` VARCHAR(30) NOT NULL,
				`code` VARCHAR(7) NOT NULL,
				`age` tinyint(3) unsigned NOT NULL,
				`line` tinyint(3) unsigned NOT NULL,

				`regimen1` VARCHAR(7) DEFAULT NULL,
				`regimen2` VARCHAR(7) DEFAULT NULL,
				`regimen3` VARCHAR(7) DEFAULT NULL,
				`regimen4` VARCHAR(7) DEFAULT NULL,
				`regimen5` VARCHAR(7) DEFAULT NULL,

				`regimen1_class_id` tinyint(3) unsigned DEFAULT NULL,
				`regimen2_class_id` tinyint(3) unsigned DEFAULT NULL,
				`regimen3_class_id` tinyint(3) unsigned DEFAULT NULL,
				`regimen4_class_id` tinyint(3) unsigned DEFAULT NULL,
				`regimen5_class_id` tinyint(3) unsigned DEFAULT NULL,

				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");

		/*
			DB::statement("INSERT INTO `viralregimen` (`name`, `code`, `age`, `line`) VALUES
				# Adult First Line
				('TDF+3TC+DTG', 'AF2E', 1, 1),
				('TDF+3TC+EFV', 'AF2B', 1, 1),
				('AZT+3TC+DTG', 'AF1D', 1, 1),
				('AZT+3TC+EFV', 'AF1B', 1, 1),
				('ABC+3TC+EFV', 'AF4B', 1, 1),
				('ABC+3TC+DTG', 'AF4C', 1, 1),
				('TDF+3TC+ATV/r', 'AF2D', 1, 1),
				('TDF+3TC+LPV/r', 'AF2F', 1, 1),
				('AZT+3TC+LPV/r', 'AF1E', 1, 1),
				('AZT+3TC+ATV/r', 'AF1F', 1, 1),
				('TDF+3TC+NVP', 'AF2A', 1, 1),
				('AZT+3TC+NVP', 'AF1A', 1, 1),
				('ABC+3TC+NVP', 'AF4A', 1, 1),
				('Other', 'AF5X', 1, 1),

				#Adult Second Line
				('AZT+3TC+LPV/r', 'AS1A', 1, 2),
				('AZT+3TC+ATV/r', 'AS1B', 1, 2),
				('AZT+3TC+DTG', 'AS1C', 1, 2),
				('TDF+3TC+LPV/r', 'AS2A', 1, 2),
				('TDF+3TC+DTG', 'AS2B', 1, 2),
				('TDF+3TC+ATV/r', 'AS2C', 1, 2),
				('ABC+3TC+LPV/r', 'AS5A', 1, 2),
				('ABC+3TC+ATV/r', 'AS5B', 1, 2),
				('ABC+3TC+DTG', 'AS5C', 1, 2),
				('Other', 'AS6X', 1, 2),


				#Adult Third Line
				('TDF+3TC+DTG+DRV/r', 'AT2D', 1, 3),
				('TDF+3TC+RAL+DRV/r', 'AT2E', 1, 3),
				('TDF+3TC+DTG+ETV+DRV/r', 'AT2F', 1, 3),
				('Other', 'AT2X', 1, 3),


				# Paediatric First Line
				('ABC+3TC+EFV', 'CF2B', 2, 1),
				('ABC+3TC+LPV/r', 'CF2D', 2, 1),
				('ABC+3TC+NVP', 'CF2A', 2, 1),
				('ABC+3TC+RAL', 'CF2F', 2, 1),
				('AZT+3TC+NVP', 'CF1A', 2, 1),
				('AZT+3TC+EFV', 'CF1B', 2, 1),
				('AZT+3TC+LPV/r', 'CF1C', 2, 1),
				('Other', 'CF5X', 2, 1),


				# Paediatric Second Line
				('AZT+3TC+LPV/r', 'CS1A', 2, 2),
				('ABC+3TC+LPV/r', 'CS2A', 2, 2),
				('AZT+3TC+DRV/r+RAL', 'CS1C', 2, 2),
				('ABC+3TC+DRV/r+RAL', 'CS1C', 2, 2),
				('Other', 'CS4X', 2, 2),


				# Paediatric Third Line
				('AZT+3TC+DRV/r+RAL', 'CT1H', 2, 3),
				('ABC+3TC+DRV/r+RAL', 'CT2D', 2, 3),
				('Other', 'CT3X', 2, 3);
			");

			$regimens = DB::table('viralregimen')->where('name', '!=', 'Other')->get();

			foreach ($regimens as $reg) {
				$drugs = explode('+', $reg->name);
				$data = [];
				foreach ($drugs as $key => $drug) {
					$no = $key+1;
					$data['regimen' . $no] = $drug;
					$data['regimen' . $no . '_class_id'] = DB::table('regimen_classes')->where('short_name', $drug)->first()->id ?? null;
				}
				DB::table('viralregimen')->where('id', $reg->id)->update($data);
			}
		*/

		DB::statement("INSERT INTO `viralregimen` (`id`, `name`, `code`, `age`, `line`, `regimen1`, `regimen2`, `regimen3`, `regimen4`, `regimen5`, `regimen1_class_id`, `regimen2_class_id`, `regimen3_class_id`, `regimen4_class_id`, `regimen5_class_id`) VALUES
			(1,	'TDF+3TC+DTG',	'AF2E',	1,	1,	'TDF',	'3TC',	'DTG',	NULL,	NULL,	16,	10,	2,	NULL,	NULL),
			(2,	'TDF+3TC+EFV',	'AF2B',	1,	1,	'TDF',	'3TC',	'EFV',	NULL,	NULL,	16,	10,	6,	NULL,	NULL),
			(3,	'AZT+3TC+DTG',	'AF1D',	1,	1,	'AZT',	'3TC',	'DTG',	NULL,	NULL,	12,	10,	2,	NULL,	NULL),
			(4,	'AZT+3TC+EFV',	'AF1B',	1,	1,	'AZT',	'3TC',	'EFV',	NULL,	NULL,	12,	10,	6,	NULL,	NULL),
			(5,	'ABC+3TC+EFV',	'AF4B',	1,	1,	'ABC',	'3TC',	'EFV',	NULL,	NULL,	11,	10,	6,	NULL,	NULL),
			(6,	'ABC+3TC+DTG',	'AF4C',	1,	1,	'ABC',	'3TC',	'DTG',	NULL,	NULL,	11,	10,	2,	NULL,	NULL),
			(7,	'TDF+3TC+ATV/r',	'AF2D',	1,	1,	'TDF',	'3TC',	'ATV/r',	NULL,	NULL,	16,	10,	17,	NULL,	NULL),
			(8,	'TDF+3TC+LPV/r',	'AF2F',	1,	1,	'TDF',	'3TC',	'LPV/r',	NULL,	NULL,	16,	10,	21,	NULL,	NULL),
			(9,	'AZT+3TC+LPV/r',	'AF1E',	1,	1,	'AZT',	'3TC',	'LPV/r',	NULL,	NULL,	12,	10,	21,	NULL,	NULL),
			(10,	'AZT+3TC+ATV/r',	'AF1F',	1,	1,	'AZT',	'3TC',	'ATV/r',	NULL,	NULL,	12,	10,	17,	NULL,	NULL),
			(11,	'TDF+3TC+NVP',	'AF2A',	1,	1,	'TDF',	'3TC',	'NVP',	NULL,	NULL,	16,	10,	8,	NULL,	NULL),
			(12,	'AZT+3TC+NVP',	'AF1A',	1,	1,	'AZT',	'3TC',	'NVP',	NULL,	NULL,	12,	10,	8,	NULL,	NULL),
			(13,	'ABC+3TC+NVP',	'AF4A',	1,	1,	'ABC',	'3TC',	'NVP',	NULL,	NULL,	11,	10,	8,	NULL,	NULL),
			(14,	'Other',	'AF5X',	1,	1,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
			(15,	'AZT+3TC+LPV/r',	'AS1A',	1,	2,	'AZT',	'3TC',	'LPV/r',	NULL,	NULL,	12,	10,	21,	NULL,	NULL),
			(16,	'AZT+3TC+ATV/r',	'AS1B',	1,	2,	'AZT',	'3TC',	'ATV/r',	NULL,	NULL,	12,	10,	17,	NULL,	NULL),
			(17,	'AZT+3TC+DTG',	'AS1C',	1,	2,	'AZT',	'3TC',	'DTG',	NULL,	NULL,	12,	10,	2,	NULL,	NULL),
			(18,	'TDF+3TC+LPV/r',	'AS2A',	1,	2,	'TDF',	'3TC',	'LPV/r',	NULL,	NULL,	16,	10,	21,	NULL,	NULL),
			(19,	'TDF+3TC+DTG',	'AS2B',	1,	2,	'TDF',	'3TC',	'DTG',	NULL,	NULL,	16,	10,	2,	NULL,	NULL),
			(20,	'TDF+3TC+ATV/r',	'AS2C',	1,	2,	'TDF',	'3TC',	'ATV/r',	NULL,	NULL,	16,	10,	17,	NULL,	NULL),
			(21,	'ABC+3TC+LPV/r',	'AS5A',	1,	2,	'ABC',	'3TC',	'LPV/r',	NULL,	NULL,	11,	10,	21,	NULL,	NULL),
			(22,	'ABC+3TC+ATV/r',	'AS5B',	1,	2,	'ABC',	'3TC',	'ATV/r',	NULL,	NULL,	11,	10,	17,	NULL,	NULL),
			(23,	'ABC+3TC+DTG',	'AS5C',	1,	2,	'ABC',	'3TC',	'DTG',	NULL,	NULL,	11,	10,	2,	NULL,	NULL),
			(24,	'Other',	'AS6X',	1,	2,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
			(25,	'TDF+3TC+DTG+DRV/r',	'AT2D',	1,	3,	'TDF',	'3TC',	'DTG',	'DRV/r',	NULL,	16,	10,	2,	18,	NULL),
			(26,	'TDF+3TC+RAL+DRV/r',	'AT2E',	1,	3,	'TDF',	'3TC',	'RAL',	'DRV/r',	NULL,	16,	10,	4,	18,	NULL),
			(27,	'TDF+3TC+DTG+ETV+DRV/r',	'AT2F',	1,	3,	'TDF',	'3TC',	'DTG',	'ETV',	'DRV/r',	16,	10,	2,	NULL,	18),
			(28,	'Other',	'AT2X',	1,	3,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
			(29,	'ABC+3TC+EFV',	'CF2B',	2,	1,	'ABC',	'3TC',	'EFV',	NULL,	NULL,	11,	10,	6,	NULL,	NULL),
			(30,	'ABC+3TC+LPV/r',	'CF2D',	2,	1,	'ABC',	'3TC',	'LPV/r',	NULL,	NULL,	11,	10,	21,	NULL,	NULL),
			(31,	'ABC+3TC+NVP',	'CF2A',	2,	1,	'ABC',	'3TC',	'NVP',	NULL,	NULL,	11,	10,	8,	NULL,	NULL),
			(32,	'ABC+3TC+RAL',	'CF2F',	2,	1,	'ABC',	'3TC',	'RAL',	NULL,	NULL,	11,	10,	4,	NULL,	NULL),
			(33,	'AZT+3TC+NVP',	'CF1A',	2,	1,	'AZT',	'3TC',	'NVP',	NULL,	NULL,	12,	10,	8,	NULL,	NULL),
			(34,	'AZT+3TC+EFV',	'CF1B',	2,	1,	'AZT',	'3TC',	'EFV',	NULL,	NULL,	12,	10,	6,	NULL,	NULL),
			(35,	'AZT+3TC+LPV/r',	'CF1C',	2,	1,	'AZT',	'3TC',	'LPV/r',	NULL,	NULL,	12,	10,	21,	NULL,	NULL),
			(36,	'Other',	'CF5X',	2,	1,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
			(37,	'AZT+3TC+LPV/r',	'CS1A',	2,	2,	'AZT',	'3TC',	'LPV/r',	NULL,	NULL,	12,	10,	21,	NULL,	NULL),
			(38,	'ABC+3TC+LPV/r',	'CS2A',	2,	2,	'ABC',	'3TC',	'LPV/r',	NULL,	NULL,	11,	10,	21,	NULL,	NULL),
			(39,	'AZT+3TC+DRV/r+RAL',	'CS1C',	2,	2,	'AZT',	'3TC',	'DRV/r',	'RAL',	NULL,	12,	10,	18,	4,	NULL),
			(40,	'ABC+3TC+DRV/r+RAL',	'CS1C',	2,	2,	'ABC',	'3TC',	'DRV/r',	'RAL',	NULL,	11,	10,	18,	4,	NULL),
			(41,	'Other',	'CS4X',	2,	2,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
			(42,	'AZT+3TC+DRV/r+RAL',	'CT1H',	2,	3,	'AZT',	'3TC',	'DRV/r',	'RAL',	NULL,	12,	10,	18,	4,	NULL),
			(43,	'ABC+3TC+DRV/r+RAL',	'CT2D',	2,	3,	'ABC',	'3TC',	'DRV/r',	'RAL',	NULL,	11,	10,	18,	4,	NULL),
			(44,	'Other',	'CT3X',	2,	3,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);
		");

		DB::statement("INSERT INTO `viralregimen` (`id`, `name`, `code`, `age`, `line`) VALUES (45,	'None',	'',	0, 0), (46,	'No Data',	'',	0, 0); ");

		DB::statement("INSERT INTO `viralregimen` (`id`, `name`, `code`, `age`, `line`, `regimen1`, `regimen2`, `regimen3`, `regimen4`, `regimen5`, `regimen1_class_id`, `regimen2_class_id`, `regimen3_class_id`, `regimen4_class_id`, `regimen5_class_id`) VALUES
			(47,	'ABC+3TC+DTG',	'CF2G',	2,	1,	'ABC',	'3TC',	'DTG',	NULL,	NULL,	11,	10,	2,	NULL,	NULL),
			(48,	'TDF+3TC+DTG',	'CF4E',	2,	1,	'TDF',	'3TC',	'DTG',	NULL,	NULL,	16,	10,	2,	NULL,	NULL);
		");
	}

	public static function alter_columns()
	{
		DB::statement('ALTER TABLE viralsamples CHANGE `prophylaxis` `regimen` TINYINT UNSIGNED DEFAULT 0;');
		DB::statement('ALTER TABLE viralsamples ADD COLUMN `prophylaxis` TINYINT UNSIGNED DEFAULT 0 after `sampletype`;');

		DB::statement('ALTER TABLE viralsamples CHANGE `dateseparated` `dateseparated` DATETIME DEFAULT NULL;');
	}

	public static function recreate_views()
	{
        DB::statement("
	        CREATE OR REPLACE VIEW viralsamples_view AS
	        (
	          SELECT s.*, b.national_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.tat5, b.time_received, b.site_entry, b.batch_complete, b.lab_id, b.user_id, b.received_by, b.entered_by, b.datedispatchedfromfacility, f.facilitycode, f.name as facilityname, b.facility_id, b.input_complete,
	          p.national_patient_id, p.patient, p.initiation_date, p.sex, p.dob, p.patient_name, p.patient_phone_no, p.preferred_language

	          FROM viralsamples s
	            JOIN viralbatches b ON b.id=s.batch_id
	            JOIN viralpatients p ON p.id=s.patient_id
	            LEFT JOIN facilitys f ON f.id=b.facility_id

	        );
        ");

        DB::statement("
	        CREATE OR REPLACE VIEW viralsample_complete_view AS
	        (
	          SELECT s.*, b.national_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.site_entry, b.lab_id, b.facility_id, b.user_id, b.batch_complete,
	          p.national_patient_id, p.patient, p.sex, p.dob, g.gender_description, rs.name as receivedstatus_name, vp.name as prophylaxis_name, vj.name as justification_name, vs.name as sampletype_name

	          FROM viralsamples s
	            JOIN viralbatches b ON b.id=s.batch_id
	            JOIN viralpatients p ON p.id=s.patient_id
	            LEFT JOIN gender g on g.id=p.sex
	            LEFT JOIN receivedstatus rs on rs.id=s.receivedstatus
	            LEFT JOIN viralprophylaxis vp on vp.id=s.prophylaxis
	            LEFT JOIN viraljustifications vj on vj.id=s.justification
	            LEFT JOIN viralsampletype vs on vs.id=s.sampletype

	        );
        ");
	}

	public static function recreate_views_national()
	{		
        DB::statement("
	        CREATE OR REPLACE VIEW viralsamples_view AS
	        (
	          SELECT s.*, b.original_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.site_entry, b.lab_id, b.facility_id,
	          p.original_patient_id, p.patient_status, p.patient, p.sex, p.dob, p.initiation_date

	          FROM viralsamples s
	            JOIN viralbatches b ON b.id=s.batch_id
	            JOIN viralpatients p ON p.id=s.patient_id

	        );
        ");

        DB::statement("
	        CREATE OR REPLACE VIEW viralsample_complete_view AS
	        (
	          SELECT s.*, b.original_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.site_entry, b.lab_id, b.facility_id, b.batch_complete,
	          p.original_patient_id, p.patient_status, p.patient, p.sex, p.dob, p.initiation_date, g.gender_description, rs.name as receivedstatus_name, vp.name as prophylaxis_name, vj.name as justification_name, vs.name as sampletype_name, vpt.name as pmtct_name, vr.name as rejected_name

	          FROM viralsamples s
	            JOIN viralbatches b ON b.id=s.batch_id
	            JOIN viralpatients p ON p.id=s.patient_id
	            LEFT JOIN gender g on g.id=p.sex
	            LEFT JOIN receivedstatus rs on rs.id=s.receivedstatus
	            LEFT JOIN viralprophylaxis vp on vp.id=s.prophylaxis
	            LEFT JOIN viraljustifications vj on vj.id=s.justification
	            LEFT JOIN viralsampletype vs on vs.id=s.sampletype
	            LEFT JOIN viralpmtcttype vpt on vpt.id=s.pmtct
	            LEFT JOIN viralrejectedreasons vr on vr.id=s.rejectedreason

	        );
        ");

        DB::statement("
			CREATE OR REPLACE VIEW viralsample_alert_view AS
			(
				SELECT s.*, b.original_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.site_entry, b.batch_complete,
				p.original_patient_id, p.patient_status, p.patient, p.sex, p.dob,
				b.lab_id, b.facility_id, f.name as facility, f.facilitycode,
				f.partner as partner_id, pa.name as partner, 
				f.district as subcounty_id, d.name as subcounty,
				d.county as county_id, c.name as county

				FROM viralsamples s
				JOIN viralbatches b ON b.id=s.batch_id
				JOIN viralpatients p ON p.id=s.patient_id

				LEFT JOIN facilitys f ON b.facility_id=f.id
				LEFT JOIN districts d ON d.id=f.district 
				LEFT JOIN countys c ON c.id=d.county 
				LEFT JOIN partners pa ON pa.id=f.partner 
			);
        ");

        DB::statement("
			CREATE OR REPLACE VIEW viralsample_synch_view AS
			(
				SELECT s.*, b.original_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.site_entry, b.lab_id, b.lab_id as lab, b.facility_id, b.facility_id as facility, f.partner, f.district as subcounty, d.county, b.batch_complete,
				p.original_patient_id, p.patient_status, p.patient, p.sex, p.dob

				FROM viralsamples s
				JOIN viralbatches b ON b.id=s.batch_id
				JOIN viralpatients p ON p.id=s.patient_id

				LEFT JOIN facilitys f ON b.facility_id=f.id
				LEFT JOIN districts d ON d.id=f.district 
			);
        ");
	}


	public static function alter_regimen()
	{		
		$tld = DB::table('viralregimen')->where('code', 'AF2E')->first()->id;
		Viralsample::where(['regimen' => 17, 'prophylaxis' => 0])->update(['prophylaxis' => $tld]);


		$nvp = DB::table('viralregimen')->where('code', 'AF2A')->first()->id;
		Viralsample::where(['regimen' => 3, 'prophylaxis' => 0])->update(['prophylaxis' => $nvp]);


		$lpv1 = DB::table('viralregimen')->where('code', 'AF2F')->first()->id;
		$lpv2 = DB::table('viralregimen')->where('code', 'AS2A')->first()->id;
		Viralsample::where(['regimen' => 7, 'prophylaxis' => 0])->whereBetween('age', [3, 14])->update(['prophylaxis' => $lpv1]);
		Viralsample::where(['regimen' => 7, 'prophylaxis' => 0])->update(['prophylaxis' => $lpv2]);


		$efv = DB::table('viralregimen')->where('code', 'AF2B')->first()->id;
		Viralsample::where(['regimen' => 4, 'prophylaxis' => 0])->update(['prophylaxis' => $efv]);



		$atv1 = DB::table('viralregimen')->where('code', 'AF2D')->first()->id;
		$atv2 = DB::table('viralregimen')->where('code', 'AS2C')->first()->id;
		self::women_regimen(9, $atv1, $atv2);
		Viralsample::where(['regimen' => 9, 'prophylaxis' => 0])->update(['prophylaxis' => $atv1]);



		$other = DB::table('viralregimen')->where('code', 'AS6X')->first()->id;
		Viralsample::where(['regimen' => 14, 'regimenline' => 2, 'prophylaxis' => 0])->where('age', '>', 14)->update(['prophylaxis' => $other]);

		$other = DB::table('viralregimen')->where('code', 'AT2X')->first()->id;
		Viralsample::where(['regimen' => 14, 'regimenline' => 3, 'prophylaxis' => 0])->where('age', '>', 14)->update(['prophylaxis' => $other]);

		$other = DB::table('viralregimen')->where('code', 'AF5X')->first()->id;
		Viralsample::where(['regimen' => 14, 'regimenline' => 1, 'prophylaxis' => 0])->where('age', '>', 14)->update(['prophylaxis' => $other]);
		Viralsample::where(['regimen' => 14, 'prophylaxis' => 0])->where('age', '>', 14)->update(['prophylaxis' => $other]);



		$other = DB::table('viralregimen')->where('code', 'CS4X')->first()->id;
		Viralsample::where(['regimen' => 14, 'regimenline' => 2, 'prophylaxis' => 0])->whereBetween('age', [0, 14])->update(['prophylaxis' => $other]);

		$other = DB::table('viralregimen')->where('code', 'CT3X')->first()->id;
		Viralsample::where(['regimen' => 14, 'regimenline' => 3, 'prophylaxis' => 0])->whereBetween('age', [0, 14])->update(['prophylaxis' => $other]);

		$other = DB::table('viralregimen')->where('code', 'CF5X')->first()->id;
		Viralsample::where(['regimen' => 14, 'regimenline' => 1, 'prophylaxis' => 0])->whereBetween('age', [0, 14])->update(['prophylaxis' => $other]);
		Viralsample::where(['regimen' => 14, 'prophylaxis' => 0])->whereBetween('age', [0, 14])->update(['prophylaxis' => $other]);




		$none = DB::table('viralregimen')->where('name', 'none')->first()->id;
		Viralsample::where(['regimen' => 15, 'prophylaxis' => 0])->update(['prophylaxis' => $none]);


		$no_data = DB::table('viralregimen')->where('name', 'no data')->first()->id;
		Viralsample::where(['regimen' => 15, 'prophylaxis' => 0])->update(['prophylaxis' => $no_data]);



		$nvp1 = DB::table('viralregimen')->where('code', 'CF1A')->first()->id;
		$nvp2 = DB::table('viralregimen')->where('code', 'AF1A')->first()->id;
		Viralsample::where(['regimen' => 1, 'prophylaxis' => 0])->whereBetween('age', [0, 3])->update(['prophylaxis' => $nvp1]);
		Viralsample::where(['regimen' => 1, 'prophylaxis' => 0])->where('age', '>', 3)->update(['prophylaxis' => $nvp2]);


		$lpv1 = DB::table('viralregimen')->where('code', 'CF1C')->first()->id;
		$lpv2 = DB::table('viralregimen')->where('code', 'AF1E')->first()->id;
		$lpv3 = DB::table('viralregimen')->where('code', 'AS1A')->first()->id;
		Viralsample::where(['regimen' => 5, 'prophylaxis' => 0])->whereBetween('age', [0, 3])->update(['prophylaxis' => $lpv1]);
		Viralsample::where(['regimen' => 5, 'prophylaxis' => 0])->whereBetween('age', [4, 14])->update(['prophylaxis' => $lpv2]);
		Viralsample::where(['regimen' => 5, 'prophylaxis' => 0])->where('age', '>', 14)->update(['prophylaxis' => $lpv3]);


		$dtg = DB::table('viralregimen')->where('code', 'AF1D')->first()->id;
		Viralsample::where(['regimen' => 20, 'prophylaxis' => 0])->update(['prophylaxis' => $dtg]);



		$atv1 = DB::table('viralregimen')->where('code', 'AF1F')->first()->id;
		$atv2 = DB::table('viralregimen')->where('code', 'AS1B')->first()->id;
		self::women_regimen(8, $atv1, $atv2);
		Viralsample::where(['regimen' => 8, 'prophylaxis' => 0])->update(['prophylaxis' => $atv1]);


		// ABC



		$nvp1 = DB::table('viralregimen')->where('code', 'CF2A')->first()->id;
		$nvp2 = DB::table('viralregimen')->where('code', 'AF4A')->first()->id;
		Viralsample::where(['regimen' => 11, 'prophylaxis' => 0])->whereBetween('age', [0, 3])->update(['prophylaxis' => $nvp1]);
		Viralsample::where(['regimen' => 11, 'prophylaxis' => 0])->where('age', '>', 3)->update(['prophylaxis' => $nvp2]);


		$lpv1 = DB::table('viralregimen')->where('code', 'CF2D')->first()->id;
		$lpv2 = DB::table('viralregimen')->where('code', 'CS2A')->first()->id;
		$lpv3 = DB::table('viralregimen')->where('code', 'AS5A')->first()->id;
		Viralsample::where(['regimen' => 13, 'prophylaxis' => 0])->whereBetween('age', [0, 3])->update(['prophylaxis' => $lpv1]);
		Viralsample::where(['regimen' => 13, 'prophylaxis' => 0])->whereBetween('age', [4, 14])->update(['prophylaxis' => $lpv2]);
		Viralsample::where(['regimen' => 13, 'prophylaxis' => 0])->where('age', '>', 14)->update(['prophylaxis' => $lpv3]);



		$efv1 = DB::table('viralregimen')->where('code', 'CF2B')->first()->id;
		$efv2 = DB::table('viralregimen')->where('code', 'AF4B')->first()->id;
		Viralsample::where(['regimen' => 12, 'prophylaxis' => 0])->whereBetween('age', [0, 14])->update(['prophylaxis' => $efv1]);
		Viralsample::where(['regimen' => 12, 'prophylaxis' => 0])->where('age', '>', 14)->update(['prophylaxis' => $efv2]);


		
		$dtg = DB::table('viralregimen')->where('code', 'AF4C')->first()->id;
		Viralsample::where(['regimen' => 18, 'prophylaxis' => 0])->update(['prophylaxis' => $dtg]);


		
		$atv = DB::table('viralregimen')->where('code', 'AS5B')->first()->id;
		Viralsample::where(['regimen' => 10, 'prophylaxis' => 0])->update(['prophylaxis' => $atv]);
	}


	public static function women_regimen($old_reg, $first_line, $second_line)
	{
		$offset = 0;
		$limit = 200;
		while (true) {
			$samples = ViralsampleView::where(['sex' => 2, 'regimen' => $old_reg, 'prophylaxis' => 0])->whereBetween('age', [10, 49])->limit($limit)->offset($offset)->get();
			if($samples->isEmpty()) break;

			foreach ($samples as $sample) {
				$s = Viralsample::find($sample->id);

				$other_reg_sample = Viralsample::where(['patient_id' => $s->patient_id])->where('regimen', '!=', $old_reg)->where('id', '<', $s->id)->first();

				if($other_reg_sample) Viralsample::where(['id' => $s->id])->update(['prophylaxis' => $second_line]);
				else{
					Viralsample::where(['id' => $s->id])->update(['prophylaxis' => $first_line]);	
				}
			}
			$offset += $limit;
		}
	}

	public static function recency_testing()
	{
		DB::statement("INSERT INTO `viraljustifications` (`id`, `name`, `flag`, `rank`) VALUES
			(12, 'Recency Testing', 1, 9)
		");
		DB::statement('ALTER TABLE viralsamples ADD COLUMN `recency_number` VARCHAR(30) DEFAULT NULL after `justification`;');
	}

}
