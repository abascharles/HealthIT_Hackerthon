<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\DrDashboard;
use App\MiscDr;
use App\DrSample;
use App\DrCall;
use App\DrCallDrug;

use DB;

class DrDashboardTestingController extends DrDashboardBaseController
{


	// Views
	public function index()
	{        
		DrDashboard::clear_cache();
		return view('dashboard.dr_testing', DrDashboard::get_divisions());
	}


	// Charts
	public function testing()
	{
		$rows = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS samples")
			->when(true, $this->get_callback('samples', 'datetested'))
			->whereNotNull('datetested')
			->get();

		// dd($rows);

		$data = DrDashboard::bars(['Samples Tested'], 'column', ["#00ff00"]);

		foreach ($rows as $key => $row) {
			$data['categories'][$key] = DrDashboard::get_category($row);

			$data["outcomes"][0]["data"][$key] = (int) $row->samples;
		}
		return view('charts.bar_graph', $data);
	}


	// Charts
	public function rejected()
	{
		$rows = DrSample::join('view_facilitys', 'view_facilitys.id', '=', 'dr_samples.facility_id')
			->leftJoin('dr_projects', 'dr_projects.id', '=', 'dr_samples.project')
			->selectRaw("COUNT(dr_samples.id) AS samples")
			->when(true, $this->get_callback('samples', 'datereceived'))
			->where('receivedstatus', 2)
			->get();

		// dd($rows);

		$data = DrDashboard::bars(['Rejected Samples'], 'column', ["#ff1a1a"]);

		foreach ($rows as $key => $row) {
			$data['categories'][$key] = DrDashboard::get_category($row);

			$data["outcomes"][0]["data"][$key] = (int) $row->samples;
		}
		return view('charts.bar_graph', $data);
	}
}
