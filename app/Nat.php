<?php

namespace App;

use Excel;
use DB;
use App\Facility;

use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;

use App\Mail\CustomMailOld;
use App\Mail\TestMail;

class Nat
{

	public static function email_csv($filename, $rows, $emails=['joelkith@gmail.com'])
	{		
		Common::csv_download($rows, $filename, true, true);

		$attachments = [storage_path("exports/" . $filename . ".csv")];


		Mail::to($emails)->send(new TestMail($attachments));
	}

	public static function get_county_current_query($year, $paed=false, $suppressed=true, $ages=true, $suppression=true)
	{
    	$sql = 'SELECT f.county_id, sex, count(*) as totals ';
		$sql .= 'FROM ';
		$sql .= '(SELECT v.id, v.facility_id, v.sex, v.rcategory ';
		$sql .= 'FROM viralsamples_view v ';
		$sql .= 'RIGHT JOIN ';
		$sql .= '(SELECT ID, patient_id, max(datetested) as maxdate ';
		$sql .= 'FROM viralsamples_view ';
		$sql .= "WHERE ( datetested between '{$year}-01-01' and '{$year}-12-31' ) ";
		$sql .= "AND patient != '' AND patient != 'null' AND patient is not null ";
		if($ages){
			if($paed) $sql .= "AND age < 15 ";
			else{
				$sql .= "AND age > 14 ";
			}
		}
		$sql .= 'AND flag=1 AND repeatt=0 AND rcategory in (1, 2, 3, 4) ';
		$sql .= 'AND justification != 10 and facility_id != 7148 ';
		$sql .= 'GROUP BY patient_id) gv ';
		$sql .= 'ON v.id=gv.id) tb ';
		$sql .= 'JOIN view_facilitys f on f.id=tb.facility_id ';
		if($suppression){
			if($suppressed) $sql .= 'WHERE rcategory IN (1,2) ';
			else{
				$sql .= 'WHERE rcategory IN (3,4) ';
			}
		}
		$sql .= 'GROUP BY f.county_id, sex ';
		$sql .= 'ORDER BY f.county_id, sex ';

		// return $sql;
		return collect(DB::select($sql));
	}

	public static function get_county_current()
	{
		$data = [];

		$counties = DB::table('countys')->get();

		for ($year=2019; $year < 2020; $year++) { 
			$paeds_sup = self::get_county_current_query($year, true);
			$paeds = self::get_county_current_query($year, true, false);
			$adults_sup = self::get_county_current_query($year, false);
			$adults = self::get_county_current_query($year, false, false);
			$all = self::get_county_current_query($year, false, false, false, false);
			foreach ($counties as $county) {
				$row = ['Year' => $year, 'County' => $county->name];

				$row['Paedeatric Suppressed Male'] = $paeds_sup->where('county_id', $county->id)->where('sex', 1)->first()->totals ?? 0;
				$row['Paedeatric Suppressed Female'] = $paeds_sup->where('county_id', $county->id)->where('sex', 2)->first()->totals ?? 0;

				$row['Paedeatric Non-Suppressed Male'] = $paeds->where('county_id', $county->id)->where('sex', 1)->first()->totals ?? 0;
				$row['Paedeatric Non-Suppressed Female'] = $paeds->where('county_id', $county->id)->where('sex', 2)->first()->totals ?? 0;

				$row['Adult Suppressed Male'] = $adults_sup->where('county_id', $county->id)->where('sex', 1)->first()->totals ?? 0;
				$row['Adult Suppressed Female'] = $adults_sup->where('county_id', $county->id)->where('sex', 2)->first()->totals ?? 0;

				$row['Adult Non-Suppressed Male'] = $adults->where('county_id', $county->id)->where('sex', 1)->first()->totals ?? 0;
				$row['Adult Non-Suppressed Female'] = $adults->where('county_id', $county->id)->where('sex', 2)->first()->totals ?? 0;

				$row['All Male'] = $all->where('county_id', $county->id)->where('sex', 1)->first()->totals ?? 0;
				$row['All Female'] = $all->where('county_id', $county->id)->where('sex', 2)->first()->totals ?? 0;

				$data[] = $row;
			}
		}
		self::email_csv('county_suppression', $data);
	}

	public static function get_current_gender_query($facility_id, $date_params = ['2019-01-01', '2019-12-31'])
	{
    	$sql = 'SELECT sex, rcategory, count(*) as totals ';
		$sql .= 'FROM ';
		$sql .= '(SELECT v.id, v.facility_id, v.sex, v.rcategory ';
		$sql .= 'FROM viralsamples_view v ';
		$sql .= 'RIGHT JOIN ';
		$sql .= '(SELECT ID, patient_id, max(datetested) as maxdate ';
		$sql .= 'FROM viralsamples_view ';
		$sql .= 'WHERE ( datetested between "' . $date_params[0] . '" and "' . $date_params[1] . '" ) ';
		$sql .= "AND patient != '' AND patient != 'null' AND patient is not null ";
		$sql .= 'AND flag=1 AND repeatt=0 AND rcategory in (1, 2, 3, 4) ';
		$sql .= 'AND justification != 10 and facility_id != 7148 ';
		$sql .= "AND facility_id={$facility_id} ";
		$sql .= 'GROUP BY patient_id) gv ';
		$sql .= 'ON v.id=gv.id) tb ';
		// $sql .= 'WHERE ';		
		// if($param == 1) $sql .= ' rcategory = 1 ';
		// if($param == 2) $sql .= ' rcategory = 2 ';
		// if($param == 4) $sql .= ' (rcategory IN (3,4)) ';
		$sql .= 'GROUP BY sex, rcategory ';
		$sql .= 'ORDER BY sex, rcategory ';

		return $sql;
	}


	public static function save_gender_results()
	{
		ini_set("memory_limit", "-1");

		$start_date = Carbon::now()->subYear();
		$days = $start_date->day;

		$start_date = $start_date->subDays($days-1)->toDateString();
		$end_date = Carbon::now()->subDays($days)->toDateString();
		$date_params = [$start_date, $end_date];

		$facilities = \App\Facility::whereRaw("id IN (SELECT DISTINCT facility_id FROM viralsamples_view where datetested between '{$start_date}' AND '{$end_date}' )")->get();

		foreach ($facilities as $key => $facility) {

			$sql = self::get_current_gender_query($facility->id, $date_params);
			$data = collect(DB::select($sql));

			$rows[] = [
				'No' => ($key+1),
				'MFL Code' => $facility->facilitycode,
				'Facility' => $facility->name,
				'Male 400 and less' => $data->where('sex', 1)->where('rcategory', 1)->first()->totals ?? null,
				'Female 400 and less' => $data->where('sex', 2)->where('rcategory', 1)->first()->totals ?? null,
				'Male Above 400 Less 1000' => $data->where('sex', 1)->where('rcategory', 2)->first()->totals ?? null,
				'Female Above 400 Less 1000' => $data->where('sex', 2)->where('rcategory', 2)->first()->totals ?? null,
				'Male Above 1000' => ($data->where('sex', 1)->where('rcategory', 3)->first()->totals ?? null) + ($data->where('sex', 1)->where('rcategory', 4)->first()->totals ?? null),
				'Female Above 1000' => ($data->where('sex', 2)->where('rcategory', 3)->first()->totals ?? null) + ($data->where('sex', 2)->where('rcategory', 4)->first()->totals ?? null),
			];

		}
		$file = "gender_totals_all_sites_between_{$start_date}_and_{$end_date}_by_most_recent_test";
		
		Excel::create($file, function($excel) use($rows){
			$excel->sheet('Sheetname', function($sheet) use($rows) {
				$sheet->fromArray($rows);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com', 'kmugambi@clintonhealthaccess.org', 'tngugi@clintonhealthaccess.org'])->send(new TestMail($data, 'Gender Totals All Facilities'));
	}


	public static function save_gender_ordering_results()
	{
		ini_set("memory_limit", "-1");

		$start_date = Carbon::now()->subYear();
		$days = $start_date->day;

		$start_date = $start_date->subDays($days-1)->toDateString();
		$end_date = Carbon::now()->subDays($days)->toDateString();
		$date_params = [$start_date, $end_date];

        config(['excel.import.heading' => true]);
		$path = public_path('art_ordering.csv');

		$facilities = Excel::load($path, function($reader){})->get();

		// $facilities = \App\Facility::whereRaw("id IN (SELECT DISTINCT facility_id FROM viralsamples_view where datetested between '{$start_date}' AND '{$end_date}' )")->get();

		foreach ($facilities as $key => $fac) {
			$facility = \App\Facility::where(['facilitycode' => $fac->mfl_code])->first();
			if(!$facility) continue;

			$sql = self::get_current_gender_query($facility->id, $date_params);
			$data = collect(DB::select($sql));

			$rows[] = [
				'No' => ($key+1),
				'MFL Code' => $facility->facilitycode,
				'Facility' => $facility->name,
				'Male 400 and less' => $data->where('sex', 1)->where('rcategory', 1)->first()->totals ?? null,
				'Female 400 and less' => $data->where('sex', 2)->where('rcategory', 1)->first()->totals ?? null,
				'Male Above 400 Less 1000' => $data->where('sex', 1)->where('rcategory', 2)->first()->totals ?? null,
				'Female Above 400 Less 1000' => $data->where('sex', 2)->where('rcategory', 2)->first()->totals ?? null,
				'Male Above 1000' => ($data->where('sex', 1)->where('rcategory', 3)->first()->totals ?? null) + ($data->where('sex', 1)->where('rcategory', 4)->first()->totals ?? null),
				'Female Above 1000' => ($data->where('sex', 2)->where('rcategory', 3)->first()->totals ?? null) + ($data->where('sex', 2)->where('rcategory', 4)->first()->totals ?? null),
			];

		}
		$file = "gender_totals_ordering_sites_between_{$start_date}_and_{$end_date}_by_most_recent_test";
		
		Excel::create($file, function($excel) use($rows){
			$excel->sheet('Sheetname', function($sheet) use($rows) {
				$sheet->fromArray($rows);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com', 'kmugambi@clintonhealthaccess.org', 'tngugi@clintonhealthaccess.org'])->send(new TestMail($data, 'Gender Totals Ordering Facilities'));
	}

	public static function get_county_ages_current_query($suppressed=true, $ages=null)
	{
		$year='2019';
    	$sql = 'SELECT f.county_id, sex, count(*) as totals ';
		$sql .= 'FROM ';
		$sql .= '(SELECT v.id, v.facility_id, v.sex, v.rcategory ';
		$sql .= 'FROM viralsamples_view v ';
		$sql .= 'RIGHT JOIN ';
		$sql .= '(SELECT ID, patient_id, max(datetested) as maxdate ';
		$sql .= 'FROM viralsamples_view ';
		$sql .= "WHERE ( datetested between '{$year}-01-01' and '{$year}-12-31' ) ";
		$sql .= "AND patient != '' AND patient != 'null' AND patient is not null ";
		if($ages){
			if($ages[0] != 0) $sql .= "AND age >= {$ages[0]} AND age < {$ages[1]} ";
			else{
				$sql .= "AND age > {$ages[0]} AND age < {$ages[1]} ";
			}
		}

		$sql .= 'AND flag=1 AND repeatt=0 AND rcategory in (1, 2, 3, 4) ';
		$sql .= 'AND justification != 10 and facility_id != 7148 ';
		$sql .= 'GROUP BY patient_id) gv ';
		$sql .= 'ON v.id=gv.id) tb ';
		$sql .= 'JOIN view_facilitys f on f.id=tb.facility_id ';
		// if($suppression){
			if($suppressed) $sql .= 'WHERE rcategory IN (1,2) ';
			else{
				$sql .= 'WHERE rcategory IN (3,4) ';
			}
		// }
		$sql .= 'GROUP BY f.county_id, sex ';
		$sql .= 'ORDER BY f.county_id, sex ';

		// return $sql;
		return collect(DB::select($sql));
	}

	public static function get_county_fine_ages()
	{
		$data = [];
		$ages = [];
		$i=0;

		while(true){
			$f = $i;
			$i += 4;
			// if($i == 4) $i++;
			if($i == 84) $i = 100;
			$s = $i;
			$ages['a_' . $f . '-' . $s] = [$f, ($s+1)];
			if($i >= 100) break;
			$i++;
		}
		$ages['a_all_ages'] = [];

		$counties = DB::table('countys')->get();

		foreach ($ages as $key => $value) {
			$sup = $key . '_suppressed';
			$nonsup = $key . '_nonsuppressed';
			$$sup = self::get_county_ages_current_query(true, $value);
			$$nonsup = self::get_county_ages_current_query(false, $value);
		}

		foreach ($counties as $county) {
			$row = ['Year' => 2019, 'County' => $county->name];

			foreach ($ages as $key => $value) {
				$sup = $key . '_suppressed';
				$nonsup = $key . '_nonsuppressed';

				$row[$sup . '_male'] = $$sup->where('county_id', $county->id)->where('sex', 1)->first()->totals ?? 0;
				$row[$sup . '_female'] = $$sup->where('county_id', $county->id)->where('sex', 2)->first()->totals ?? 0;
				
				$row[$nonsup . '_male'] = $$nonsup->where('county_id', $county->id)->where('sex', 1)->first()->totals ?? 0;
				$row[$nonsup . '_female'] = $$nonsup->where('county_id', $county->id)->where('sex', 2)->first()->totals ?? 0;
			}
			$data[] = $row;
		}
		self::email_csv('county_fine_age_suppression', $data);
	}

	public static function get_national_ages_current_query($year, $suppressed=true, $ages=null)
	{
    	$sql = 'SELECT sex, count(*) as totals ';
		$sql .= 'FROM ';
		$sql .= '(SELECT v.id, v.facility_id, v.sex, v.rcategory ';
		$sql .= 'FROM viralsamples_view v ';
		$sql .= 'RIGHT JOIN ';
		$sql .= '(SELECT ID, patient_id, max(datetested) as maxdate ';
		$sql .= 'FROM viralsamples_view ';
		$sql .= "WHERE ( datetested between '{$year}-01-01' and '{$year}-12-31' ) ";
		$sql .= "AND patient != '' AND patient != 'null' AND patient is not null ";
		if($ages){
			if($ages[0] == 0) $sql .= "AND age > {$ages[0]} AND age < {$ages[1]} ";
			else{
				$sql .= "AND age >= {$ages[0]} AND age < {$ages[1]} ";
			}			
		}

		$sql .= 'AND flag=1 AND repeatt=0 AND rcategory in (1, 2, 3, 4) ';
		$sql .= 'AND justification != 10 and facility_id != 7148 ';
		$sql .= 'GROUP BY patient_id) gv ';
		$sql .= 'ON v.id=gv.id) tb ';
		// if($suppression){
			if($suppressed) $sql .= 'WHERE rcategory IN (1,2) ';
			else{
				$sql .= 'WHERE rcategory IN (3,4) ';
			}
		// }
		$sql .= 'GROUP BY sex ';
		$sql .= 'ORDER BY sex ';

		// return $sql;
		return collect(DB::select($sql));
	}

	public static function get_national_fine_ages()
	{
		$data = [];
		$ages = [];
		$i=0;

		while(true){
			$f = $i;
			$i += 4;
			// if($i == 4) $i++;
			if($i == 54) $i = 100;
			$s = $i;
			$ages['a_' . $f . '-' . $s] = [$f, ($s+1)];
			if($i >= 100) break;
			$i++;
		}
		$ages['a_all_ages'] = [];
		// return $ages;

		for ($year=2014; $year < 2020; $year++) { 
			$row = ['Year' => $year];

			foreach ($ages as $key => $value) {
				$sup = $key . '_suppressed';
				$nonsup = $key . '_nonsuppressed';
				$suppressed = self::get_national_ages_current_query($year, true, $value);
				$nonsuppressed = self::get_national_ages_current_query($year, false, $value);

				$row[$sup . '_male'] = $suppressed->where('sex', 1)->first()->totals ?? 0;
				$row[$sup . '_female'] = $suppressed->where('sex', 2)->first()->totals ?? 0;
				
				$row[$nonsup . '_male'] = $nonsuppressed->where('sex', 1)->first()->totals ?? 0;
				$row[$nonsup . '_female'] = $nonsuppressed->where('sex', 2)->first()->totals ?? 0;
			}
			$data[] = $row;
		}
		self::email_csv('national_fine_age_suppression', $data);
	}




	public static function get_county_ovf_ages_current_query($suppressed=true, $ages=null)
	{
    	$sql = 'SELECT f.county_id, ovf, count(*) as totals ';
		$sql .= 'FROM ';
		$sql .= '(SELECT v.id, v.facility_id, v.ovf, v.rcategory ';
		$sql .= 'FROM viralsamples_view v ';
		$sql .= 'RIGHT JOIN ';
		$sql .= '(SELECT ID, patient_id, max(datetested) as maxdate ';
		$sql .= 'FROM viralsamples_view ';
		$sql .= "WHERE patient != '' AND patient != 'null' AND patient is not null ";
		$sql .= "AND ( datetested between '2020-01-01' and '2020-05-30' ) ";
		if($ages){
			if($ages[0] != 0) $sql .= "AND age >= {$ages[0]} AND age < {$ages[1]} ";
			else{
				$sql .= "AND age > {$ages[0]} AND age < {$ages[1]} ";
			}
		}

		$sql .= 'AND flag=1 AND repeatt=0 AND rcategory in (1, 2, 3, 4) ';
		$sql .= 'AND facility_id != 7148 ';
		$sql .= 'GROUP BY patient_id) gv ';
		$sql .= 'ON v.id=gv.id) tb ';
		$sql .= 'JOIN view_facilitys f on f.id=tb.facility_id ';
		if($suppressed) $sql .= 'WHERE rcategory IN (1,2) ';
		else{
			$sql .= 'WHERE rcategory IN (3,4) ';
		}
		$sql .= 'GROUP BY f.county_id, ovf  ';
		$sql .= 'ORDER BY f.county_id, ovf  ';

		// return $sql;
		return collect(DB::select($sql));
	}

	public static function get_county_ovf_fine_ages()
	{
		$data = [];
		$ages = [];
		$i=0;

		while(true){
			$f = $i;
			$i += 4;
			if($i > 25) break;
			if($i == 84) $i = 100;
			$s = $i;
			$ages['a_' . $f . '-' . $s] = [$f, ($s+1)];
			if($i >= 100) break;
			$i++;
		}
		$ages['a_0-24'] = [0, 24];

		$counties = DB::table('countys')->get();

		foreach ($ages as $key => $value) {
			$sup = $key . '_suppressed';
			$nonsup = $key . '_nonsuppressed';
			$$sup = self::get_county_ovf_ages_current_query(true, $value);
			$$nonsup = self::get_county_ovf_ages_current_query(false, $value);
		}

		foreach ($counties as $county) {
			$row = ['Year' => 2019, 'County' => $county->name];

			foreach ($ages as $key => $value) {
				$sup = $key . '_suppressed';
				$nonsup = $key . '_nonsuppressed';

				$row[$sup . '_non_ovc'] = $$sup->where('county_id', $county->id)->where('ovf', 0)->first()->totals ?? 0;
				$row[$sup . '_ovc'] = $$sup->where('county_id', $county->id)->where('ovf', 1)->first()->totals ?? 0;
				
				$row[$nonsup . '_non_ovc'] = $$nonsup->where('county_id', $county->id)->where('ovf', 0)->first()->totals ?? 0;
				$row[$nonsup . '_ovc'] = $$nonsup->where('county_id', $county->id)->where('ovf', 1)->first()->totals ?? 0;
			}
			$data[] = $row;
		}
		self::email_csv('county_ovf_fine_age_suppression', $data);
	}

	public static function get_county_gen_ages_current_query($suppressed=true, $ages=null)
	{
    	$sql = 'SELECT f.county_id, sex, count(*) as totals ';
		$sql .= 'FROM ';
		$sql .= '(SELECT v.id, v.sex, v.facility_id, v.rcategory ';
		$sql .= 'FROM viralsamples_view v ';
		$sql .= 'RIGHT JOIN ';
		$sql .= '(SELECT ID, patient_id, max(datetested) as maxdate ';
		$sql .= 'FROM viralsamples_view ';
		$sql .= "WHERE patient != '' AND patient != 'null' AND patient is not null ";
		$sql .= "AND ( datetested between '2020-01-01' and '2020-05-30' ) ";
		if($ages){
			if($ages[0] != 0) $sql .= "AND age >= {$ages[0]} AND age < {$ages[1]} ";
			else{
				$sql .= "AND age > {$ages[0]} AND age < {$ages[1]} ";
			}
		}

		$sql .= 'AND flag=1 AND repeatt=0 AND rcategory in (1, 2, 3, 4) ';
		$sql .= 'AND facility_id != 7148 ';
		$sql .= 'GROUP BY patient_id) gv ';
		$sql .= 'ON v.id=gv.id) tb ';
		$sql .= 'JOIN view_facilitys f on f.id=tb.facility_id ';
		if($suppressed) $sql .= 'WHERE rcategory IN (1,2) ';
		else{
			$sql .= 'WHERE rcategory IN (3,4) ';
		}
		$sql .= 'GROUP BY f.county_id, sex  ';
		$sql .= 'ORDER BY f.county_id, sex  ';

		// return $sql;
		return collect(DB::select($sql));
	}

	public static function get_county_generalised_ages()
	{
		$data = [];
		$ages = [];
		$i=0;

		$ages['less_15'] = [0, 14];
		$ages['above_15'] = [15, 100];

		$sexes = ['Male' => 1, 'Female' => 2];

		$counties = DB::table('countys')->get();

		foreach ($ages as $key => $value) {
			$sup = $key . '_suppressed';
			$nonsup = $key . '_nonsuppressed';
			$$sup = self::get_county_gen_ages_current_query(true, $value);
			$$nonsup = self::get_county_gen_ages_current_query(false, $value);
		}

		foreach ($counties as $county) {
			$row = ['Year' => 2020, 'County' => $county->name];

			foreach ($sexes as $sex => $sex_value) {

				foreach ($ages as $age => $value) {
					$sup = $age . '_suppressed';
					$nonsup = $age . '_nonsuppressed';
					$suppression = $age . '_suppression';

					$sup2 = $age . '_' . $sex . '_suppressed';
					$nonsup2 = $age . '_' . $sex . '_nonsuppressed';
					$suppression2 = $age . '_' . $sex . '_suppression';

					$row[$sup2] = $$sup->where('county_id', $county->id)->where('sex', $sex_value)->first()->totals ?? 0;				
					$row[$nonsup2] = $$nonsup->where('county_id', $county->id)->where('sex', $sex_value)->first()->totals ?? 0;
					if(($row[$sup2] + $row[$nonsup2]) == 0) $row[$suppression2] = 0;
					else{
						$row[$suppression2] = round(($row[$sup2] / ($row[$sup2] + $row[$nonsup2])) * 100, 2);
					}
				}
			}
			$data[] = $row;
		}
		self::email_csv('county_generalised_age_suppression', $data);
	}


	public static function get_pmtct_query($suppressed=true)
	{
    	$sql = 'SELECT f.id, count(*) as totals ';
    	if($suppressed == 2) $sql .= ', f.facilitycode, f.name, f.county, f.subcounty ';
		$sql .= 'FROM ';
		$sql .= '(SELECT v.id, v.facility_id, v.rcategory ';
		$sql .= 'FROM viralsamples_view v ';
		$sql .= 'RIGHT JOIN ';
		$sql .= '(SELECT ID, patient_id, max(datetested) as maxdate ';
		$sql .= 'FROM viralsamples_view ';
		$sql .= "WHERE ( datetested between '2020-01-01' and '2020-03-31' ) ";
		$sql .= "AND patient != '' AND patient != 'null' AND patient is not null and pmtct IN (1,2) ";
		$sql .= 'AND flag=1 AND repeatt=0 AND rcategory in (1, 2, 3, 4) ';
		$sql .= 'AND justification != 10 and facility_id != 7148 ';
		$sql .= 'GROUP BY patient_id) gv ';
		$sql .= 'ON v.id=gv.id) tb ';
		$sql .= 'JOIN view_facilitys f on f.id=tb.facility_id ';
		if($suppressed == 1) $sql .= 'WHERE rcategory IN (1,2) ';
		else if($suppressed == 0) $sql .= 'WHERE rcategory IN (3,4) ';
		$sql .= 'GROUP BY f.id ';
		$sql .= 'HAVING totals > 0 ';
		$sql .= 'ORDER BY f.county_id ASC, f.subcounty_id ASC, f.id ASC ';

		// return $sql;
		return collect(DB::select($sql));
	}


	public static function get_pmtct()
	{
		$data = [];

		$all = self::get_pmtct_query(2);
		$suppressed = self::get_pmtct_query(1);
		$non_suppressed = self::get_pmtct_query(0);

		foreach ($all as $key => $value) {
			$data[] = [
				'Facility Name' => $value->name,
				'Facility MFL' => $value->facilitycode,
				'County' => $value->county,
				'Subcounty' => $value->subcounty,
				'PMTCT' => $value->totals,
				'Suppressed' => $suppressed->where('id', $value->id)->first()->totals ?? 0,
				'Non Suppressed' => $non_suppressed->where('id', $value->id)->first()->totals ?? 0,
			];
		}
		self::email_csv('pmtct_data', $data);
	}



	/*
	public static function save_gender_results()
	{
		ini_set("memory_limit", "-1");
        config(['excel.import.heading' => true]);
		$path = public_path('facilities.csv');
		$data = Excel::load($path, function($reader){})->get();

		$rows = [];

		$start_date = Carbon::now()->subYear();
		$days = $start_date->day;

		$start_date = $start_date->subDays($days-1)->toDateString();
		$end_date = Carbon::now()->subDays($days)->toDateString();
		$date_params = [$start_date, $end_date];

		foreach ($data as $key => $row) {

			$facility = \App\Facility::where(['facilitycode' => $row->mfl_code])->first();
			if(!$facility) continue;

			$sql = self::get_current_gender_query(1, $facility->id, $date_params);
			$one = collect(DB::select($sql));

			$sql = self::get_current_gender_query(2, $facility->id, $date_params);
			$two = collect(DB::select($sql));

			$sql = self::get_current_gender_query(4, $facility->id, $date_params);
			$four = collect(DB::select($sql));

			$rows[] = [
				'MFL Code' => $facility->facilitycode,
				'Facility' => $facility->name,
				'Male 400 and less' => $one->where('sex', 1)->first()->totals ?? null,
				'Female 400 and less' => $one->where('sex', 2)->first()->totals ?? null,
				'Male Above 400 Less 1000' => $two->where('sex', 1)->first()->totals ?? null,
				'Female Above 400 Less 1000' => $two->where('sex', 2)->first()->totals ?? null,
				'Male Above 1000' => $four->where('sex', 1)->first()->totals ?? null,
				'Female Above 1000' => $four->where('sex', 2)->first()->totals ?? null,
			];

		}
		$file = "gender_totals_ordering_sites_between_{$start_date}_and_{$end_date}_by_most_recent_test";
		
		Excel::create($file, function($excel) use($rows){
			$excel->sheet('Sheetname', function($sheet) use($rows) {
				$sheet->fromArray($rows);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com', 'kmugambi@clintonhealthaccess.org'])->send(new TestMail($data));
	}*/


	public static function dtg_llv()
	{
		$data = [];

		$sql = "SELECT v.id, v.patient_id, v.datetested, v.result
				FROM viralsamples_view v
				RIGHT JOIN 
				(
					SELECT id, patient_id, max(datetested) as maxdate
					FROM viralsamples_view
					WHERE datetested > '2018-01-01'
					AND patient != '' AND patient != 'null' AND patient is not null
					AND flag=1 AND repeatt=0 AND result > 400
					AND justification != 10 and facility_id != 7148
					AND prophylaxis=1
					GROUP BY patient_id
				) gv
				ON v.id=gv.id
		";

		$rows = DB::select($sql);

		foreach ($rows as $key => $row) {

			$sql = "SELECT v.id, v.patient_id, v.patient, v.facility_id, v.datetested, v.result, f.name, f.facilitycode, f.subcounty, f.county
					FROM viralsamples_view v
					RIGHT JOIN 
					(
						SELECT id, patient_id, max(datetested) as maxdate
						FROM viralsamples_view
						WHERE datetested < '{$row->datetested}'
						AND patient_id = {$row->patient_id}
						AND patient != '' AND patient != 'null' AND patient is not null
						AND flag=1 AND repeatt=0 AND result > 400
						AND justification != 10 and facility_id != 7148
						AND prophylaxis=1
						GROUP BY patient_id
					) gv
					ON v.id=gv.id
					LEFT JOIN view_facilitys f ON f.id=v.facility_id
			";

			$p = DB::select($sql);

			if(!$p) continue;
			$p = $p[0];
			if($p->result <= 400) continue;

			$data[] = [
				'CCC Number' => $p->patient,
				'Facility' => $p->name,
				'Facility MFL Code' => $p->facilitycode,
				'Subcounty' => $p->subcounty,
				'County' => $p->county,
				'Most Recent Result' => $row->result,
				'Most Recent Test Date' => $row->datetested,
				'Second Most Recent Result' => $p->result,
				'Second Most Recent Test Date' => $p->datetested,
			];
		}

		$file = "dtg_llv";
		
		Excel::create($file, function($excel) use($data){
			$excel->sheet('Sheetname', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com', 'kmugambi@clintonhealthaccess.org', 'tngugi@clintonhealthaccess.org'])->send(new TestMail($data, 'Gender Totals Ordering Facilities'));
	}

	public static function dtg_llv_two()
	{
		ini_set('memory_limit', '-1');
		$data = [];

		$sql = "SELECT v.id, v.patient_id, v.datetested, v.result
				FROM viralsamples_view v
				RIGHT JOIN 
				(
					SELECT id, patient_id, max(datetested) as maxdate
					FROM viralsamples_view
					WHERE datetested > '2018-07-01'
					AND patient != '' AND patient != 'null' AND patient is not null
					AND flag=1 AND repeatt=0 AND result > 400
					AND justification != 10 and facility_id != 7148
					AND prophylaxis=1
					GROUP BY patient_id
				) gv
				ON v.id=gv.id
		";

		$rows = DB::select($sql);

		foreach ($rows as $key => $row) {

			$sql = "SELECT v.id, v.patient_id, v.patient, v.facility_id, v.datetested, v.result, r.name AS `regimen`, f.name, f.facilitycode, f.subcounty, f.county
					FROM viralsamples_view v
					LEFT JOIN view_facilitys f ON f.id=v.facility_id
					LEFT JOIN viralregimen r ON r.id=v.prophylaxis
					WHERE patient_id = {$row->patient_id} AND datetested > '2018-07-01' and repeatt=0 and rcategory IN (1,2,3,4)
					ORDER BY datetested asc
			";

			$results = DB::select($sql);

			foreach ($results as $key2 => $p) {
				$data[] = [
					'Patient' => $key+1,
					'Order' => $key2+1,
					'CCC Number' => $p->patient,
					'Facility' => $p->name,
					'Result' => $p->result,
					'Date Tested' => $p->datetested,
					'Regimen' => $p->regimen,
					'Facility MFL Code' => $p->facilitycode,
					'Subcounty' => $p->subcounty,
					'County' => $p->county,
				];
			}
		}

		$file = "dtg_llv";
		
		Excel::create($file, function($excel) use($data){
			$excel->sheet('Sheetname', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com', 'kmugambi@clintonhealthaccess.org', 'tngugi@clintonhealthaccess.org'])->send(new TestMail($data, 'Gender Totals Ordering Facilities'));


	}

	public static function kids_data()
	{
		$sql =  "SELECT patient AS `CCC Number`, f.facilitycode AS `MFL Code`, f.NAME AS `Facility`, 
			s.initiation_date AS `Date Started on Treatment`, r.name AS `Regimen`, s.dob, s.age, 
			s.result, s.datecollected, s.datetested, s.datedispatched
			FROM viralsamples_view s
			LEFT JOIN facilitys f ON f.id=s.facility_id
			LEFT JOIN viralregimen r ON r.id=s.regimen
			WHERE s.age < 15 AND s.initiation_date IS NOT NULL AND repeatt=0 AND datetested BETWEEN '2018-06-01' AND '2019-05-30'";


		ini_set('memory_limit', '-1');
		$data = [];
		$rows = DB::select($sql);

		foreach ($rows as $key => $row) {
			// $data[] = $row->toArray();
			$data[] = get_object_vars($row);
		}

		$file = "2018-06-01_2019-05-30_children_below_15_with_date_started_on_treatment";
		
		Excel::create($file, function($excel) use($data){
			$excel->sheet('Sheetname', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com'])->send(new TestMail($data));
	} 

	public static function gt_k()
	{
		ini_set('memory_limit', '-1');
		$data = [];

		$sql = "SELECT v.patient_id, v.patient AS `ccc_number`, f.name as `facility`, f.subcounty, f.county, v.patient_name, v.dob AS `date_of_birth`, v.age, g.gender, v.initiation_date AS `date_enrolled`, v.dateinitiatedonregimen AS `start_regimen_date`, vr.name AS `current_regimen`, v.result AS `VL>1000`
				FROM viralsamples_view v
				RIGHT JOIN 
				(
					SELECT id, patient_id, max(datetested) as maxdate
					FROM viralsamples_view
					WHERE datetested BETWEEN '2016-07-01' AND '2019-06-30'
					AND patient != '' AND patient != 'null' AND patient is not null
					AND flag=1 AND repeatt=0 AND result > 1000
					AND justification != 10 and facility_id != 7148
					GROUP BY patient_id
				) gv
				ON v.id=gv.id
				LEFT JOIN gender g on g.id=v.sex
				LEFT JOIN viralregimen vr on vr.id=v.prophylaxis
				LEFT JOIN view_facilitys f on f.id=v.facility_id
		";

		$rows = DB::select($sql);

		foreach ($rows as $key => $row) {
			$sql2 = "SELECT * FROM viralsamples_view WHERE patient_id={$row->patient_id} AND repeatt=0 AND rcategory IN (1,2,3,4) ORDER BY datetested DESC LIMIT 3";

			$samples = DB::select($sql2);

			foreach ($samples as $s) {
				if($s->rcategory < 3) continue 2;
			}

			// $data[] = $row->toArray();
			$data[] = get_object_vars($row);
		}

		$file = "three_years_nonsuppressed_three_consecutive";
		
		Excel::create($file, function($excel) use($data){
			$excel->sheet('Sheetname', function($sheet) use($data) {
				$sheet->fromArray($data);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com'])->send(new TestMail($data));

	}

	public static function ovf()
	{
		$file = public_path('ccc.csv');
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            $ccc = rtrim($data[0]);
            $p = \App\Viralpatient::where('patient', $ccc)->first();
            if(!$p) continue;
            $p->ovf = 1;
            $p->save();
        }
	}

	public static function ovc()
	{
		$file = public_path('ccc_2.csv');
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
        	if($data[0] == 'mfl_code') continue;
            $ccc = rtrim($data[1]);
            $code = rtrim($data[0]);
            $p = \App\Viralpatient::select('viralpatients.*')
            	->join('facilitys', 'viralpatients.facility_id', '=', 'facilitys.id')
            	->where(['patient' => $ccc, 'facilitycode' => $code])->first();
            if(!$p) continue;
            $p->ovf = 1;
            $p->save();
        }

        $file = public_path('ccc_1.csv'); $handle = fopen($file, "r"); $rows=[]; $size=0; $i=0;
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        	$ccc = rtrim($data[0]);
        	$rows[] = $ccc;
        	$size++;

        	if($size == 200){
        		\App\Viralpatient::whereIn('patient', $rows)->update(['ovf' => 1]);
        		$rows = [];
        		$size = 0;
        	}    	
        }
        if($rows) \App\Viralpatient::whereIn('patient', $rows)->update(['ovf' => 1]);
	}

	// $file = public_path('ccc.csv'); $handle = fopen($file, "r"); $rows=[]; $size=0; $i=0;
	// while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){ $i++; if($i < 19500){ continue; } $ccc = rtrim($data[0]); $rows[] = $ccc; $size++; if($size == 200){ \App\Viralpatient::whereIn('patient', $rows)->update(['ovf' => 1]); $rows=[]; $size=0; } }
}
