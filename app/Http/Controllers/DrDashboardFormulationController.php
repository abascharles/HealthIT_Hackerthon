<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DrDashboard;
use App\MiscDr;
use App\DrSample;
use App\DrCall;
use App\DrCallDrug;
use App\DrSampleMutation;

use DB;
use Str;
use Carbon\Carbon;

class DrDashboardFormulationController extends DrDashboardBaseController
{


	// Views
	public function index()
	{        
		DrDashboard::clear_cache();
		return view('dashboard.dr_formulation', DrDashboard::get_divisions());
	}

	public function dr_mutation_index()
	{        
		DrDashboard::clear_cache();
		return view('dashboard.dr_mutation_frequency', DrDashboard::get_divisions());
	}



	public function formulation_resistance()
	{
		$drug_class = session('filter_drug_class');
		$regimen_classes = DB::table('regimen_classes')
			->when($drug_class, function($query) use($drug_class){
				if(is_array($drug_class)) return $query->whereIn('drug_class_id', $drug_class);
				return $query->where('drug_class_id', $drug_class);
			})
			->get();

		$rows = DrCallDrug::join('dr_calls', 'dr_calls.id', '=', 'dr_call_drugs.call_id')
			// ->join('drug_classes', 'drug_classes.id', '=', 'dr_calls.drug_class_id')
			->join('dr_samples', 'dr_samples.id', '=', 'dr_calls.sample_id')
			->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("short_name_id, dr_call_drugs.call, COUNT(dr_call_drugs.id) AS total")
			->whereRaw(DrDashboard::date_query('datetested'))
			->whereRaw(DrDashboard::divisions_query())
			->when($drug_class, function($query) use($drug_class){
				if(is_array($drug_class)) return $query->whereIn('drug_class_id', $drug_class);
				return $query->where('drug_class_id', $drug_class);
			})
			->whereIn('dr_call_drugs.short_name_id', $regimen_classes->pluck('id')->toArray())
			// ->whereIn('dr_call_drugs.call', ['R', 'I'])
			->groupBy('short_name_id', 'dr_call_drugs.call')
			->get();

		// $data = DrDashboard::bars(['Low Coverage', 'Susceptible', 'Intermediate Resistance', 'High Level Resistance'], 'bar', ["#595959", '#00ff00', '#ff9900', '#ff0000']);

		$data = DrDashboard::bars(['High Level Resistance', 'Intermediate Resistance', 'Susceptible', 'Low Coverage',], 'bar', ['#ff0000', '#ff9900', '#00ff00', "#595959", ]);

		if(!$drug_class) $data['height'] = 800;
		$data['stacking_percentage'] = true;
		// $data['outcomes']['dataLabels'] = ['enabled' => true];
		$data['data_labels'] = true;
		$data['yAxis'] = "Resistance (%)";
		// $data['suffix'] = "%";

		$temp_collection = collect([]);

		foreach ($regimen_classes as $key => $rc) {

			$resistant = (int) ($rows->where('short_name_id', $rc->id)->where('call', 'R')->first()->total ?? 0);
			$intermediate = (int) ($rows->where('short_name_id', $rc->id)->where('call', 'I')->first()->total ?? 0);
			$susceptible = (int) ($rows->where('short_name_id', $rc->id)->whereIn('call', ['S', 'L', 'PL'])->sum('total'));
			$low_coverage = (int) ($rows->where('short_name_id', $rc->id)->where('call', 'LC')->first()->total ?? 0);
			$total = $low_coverage + $susceptible + $intermediate + $resistant;

			$temp_collection->push([
				'drug' => $rc->name,
				'resistant' => $resistant,
				'intermediate' => $intermediate,
				'susceptible' => $susceptible,
				'low_coverage' => $low_coverage,
				'resistance' => DrDashboard::get_percentage($resistant, $total),
			]);

			/*$data['categories'][$key] = $rc->name;
			$data["outcomes"][0]["data"][$key] = (int) ($rows->where('short_name_id', $rc->id)->where('call', 'R')->first()->total ?? 0);
			$data["outcomes"][1]["data"][$key] = (int) ($rows->where('short_name_id', $rc->id)->where('call', 'I')->first()->total ?? 0);
			$data["outcomes"][2]["data"][$key] = (int) ($rows->where('short_name_id', $rc->id)->whereIn('call', ['S', 'L', 'PL'])->sum('total'));
			$data["outcomes"][3]["data"][$key] = (int) ($rows->where('short_name_id', $rc->id)->where('call', 'LC')->first()->total ?? 0);*/
		}
		// dd($temp_collection);
		$ordered_collection = $temp_collection->sortByDesc('resistance')->values()->all();

		foreach($ordered_collection as $key => $val){
			$data['categories'][$key] = $val['drug'];
			$data["outcomes"][0]["data"][$key] = $val['resistant'];
			$data["outcomes"][1]["data"][$key] = $val['intermediate'];
			$data["outcomes"][2]["data"][$key] = $val['susceptible'];
			$data["outcomes"][3]["data"][$key] = $val['low_coverage'];
		}

		$data['outcomes'][0]['dataLabels'] = $data['outcomes'][1]['dataLabels'] = $data['outcomes'][2]['dataLabels'] = $data['outcomes'][3]['dataLabels'] = [
			'format' => '{point.y} ({point.percentage:.1f}%)',
			'color' => 'black',
		];
		/*$outcomes = $data['outcomes'];
		$dataLabels[0] = [
			'format' => '{point.y} ({point.percentage:.1f}%)'
		];
		$data['outcomes'] = [];
		$data['outcomes'][0]['dataLabels'] = $dataLabels;
		$data['outcomes'][0]['data'] = $outcomes;*/
		return view('charts.bar_graph', $data);
	}


	public function mutation_frequency()
	{
		$drug_classes = DB::table('drug_classes')->get();

		$rows = DrSampleMutation::join('dr_samples', 'dr_samples.id', '=', 'dr_sample_mutations.sample_id')
			->join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("mutation_id, COUNT(dr_sample_mutations.id) AS total")
			->groupBy('mutation_id')
			->get();

		$view_data = '';

		foreach ($drug_classes as $drug_class) {
			$mutations = DB::table('dr_mutations')->where('drug_class_id', $drug_class->id)->get();

			// $data = DrDashboard::bars(['Frequency'], 'bar', ["#00ff00"]);
			$data = DrDashboard::bars(['Frequency'], 'bar', [$drug_class->drug_colour]);
			$data['title'] = "Frequency of {$drug_class->name} mutation";
			$data['height'] = 800;
			$data['div_class'] = 'col-md-6';

			$temp_collection = collect([]);

			foreach ($mutations as $key => $mutation) {
				$temp_collection->push([
					'mutation' => $mutation->mutation,
					'total' => (int) ($rows->where('mutation_id', $mutation->id)->first()->total ?? 0),
				]);
				$data['categories'][$key] = $mutation->mutation;
				$data["outcomes"][0]["data"][$key] = (int) ($rows->where('mutation_id', $mutation->id)->first()->total ?? 0);
			}

			// dd($temp_collection);

			$sorted_collection = $temp_collection->sortByDesc('total')->values()->all();
			foreach ($sorted_collection as $key => $val) {
				$data['categories'][$key] = $val['mutation'];
				$data["outcomes"][0]["data"][$key] = $val['total'];
			}

			$view_data .= view('charts.bar_graph', $data)->render();
		}
		return $view_data;
	}


}