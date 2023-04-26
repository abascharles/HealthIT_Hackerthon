<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DrDashboard;
use App\MiscDr;
use App\DrSample;
use App\DrCall;
use App\DrCallDrug;

use DB;

class DrDashboardController extends DrDashboardBaseController
{

	// Filter routes
	public function filter_date(Request $request)
	{
		$filter_start_date = $request->input('start_date');
		$filter_end_date = $request->input('end_date');
		session(compact('filter_start_date', 'filter_end_date'));
		return compact('filter_start_date', 'filter_end_date');
	}


	public function filter_any(Request $request)
	{
		// if(!session('filter_groupby')) abort(400);
		$var = $request->input('session_var');
		$val = $request->input('value');

		if($val == null || (!is_array($val) && in_array($val, ['null', ''])) || (is_array($val) && in_array('null', $val)) ) $val = null;
		session([$var => $val]);

		return [$var => $val];
	}


	// Views
	public function index()
	{        
		DrDashboard::clear_cache();
		return view('dashboard.dr', DrDashboard::get_divisions());
	}

	// Charts
	public function drug_resistance($current_only=false)
	{
		$rows = DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')
			->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')
			->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->selectRaw("dr_call_drugs.short_name_id, dr_call_drugs.call, COUNT(dr_call_drugs.id) AS samples")
			->when($current_only, function($query){
				return $query->where('current_drug', true);
			})
			->whereRaw(DrDashboard::divisions_query())
			->groupBy('short_name_id', 'call')
			->get();

		$drugs = DB::table('regimen_classes')->get();
		$call_array = MiscDr::$call_array;

		$data = DrDashboard::bars(['Low Coverage', 'Resistant', 'Intermediate Resistance', 'Susceptible'], 'column', ['#595959', "#ff0000", "#ff9900", "#00ff00"]);

		foreach ($drugs as $key => $drug) {
			$data['categories'][$key] = $drug->short_name;
			$i = 0;
			foreach ($call_array as $call_key => $c) {
				if($i==4) break;
				$data["outcomes"][$i]["data"][$key] = (int) ($rows->where('short_name_id', $drug->id)->where('call', $call_key)->first()->samples ?? 0);
				$i++;
			}
		}
		return view('charts.bar_graph', $data);
	}

	public function heat_map($current_only=false)
	{
		$rows = DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')
			->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')
			->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("dr_call_drugs.call, COUNT(dr_call_drugs.id) AS samples")
			->groupBy('call')
			->when($current_only, function($query){
				return $query->where('current_drug', true);
			})
			->when(true, $this->get_callback_no_dates('name'))
			->get();

		// dd($rows);

		$categories = $rows->pluck('name')->unique()->flatten()->toArray();

		$data = DrDashboard::bars(['Low Coverage', 'Resistant', 'Intermediate Resistance', 'Susceptible'], 'column', ['#595959', "#ff0000", "#ff9900", "#00ff00"]);
		$data['point_percentage'] = true;

		$call_array = MiscDr::$call_array;

		foreach ($categories as $key => $value) {
			$data['categories'][$key] = $value;

			$i = 0;
			foreach ($call_array as $call_key => $c) {
				if($i==4) break;
				$data["outcomes"][$i]["data"][$key] = (int) ($rows->where('name', $value)->where('call', $call_key)->first()->samples ?? 0);
				$i++;
			}
		}
		return view('charts.bar_graph', $data);
	}


	/*public function heat_map()
	{
		$rows = DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')
			->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')
			->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->selectRaw("county, dr_call_drugs.call, COUNT(dr_call_drugs.id) AS samples")
			->groupBy('county_id', 'call')
			->orderBy('county_id')
			->get();

		$rows = \App\DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')->selectRaw("county, dr_call_drugs.call, COUNT(dr_call_drugs.id) AS samples")->groupBy('county_id', 'call')->orderBy('county_id')->get();

		// dd($rows);

		$categories = $rows->pluck('county')->unique('county')->flatten();

		$data = DrDashboard::bars(['Low Coverage', 'Resistant', 'Intermediate Resistance', 'Susceptible'], 'column', ['#595959', "#ff0000", "#ff9900", "#00ff00"]);

		$call_array = MiscDr::$call_array;

		foreach ($categories as $key => $value) {
			$data['categories'][$key] = $value;

			$i = 0;
			foreach ($call_array as $call_key => $c) {
				if($i==4) break;
				$data["outcomes"][$i]["data"][$key] = (int) ($rows->where('county', $value)->where('call', $call_key)->first()->samples ?? 0);
				$i++;
			}
		}
		return view('charts.bar_graph', $data);
	}*/
}
