<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DrDashboard;
use App\MiscDr;
use App\DrSample;
use App\DrCall;
use App\DrCallDrug;

use DB;
use Str;
use Carbon\Carbon;

class DrDashboardProposedController extends DrDashboardBaseController
{	

	// Views
	public function index()
	{        
		DrDashboard::clear_cache();
		return view('dashboard.dr_waterfall', DrDashboard::get_divisions());
	}

	public function get_waterfall()
	{
    	$divisions_query = DrDashboard::divisions_query();
        $date_query = DrDashboard::date_query('created_at');

		$total_requests = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0])
			->first();

		$total_accepted = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1])
			->first();

		$total_rejected = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 2])
			->first();

		$total_failed_gel = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1, 'passed_gel_documentation' => 0])
			->first();

		$total_passed_gel = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1, 'passed_gel_documentation' => 1])
			->first();

		$failed_sequencing = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1, 'passed_gel_documentation' => 1, ])
			->whereIn('status_id', [2, 3])
			->first();

		$passed_sequencing = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1, 'passed_gel_documentation' => 1, 'status_id' => 1])
			->first();

		$first_line = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->leftJoin('viralregimen', 'viralregimen.id', '=', 'dr_samples.prophylaxis')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1, 'passed_gel_documentation' => 1, 'status_id' => 1, 'viralregimen.line' => 1])
			->first();

		$second_line = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->leftJoin('viralregimen', 'viralregimen.id', '=', 'dr_samples.prophylaxis')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1, 'passed_gel_documentation' => 1, 'status_id' => 1, 'viralregimen.line' => 2])
			->first();

		$third_line = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->leftJoin('viralregimen', 'viralregimen.id', '=', 'dr_samples.prophylaxis')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1, 'passed_gel_documentation' => 1, 'status_id' => 1, 'viralregimen.line' => 3])
			->first();

		$total_genotyped = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0, 'receivedstatus' => 1, 'passed_gel_documentation' => 1, ])
			// ->whereIn('status_id', [1, 2, 3, 6])
			->whereNotNull('datetested')
			->first();

		return compact('total_requests', 'total_accepted', 'total_rejected', 'total_passed_gel', 'total_failed_gel', 'failed_sequencing', 'passed_sequencing', 'first_line', 'second_line', 'third_line', 'total_genotyped');
	}

	public function waterfall_table()
	{
		extract($this->get_waterfall());

		$str = "
			<table class='table table-bordered'>
				<tr>
					<td colspan='3'> Summary </td>
				</tr>
				<tr>
					<td>Total Received </td>
					<td>".$total_requests->total."</td>
					<td> </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp; Rejected </td>
					<td>".$total_rejected->total." </td>
					<td>".DrDashboard::get_percentage($total_rejected->total, $total_requests->total, 0)."% </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp; Accepted </td>
					<td>".$total_accepted->total." </td>
					<td>".DrDashboard::get_percentage($total_accepted->total, $total_requests->total, 0)."% </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Failed Gel Documentation </td>
					<td>".$total_failed_gel->total." </td>
					<td>".DrDashboard::get_percentage($total_failed_gel->total, $total_accepted->total, 0)."% </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Passed Gel Documentation </td>
					<td>".$total_passed_gel->total." </td>
					<td>".DrDashboard::get_percentage($total_passed_gel->total, $total_accepted->total, 0)."% </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Failed Sequencing </td>
					<td>".$failed_sequencing->total." </td>
					<td>".DrDashboard::get_percentage($failed_sequencing->total, $total_passed_gel->total, 0)."% </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Sequenced Successfully </td>
					<td>".$passed_sequencing->total." </td>
					<td>".DrDashboard::get_percentage($passed_sequencing->total, $total_passed_gel->total, 0)."% </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 1st Line Patients </td>
					<td>".$first_line->total." </td>
					<td>".DrDashboard::get_percentage($first_line->total, $passed_sequencing->total, 0)."% </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 2nd Line Patients </td>
					<td>".$second_line->total." </td>
					<td>".DrDashboard::get_percentage($second_line->total, $passed_sequencing->total, 0)."% </td>
				</tr>
				<tr>
					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 3rd Line Patients </td>
					<td>".$third_line->total." </td>
					<td>".DrDashboard::get_percentage($third_line->total, $passed_sequencing->total, 0)."% </td>
				</tr>
			</table>
		";
		return $str;
	}


	// Charts
	public function waterfall()
	{
		extract($this->get_waterfall());

		// dd($rows);

		$data = DrDashboard::bars(['Total (%)'], 'column', ["#4472c4"]);
		$data['extra_tooltip'] = true;

		$data['categories'][0] = 'Total Received';
		$data["outcomes"][0]["data"][0]['y'] = (int) 100;
		$data["outcomes"][0]["data"][0]['z'] = ' (' . number_format($total_requests->total) . ')';

		$data['categories'][1] = 'Total Accepted';
		$data["outcomes"][0]["data"][1]['y'] = DrDashboard::get_percentage($total_accepted->total, $total_requests->total);
		$data["outcomes"][0]["data"][1]['z'] = ' (' . number_format($total_accepted->total) . ')';

		/*$data['categories'][2] = 'Accepted Using VL Criteria';
		$data["outcomes"][0]["data"][2]['y'] = DrDashboard::get_percentage($total_accepted->total, $total_requests->total);
		$data["outcomes"][0]["data"][2]['z'] = ' (' . number_format($total_accepted->total) . ')';*/

		$data['categories'][2] = 'Passed Gel Documentation';
		$data["outcomes"][0]["data"][2]['y'] = DrDashboard::get_percentage($total_passed_gel->total, $total_requests->total);
		$data["outcomes"][0]["data"][2]['z'] = ' (' . number_format($total_passed_gel->total) . ')';

		$data['categories'][3] = 'Successfully Genotyped';
		$data["outcomes"][0]["data"][3]['y'] = DrDashboard::get_percentage($total_genotyped->total, $total_requests->total);
		$data["outcomes"][0]["data"][3]['z'] = ' (' . number_format($total_genotyped->total) . ')';

		return view('charts.line_graph', $data);
	}


	public function requests_table()
	{
        $date_query = DrDashboard::date_query('created_at');
    	$divisions_query = DrDashboard::divisions_query();

    	$facility = false;
    	if(session('filter_county')) $facility = true;

		$rows = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->when(true, function($query) use ($facility){
				if($facility){
					return $query->addSelect('view_facilitys.name', 'facilitycode')
						->groupBy('view_facilitys.id');
				}
				else{
					return $query->addSelect('county')
						->groupBy('county_id');
				}
			})
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0])
			->orderBy('total', 'desc')
			->get();

		$div = Str::random(15);

		return view('charts.table_requests', compact('div', 'rows', 'facility'));
	}


	public function requests_chart()
	{
        $date_query = DrDashboard::date_query('created_at');
    	$divisions_query = DrDashboard::divisions_query();

    	$facility = false;
    	if(session('filter_county')) $facility = true;

		$rows = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS total")
			->when(true, function($query) use ($facility){
				if($facility){
					return $query->addSelect('view_facilitys.name', 'facilitycode')
						->groupBy('view_facilitys.id');
				}
				else{
					return $query->addSelect('county')
						->groupBy('county_id');
				}
			})
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0])
			->orderBy('total', 'desc')
			->get();

		$data = DrDashboard::bars(['Total Samples'], 'column', ["#4472c4"]);

		foreach ($rows as $key => $row) {
			if($facility)
				$data['categories'][$key] = $row->name;
			else
				$data['categories'][$key] = $row->county;

			$data["outcomes"][0]["data"][$key] = (int) $row->total;
		}

		return view('charts.bar_graph', $data);
	}


	public function gender()
	{
    	$divisions_query = DrDashboard::divisions_query();
        $date_query = DrDashboard::date_query('dr_samples.created_at');

		$rows = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->join('viralpatients', 'viralpatients.id', '=', 'dr_samples.patient_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("sex, COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0])
			->groupBy('sex')
			->get();

		$data['div'] = Str::random(15);

		$data['outcomes']['name'] = "Tests";
		$data['outcomes']['colorByPoint'] = true;


		$data['outcomes']['data'][0]['name'] = "Male";
		$data['outcomes']['data'][1]['name'] = "Female";

		$data['outcomes']['data'][0]['y'] = (int) ($rows->where('sex', 1)->first()->total ?? 0);
		$data['outcomes']['data'][1]['y'] = (int) ($rows->where('sex', 2)->first()->total ?? 0);

		return view('charts.pie_chart', $data);

	}


	public function dr_reasons()
	{
    	$divisions_query = DrDashboard::divisions_query();
        $date_query = DrDashboard::date_query('dr_samples.created_at');

		$rows = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->join('viralpatients', 'viralpatients.id', '=', 'dr_samples.patient_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("dr_reason_id, COUNT(dr_samples.id) AS total")
			->whereRaw($divisions_query)
            ->whereRaw($date_query)
			->where(['repeatt' => 0])
			->whereNotNull('dr_reason_id')
			->groupBy('dr_reason_id')
			->get();

		$data['div'] = Str::random(15);

		$data['outcomes']['name'] = "Tests";
		$data['outcomes']['colorByPoint'] = true;

		$drReasons = DB::table('drug_resistance_reasons')->get();

		foreach ($drReasons as $key => $drReason) {
			$data['outcomes']['data'][$key]['name'] = $drReason->name;
			$data['outcomes']['data'][$key]['y'] = (int) ($rows->where('dr_reason_id', $drReason->id)->first()->total ?? 0);
		}

		return view('charts.pie_chart', $data);

	}


	// Charts
	public function age()
	{
		$rows = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->selectRaw("age_category, COUNT(dr_samples.id) AS samples")
			->whereRaw(DrDashboard::date_query())
			->whereRaw(DrDashboard::divisions_query())
			->groupBy('age_category')
			->get();

		$age_categories = DB::table('age_categories')->get();

		$data = DrDashboard::bars(['Total Requests'], 'column', ["#4472c4"]);

		foreach ($age_categories as $key => $age_category) {
			$data['categories'][$key] = $age_category->name;
			$data["outcomes"][0]["data"][$key] = (int) ($rows->where('age_category', $age_category->id)->first()->samples ?? 0);
		}

		return view('charts.bar_graph', $data);
	}


	// Charts
	public function tat()
	{
		$row = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->selectRaw("
					AVG(dr_samples.tat1) AS tat1,
					AVG(dr_samples.tat2) AS tat2,
					AVG(dr_samples.tat3) AS tat3,
					AVG(dr_samples.tat4) AS tat4
				")
			->whereRaw(DrDashboard::date_query('datedispatched'))
			->whereRaw(DrDashboard::divisions_query())
			->first();

		$data['div'] = \Str::random(15);
		$data['tat1'] = $row->tat1;
		$data['tat2'] = $row->tat2;
		$data['tat3'] = $row->tat3;
		$data['tat4'] = $row->tat4;

		return view('charts.tat', $data);
	}

	// Charts
	public function monthly_chart()
	{
		$rows = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("YEAR(datetested) AS year_tested, MONTH(datetested) AS month_tested, COUNT(dr_samples.id) AS total")
			->whereRaw(DrDashboard::date_query('datetested'))
			->whereRaw(DrDashboard::divisions_query())
			->groupBy('year_tested', 'month_tested')
			->orderBy('year_tested', 'ASC')
			->orderBy('month_tested', 'ASC')
			->get();

		$data = DrDashboard::bars(['Monthly Total'], 'column', ["#4472c4"]);

		foreach ($rows as $key => $row) {
			$day = Carbon::create("{$row->year_tested}-{$row->month_tested}-01");
			$data['categories'][$key] = $day->year . ', ' . $day->shortMonthName;
			$data["outcomes"][0]["data"][$key] = (int) $row->total;
		}

		return view('charts.bar_graph', $data);
	}


	public function resistance_table()
	{

		$rows = DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')
			->join('drug_classes', 'drug_classes.id', '=', 'dr_calls.drug_class_id')
			->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')
			->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("drug_classes.name, COUNT(dr_call_drugs.id) AS total")
			->whereRaw(DrDashboard::date_query('datetested'))
			->whereRaw(DrDashboard::divisions_query())
			->groupBy('dr_calls.drug_class_id')
			->get();

		$resistance_rows = DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')
			->join('drug_classes', 'drug_classes.id', '=', 'dr_calls.drug_class_id')
			->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')
			->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("drug_classes.name, COUNT(dr_call_drugs.id) AS total")
			->whereRaw(DrDashboard::date_query('datetested'))
			->whereRaw(DrDashboard::divisions_query())
			->whereIn('dr_call_drugs.call', ['R', 'I'])
			->groupBy('dr_calls.drug_class_id')
			->get();

		$content_rows = '';

		foreach($rows as $row){
			$num = $resistance_rows->where('name', $row->name)->first()->total ?? 0;
			$resistance = DrDashboard::get_percentage($num, $row->total, 0);
			$content_rows .= "
			<tr>
				<td>{$row->name}</td>
				<td>{$resistance}%</td>
			</tr>
			";
		}

		$str = "
		<table class='table table-bordered'>
			<tr>
				<td colspan='2'>Resistance Prevalence </td>
			</tr>
			{$content_rows}
		</table>

		";
		return $str;
	}

	public function cross_resistance_table()
	{
		$cross_array = [
			'NNRTI + NRTI' => [2, 3],
			'NNRTI + PI' => [2, 4],
			'NRTI + PI' => [3, 4],
			'NNRTI + NRTI + PI' => [2, 3, 4],
		];

		$content_rows = '';
		foreach ($cross_array as $label => $drug_array) {

			$t = DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')
				->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')
				->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
				->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
				->selectRaw("COUNT(dr_call_drugs.id) AS total")
				->whereRaw(DrDashboard::date_query('datetested'))
				->whereRaw(DrDashboard::divisions_query())
				->whereIn('dr_calls.drug_class_id', $drug_array)
				->first()->total;

			$r = DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')
				->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')
				->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
				->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
				->selectRaw("COUNT(dr_call_drugs.id) AS total")
				->whereRaw(DrDashboard::date_query('datetested'))
				->whereRaw(DrDashboard::divisions_query())
				->whereIn('dr_call_drugs.call', ['R', 'I'])
				->whereIn('dr_calls.drug_class_id', $drug_array)
				->first()->total;

			$resistance = DrDashboard::get_percentage($r, $t, 0);
			$content_rows .= "
			<tr>
				<td>{$label}</td>
				<td>{$resistance}%</td>
			</tr>
			";
		}

		$str = "
		<table class='table table-bordered'>
			<tr>
				<td colspan='2'>Cross Resistance Prevalence </td>
			</tr>
			{$content_rows}
		</table>

		";
		return $str;
	}




}
