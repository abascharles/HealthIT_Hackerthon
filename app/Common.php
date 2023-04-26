<?php

namespace App;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Mail\TestMail;
use App\Mail\CriticalResults;
use App\Mail\EidDispatch;
use App\Mail\VlDispatch;
use App\Mail\UrgentCommunication;
use App\Mail\NoDataReport;
use App\Mail\LabTracker;
use App\Mail\DelayedBatches;
use Carbon\Carbon;
use Exception;
use App\EquipmentMailingList as MailingList;
use Mpdf\Mpdf;
use DB;
use App\Synch;

class Common
{
	// public static $sms_url = 'http://sms.southwell.io/api/v1/messages';
	// public static $sms_url = 'https://api.vaspro.co.ke/v3/BulkSMS/api/create';
	public static $sms_url = 'https://mysms.celcomafrica.com/api/services/sendsms/';
	public static $sms_callback = 'http://vaspro.co.ke/dlr';
	// public static $mlab_url = 'http://197.248.10.20:3001/api/results/results';
	public static $mlab_url = 'https://api.mhealthkenya.co.ke/api/vl_results';

	public static $my_classes = [
		'eid' => [
			'misc_class' => \App\Misc::class,
			'sample_class' => \App\Sample::class,
			'sampleview_class' => \App\SampleView::class,
			'batch_class' => \App\Batch::class,
			'worksheet_class' => \App\Worksheet::class,
			'patient_class' => \App\Patient::class,


			'view_table' => 'samples_view',
			'worksheets_table' => 'worksheets',
			'batch_table' => 'batches',
			'sample_table' => 'samples',
		],

		'vl' => [
			'misc_class' => \App\MiscViral::class,
			'sample_class' => \App\Viralsample::class,
			'sampleview_class' => \App\ViralsampleView::class,
			'batch_class' => \App\Viralbatch::class,
			'worksheet_class' => \App\Viralworksheet::class,
			'patient_class' => \App\Viralpatient::class,


			'view_table' => 'viralsamples_view',
			'worksheets_table' => 'viralworksheets',
			'batch_table' => 'viralbatches',
			'sample_table' => 'viralsamples',
		],
	];

	public static function api_limit()
	{
		$h = date('H');
		if(env('APP_LAB') == 5){
			if($h < 5 || $h > 19) return 10000;
		}else{
			if($h < 5 || $h > 19) return 10000;			
		}
		return env('API_LIMIT', 30);
	}


	public static function csv_download($data, $file_name='page-data-export', $first_row=true, $save_file=false)
	{
		if(!$data) return;

		if($save_file){
			$fp = fopen(storage_path("exports/{$file_name}.csv"), 'w');
		}else{
			header('Content-Description: File Transfer');
			header('Content-Type: application/csv');
			header("Content-Disposition: attachment; filename={$file_name}.csv");
			// header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			
			$fp = fopen('php://output', 'w');			
		}
		// ob_clean();

		if($first_row){

			$first = [];

			foreach ($data[0] as $key => $value) {
				$first[] = $key;
			}
			fputcsv($fp, $first);

		}

		foreach ($data as $key => $value) {
			fputcsv($fp, $value);
		}
		// ob_flush();
		fclose($fp);
	}

    public static function test_email()
    {
        Mail::to(['joelkith@gmail.com', 'baksajoshua09@gmail.com'])->send(new TestMail());
        // Mail::to(['aaron.mbowa@dataposit.co.ke', 'joshua.bakasa@dataposit.co.ke', 'tngugi@clintonhealthaccess.org', 'joel.kithinji@dataposit.co.ke'])->send(new TestMail());
    }

    public static function get_misc_class($type)
    {
    	if($type == 'eid') return \App\Misc::class;
    	return \App\MiscViral::class;
    }

    public static function get_batch_class($type)
    {
    	if($type == 'eid') return \App\Batch::class;
    	return \App\Viralbatch::class;
    }

    public static function get_patient_class($type)
    {
    	if($type == 'eid') return \App\Patient::class;
    	return \App\Viralpatient::class;
    }

    public static function downloadCSV($data, $filename='export.csv')
    {
    	$f = fopen('php://output', 'w');
    	fputcsv($f, array_keys($data[0]));

    	foreach ($data as $line) {
    		fputcsv($f, $line);
    	}
    	header('Content-Type: application/csv');
    	header('Content-Disposition: attachment; filename="'.$filename.'";');
    	fpassthru($f);
    }

	public static function get_days($start, $finish, $with_holidays=true)
	{
		if(!$start || !$finish) return null;
		// $workingdays= self::working_days($start, $finish);
		$s = Carbon::parse($start);
		$f = Carbon::parse($finish);
		$totaldays = $s->diffInWeekdays($f, false);

		if($totaldays < 0) return null;

		$start_time = strtotime($start);
		$month = (int) date('m', $start_time);
		$holidays = self::get_holidays($month);
		
		if($with_holidays) $totaldays -= $holidays;
		if ($totaldays < 1)		$totaldays=1;
		return $totaldays;
	}

	public static function sms($recepient, $message)
	{
		$client = new Client(['base_uri' => self::$sms_url]);
		/*$response = $client->request('post', '', [
			// 'auth' => [env('SMS_USERNAME'), env('SMS_PASSWORD')],
			'http_errors' => false,
			'json' => [
				// 'sender' => env('SMS_SENDER_ID'),
                'apiKey' => env('SMS_KEY'),
                'shortCode' => env('SMS_SENDER_ID'),
				'recipient' => $recepient,
				'message' => $message,
                'callbackURL' => self::$sms_callback,
                'enqueue' => 0,
			],
		]);*/
		$response = $client->request('post', '', [
			// 'auth' => [env('SMS_USERNAME'), env('SMS_PASSWORD')],
			'http_errors' => false,
			// 'debug' => true,
			'json' => [
                'apikey' => env('SMS_KEY'),
                'shortcode' => env('SMS_SENDER_ID'),
                'partnerID' => env('SMS_PARTNER_ID'),
				'mobile' => $recepient,
				'message' => $message,
			],
		]);

		$body = json_decode($response->getBody());
		print_r($body);
        if($response->getStatusCode() > 399) dd($body);
        else if($response->getStatusCode() == 200 && $body->responses[0]->{"response-code"} == 200) return true;
        else{
        	die();
        	echo "Status Code is " . $response->getStatusCode();
        	echo $response->getBody();
        }

	}

    public static function get_holidays($month)
	{
		$holidays = [0 => 10, 1 => 1, 4 => 2, 5 => 1, 6 => 1, 8 => 1, 10 => 1, 12 => 3];
		return $holidays[$month] ?? 0;
	}

	public static function save_tat5($type)
	{
        ini_set("memory_limit", "-1");
        $batch_model = self::$my_classes[$type]['batch_class'];
		$batches = $batch_model::where(['batch_complete' => 1])->whereNull('tat5')->get();
		// $batches = $batch_model::where(['batch_complete' => 1])->get();

		foreach ($batches as $key => $batch) {
			$batch->tat5 = self::get_days($batch->datereceived, $batch->datedispatched, false);
			$batch->save();
		}
	}

	// $view_model will be \App\SampleView::class || \App\ViralsampleView::class
	// $sample_model will be \App\Sample::class || \App\Viralsample::class
	public function save_tat($view_model, $sample_model, $batch_id = NULL)
	{
        ini_set("memory_limit", "-1");
		$samples = $view_model::where(['batch_complete' => 1])
		->whereRaw("(synched = 0 or synched = 2 or (synched=1 and tat4=0))")
		->when($batch_id, function($query) use ($batch_id){
			return $query->where(['batch_id' => $batch_id]);
		})
		->get();

		foreach ($samples as $key => $sample) {
			$tat1 = self::get_days($sample->datecollected, $sample->datereceived);
			$tat2 = self::get_days($sample->datereceived, $sample->datetested);
			$tat3 = self::get_days($sample->datetested, $sample->datedispatched);
			$tat4 = self::get_days($sample->datecollected, $sample->datedispatched);
			// $tat4 = $tat1 + $tat2 + $tat3;
			$data = ['tat1' => $tat1, 'tat2' => $tat2, 'tat3' => $tat3, 'tat4' => $tat4];

			if($sample_model == "App\\Viralsample"){
				$viral_data = [
					'justification' => $this->set_justification($sample->justification),
					'prophylaxis' => $this->set_prophylaxis($sample->prophylaxis),
					'age_category' => $this->set_age_cat($sample->age),
				];
				$viral_data = array_merge($viral_data, $this->set_rcategory($sample->result, $sample->repeatt));
				$data = array_merge($data, $viral_data);				
			}
			if($sample->synched == 1) $data['synched'] = 2;
			$sample_model::where('id', $sample->id)->update($data);
		}
	}



	// $view_model will be \App\SampleView::class || \App\ViralsampleView::class
	// $sample_model will be \App\Sample::class || \App\Viralsample::class
	public function compute_tat($view_model, $sample_model)
	{
        ini_set("memory_limit", "-1");
        // $offset_value = 50000;
        $offset_value = 0;
        while(true){

			$samples = $view_model::where(['batch_complete' => 1])
			->limit(5000)->offset($offset_value)
			->get();
			if($samples->isEmpty()) break;

			foreach ($samples as $key => $sample) {
				$tat1 = self::get_days($sample->datecollected, $sample->datereceived);
				$tat2 = self::get_days($sample->datereceived, $sample->datetested);
				$tat3 = self::get_days($sample->datetested, $sample->datedispatched);
				$tat4 = self::get_days($sample->datecollected, $sample->datedispatched);
				// $tat4 = $tat1 + $tat2 + $tat3;
				$data = ['tat1' => $tat1, 'tat2' => $tat2, 'tat3' => $tat3, 'tat4' => $tat4];

				if($sample_model == "App\\Viralsample"){
					$viral_data = [
						'justification' => $this->set_justification($sample->justification),
						'prophylaxis' => $this->set_prophylaxis($sample->prophylaxis),
						'age_category' => $this->set_age_cat($sample->age),
					];
					$viral_data = array_merge($viral_data, $this->set_rcategory($sample->result, $sample->repeatt));	
					$data = array_merge($data, $viral_data);				
				}
				$sample_model::where('id', $sample->id)->update($data);
			}
	        $offset_value += 5000;
			echo "Completed clean at {$offset_value} " . date('d/m/Y h:i:s a', time()). "\n";
        }
	}



	// $view_model will be \App\SampleView::class || \App\ViralsampleView::class
	// $sample_model will be \App\Sample::class || \App\Viralsample::class
	public function compute_tat_sample($view_model, $sample_model, $sample_id=null)
	{
        ini_set("memory_limit", "-1");
        $offset_value = 0;

        $sample = $view_model::find($sample_id);

		$tat1 = self::get_days($sample->datecollected, $sample->datereceived);
		$tat2 = self::get_days($sample->datereceived, $sample->datetested);
		$tat3 = self::get_days($sample->datetested, $sample->datedispatched);
		$tat4 = self::get_days($sample->datecollected, $sample->datedispatched);
		// $tat4 = $tat1 + $tat2 + $tat3;
		$data = ['tat1' => $tat1, 'tat2' => $tat2, 'tat3' => $tat3, 'tat4' => $tat4];

		if($sample_model == "App\\Viralsample"){
			$viral_data = [
				'justification' => $this->set_justification($sample->justification),
				'prophylaxis' => $this->set_prophylaxis($sample->prophylaxis),
				'age_category' => $this->set_age_cat($sample->age),
			];
			$viral_data = array_merge($viral_data, $this->set_rcategory($sample->result, $sample->repeatt));	
			$data = array_merge($data, $viral_data);				
		}
		$sample_model::where('id', $sample->id)->update($data);

		dd($data);
	}


	public static function check_worklist($view_model, $worklist_id=null)
	{	
		if(!$worklist_id) return null;
        $samples = $view_model::where('worksheet_id', $worklist_id)
        	->where('site_entry', 2)
        	->whereNull('result')
        	->get();

        if($samples->isEmpty()){
        	$worklist = \App\Worklist::find($worklist_id);
        	if(!$worklist) return;
        	$worklist->status_id = 3;
        	$worklist->pre_update();
        }
	}

	public static function input_complete_batches($type)
	{
		if($type == 'eid'){
			$batch_model = \App\Batch::class;
		}else{
			$batch_model = \App\Viralbatch::class;
		}
		$batch_model::where(['input_complete' => false])->update(['input_complete' => true]);
		$batch_model::whereNull('input_complete')->update(['input_complete' => true]);
		return "Batches of {$type} have been marked as input complete";
	}

	public static function check_batches($type)
	{
		if($type == 'eid'){
			$batch_model = \App\Batch::class;
			$misc_model = \App\Misc::class;
			$sample_model = \App\Sample::class;
		}else{
			$batch_model = \App\Viralbatch::class;
			$misc_model = \App\MiscViral::class;
			$sample_model = \App\Viralsample::class;
		}

		$sample_model::whereNull('repeatt')->update(['repeatt' => 0]);

		$batches = $batch_model::select('id')->where(['input_complete' => true, 'batch_complete' => 0])->get();
		foreach ($batches as $key => $batch) {
			$str = $misc_model::check_batch($batch->id);
			// if($str) echo $str . "\n";
		}
	}

	public static function delete_delayed_batches($type)
	{
		$batch_model = self::get_batch_class($type);
        $min_time = date('Y-m-d', strtotime("-14 days"));
        if(env('APP_LAB') == 3) $min_time = date('Y-m-d', strtotime("-28 days"));

		$batches = $batch_model::where(['site_entry' => 1, 'batch_complete' => 0, 'lab_id' => env('APP_LAB')])->where('created_at', '<', $min_time)->whereNull('datereceived')->whereNull('datedispatched')->get();

		foreach ($batches as $batch) {
			$batch->batch_delete();
		}
	}

    public static function delete_folder($path)
    {
        if(!ends_with($path, '/')) $path .= '/';
        $files = scandir($path);
        if(!$files) rmdir($path);
        else{
            foreach ($files as $file) {
            	if($file == '.' || $file == '..') continue;
            	$a=true;
                if(is_dir($path . $file)) self::delete_folder($path . $file);
                else{
                	unlink($path . $file);
                }              
            }
            rmdir($path);
        }
    }

    public static function dispatch_batch($batch, $view_name=null)
    {
    	$facility = $batch->facility; 
    	
        $mail_array = array('joelkith@gmail.com', 'tngugi@clintonhealthaccess.org', 'baksajoshua09@gmail.com');
        if(env('APP_ENV') == 'production') $mail_array = $facility->email_array;
        if(!$mail_array) return null;

        if(get_class($batch) == "App\\Batch") $mail_class = EidDispatch::class; 

        if(get_class($batch) == "App\\Viralbatch") $mail_class = VlDispatch::class;

        try {
        	if($view_name) $new_mail = new $mail_class($batch, $view_name);
        	else{
        		$new_mail = new $mail_class($batch);
        	}
        	Mail::to($mail_array)
        	// ->bcc(['joel.kithinji@dataposit.co.ke', 'joshua.bakasa@dataposit.co.ke'])
        	->send($new_mail);
        	// $batch->save();
        } catch (Exception $e) {
        	
        }
    }

    public static function dispatch_results($type = 'eid')
    {
    	ini_set('memory_limit', "-1");
		if($type == 'eid'){
			$batch_model = \App\Batch::class;
		}else{
			$batch_model = \App\Viralbatch::class;
		}

		$min_date = date('Y-m-d', strtotime('-1 month'));

		$batches = $batch_model::where('batch_complete', 1)
		->where('sent_email', 0)
		->where('datedispatched', '>', $min_date)
		->get();

		foreach ($batches as $batch) {

            $batch->sent_email = true;
            $batch->dateemailsent = date('Y-m-d');
            $batch->save();

		 	self::dispatch_batch($batch);
		} 
    }

    public static function dup_worksheet_rows(&$doubles, &$sample_array, $sample_id, $interpretation)
    {
    	if(is_numeric($sample_id) && $sample_id > 100){
	        if(isset($sample_array[$sample_id])){
	            $doubles[] = ['duplicate lab id' => $sample_id, 'duplicate result' => $sample_array[$sample_id]];
	            $doubles[] = ['duplicate lab id' => $sample_id, 'duplicate result' => $interpretation];
	        }else{
	            $sample_array[$sample_id] = $interpretation;
	        }
        }
    }

    public static function create_facility_users()
    {
    	$facilities = \App\Facility::whereRaw("id not in (select facility_id from users where user_type_id = 5)")->get();
    	foreach ($facilities as $facility) {
    		$u = \App\User::create([
                'user_type_id' => 5,
                'surname' => '',
                'oname' => '',
                'lab_id' => env('APP_LAB'),
                'facility_id' => $facility->id,
                'email' => 'facility' . $facility->id . '@nascop-lab.com',
                'password' => encrypt($facility->name)
    		]);
    	}

    	$facilities = \App\Facility::whereRaw("id not in (select facility_id from facility_contacts)")->get();
    	foreach ($facilities as $facility) {
	        $contact_array = ['telephone', 'telephone2', 'fax', 'email', 'PostalAddress', 'contactperson', 'contacttelephone', 'contacttelephone2', 'physicaladdress', 'G4Sbranchname', 'G4Slocation', 'G4Sphone1', 'G4Sphone2', 'G4Sphone3', 'G4Sfax', 'ContactEmail'];

	        $contact = new FacilityContact();
	        $contact->fill($facility->only($contact_array));
	        $contact->facility_id = $facility->id;
	        $contact->save();
    	}
    }

    public static function nhrl($type)
    {
    	ini_set('memory_limit', "-1");

    	$batch_model = self::$my_classes[$type]['batch_class'];
    	$sample_model = self::$my_classes[$type]['sample_class'];

    	/*$batches = $batch_model::where(['synched' => 5])->whereIn('lab_id', [10])->get();

    	foreach ($batches as $batch) {
    		$sample = $sample_model::where(['batch_id' => $batch->id, 'synched' => 5])->first();
    		if(!$sample){
	    		$batch->synched=0;
	    		$batch->save();    			
    		}
    	}*/

    	$batches = $batch_model::where(['batch_complete' => 2])->whereIn('lab_id', [7, 10])->update(['batch_complete' => 1]);

    	$batches = $batch_model::where(['synched' => 5])->whereIn('lab_id', [7, 10])->get();

    	foreach ($batches as $batch) {
    		$sample = $sample_model::where(['batch_id' => $batch->id, 'synched' => 5])->update(['synched' => 0]);
    		$batch->synched=0;
    		$batch->save();   
    	}
    }

    public static function transfer_delayed_samples($type, $not_received=true)
    {
    	ini_set('memory_limit', "-1");

    	$batch_model = self::$my_classes[$type]['batch_class'];
    	$batch_table = self::$my_classes[$type]['batch_table'];

    	$sample_class = self::$my_classes[$type]['sample_class'];
    	$sample_table = self::$my_classes[$type]['sample_table'];

    	$where_raw = '';

    	if($type == 'eid'){
    		if(in_array(env('APP_LAB'), \App\Lookup::$double_approval)){
    			$where_raw = "( receivedstatus=2 OR  (result > 0 AND (repeatt = 0 or repeatt is null) AND approvedby IS NOT NULL AND approvedby2 IS NOT NULL) )";
    		}
    		else{
    			$where_raw = "( receivedstatus=2 OR  (result > 0 AND (repeatt = 0 or repeatt is null) AND approvedby IS NOT NULL) )";
    		}
    	}
    	else{
    		if(in_array(env('APP_LAB'), \App\Lookup::$double_approval)){
    			$where_raw = "( (receivedstatus=2 and repeatt=0) OR  (result IS NOT NULL AND result != 'Failed' AND result != '' AND (repeatt = 0 or repeatt is null) AND ((approvedby IS NOT NULL AND approvedby2 IS NOT NULL) or (dateapproved IS NOT NULL AND dateapproved2 IS NOT NULL)) ))";
    		}
    		else{
    			$where_raw = "( (receivedstatus=2 and repeatt=0) OR  (result IS NOT NULL AND result != 'Failed' AND result != '' AND (repeatt = 0 or repeatt is null) AND (approvedby IS NOT NULL OR dateapproved IS NOT NULL)) )";
    		}
    	}


        $batches = $batch_model::selectRaw("{$batch_table}.*, COUNT({$sample_table}.id) AS `samples_count`")
            ->join("{$sample_table}", "{$batch_table}.id", '=', "{$sample_table}.batch_id")
            ->where(['batch_complete' => 0, "{$batch_table}.lab_id" => env('APP_LAB')])
            ->whereRaw($where_raw)
            ->groupBy("{$batch_table}.id")
            // ->having('samples_count', '>', 0)
            ->havingRaw("COUNT({$sample_table}.id) > 0")
            ->get();

    	foreach ($batches as $batch) {
    		if($not_received) $samples = $sample_class::where(['batch_id' => $batch->id])->whereNull('receivedstatus')->get();
    		else{
    			$samples = $sample_class::where(['batch_id' => $batch->id, 'repeatt' => 0])->whereNull('result')->get();
    		}
    		if($samples->count() > 0){
		        unset($batch->samples_count);
    			$sample_ids = $samples->pluck('id')->toArray();
    			echo "{$type} batch {$batch->id} \n ";
    			$batch->transfer_samples($sample_ids, 'new_facility');
    		}
    	}
    }

    public static function delayed_samples_notification($type)
    {
    	ini_set('memory_limit', "-1");

    	$sampleview_class = self::$my_classes[$type]['sampleview_class'];
    	$sample_class = self::$my_classes[$type]['sample_class'];
    	$batch_class = self::$my_classes[$type]['batch_class'];

    	if($type == 'eid'){
    		$days = 7;
    	}
    	else{
    		$days = 21;
    	}
    	$start_date = date('Y-m-d', strtotime('-' . ($days-1) . ' days'));
    	$end_date = date('Y-m-d', strtotime('-' . ($days+1) . ' days'));

    	$batches = $batch_class::with(['facility'])
    		->whereBetween('created_at', [$start_date, $end_date])
    		// ->where('created_at', '<', $start_date)
    		->whereNull('datereceived')
    		->get();

    	$mail_array = User::where('receive_notification', true)->where('user_type_id', '!=', 5)->get()->pluck('email')->toArray();

    	if($batches->count()){
    		$new_mail = new DelayedBatches($batches, $type);
			Mail::to($mail_array)->send($new_mail);
    	}
    }


    public static function reject_delayed_samples($type)
    {
    	ini_set('memory_limit', "-1");

    	if($type == 'eid'){
    		$days = 14;
    		$rej = 18;
    	}
    	else{
    		$days = 28;
    		// $rej = 17;
    		$rej = 23;
    		$rejectedreason = DB::table('viralrejectedreasons')->where('id', $rej)->first();
    		if(!$rejectedreason){
    			$name = 'Delayed Sample Shipment';
    			DB::table('viralrejectedreasons')->insert(['id' => $rej, 'name' => $name, 'alias' => $name]);
    		}
    	}

    	$sampleview_class = self::$my_classes[$type]['sampleview_class'];
    	$sample_class = self::$my_classes[$type]['sample_class'];
    	$sample_table = self::$my_classes[$type]['sample_table'];

        $samples = $sampleview_class::whereNull('receivedstatus')
	        ->when((env('APP_LAB') == 1 && $type == 'vl'), function($query){
	        	return $query->select('viralsamples_view.*')
		        	->join('view_facilitys', 'view_facilitys.id', '=', 'viralsamples_view.facility_id')
		        	->where('county_id', '!=', 17);
	        })
        	->where(['batch_complete' => 0, 'lab_id' => env('APP_LAB')])
        	->where('created_at', '<', date('Y-m-d H:i:s', strtotime("-{$days} days")))
            ->get();

        $sample_ids = $samples->pluck(['id'])->toArray();
        $sample_class::whereIn('id', $sample_ids)->update(['receivedstatus' => 2, 'rejectedreason' => $rej, 'updated_at' => date('Y-m-d H:i:s')]);
    }

	public static function fix_no_age($type)
	{
    	ini_set('memory_limit', "-1");
		if($type == 'eid'){
			$sample_model = \App\Sample::class;
			$view_model = \App\SampleView::class;
			$func_name = 'calculate_age';
		}else{
			$sample_model = \App\Viralsample::class;
			$view_model = \App\ViralsampleView::class;
			$func_name = 'calculate_viralage';
		}

		$samples = $view_model::select('id', 'dob', 'datecollected')
								->whereNotNull('dob')
								->whereRaw("(age is null or age=0)")
								->get();

		foreach ($samples as $sample) {
			$s = $sample_model::find($sample->id);
			$s->age = \App\Lookup::$func_name($sample->datecollected, $sample->dob);
			$s->pre_update();
		}
	}

	public static function fix_kisumu_approval($type)
	{
		$worksheet_class = Synch::$synch_arrays[$type]['worksheet_class'];

		$worksheets = $worksheet_class::where('datereviewed2', '>', date('Y-m-d', strtotime('-6 months')))
			->whereNotNull('datereviewed2')->whereNull('datereviewed')
			->get();

		foreach ($worksheets as $key => $worksheet) {
			$worksheet->datereviewed = $worksheet->datereviewed2;
			$worksheet->save();

			$worksheet->sample()->update(['dateapproved' => $worksheet->datereviewed]);
		}
	}


	public static function worksheet_date($date_tested, $created_at, $default=null)
	{
		if(!$default) $default = date('Y-m-d');

		if((strtotime($date_tested) > strtotime($created_at)) && (strtotime($date_tested) < strtotime('now'))) return $date_tested;
		return $default;
	}

	public static function critical_results($type)
	{
		$sampleview_class = self::$my_classes[$type]['sampleview_class'];
		$view_table = self::$my_classes[$type]['view_table'];
		$dt = date('Y-m-d', strtotime('-1 day'));
		$q = 'rcategory IN (3, 4)';
		$lab = \App\Lab::find(env('APP_LAB'));
		if($type == 'eid') $q = 'result=2';

		$facilities = Facility::whereRaw("id IN (SELECT DISTINCT facility_id FROM {$view_table} WHERE datedispatched = '{$dt}' AND repeatt=0 AND {$q})")->get();

		foreach ($facilities as $key => $facility) {
			$view_facility = ViewFacility::find($facility->id);
			$facility_partners = $view_facility->facility_partners->pluck('email')->toArray();
			array_push($facility_partners, $lab->email);
			
	        $mail_array = ['joelkith@gmail.com', 'tngugi@clintonhealthaccess.org', 'baksajoshua09@gmail.com'];
	        if(env('APP_ENV') == 'production') $mail_array = $facility->email_array;
	        
	        if(!$mail_array) continue;

			$samples = $sampleview_class::whereRaw($q)
						->where(['datedispatched' => $dt, 'facility_id' => $facility->id, 'repeatt' => 0])
						->get();

			try {				
				$comm = new CriticalResults($facility, $type, $samples, $dt, $lab);
				Mail::to($mail_array)->cc($facility_partners)->send($comm);
			} catch (Exception $e) {
				dd($e->getMessage());
			}
		}
	}






	public static function no_data_report($type)
	{
		$noage = self::no_data($type, 'age');
		$nogender = self::no_data($type, 'gender');

		$comm = new NoDataReport($type, $noage, $nogender);

		Mail::to(['joel.kithinji@dataposit.co.ke', 'joshua.bakasa@dataposit.co.ke'])->send($comm);
	}

	public static function no_data($type, $param)
	{
    	ini_set('memory_limit', "-1");

		if($type == 'eid'){
			$view_model = \App\SampleView::class;
		}else{
			$view_model = \App\ViralsampleView::class;
		}

		$samples = $view_model::selectRaw("id as 'Lab ID', site_entry, facilitycode as 'MFL Code', facilityname AS 'Facility', patient, sex, age, dob, datecollected, datereceived, datetested, datedispatched ")
				->where('facility_id', '!=', 7148)
				->where('repeatt', 0)
				->where('datereceived', '>', '2018-01-01')
				->where('lab_id', env('APP_LAB'))	
				->when(true, function($query) use ($param){
					if($param == 'age') return $query->whereRaw("(age is null or age=0)");
					return $query->where('sex', 3);
				})			
				->get();

		$filename = storage_path("app/" . $type . "_no_" . $param . "_data_report.csv");
        if(file_exists($filename)) unlink($filename);

        $fp = fopen($filename, 'w');

        fputcsv($fp, ['Lab ID', 'Entry Type', 'MFL Code', 'Facility', 'Patient', 'Sex', 'Age', 'DOB', 'Date Collected', 'Date Received', 'Date Tested', 'Date Dispatched']);

        foreach ($samples as $key => $value) {
        	$val = $value->toArray();
        	// $val = get_object_vars($value);
        	$val['sex'] = $value->gender;
        	$val['site_entry'] = 'Lab Entry';
        	if($value->site_entry == 1) $val['site_entry'] = 'Site Entry';
        	fputcsv($fp, $val);
        }
        fclose($fp);
        return $filename;
	}

    // public static function send_communication()
    // {
    //     ini_set("memory_limit", "-1");
    //     $facilities = \App\Facility::where('flag', 1)->get();

    //     foreach ($facilities as $key => $facility) {
    //     	$mail_array = $facility->email_array;
    //     	// $mail_array = array('joelkith@gmail.com', 'tngugi@gmail.com', 'baksajoshua09@gmail.com');
    //     	$comm = new UrgentCommunication;
    //     	try {
	   //      	Mail::to($mail_array)->bcc(['joel.kithinji@dataposit.co.ke', 'joshua.bakasa@dataposit.co.ke', 'tngugi@gmail.com'])
	   //      	->send($comm);
	   //      } catch (Exception $e) {
        	
	   //      }
    //     	// break;
    //     }
    // }

    public static function send_communication()
    {
        $emails = \App\Email::where('sent', false)->where('time_to_be_sent', '<', date('Y-m-d H:i:s'))->get();

        foreach ($emails as $email) {
        	$email->dispatch();
        }
    }

    public static function correct_facility($mfl)
    {
        ini_set("memory_limit", "-1");

        $classes = \App\Synch::$synch_arrays;

        $facility = \App\Facility::locate($mfl)->first();

        foreach ($classes as $c) {

	        $sampleview_class = $c['sampleview_class'];
	        $patient_class = $c['patient_class'];
	        $batch_class = $c['batch_class'];

	        $samples = $sampleview_class::where('patient', 'like', "{mfl}%")->where('facility_id', '!=', $facility->id)->get();

	        foreach ($samples as $sample) {
	        	$batch = $batch_class::find($sample->batch_id);
	        	$batch->facility_id = $facility->id;
	        	$batch->pre_update();

	        	$patient = $patient_class::find($sample->patient_id);
	        	$patient->facility_id = $facility->id;
	        	$patient->pre_update();
	        }
        }
    }

    public static function find_facility_mismatch()
    {
        ini_set("memory_limit", "-1");
        $facilities = \App\OldModels\Facility::all();

        $classes = [
        	\App\Mother::class,
        	\App\Batch::class,
        	\App\Patient::class,


        	\App\Viralbatch::class,
        	\App\Viralpatient::class,
        ];

        $conflict = [];

        foreach ($facilities as $facility) {
        	$fac = \App\Facility::locate($facility->facilitycode)->first();
        	if(!$fac) continue;
        	if($facility->facilitycode == 0) continue;
         	// if($fac->id < 55000) continue;

        	if($fac->id != $facility->ID){

        		// dd([$fac->toArray(), $facility->toArray()]);

        		$new_fac = \App\ViewFacility::find($facility->ID);
        		// if($new_fac) dd([$fac->toArray(), $facility->toArray(), $new_fac->toArray()]);
        		if($new_fac){
        			$conflict[] = [
        				'id' => $new_fac->id,
        				'code' => $new_fac->facilitycode,
        				'name' => $new_fac->name,
        				'county' => $new_fac->county,
        			];
        			continue;
        		}

        		foreach ($classes as $class) {
        			$class::where(['facility_id' => $facility->ID, 'synched' => 1])->update(['facility_id' => $fac->id, 'synched' => 2]);
        			$class::where(['facility_id' => $facility->ID])->update(['facility_id' => $fac->id]);
        		}

        		if(env('APP_LAB') == 5) \App\Cd4Sample::where(['facility_id' => $facility->ID])->update(['facility_id' => $fac->id]);
        	}
        }

        dd($conflict);
    }


    public static function change_facility_id($old_id, $new_id, $also_facility=false, $created_at=false)
    {
        $classes = [
        	\App\Mother::class,
        	\App\Batch::class,
        	\App\Patient::class,


        	\App\Viralbatch::class,
        	\App\Viralpatient::class,

        	\App\CovidPatient::class,
        ];

		if($also_facility){
			\App\Facility::where(['id' => $old_id])->update(['id' => $new_id]);
			\App\User::where(['facility_id' => $old_id])->update(['facility_id' => $new_id]);
			\App\FacilityContact::where(['facility_id' => $old_id])->update(['facility_id' => $new_id]);
		}
		
		foreach ($classes as $key => $class) {
			if($key < 5) $class::where(['facility_id' => $old_id, 'synched' => 1])
				->when($created_at, function($query) use ($created_at){
					return $query->whereDate('created_at', '>', $created_at);
				})
				->update(['facility_id' => $new_id, 'synched' => 2]);

			$class::where(['facility_id' => $old_id])->update(['facility_id' => $new_id]);
		}

		if(env('APP_LAB') == 5) \App\Cd4Sample::where(['facility_id' => $old_id])->update(['facility_id' => $new_id]);
    }


    public static function change_facility_id_two($old_id, $new_id, $also_facility=false, $created_at=false)
    {
        $classes = [
        	\App\Mother::class,
        	\App\Batch::class,
        	\App\Patient::class,


        	\App\Viralbatch::class,
        	\App\Viralpatient::class,
        ];

		if($also_facility){
			\App\Facility::where(['id' => $old_id])->update(['id' => $new_id]);
			\App\User::where(['facility_id' => $old_id])->update(['facility_id' => $new_id]);
			\App\FacilityContact::where(['facility_id' => $old_id])->update(['facility_id' => $new_id]);
		}

		foreach ($classes as $key => $class) {
			if($key < 5) $class::where(['facility_id' => $old_id, 'synched' => 1])
				->when($created_at, function($query) use ($created_at){
					return $query->whereBetween('created_at', $created_at);
				})
				->update(['facility_id' => $new_id, 'synched' => 2]);

			$class::where(['facility_id' => $old_id])
				->when($created_at, function($query) use ($created_at){
					return $query->whereBetween('created_at', $created_at);
				})->update(['facility_id' => $new_id]);
		}

		if(env('APP_LAB') == 5) \App\Cd4Sample::where(['facility_id' => $old_id])->update(['facility_id' => $new_id]);
    }

    public static function send_lab_tracker($year, $previousMonth, $check_email_sent = true) {
    	echo "==> Pulling the data \n";
    	$data = Random::__getLablogsData($year, $previousMonth, $check_email_sent);
    	// dd($data);
    	if ($data) {
    		echo "==> Getting mailing list\n";
    		$mailinglist = ['sinjiri@healthit.uonbi.ac.ke']; // For testing
	        
	        if(env('APP_ENV') == 'production') 
	        	$mailinglist = MailingList::where('type', '=', 2)->pluck('email')->toArray();

	        try {				
				$comm = new LabTracker($data);
				Mail::to($mailinglist)->cc(['sinjiri@healthit.uonbi.ac.ke'])->send($comm);
	        	LabPerformanceTracker::resetemail($year, $previousMonth);
	        	LabEquipmentTracker::resetemail($year, $previousMonth);
	        	return true;
			} catch (Exception $e) {
				print_r($e->getMessage());
				return false;
			}
    	}
    	echo "==> No pending lab tracker notification found \n";
    	return false;
    }

    private static function extractConsumptionDetails($consumption, $class, $machine)
    {
    	$data = [];
    	$kits = Kits::where('machine_id', $machine)->get();
    	$type = TestType::find($consumption->testtype)->name;
    	$previousMonthConsumption = date('m', strtotime("-1 Month", strtotime($consumption->year.'-'.$consumption->month)));
    	$previousYearConsumption = date('Y', strtotime("-1 Month", strtotime($consumption->year.'-'.$consumption->month)));
    	$previousConsumption = $class::where('testtype', $consumption->testtype)
    							->where('year', $previousYearConsumption)->where('month', $previousMonthConsumption)
    							->first();
    	$fields = [
    		'begining_balance' => 'begining_balance',
    		'used' => 'used',
    		'pos' => 'positive_adjustment', 
    		'issued' => 'negative_adjustment',
    		'wasted' => 'wasted',
    		'ending' => 'ending_balance',
    		'request' => 'request'
    	];
    	foreach ($fields as $fieldkey => $field) {
			foreach ($kits as $kitkey => $kit) {
				$data[$kitkey]['kit_id'] = $kit->id;
				if ($field == 'begining_balance') {
					$column = 'ending'.$kit->alias;
	    			$data[$kitkey][$field] = $previousConsumption->$column ?? 0;
	    		} else if ($field == 'used') {
	    			$column = $fieldkey.$kit->alias;
	    			$test_factor = json_decode($kit->testFactor);
	    			$test_factor = $test_factor->$type ?? $test_factor;
	    			$factor = json_decode($kit->factor);
	    			$factor = $factor->$type ?? $factor;
	    			$data[$kitkey][$field] = round((@($consumption->tests/$test_factor)*$factor),2);
	    		} else {
	    			$column = $fieldkey.$kit->alias;
	    			$data[$kitkey][$field] = $consumption->$column;
	    		}
    		}
    	}
    	
    	return $data;
    }

    public static function transferconsumptions() {
    	$newconsumptions = [];
    	echo "==> Retrieve the abbott consumptions\n";
    	$consumptions = Abbotprocurement::get();
    	echo "\t Processing {$consumptions->count()} abbott consumptions\n";
    	foreach ($consumptions as $key => $consumption) {
    		$newconsumptions[] = [
    						'year' => $consumption->year,
    						'month' => $consumption->month,
    						'type' => $consumption->testtype,
    						'machine' => 2,
    						'tests' => $consumption->tests,
    						'datesubmitted' => $consumption->datesubmitted,
    						'submittedby' => $consumption->submittedby,
    						'lab_id' => $consumption->lab_id,
    						'comments' => $consumption->comments,
    						'issuedcomments' => $consumption->issuedcomments,
    						'approve' => $consumption->approve,
    						'disapprovereason' => $consumption->disapprovereason,
    						'details' => self::extractConsumptionDetails($consumption, Abbotprocurement::class, 2)
    					];
    	}
    	echo "\t Inserting abbott consumptions\n";
    	foreach ($newconsumptions as $key => $consumption) {
    		$details = $consumption['details'];
    		unset($consumption['details']);
    		if (Consumption::duplicate($consumption['year'], $consumption['month'], $consumption['type'], $consumption['machine'], $consumption['lab_id'])->get()->isEmpty()){
    			$insertedConsumption = Consumption::create($consumption);
	    		foreach ($details as $key => $detail) {
	    			$line = new ConsumptionDetail;
	    			$line->fill($detail);
	    			$line->consumption_id = $insertedConsumption->id;
	    			$line->save();
	    		}
    		}
    	}
    	$newconsumptions = [];
    	echo "==> Finished abbott consumptions\n";
    	
		// Finally getting the Roche consumptions
    	echo "==> Retrieve the Cobas consumptions\n";
    	$consumptions = Taqmanprocurement::get();
    	echo "\tProcessing {$consumptions->count()} Cobas consumptions\n";
    	foreach ($consumptions as $key => $consumption) {
			$newconsumptions[] = [
    						'year' => $consumption->year,
    						'month' => $consumption->month,
    						'type' => $consumption->testtype,
    						'machine' => 1,
    						'tests' => $consumption->tests,
    						'datesubmitted' => $consumption->datesubmitted,
    						'submittedby' => $consumption->submittedby,
    						'lab_id' => $consumption->lab_id,
    						'comments' => $consumption->comments,
    						'issuedcomments' => $consumption->issuedcomments,
    						'approve' => $consumption->approve,
    						'disapprovereason' => $consumption->disapprovereason,
    						'details' => self::extractConsumptionDetails($consumption, Taqmanprocurement::class, 1)
    					];
    	}
    	echo "\tInserting abbott consumptions\n";
    	foreach ($newconsumptions as $key => $consumption) {
    		$details = $consumption['details'];
    		unset($consumption['details']);
    		if (Consumption::duplicate($consumption['year'], $consumption['month'], $consumption['type'], $consumption['machine'], $consumption['lab_id'])->get()->isEmpty()){
    			$insertedConsumption = Consumption::create($consumption);
	    		foreach ($details as $key => $detail) {
	    			$line = new ConsumptionDetail;
	    			$line->fill($detail);
	    			$line->consumption_id = $insertedConsumption->id;
	    			$line->save();
	    		}
    		}
    	}
    	$newconsumptions = [];
    	echo "==> Finished abbott consumptions\n";
		
		echo "==> Delete all future consumptions\n";
		$consumptions = Consumption::where('year', date('Y', strtotime("-1 Month", strtotime(date('Y-m-d')))))->where('month', date('m', strtotime("-1 Month", strtotime(date('Y-m-d')))))->get();
		foreach ($consumptions as $key => $consumption) {
			foreach ($consumption->details as $key => $detail) {
				$detail->delete();
			}
			$consumption->delete();
		}
		echo "\t Finished deleting all future consumptions\n";
		echo "==> Completed inserting consumptions\n";
		return true;

    }

    private static function createConsumption($consumption, $machine)
    {
		if (Consumption::existing($consumption->year, $consumption->month, $consumption->type, $consumption->lab_id)->get()->isEmpty()){
			return Consumption::create([
						'month' => $consumption->month,
		    			'year' => $consumption->year,
						'type' => $consumption->testtype,
						'tests' => $consumption->tests,
						'datesubmitted' => $consumption->datesubmitted,
						'submittedby' => $consumption->submittedby,
						'lab_id' => $consumption->lab_id,
						'comments' => $consumption->comments,
						'issuedcomments' => $consumption->issuedcomments,
						'approve' => $consumption->approve,
						'disapprovereason' => $consumption->disapprovereason,
						'synched' => $consumption->synched,
						'datesynched' => $consumption->datesynched,
						'created_at' => $consumption->created_at,
						'updated_at' => $consumption->updated_at,
					]);
		}
		return false;
    }

    public static function transferdeliveries() {
    	$kits = Kits::get();
		$quarters = [1=>'(1,2,3)', 2=>'(4,5,6)', 3=>'(7,8,9)', 4=>'(10,11,12)'];
    	echo "==> Begining Deliveries transfer\n";
    	// Bringing in the abbott deliveries
    	$deliveries = Abbotdeliveries::get();
    	echo "====> Begining Abbott Deliveries transfer\n";
    	foreach ($deliveries as $key => $delivery) {
    		$insertedDelivery = self::createDelivery($delivery, 2);
    		if ($insertedDelivery){
    			foreach ($kits->where('machine_id', 2) as $key => $kit) {
	    			$lotno = $kit->alias."lotno"; $expiry = $kit->alias."expiry";
	    			$received = $kit->alias."received"; $damaged = $kit->alias."damaged";
	    			DeliveryDetail::create([
	    					'delivery_id' => $insertedDelivery->id,
	    					'kit_id' => $kit->id,
	    					'kit_type' => Kits::class,
	    					'lotno' => $delivery->$lotno,
	    					'expiry' => $delivery->$expiry,
	    					'received' => $delivery->$received,
	    					'damaged' => $delivery->$damaged,
	    				]);
	    		}
    		}
    		echo "\tTransfer abbott deliver " . $delivery->quarter ." " . $delivery->year . " complete\n";
    	}

		// Bringing in the abbott deliveries
		$deliveries = Taqmandeliveries::get();
		echo "====> Begining Roche Deliveries transfer\n";
		foreach ($deliveries as $key => $delivery) {
			$insertedDelivery = self::createDelivery($delivery, 1);
    		if ($insertedDelivery){
    			foreach ($kits->where('machine_id', 1) as $key => $kit) {
	    			$lotno = $kit->alias."lotno"; $expiry = $kit->alias."expiry";
	    			$received = $kit->alias."received"; $damaged = $kit->alias."damaged";
	    			DeliveryDetail::create([
	    					'delivery_id' => $insertedDelivery->id,
	    					'kit_id' => $kit->id,
	    					'kit_type' => Kits::class,
	    					'lotno' => $delivery->$lotno,
	    					'expiry' => $delivery->$expiry,
	    					'received' => $delivery->$received ?? 0,
	    					'damaged' => $delivery->$damaged ?? 0,
	    				]);
	    			echo "\tTransfer Roche deliver " . $delivery->quarter ." " . $delivery->year . " complete\n";
	    		}
	    	}
    		
			// $vlsamples = \App\Viralsample::selectRaw("count(if(viralworksheets.machine_type = 1, 1, null)) as `taqman`, count(if(viralworksheets.machine_type = 3, 1, null)) as `C8800`")
			// 				->join('viralworksheets', 'viralworksheets.id', '=', 'viralsamples.worksheet_id')
			// 				->whereIn('machine_type', [1,3])
			// 				->whereYear('datetested', $insertedDelivery->year)
			// 				->whereRaw("month(datetested) in " . $quarter[$insertedDelivery->quarter])->first();
			// $eidsamples = \App\Sample::selectRaw("count(if(worksheets.machine_type = 1, 1, null)) as `taqman`, count(if(worksheets.machine_type = 3, 1, null)) as `C8800`")
			// 			->join('worksheets', 'worksheets.id', '=', 'samples.worksheet_id')
			// 			->whereIn('machine_type', [1,3])
			// 			->whereYear('datetested', $insertedDelivery->year)
			// 			->whereRaw("month(datetested) in " . $quarter[$insertedDelivery->quarter])->first();
			// if ($delivery->testtype == 1)
   //  			$model = $eidsamples;
   //  		else if ($delivery->testtype == 2)
   //  			$model = $vlsamples;


			// $total = $model->taqman + $model->C8800;
			// $taqmanratio = ($model->taqman / $total);
			// $C8800ratio = ($model->C8800 / $total);

    		// foreach ($kits->where('machine_id', 1) as $key => $kit) {
    		// 	$lotno = $kit->alias."lotno"; $expiry = $kit->alias."expiry";
    		// 	$receivedalias = $kit->alias."received"; $damagedalias = $kit->alias."damaged";
    		// 	if ($model->taqman == 0) {
    		// 		$received = ($delivery->$receivedalias / 2); $damaged = ($delivery->$damagedalias / 2);
    		// 	} else {
	    	// 		$received = ($delivery->$receivedalias * $taqmanratio); $damaged = ($delivery->$damagedalias * $taqmanratio);
    		// 	}
    		// 	DeliveryDetail::create([
    		// 			'delivery_id' => $insertedDelivery->id,
    		// 			'kit_id' => $kit->id,
    		// 			'kit_type' => Kits::class,
    		// 			'lotno' => $delivery->$lotno,
    		// 			'expiry' => $delivery->$expiry,
    		// 			'received' => $received,
    		// 			'damaged' => $damaged,
    		// 		]);
    		// }

    		// foreach ($kits->where('machine_id', 3) as $key => $kit) {
    		// 	$lotno = $kit->alias."lotno"; $expiry = $kit->alias."expiry";
    		// 	$receivedalias = $kit->alias."received"; $damagedalias = $kit->alias."damaged";
    		// 	if ($model->taqman == 0) {
    		// 		$received = ($delivery->$receivedalias / 2); $damaged = ($delivery->$damagedalias / 2);
    		// 	} else {
	    	// 		$received = ($delivery->$receivedalias * $C8800ratio); $damaged = ($delivery->$damagedalias * $C8800ratio);
    		// 	}
    		// 	DeliveryDetail::create([
    		// 			'delivery_id' => $insertedDelivery->id,
    		// 			'kit_id' => $kit->id,
    		// 			'kit_type' => Kits::class,
    		// 			'lotno' => $delivery->$lotno,
    		// 			'expiry' => $delivery->$expiry,
    		// 			'received' => $received,
    		// 			'damaged' => $damaged,
    		// 		]);
    		// 	echo "\tTransfer Roche deliver " . $delivery->quarter ." " . $delivery->year . " complete\n";
    		// }
    		
		}

		echo "==> Finished Deliveries transfer\n";
		return true;
    }

    private static function createDelivery($delivery, $machine)
    {
    	if ($delivery->year == 0)
			$delivery->year = date('Y', strtotime($delivery->datereceived));
		if (Deliveries::existing( $delivery->year, $delivery->quarter, $delivery->type, $delivery->lab_id )->get()->isEmpty()){
			return Deliveries::create([
					'type' => $delivery->testtype,
					'lab_id' => $delivery->lab_id,
					'quarter' => $delivery->quarter,
					'year' => $delivery->year,
					'machine' => $machine,
					'receivedby' => $delivery->receivedby ?? $delivery->enteredby ?? 0,
					'datereceived' => $delivery->datereceived ?? $delivery->dateentered ?? date('Y-m-d'),
					'enteredby' => $delivery->enteredby ?? $delivery->receivedby ?? 0,
					'dateentered' => $delivery->dateentered ?? $delivery->datereceived ?? date('Y-m-d'),
				]);
		}
		return false;
    }

    public static function resend_lab_tracker()
    {
		$start_date = '2020-09-01';
		$end_date = '2020-11-01';
		while (strtotime($start_date) <= strtotime($end_date)) {
			self::send_lab_tracker(date('Y', strtotime($start_date)), date('m', strtotime($start_date)));
			$start_date = date('Y-m-d', strtotime('+1 month', strtotime($start_date)));
		}
    }

    public static function updateReporters()
    {
    	foreach (User::where('covid_consumption_allowed', 1)->get() as $key => $user) {
    		$user->eidvl_consumption_allowed = 1;
    		$user->save();
    	}
    	return true;
    }
}
