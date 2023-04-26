<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;
use Mpdf\Mpdf;
use DB;

use App\Mail\EdarpMachakosFailed;
use App\Mail\EdarpMachakosDelayed;

class Console
{

	public static function create_alert_row()
	{
		$w = WeeklyAlert::where(['lab_id' => 10, 'start_date' => date('Y-m-d')])->first();
		if($w) return;

		WeeklyAlert::create(['lab_id' => 10, 'start_date' => date('Y-m-d'), 'end_date' => date('Y-m-d', strtotime('+4 days'))]);

	}

	public static function edarp_weekly_activity()
	{
		$users = DB::table('musers')->where('edarpalert', 1)->get();

		$currentdaydisplay =date('d-M-Y');
		$weekstartdisplay =date("d-M-Y",strtotime('-4 days'));
		$labname = Lab::find(10)->labname ?? '';

		$row = WeeklyAlert::where(['lab_id' => 10, 'end_date' => date('Y-m-d')])->first();
		if(!$row) return;

		foreach ($users as $user) {

			$message = 
			" Hi {$user->name}\nWEEKLY EID/VL REPORT - {$weekstartdisplay} - {$currentdaydisplay}\n{$labname}\nEID\nSamples Received - {$row->eid_samples_received}\nTotal Tests Done - {$row->eid_total_tests}\nIn Process Samples - {$row->eid_in_process_samples}\nWaiting (Testing) Samples - {$row->eid_pending_samples}\nResults Dispatched - {$row->eid_dispatched_samples}\nLAB TAT => {$row->eid_lab_tat}\nOldest Sample In Queue - {$row->eid_oldest_in_queue}\n";
			$message .=
			"VL\nSamples Received - {$row->vl_samples_received}\nTotal Tests Done - {$row->vl_total_tests}\nIn Process Samples - {$row->vl_in_process_samples}\nWaiting (Testing) Samples - {$row->vl_pending_samples}\nResults Dispatched - {$row->vl_dispatched_samples}\nLAB TAT => {$row->vl_lab_tat}\nOldest Sample In Queue - {$row->vl_oldest_in_queue}";

			\App\Common::sms($user->mobile, $message);
		}
	}

	public static function send_weekly_activity()
	{
		$day_of_week = date('N');
		if($day_of_week == 5) $sub_days = 0;
		else if($day_of_week > 5){
			$sub_days = 7 - $day_of_week;
		}else{
			die();
		}
		$today = date('Y-m-d', strtotime('-' . $sub_days . ' days'));
		$weekstartdate = date('Y-m-d', strtotime('-' . ($sub_days+4) . ' days'));

		$eid = self::weeklylabactivity('eid', $today, $weekstartdate);
		$vl = self::weeklylabactivity('vl', $today, $weekstartdate);

		$users = DB::table('musers')->where('weeklyalert', 1)->get();

		$currentdaydisplay =date('d-M-Y', strtotime($today));
		$weekstartdisplay =date("d-M-Y",strtotime($weekstartdate));
		$labname = Lab::find(env('APP_LAB'))->labname ?? '';

		foreach ($users as $user) {

			$message = 
			" Hi {$user->name}\nWEEKLY EID/VL REPORT - {$weekstartdisplay} - {$currentdaydisplay}\n{$labname}\nEID\nSamples Received - {$eid['numsamplesreceived']}\nTotal Tests Done - {$eid['tested']}\nTaqman Tests - {$eid['roche_tested']}\nAbbott Tests - {$eid['abbott_tested']}\nC8800 Tests - {$eid['c8800_tested']}\nIn Process Samples - {$eid['inprocess']}\nWaiting (Testing) Samples - {$eid['pendingresults']}\nResults Dispatched - {$eid['dispatched']}\nLAB TAT => {$eid['tat']}\nOldest Sample In Queue - {$eid['oldestinqueuesample']}\n";
			$message .=
			"VL\nSamples Received - {$vl['numsamplesreceived']}\nTotal Tests Done - {$vl['tested']}\nTaqman Tests - {$vl['roche_tested']}\nAbbott Tests - {$vl['abbott_tested']}\nC8800 Tests - {$vl['c8800_tested']}\nPantha Tests - {$vl['pantha_tested']}\nIn Process Samples - {$vl['inprocess']}\nWaiting (Testing) Samples - {$vl['pendingresults']}\nResults Dispatched - {$vl['dispatched']}\nLAB TAT => {$vl['tat']}\nOldest Sample In Queue - {$vl['oldestinqueuesample']}";

			\App\Common::sms($user->mobile, $message);
		}
	}

	public static function weeklylabactivity($type, $today, $weekstartdate)
	{
		ini_set('memory_limit', '-1');
		$classes = Synch::$synch_arrays[$type];
		$sample_class = $classes['sample_class'];
		$sampleview_class = $classes['sampleview_class'];
		$view_table = $classes['view_table'];
		$worksheets_table = $classes['worksheets_table'];

		$data['smsfoot'] = \App\Lab::find(env('APP_LAB'))->labname ?? '';
		$data['testtype'] = 1;

		if($type == 'vl') $data['testtype'] = 2;

		/*$today = date("Y-m-d");
		$weekstartdate= date ( "Y-m-d", strtotime ('-4 days') );

		$currentdaydisplay =date('d-M-Y');
		$weekstartdisplay =date("d-M-Y",strtotime($weekstartdate));

		$data['currentdaydisplay'] = $currentdaydisplay;
		$data['weekstartdisplay'] = $weekstartdisplay;*/

		$minimum_date= date ( "Y-m-d", strtotime ('-1 year') );

		$data['numsamplesreceived'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereBetween('datereceived', [$weekstartdate, $today])
								->where('site_entry', '!=', 2)
								->where(['flag' => 1, 'parentid' => 0, 'lab_id' => env('APP_LAB', null)])
								->first()->totals;

		$data['roche_tested'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->join($worksheets_table, "{$view_table}.worksheet_id", '=', "{$worksheets_table}.id")
						->where('machine_type', 1)
						->where('site_entry', '!=', 2)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => env('APP_LAB', null)])
						->whereBetween('datetested', [$weekstartdate, $today])
						->first()->totals;

		$data['abbott_tested'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->join($worksheets_table, "{$view_table}.worksheet_id", '=', "{$worksheets_table}.id")
						->where('machine_type', 2)
						->where('site_entry', '!=', 2)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => env('APP_LAB', null)])
						->whereBetween('datetested', [$weekstartdate, $today])
						->first()->totals;

		$data['c8800_tested'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->join($worksheets_table, "{$view_table}.worksheet_id", '=', "{$worksheets_table}.id")
						->where('machine_type', 3)
						->where('site_entry', '!=', 2)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => env('APP_LAB', null)])
						->whereBetween('datetested', [$weekstartdate, $today])
						->first()->totals;

		$data['pantha_tested'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->join($worksheets_table, "{$view_table}.worksheet_id", '=', "{$worksheets_table}.id")
						->where('machine_type', 4)
						->where('site_entry', '!=', 2)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => env('APP_LAB', null)])
						->whereBetween('datetested', [$weekstartdate, $today])
						->first()->totals;

		$data['tested'] = $data['roche_tested'] + $data['abbott_tested'] + $data['c8800_tested'] + $data['pantha_tested'];

		$data['inprocess'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->join($worksheets_table, "{$view_table}.worksheet_id", '=', "{$worksheets_table}.id")
						->where('status_id', 1)
						->where('site_entry', '!=', 2)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => env('APP_LAB', null)])
						->first()->totals;


		$samples = $sampleview_class::select('datereceived', 'datedispatched')
						->where('site_entry', '!=', 2)
						->where('batch_complete', 1)
						->where('repeatt', 0)
						->whereBetween('datetested', [$weekstartdate, $today])
						->get();

		$sample_count = $samples->count();

		$tat = 0;

		foreach ($samples as $sample) {
			$tat += \App\Common::get_days($sample->datereceived, $sample->datedispatched);
		}
		$data['tat'] = round(@($tat / $sample_count), 1);

		$data['dispatched'] = $sample_count;

		$data['pendingresults'] = $sampleview_class::selectRaw('count(id) as totals')
								->where('site_entry', '!=', 2)
								->whereNull('worksheet_id')
								->whereNull('datedispatched')
								->whereRaw("(result is null or result=0)")
								->where(['receivedstatus' => 1, 'flag' => 1, 'input_complete' => 1, 'lab_id' => env('APP_LAB', null)])
								->first()->totals;

		$mindate = $sampleview_class::selectRaw('MIN(datereceived) as mindate')
								->where('datereceived', '>', $minimum_date)
								->whereNull('worksheet_id')
								->whereNull('approvedby')
								->whereNull('datedispatched')
								// ->where('receivedstatus', '!=', 2)
								->where('site_entry', '!=', 2)
								->whereRaw("(result is null or result=0)")
								->where(['receivedstatus' => 1, 'flag' => 1, 'input_complete' => 1, 'lab_id' => env('APP_LAB', null)])
								->first()->mindate;

		$data['oldestinqueuesample'] = \App\Common::get_days($mindate, $today);
		return $data;
	}

	public static function send_weekly_backlog()
	{
		$currentdaydisplay =date('d-M-Y');
		$lab = \App\Lab::where('id', '=', env('APP_LAB'))->first()->labname;
		$logs = self::get_backlogs();
    	
    	$users = DB::table('musers')->where('weeklyalert', 1)->get();

		foreach ($users as $user) {

			$message = "Hi ".$user->name."\n"." BACK LOG ALERT AS OF ".$currentdaydisplay." " . $lab."\n". " EID "."\n"." Samples Logged in NOT in Worksheet : ". $logs->pendingeidsamples."\n"." Samples In Process : ".$logs->totaleidsamplesrun."\n"." VL "."\n". " Samples Logged in and NOT in Worksheet :".$logs->pendingvlsamples."\n"." Samples In Process:".$logs->totalvlsamplesrun;


			\App\Common::sms($user->mobile, $message);
		}
	}

	public static function get_backlogs(){    	
    	/**** Total samples run ****/
    	$totaleidsamplesrun = SampleView::selectRaw("count(*) as samples_run")
    								->join('worksheets', 'worksheets.id', '=', 'samples_view.worksheet_id')
    								->where('site_entry', '!=', 2)
    								->where(['samples_view.lab_id' => env('APP_LAB'), 'receivedstatus' => 1])
    								->where('worksheets.status_id', '<', 3)->first()->samples_run;
    	$totalvlsamplesrun = ViralsampleView::selectRaw("count(*) as samples_run")
    								->join('viralworksheets', 'viralworksheets.id', '=', 'viralsamples_view.worksheet_id')
    								->where('site_entry', '!=', 2)
    								->where(['viralsamples_view.lab_id' => env('APP_LAB'), 'receivedstatus' => 1])
    								->where('viralworksheets.status_id', '<', 3)->first()->samples_run;

    	/**** Samples pending results ****/
    	$pendingeidsamples = SampleView::selectRaw("count(*) as pending_samples")
							    	->whereNull('worksheet_id')
    								->whereNull('approvedby')->whereRaw("YEAR(datereceived) > 2015")
    								->whereRaw("((result IS NULL ) OR (result = 0 ))")
    								->where('site_entry', '!=', 2)
    								->where(['lab_id' => env('APP_LAB'), 'repeatt' => 0, 'receivedstatus' => 1, 'input_complete' => 1])
    								->where('flag', '=', 1)->first()->pending_samples;
    	$pendingvlsamples = ViralsampleView::selectRaw("count(*) as pending_samples")
							    	->whereNull('worksheet_id')
    								->whereNull('approvedby')->whereRaw("YEAR(datereceived) > 2015")
    								->whereRaw("((result IS NULL ) OR (result ='0' ) OR (result !='Collect New Sample') )")
    								->where('sampletype', '>', 0)
    								->where('site_entry', '!=', 2)
    								->where(['lab_id' => env('APP_LAB'), 'repeatt' => 0, 'receivedstatus' => 1, 'input_complete' => 1])
    								->where('flag', '=', 1)->first()->pending_samples;

    	return (object)[
    					'totaleidsamplesrun' => $totaleidsamplesrun,
						'totalvlsamplesrun' => $totalvlsamplesrun,
						'pendingeidsamples' => $pendingeidsamples,
						'pendingvlsamples' => $pendingvlsamples
					];
	}




    public static function machakos_edarp($batch_id=null)
    {
        ini_set('memory_limit', "-1");
        $prophylaxis = \DB::table('viralregimen')->get();
        $justifications = \DB::table('viraljustifications')->orderBy('rank_id', 'asc')->where('flag', 1)->get();

        $min_date = date('Y-m-d', strtotime('-3 weeks'));
        // $min_date = date('Y-m-d', strtotime('-9 months'));
        $samples = ViralsampleView::join('view_facilitys', 'view_facilitys.id', '=', 'viralsamples_view.facility_id')
                ->select('viralsamples_view.*')
                ->where(['repeatt' => 0, 'county_id' => 17])                
                ->when(true, function($query) use($batch_id, $min_date){
                    if($batch_id) return $query->where('batch_id', $batch_id);
                    return $query->where('created_at', '>', $min_date);
                })
                ->whereNull('receivedstatus')
                ->whereRaw("(time_sent_to_edarp IS NULL OR edarp_error like '%400%')")
                // ->whereNull('time_sent_to_edarp')
                ->get();

        $client = new Client(['base_uri' => 'http://41.203.216.114:81/nascop/vl/receive']);
        foreach ($samples as $sample) {
            $s = Viralsample::find($sample->id);
            // if($s->time_sent_to_edarp) continue;

            $post_data = [
                    'lab' => "10",
                    'specimenlabelID' => '',
                    'batchno' => $sample->batch_id,
                    'patient_identifier' => $sample->patient,
                    'dob' => $sample->dob,
                    'mflCode' => $sample->facilitycode,
                    'sex' => substr($sample->gender, 0, 1),
                    'pmtct' => $sample->pmtct,
                    'sampletype' => $sample->sampletype,
                    'datecollected' => $sample->datecollected,
                    'artinitiationdate' => $sample->initiation_date,
                    'prophylaxis' => $sample->get_prop_name($prophylaxis, 'prophylaxis', 'code'),
                    'regimenline' => 1,
                    'justification' => $sample->get_prop_name($justifications, 'justification', 'rank_id'),
                    'receivedstatus' => '',
                    'datedispatched' => null,
                ];

            try {
                $response = $client->request('post', '', [
                    // 'debug' => true,
                    'timeout' => 3,
                    'http_errors' => false,
                    'verify' => false,
                    'json' => $post_data,
                ]);   
                $body = json_decode($response->getBody());
                $s->time_sent_to_edarp = date('Y-m-d H:i:s');
                $s->edarp_error = $body[0]->message ?? $body;
                $s->save();
                /*if($response->getStatusCode() > 399){
                    $s->edarp_error = $body[0] ?? $body;
                    $s->save();
                    // print_r($post_data);
                    // return null;
                }
                else if(isset($body[0]->status_code) && in_array($body[0]->status_code, [300])){
                    $s->time_sent_to_edarp = date('Y-m-d H:i:s');
                    // $s->edarp_error = $body;
                    $s->save();
                }
                else{
                    $s->time_sent_to_edarp = date('Y-m-d H:i:s');
                    $s->edarp_error = $body[0] ?? $body;
                    $s->save();

                } */         
            } catch (\Exception $e) {
		dd($e);                
            }
        }
    }

	public static function send_failed_edarp_samples()
	{
        $min_date = date('Y-m-d', strtotime('-5 months'));
        $samples = ViralsampleView::join('view_facilitys', 'view_facilitys.id', '=', 'viralsamples_view.facility_id')
                ->select('viralsamples_view.*')
                ->where(['repeatt' => 0, 'county_id' => 17])                
                /*->when(true, function($query) use($batch_id, $min_date){
                    if($batch_id) return $query->where('batch_id', $batch_id);
                    return $query->where('created_at', '>', $min_date);
                })*/
                ->where('created_at', '>', $min_date)
                ->whereNotNull('time_sent_to_edarp')
                ->where('edarp_error', 'like', '%400%')
                ->get();

        $mail_array = ["David@edarp.org", "Jkarimi@edarp.org", "WilsonNdungu@edarp.org", "Chris@edarp.org", "Administrator@edarp.org", "mutewa@edarp.org", "Muma@edarp.org", "tngugi@clintonhealthaccess.org", "Peter@edarp.org"];

        if(!$samples->count()) return;

        $file_path = storage_path('app/batches/vl/samples-that-failed-sending-to-edarp.pdf');

        $mpdf = new Mpdf();
        $data['samples'] = $samples;
        $view_data = view('exports.mpdf_edarp_not_sent', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($file_path, \Mpdf\Output\Destination::FILE);

        // $mail_array = ['joel.kithinji@dataposit.co.ke', 'joelkith@gmail.com'];

        Mail::to($mail_array)->send(new EdarpMachakosFailed($file_path));
	}

	public static function send_edarp_delayed()
	{
        $min_date = date('Y-m-d', strtotime('-5 months'));
        // $min_date = date('Y-m-d', strtotime('-5 weeks'));
        $max_date = date('Y-m-d', strtotime('-1 weeks'));

        $samples = ViralsampleView::join('view_facilitys', 'view_facilitys.id', '=', 'viralsamples_view.facility_id')
                ->select('viralsamples_view.*')
                ->where(['repeatt' => 0, 'county_id' => 17])                
                /*->when(true, function($query) use($batch_id, $min_date){
                    if($batch_id) return $query->where('batch_id', $batch_id);
                    return $query->where('created_at', '>', $min_date);
                })*/
                ->whereBetween('created_at', [$min_date, $max_date])
                ->whereNotNull('time_sent_to_edarp')
                ->whereNull('result')
                ->get();

        $mail_array = ["David@edarp.org", "Jkarimi@edarp.org", "WilsonNdungu@edarp.org", "Chris@edarp.org", "Administrator@edarp.org", "mutewa@edarp.org", "Muma@edarp.org", "tngugi@clintonhealthaccess.org", "Peter@edarp.org"];

        if(!$samples->count()) return;

        $file_path = storage_path('app/batches/vl/delayed_machakos-samples.pdf');

        $mpdf = new Mpdf();
        $data['samples'] = $samples;
        $view_data = view('exports.mpdf_edarp_not_sent', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($file_path, \Mpdf\Output\Destination::FILE);

        // $mail_array = ['joel.kithinji@dataposit.co.ke', 'joelkith@gmail.com'];

        Mail::to($mail_array)->send(new EdarpMachakosDelayed($file_path));
	}

}
