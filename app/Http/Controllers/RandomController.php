<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lab;
use App\LabEquipmentTracker;
use App\LabPerformanceTracker;
use App\Sample;
use App\SampleView;
use App\Viralsample;
use App\ViralsampleView;
use App\Random;
use Mpdf\Mpdf;
use DB;

class RandomController extends Controller
{

	public function refresh_cache()
	{		
		$lookup = \App\Lookup::refresh_cache();
		return back();
	}

	public function refresh_dashboard()
	{		
		$lookup = \App\DashboardCacher::refresh_cache();
		return back();
	}

	public function download_api()
	{		
    	$path = public_path('Lab.postman_collection.json');
    	return response()->download($path);
	}

	public function download_notice()
	{		
		if(env('APP_LAB') == 1){
			$path = $path = public_path('kemri_nairobi.pdf');
			$name = 'KEMRI LMB LAB TEMPORARY SERVICE INTERRUPTION INTERNAL MEMO.pdf';
		}	
		else if(env('APP_LAB') == 3){
			$path = $path = public_path('kemri_alupe.pdf');
			$name = 'KEMRI ALUPE LAB TEMPORARY SERVICE INTERRUPTION INTERNAL MEMO.pdf';
		}
		else{
			return redirect('/batch');
		}
		session(['downloaded_notice' => true]);
    	return response()->download($path, $name);
	}

	public function send_to_login($param = null)
	{
		return redirect('/login');
	}



	public function sysswitch($sys)
	{
		if($sys == 'EID'){
			$new = session(['testingSystem' => 'EID']);
		}else if ($sys == 'Viralload'){
			$new = session(['testingSystem' => 'Viralload']);
		}else if ($sys == 'DR'){
			$new = session(['testingSystem' => 'DR']);
		}else if ($sys == 'CD4'){
			$new = session(['testingSystem' => 'CD4']);
		}else if ($sys == 'Covid'){
			$new = session(['testingSystem' => 'Covid']);
		}else if ($sys == 'HPV'){
			$new = session(['testingSystem' => 'HPV']);
		}

		session(['test_var' => 'changed']);
		return ['testingSystem' => session('testingSystem')];
		
		// echo json_encode(session('testingSystem'));
	}

	public function search()
	{
		return view('forms.search')->with('pageTitle', 'Search');
	}

	
	public function labcontacts()
	{
		$lab = Lab::find(env('APP_LAB'));
		return view('forms.lab', ['lab' => $lab]);
	}

	public function savelabcontact(Request $request)
	{
		$lab = Lab::find(env('APP_LAB'));
		$lab->fill($request->except(['_token', '_method']));
		$lab->save();
		session(['toast_message' => 'The lab contacts have been updated.']);
		return redirect('/home');
	}


	public function lablogs(Request $request, $year = null, $month = null)
	{
		if ($request->method() == 'POST') {
			$year = $request->input('year');
			$month = $request->input('month');
		}
		$year = strtolower($year);
		$month = strtolower($month);

		if ($year == null || $year=='null') {
			$year = (int) session('lablogyear');
			if ($year == null) 
				session(['lablogyear' => (int) date('Y', strtotime("-1 Month", strtotime(date('Y'))))]);
		} else {
			session(['lablogyear' => $year]);
		}
		
		if ($month == null || $month=='null') {
			$month = (int) session('lablogmonth');
			if ($month == null) 
				session(['lablogmonth' => (int) date('m', strtotime("-1 Month", strtotime(date('m'))))]);
		} else {
			session(['lablogmonth' => $month]);
		}
		
		$year = (int) session('lablogyear');
		$month = (int) session('lablogmonth');
		
		$data = Random::__getLablogsData($year, $month, false);
		
		if ($request->method() == 'POST') {
			$lab = \App\Lab::find(env('APP_LAB'));
	    	$download = true;
	    	$mpdf = new Mpdf();
	        $this->lab = \App\Lab::find(env('APP_LAB'));
	        $lab = $this->lab;
	        $pageData = ['data' => $data, 'lab' => $lab, 'download' => true];
	        // dd($pageData);
	        $view_data = view('exports.mpdf_labtracker', $pageData)->render();
	        $mpdf->WriteHTML($view_data);
	        $mpdf->Output('monthlabtracker ' . $data->year .  $data->month .'.pdf', \Mpdf\Output\Destination::DOWNLOAD);

			return view('exports.mpdf_labtracker', compact('data', 'lab', 'download'));
		}
		
		return view('reports.labtrackers', compact('data'))->with('pageTitle', 'Lab Equipment Log/Tracker');
	}

	public function equipmentbreakdown(Request $request) {
		if($request->method() == 'GET') {
			$data = DB::table('lab_equipment_mapping')->where('lab', '=', auth()->user()->lab_id)->get();
			$now = true;
			$data = (object)$data;
			return view('tasks.equipmentlog', compact('data', 'now'))->with('pageTitle', 'Equipment Breakdown');
		} else {
			$tracker = [];
            foreach ($request->equipmentid as $key => $value) {
                $tracker[] = [
                        'month' => date('m'),
                        'year' => date('Y'),
                        'lab_id' => auth()->user()->lab_id,
                        'equipment_id' => $value,
                        'datesubmitted' => date('Y-m-d'),
                        'submittedBy' => auth()->user()->id,
                        'datebrokendown' => ($request->datebrokendown[$key] == "") ? null : $request->datebrokendown[$key],
                        'datereported' => ($request->datereported[$key] == "") ? null : $request->datereported[$key],
                        'datefixed' => ($request->datefixed[$key] == "") ? null : $request->datefixed[$key],
                        'downtime' => ($request->downtime[$key] == "") ? null : $request->downtime[$key],
                        'samplesnorun' => ($request->samplesnorun[$key] == "") ? null : $request->samplesnorun[$key],
                        'failedruns' => ($request->failedruns[$key] == "") ? null : $request->failedruns[$key],
                        'reagentswasted' => ($request->reagentswasted[$key] == "") ? null : $request->reagentswasted[$key],
                        'breakdownreason' => ($request->breakdownreason[$key] == "") ? null : $request->breakdownreason[$key],
                        'othercomments' => ($request->otherreasons == "") ? null : $request->otherreasons
                    ];
            }
            
            foreach ($tracker as $key => $value) {
                $save = LabEquipmentTracker::create($value);
            }
            return redirect('lablogs');
		}
	}

	public function config()
	{
		return phpinfo();
	}

	public function login_edarp(Request $request) {
		if(auth()->user()) auth()->logout();
        $user = \App\User::find($request->user);
        auth()->login($user);

        if (auth()->check())
        	return redirect()->route('viralsample.nhrl');
        else
        	abort(401);
	}

	public function testlabtracker($year = 2019, $month = 4) {
		// $year = date('Y');
  //   	$month = date('m');
    	$previousMonth =  $month - 1;
    	if ($month == 1) {
    		$previousMonth = 12;
    		$year -= 1;
    	}
    	$data = Random::__getLablogsData($year, $month);
    	$lab = \App\Lab::find(env('APP_LAB'));
    	$download = true;
    	// // dd($lab);
    	// $path = storage_path('app/lablogs/monthlabtracker ' . $data->year .  $data->month .'.pdf');

     //    if(!is_dir(storage_path('app/lablogs'))) mkdir(storage_path('app/lablogs'), 0777, true);

     //    if(file_exists($path)) unlink($path);
    	// $mpdf = new Mpdf();
     //    $this->lab = \App\Lab::find(env('APP_LAB'));
     //    $lab = $this->lab;
     //    $pageData = ['data' => $data, 'lab' => $lab, 'download' => true];
     //    $view_data = view('exports.mpdf_labtracker', $pageData)->render();
     //    $mpdf->WriteHTML($view_data);
     //    $mpdf->Output($path, \Mpdf\Output\Destination::FILE);

		return view('exports.mpdf_labtracker', compact('data', 'lab', 'download'));
	}
}
