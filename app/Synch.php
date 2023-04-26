<?php

namespace App;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use DB;

use App\CovidConsumption;
use App\CovidConsumptionDetail;
use App\Sample;
use App\Batch;
use App\Patient;
use App\Mother;
use App\Worksheet;

use App\Viralsample;
use App\Viralbatch;
use App\Viralpatient;
use App\Viralworksheet;

use App\Facility;
use App\FacilityChange;

use App\Mail\AllocationReview;
use App\Mail\TestMail;

class Synch
{
	// public static $base = 'http://national/api/';
	public static $base = 'https://eiddash.nascop.org/api/';
	// public static $base = 'http://lab-2.test.nascop.org/api/';
	public static $p3_base = 'https://kemrinairobi.nascop.org/api/';
	// public static $cov_base = 'https://lab-covid19.health.go.ke/api';
	public static $cov_base = 'https://covid-19-kenya.org/api/';
	// public static $base = 'http://national.test/api/';
	private static $allocationReactionCounts, $users, $lab, $from, $to;
	public static $covid_inaccessible = [5,23,25];

	public static $synch_arrays = [
		'eid' => [
			'misc_class' => \App\Misc::class,
			'sample_class' => Sample::class,
			'sampleview_class' => \App\SampleView::class,
			'batch_class' => Batch::class,
			'worksheet_class' => Worksheet::class,
			'patient_class' => Patient::class,
			'view_table' => 'samples_view',
			'worksheets_table' => 'worksheets',
			'with_array' => ['batch.creator', 'patient.mother'],
		],

		'vl' => [
			'misc_class' => \App\MiscViral::class,
			'sample_class' => Viralsample::class,
			'sampleview_class' => \App\ViralsampleView::class,
			'batch_class' => Viralbatch::class,
			'worksheet_class' => Viralworksheet::class,
			'patient_class' => Viralpatient::class,
			'view_table' => 'viralsamples_view',
			'worksheets_table' => 'viralworksheets',
			'with_array' => ['batch.creator', 'patient'],
		],

		'covid' => [
			'misc_class' => MiscCovid::class,
			'sample_class' => CovidSample::class,
			'patient_class' => CovidPatient::class,
			'with_array' => ['patient.travel'],
		],
	];


	public static $update_arrays = [
		'eid' => [
			'worksheets' => [
				'class' => Worksheet::class,
				'update_url' => 'update/worksheets',
				'delete_url' => 'delete/worksheets',
			],
			'mothers' => [
				'class' => Mother::class,
				'update_url' => 'update/mothers',
				'delete_url' => 'delete/mothers',
			],
			'patients' => [
				'class' => Patient::class,
				'update_url' => 'update/patients',
				'delete_url' => 'delete/patients',
			],
			'batches' => [
				'class' => Batch::class,
				'update_url' => 'update/batches',
				'delete_url' => 'delete/batches',
			],
			'samples' => [
				'class' => Sample::class,
				'update_url' => 'update/samples',
				'delete_url' => 'delete/samples',
			],
		],

		'vl' => [
			'worksheets' => [
				'class' => Viralworksheet::class,
				'update_url' => 'update/viralworksheets',
				'delete_url' => 'delete/viralworksheets',
			],
			'patients' => [
				'class' => Viralpatient::class,
				'update_url' => 'update/viralpatients',
				'delete_url' => 'delete/viralpatients',
			],
			'batches' => [
				'class' => Viralbatch::class,
				'update_url' => 'update/viralbatches',
				'delete_url' => 'delete/viralbatches',
			],
			'samples' => [
				'class' => Viralsample::class,
				'update_url' => 'update/viralsamples',
				'delete_url' => 'delete/viralsamples',
			],
		],

		'covid' => [
			/*'worksheets' => [
				'class' => CovidWorksheet::class,
				'update_url' => 'update/covid_worksheets',
				'delete_url' => 'delete/covid_worksheets',
			],*/
			'samples' => [
				'class' => CovidSample::class,
				'update_url' => 'update/covid_samples',
				'delete_url' => 'delete/covid_samples',
			],
			'patients' => [
				'class' => CovidPatient::class,
				'update_url' => 'update/covid_patients',
				'delete_url' => 'delete/covid_patients',
			],

		],

		'allocations' => [
			'allocations' => [
				'class' => Allocation::class,
				'child_class' => AllocationDetail::class,
				'grand_child_class' => AllocationDetailsBreakdown::class,
				'update_url' => 'update/allocations',
				'delete_url' => 'delete/allocations',
			]
		]
	];

	public static $column_array = [
		'worksheets' => 'national_worksheet_id',
		'mothers' => 'national_mother_id',
		'patients' => 'national_patient_id',
		'batches' => 'national_batch_id',
		'samples' => 'national_sample_id',
		'allocations' => 'national_id'
	];

	public static function test_connection()
	{
		$client = new Client(['base_uri' => self::$base]);
		$response = $client->request('get', 'hello');
		$body = json_decode($response->getBody());
		return $body->message;
	}

	public static function synch_time()
	{
		$client = new Client(['base_uri' => self::$base]);
		try {
			$response = $client->request('get', 'time', ['timeout' => 1]);
			$body = json_decode($response->getBody());
			exec("date +%Y%m%d -s '" . $body->date . "'");
			exec("date +%T -s '" . $body->time . "'");			
		} catch (Exception $e) {
			
		}
	}

	public static function test_nascop()
	{
		$base = 'https://api.nascop.org/eid/ver2.0';
		$client = new Client(['base_uri' => $base]);
		$response = $client->request('get', '', ['timeout' => 1, 'http_errors' => false]);
		$body = json_decode($response->getBody());
		if($response->getStatusCode() < 399) return true;
		return false;
	}

	public static function clean_emails($base = 'https://api.mailgun.net/v3/nascop.or.ke/complaints', $iter=0)
	{
		// $base = 'https://api.mailgun.net/v3/nascop.or.ke/complaints';
		$client = new Client(['base_uri' => $base]);
		$response = $client->request('get', '', [
			'auth' => ['api', env('MAIL_API_KEY')],
		]);
		$body = json_decode($response->getBody());
		if($response->getStatusCode() > 399) return false;
		// dd($body);

		$emails = [];

		foreach ($body->items as $key => $value) {
			$emails[] = $value->address;
		}

		\App\Facility::whereIn('email', $emails)->update(['email' => null]);
		\App\Facility::whereIn('ContactEmail', $emails)->update(['ContactEmail' => null]);

		\App\FacilityContact::whereIn('email', $emails)->update(['email' => null]);
		\App\FacilityContact::whereIn('ContactEmail', $emails)->update(['ContactEmail' => null]);
		if($iter > 200) die();
		self::clean_emails($body->next, $iter++);
	}

	public static function login()
	{
		Cache::store('file')->forget('api_token');
		$client = new Client(['base_uri' => self::$base,'verify' => false]);
		//dd($client);
		$response = $client->request('post', 'auth/login', [
		'verify' => false,
            'http_errors' => false,
            'debug' => false,
			'headers' => [
				'Accept' => 'application/json',
			],
			'json' => [
				'email' => 'joelkith@gmail.com',
				'password' => 12345678,
			],
		]);
		//dd($response);
		$status_code = $response->getStatusCode();
		if($status_code > 399)
			return json_decode($response->getBody());

		$body = json_decode($response->getBody());
		// dd($response);
		Cache::store('file')->put('api_token', $body->token, (60*60));
		return true;
	}

	public static function covid_login()
	{
		Cache::store('file')->forget('covid_api_token');
		$client = new Client(['base_uri' => self::$cov_base]);
		if(in_array(env('APP_LAB'), self::$covid_inaccessible)) $client = new Client(['base_uri' => self::$p3_base]);

		$response = $client->request('post', 'auth/login', [
            'http_errors' => false,
            'debug' => false,
            'verify' => false,
            // 'timeout' => 2,
			'headers' => [
				'Accept' => 'application/json',
			],
			'json' => [
				'email' => env('COV_USERNAME', null),
				'password' => env('COV_PASSWORD', null),
			],
		]);
		$status_code = $response->getStatusCode();
		if($status_code > 399)
			return json_decode($response->getBody());

		$body = json_decode($response->getBody());
		// dd($body);
		Cache::store('file')->put('covid_api_token', $body->token, 60);

		// dd($body);
	}

	public static function get_token()
	{
		//if(Cache::store('file')->has('api_token')){dd('has token'); }
		//else{
		//	dd('no token');
			self::login();
		//}	
		return Cache::store('file')->get('api_token');
	}

	public static function get_covid_token()
	{
		if(Cache::store('file')->has('covid_api_token')){}
		else{
			self::covid_login();
		}
		return Cache::store('file')->get('covid_api_token');
	}

	public static function synch_eid_patients()
	{
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');

		while (true) {
			$patients = Patient::with(['mother'])->where('synched', 0)->limit(20)->get();
			if($patients->isEmpty()) break;

			$response = $client->request('post', 'insert/patients', [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'verify' => false,
				'json' => [
					'patients' => $patients->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);

			$body = json_decode($response->getBody());

			// dd($body);

			foreach ($body->patients as $key => $value) {
				$update_data = ['national_patient_id' => $value->national_patient_id, 'synched' => 1, 'datesynched' => $today,];
				Patient::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->mothers as $key => $value) {
				$update_data = ['national_mother_id' => $value->national_mother_id, 'synched' => 1, 'datesynched' => $today,];
				Mother::where('id', $value->original_id)->update($update_data);
			}
		}
	}

	public static function synch_vl_patients()
	{
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');

		while (true) {
			$patients = Viralpatient::where('synched', 0)->limit(30)->get();
			if($patients->isEmpty()) break;

			$response = $client->request('post', 'insert/viralpatients', [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'verify' => false,
				'json' => [
					'patients' => $patients->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);

			$body = json_decode($response->getBody());

			foreach ($body->patients as $key => $value) {
				$update_data = ['national_patient_id' => $value->national_patient_id, 'synched' => 1, 'datesynched' => $today,];
				Viralpatient::where('id', $value->original_id)->update($update_data);
			}
		}
	}

	public static function synch_batches($type)
	{
        ini_set("memory_limit", "-1");
		$classes = self::$synch_arrays[$type];

		$misc_class = $classes['misc_class'];
		$sample_class = $classes['sample_class'];
		$sampleview_class = $classes['sampleview_class'];
		$batch_class = $classes['batch_class'];

		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');
		$my = new $misc_class;
		$my->save_tat($sampleview_class, $sample_class);

		if($batch_class == "App\\Batch"){
			$url = 'insert/batches';
		}else{
			$url = 'insert/viralbatches';
		}

		while (true) {
			$batches = $batch_class::with(['sample.patient:id,national_patient_id,patient'])
			->where('synched', 0)->where('batch_complete', 1)->limit(20)->get();
			// dd($batches);
			if($batches->isEmpty()) break;

			$response = $client->request('post', $url, [
				'http_errors' => false,
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'verify' => false,
				'json' => [
					'batches' => $batches->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);

			$body = json_decode($response->getBody());

			if($response->getStatusCode() > 399)
			{
				echo 'Status code is ' . $response->getStatusCode();
				dd($body);
			}

			foreach ($body->batches as $key => $value) {
				$update_data = ['national_batch_id' => $value->national_batch_id, 'synched' => 1, 'datesynched' => $today,];
				$batch_class::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->samples as $key => $value) {
				$update_data = ['national_sample_id' => $value->national_sample_id, 'synched' => 1, 'datesynched' => $today,];
				$sample_class::where('id', $value->original_id)->update($update_data);
			}
		}
	}


	public static function synch_batches_odd($type)
	{
        ini_set("memory_limit", "-1");
		$classes = self::$synch_arrays[$type];

		$misc_class = $classes['misc_class'];
		$sample_class = $classes['sample_class'];
		$sampleview_class = $classes['sampleview_class'];
		$batch_class = $classes['batch_class'];

		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');
		$my = new $misc_class;
		$my->save_tat($sampleview_class, $sample_class);

		if($batch_class == "App\\Batch"){
			$url = 'insert/batches';
		}else{
			$url = 'insert/viralbatches';
		}

		$batch_ids = $sampleview_class::selectRaw("distinct batch_id")->where(['synched' => 0, 'batch_complete' => 1])->where('batch_id', 'like', "%.5%")->get()->pluck('batch_id');
		$offset = 0;

		while (true) {
			$batches = $batch_class::whereIn('id', $batch_ids)->limit(20)->offset($offset)->get();
			$offset+=20;
			// dd($batches);
			if($batches->isEmpty()) break;

			foreach ($batches as $batch) {
				foreach ($batch->sample as $sample) {
					$p = $sample->patient;
				}
			}

			$response = $client->request('post', $url, [
				'http_errors' => false,
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'verify' => false,
				'json' => [
					'batches' => $batches->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);

			$body = json_decode($response->getBody());

			if($response->getStatusCode() > 399)
			{
				dd($body);
			}

			foreach ($body->batches as $key => $value) {
				$update_data = ['national_batch_id' => $value->national_batch_id, 'synched' => 1, 'datesynched' => $today,];
				$batch_class::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->samples as $key => $value) {
				$update_data = ['national_sample_id' => $value->national_sample_id, 'synched' => 1, 'datesynched' => $today,];
				$sample_class::where('id', $value->original_id)->update($update_data);
			}
		}
	}



	public static function synch_worksheets($type)
	{
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');

		$worksheet_class = self::$synch_arrays[$type]['worksheet_class'];

		if($worksheet_class == "App\\Worksheet"){
			$url = 'insert/worksheets';
		}else{
			$url = 'insert/viralworksheets';
		}

		while (true) {
			$worksheets = $worksheet_class::where('synched', 0)->where('status_id', 3)->limit(30)->get();
			if($worksheets->isEmpty()) break;

			$response = $client->request('post', $url, [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'worksheets' => $worksheets->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);

			$body = json_decode($response->getBody());

			foreach ($body->worksheets as $key => $value) {
				$update_data = ['national_worksheet_id' => $value->national_worksheet_id, 'synched' => 1, 'datesynched' => $today,];
				$worksheet_class::where('id', $value->original_id)->update($update_data);
			}
		}
	}

	public static function synch_updates($type)
	{
		$client = new Client(['base_uri' => self::$base]);
		if($type == 'covid' && !in_array(env('APP_LAB'), self::$covid_inaccessible)) $client = new Client(['base_uri' => self::$cov_base]);
		if($type == 'covid' && in_array(env('APP_LAB'), self::$covid_inaccessible)) $client = new Client(['base_uri' => self::$p3_base]);
		$today = date('Y-m-d');

		if (in_array($type, ['eid', 'vl'])) {
			$c = self::$synch_arrays[$type];

			$misc_class = $c['misc_class'];
			$sample_class = $c['sample_class'];
			$sampleview_class = $c['sampleview_class'];

			$my = new $misc_class;
			$my->save_tat($sampleview_class, $sample_class);
		}
		
		$updates = self::$update_arrays[$type];
		
		foreach ($updates as $key => $value) {
			$update_class = $value['class'];
			if (isset($value['child_class'])) {
				$update_child_class = $value['child_class'];
				if (isset($value['grand_child_class']))
					$update_grand_child_class = $value['grand_child_class'];
			}
			$column = self::$column_array[$key];

			$sheet = $sample = $eid_patient = $allocate = false;
			if($key == 'worksheets') $sheet = true;
			if($key == 'samples') $sample = true;
			if($key == 'patients' && $type == 'eid') $eid_patient = true;
			if($key == 'allocations') $allocate = true;
			// dd($column);
			while(true){
				$models = $update_class::where('synched', 2)
										->when(($sample && in_array($type, ['eid', 'vl'])), function($query){
							                return $query->with(['batch', 'patient']);
										})
										->when(($sample && in_array($type, ['covid'])), function($query){
							                return $query->with(['patient']);
										})->when($allocate, function($query){
											return $query->with(array('details' => function($childquery){
												return $childquery->where('synched', 2);
											}, 'details.breakdowns' => function($childquery){
												return $childquery->where('synched', 2);
											}));
										})->when($sheet, function($query){
							                return $query->where('status_id', 3);
										})->limit(20)->get();
				
				if($models->isEmpty()) break;
				if($key == 'batches'){
					foreach ($models as $batch) {
						$my->save_tat($sampleview_class, $sample_class, $batch->id);
					}
				}

				$token = self::get_token();
				if($type == 'covid') $token = self::get_covid_token();

				// dd($value['update_url']);
				$response = $client->request('post', $value['update_url'], [
					'headers' => [
						'Accept' => 'application/json',
						'Authorization' => 'Bearer ' . $token,
					],
					'verify' => false,
					'json' => [
						$key => $models->toJson(),
						'lab_id' => env('APP_LAB', null),
					],

				]);

				$body = json_decode($response->getBody());
				// dd($body);
				$original_column = 'original_id';
				if ($type == 'allocations')
					$original_column = 'original_allocation_id';
				// dd($body);
				foreach ($body->$key as $row) {
					$update_data = [$column => $row->$column, 'synched' => 1, 'datesynched' => $today,];
					$update_class::where('id', $row->$original_column)->update($update_data);
					if ($type == 'allocations') {
						foreach ($row->details as $key => $new) {
							$update_child_data = ['national_id' => $new->$column, 'synched' => 1, 'datesynched' => $today];
							$update_child_class::where('id', $new->original_allocation_detail_id)->update($update_child_data);
							foreach($new->breakdowns as $new_breakdown) {
								$update_data = ['national_id' => $new_breakdown->$column, 'synched' => 1, 'datesynched' => $today];
								$update_grand_child_class::where('id', $new_breakdown->original_allocation_details_breakdown_id)->update($update_data);
							}
						}
					}
				}

				if($body->errors_array){
					foreach ($body->errors_array as $row) {
						$update_data = ['synched' => 1, 'datesynched' => $today,];
						$update_class::where('id', $row->id)->update($update_data);
						if ($type == 'allocations') {
							foreach ($row->details as $key => $new) {
								$update_child_data = ['synched' => 1, 'datesynched' => $today];
								$update_child_class::where('id', $new->id)->update($update_child_data);
								foreach($new->breakdowns as $breakdown) {
									$update_data = ['synched' => 1, 'datesynched' => $today];
									$update_grand_child_class::where('id', $breakdown->id)->update($update_data);
								}
							}
						}
					}
				}
			}			
		}
	}

	public static function synch_deletes($type)
	{
		$client = new Client(['base_uri' => self::$base]);
		if($type == 'covid' && in_array(env('APP_LAB'), self::$covid_inaccessible)) $client = new Client(['base_uri' => self::$p3_base]);
		$today = date('Y-m-d');

		$updates = self::$update_arrays[$type];

		foreach ($updates as $key => $value) {
			$update_class = $value['class'];
			$column = self::$column_array[$key];

			$sheet = false;
			if($key == 'worksheets') $sheet = true;

			while(true){
				$models = $update_class::where('synched', 3)
										->when($sheet, function($query){
							                return $query->where('status_id', 3);
							            })->limit(20)->get();
				if($models->isEmpty()) break;

				$token = self::get_token();
				if($type == 'covid' && env('APP_LAB') != 25) $token = self::get_covid_token();

				$response = $client->request('post', $value['delete_url'], [
					'headers' => [
						'Accept' => 'application/json',
						'Authorization' => 'Bearer ' . $token,
					],
					'json' => [
						$key => $models->toJson(),
						'lab_id' => env('APP_LAB', null),
					],

				]);

				$body = json_decode($response->getBody());

				foreach ($body->$key as $row) {
					$update_class::where('id', $row->original_id)->delete();
				}
			}			
		}
	}

	public static function synch_allocations() {
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');

		$url = 'insert/allocations';
		
		while (true) {
			$allocations = Allocation::with(['details', 'details.breakdowns'])->where('synched', 0)->limit(20)->get();
			
			if($allocations->isEmpty())
				break;
			
			$response = $client->request('post', $url, [
				'http_errors' => false,
				'debug' => false,
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'allocations' => $allocations->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);
			
			$body = json_decode($response->getBody());
			
			foreach ($body->allocations as $key => $value) {
				$update_data = ['national_id' => $value->id, 'synched' => 1, 'datesynched' => $today];
				$allocationUpdate = Allocation::find($value->original_allocation_id);
				if (isset($allocationUpdate)){
					$allocationUpdate->update($update_data);
					foreach ($value->details as $key => $detailvalue) {
						
						$detail_update_data = ['national_id' => $detailvalue->id, 'synched' => 1, 'datesynched' => $today];
						$allocationDetailUpdate = AllocationDetail::find($detailvalue->original_allocation_detail_id);
						if (isset($allocationDetailUpdate)) {
							$allocationDetailUpdate->update($detail_update_data);
							
							foreach ($detailvalue->breakdowns as $key => $breakdownvalue) {
								$breakdown_update_data = ['national_id' => $breakdownvalue->id, 'synched' => 1, 'datesynched' => $today];
								$allocationDetailBreakdownUpdate = AllocationDetailsBreakdown::find($breakdownvalue->original_allocation_details_breakdown_id);
								if (isset($allocationDetailBreakdownUpdate)){
									$allocationDetailBreakdownUpdate->update($breakdown_update_data);
								}
							}
						}
					}
				}
			}
		}
		return 'All Allocations Synched';
	}

	public static function synch_allocations_updates() {
		return self::synch_updates('allocations');
	}

	public static function synch_consumptions() {
		echo "==> Starting consumptions synch";
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');

		$url = 'insert/consumptions';

		while (true) {
			echo "\n\t Getting consumptions data 20\n";
			$consumptions = Consumption::with('details')->where('synched', 0)->limit(100)->get();
			if($consumptions->isEmpty())
				break;
			echo "\t Pushing consumptions data to national DB\n";
			$response = $client->request('post', $url, [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'consumptions' => $consumptions->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);
			echo "\t Receiving national db respose\n";
			$body = json_decode($response->getBody());
			if (empty($body->consumptions)){
				$subject = "CONSUMPTION SYNCH MISMATCH LAB " . env('APP_LAB');
				Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail(null, $subject, json_encode($consumptions->pluck('id'))));
				break;
			}

			echo "\t Updating consumptions data\n";
			foreach ($body->consumptions as $key => $value) {
				$update_data = ['national_id' => $value->national_id, 'synched' => 1, 'datesynched' => $today];
				Consumption::where('id', $value->original_id)->update($update_data);
			}
		}
		echo "==> Completed consumptions synch\n";
	}

	public static function synch_deliveries(){
		echo "==> Starting deliveries synch";
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');

		$uri = 'insert/deliveries';

		while (true) {
			echo "\n\t Getting deliveries data 20\n";
			$deliveries = Deliveries::where('synched', 0)->limit(20)->get();
			if($deliveries->isEmpty())
				break;

			echo "\t Pushing deliveries data to national DB";
			$response = $client->request('post', $uri, [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'deliveries' => $deliveries->toJson(),
					'lab_id' => env('APP_LAB', null),
				],
			]);
			echo "\t Receiving national db response\n";
			$body =json_decode($response->getBody());
			foreach ($body->deliveries as $key => $value) {
				$update_data = ['national_id' => $value->national_id, 'synched' => 1, 'datesynched' => $today];
				Deliveries::where('id', $value->original_id)->update($update_data);
			}
		}
		echo "==> Completed deliveries synch\n";
	}

	public static function labactivity($type)
	{
		// $lab_id = NULL;
		// if(!isset($lab_id)) 
		$lab_id = env('APP_LAB', null);
		
		$classes = self::$synch_arrays[$type];
		$sample_class = $classes['sample_class'];
		$sampleview_class = $classes['sampleview_class'];

		$samples_table = 'samples';
		$view_table = 'samples_view';
		$data['testtype'] = 1;
		if($type == 'vl'){
			$samples_table = 'viralsamples';
			$view_table = 'viralsamples_view';
			$data['testtype'] = 2;
		}

		$today = date('Y-m-d');
		$data['yeartodate'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereYear('datetested', date('Y'))
								->where(['flag' => 1, 'repeatt' => 0, 'lab_id' => $lab_id])
								->first()->totals;

		$data['monthtodate'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereYear('datetested', date('Y'))
								->whereMonth('datetested', date('m'))
								->where(['flag' => 1, 'repeatt' => 0, 'lab_id' => $lab_id])
								->first()->totals;

		$data['receivedsamples'] = $sampleview_class::selectRaw('count(id) as totals')
								->where('datereceived', $today)
								->where(['flag' => 1, 'parentid' => 0, 'lab_id' => $lab_id])
								->first()->totals;

		$data['enteredsamplesatlab'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereDate('created_at', $today)
								->where(['flag' => 1, 'parentid' => 0, 'lab_id' => $lab_id, 'site_entry' => 0])
								->first()->totals;

		$data['enteredsamplesatsite'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereDate('created_at', $today)
								->where(['flag' => 1, 'parentid' => 0, 'lab_id' => $lab_id, 'site_entry' => 1])
								->first()->totals;

		$data['enteredreceivedsameday'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereDate('created_at', $today)
								->where('datereceived', $today)
								->where(['flag' => 1, 'parentid' => 0, 'lab_id' => $lab_id])
								->first()->totals;

		$data['enterednotreceivedsameday'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereDate('created_at', $today)
								->where('datereceived', '!=', date('Y-m-d'))
								->where(['flag' => 1, 'parentid' => 0, 'lab_id' => $lab_id])
								->first()->totals;

		$data['inqueuesamples'] = $sampleview_class::selectRaw('count(id) as totals')
								->where('datereceived', '>', '2017-09-31')
								->whereNull('worksheet_id')
								->whereNull('approvedby')
								->whereNotIn('receivedstatus', [0, 2])
								->whereRaw("(result is null or result=0)")
								->where(['flag' => 1, 'input_complete' => 1, 'lab_id' => $lab_id])
								->when(($type == 'vl'), function($query){
									return $query->where('sampletype', '>', 0);
								})
								->first()->totals;

		$mindate = $sampleview_class::selectRaw('MIN(datereceived) as mindate')
								->where('datereceived', '>', '2017-09-31')
								->whereNull('worksheet_id')
								->whereNull('approvedby')
								->whereNull('datedispatched')
								->whereIn('receivedstatus', [1, 3])
								->whereRaw("(result is null or result=0)")
								->where(['flag' => 1, 'input_complete' => 1, 'lab_id' => $lab_id])
								->when(($type == 'vl'), function($query){
									return $query->where('sampletype', '>', 0);
								})
								->first()->mindate;

		$data['oldestinqueuesample'] = \App\Common::get_days($mindate, $today);

		$data['inprocesssamples'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 1)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;

		$data['abbottinprocess'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 1)
						->where('machine_type', 2)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;

		$data['rocheinprocess'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 1)
						->where('machine_type', 1)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;

		$data['panthainprocess'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 1)
						->where('machine_type', 4)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;

		// Check error in Tim's code
		$data['processedsamples'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 2)
						->where('datetested', $today)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;

		$data['abbottprocessed'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 2)
						->where('machine_type', 2)
						->where('datetested', $today)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;

		$data['rocheprocessed'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 2)
						->where('machine_type', 1)
						->where('datetested', $today)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;

		$data['panthaprocessed'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 2)
						->where('machine_type', 4)
						->where('datetested', $today)
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;

		$data['updatedresults'] = $sampleview_class::selectRaw('count(id) as totals')
								->where('datemodified', $today)
								->where(['flag' => 1, 'lab_id' => $lab_id])
								->first()->totals;

		$data['approvedresults'] = $sampleview_class::selectRaw('count(id) as totals')
								->where('dateapproved', $today)
								->where(['flag' => 1, 'lab_id' => $lab_id])
								->first()->totals;


		$data['pendingapproval'] = $sampleview_class::selectRaw("count({$view_table}.id) as totals")
						->when(true, self::join_worksheets($type))
						->where('status_id', 2)
						->whereNull('approvedby')
						->where(["{$view_table}.flag" => 1, "{$view_table}.lab_id" => $lab_id])
						->first()->totals;


		$data['dispatchedresults'] = $sampleview_class::selectRaw('count(id) as totals')
								->where('datedispatched', $today)
								->where(['flag' => 1, 'lab_id' => $lab_id, 'repeatt' => 0])
								->first()->totals;


		$data['oneweek'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereNull('datedispatched')
								->whereYear('datereceived', date('Y'))
								->where(['flag' => 1, 'lab_id' => $lab_id, 'repeatt' => 0, 'receivedstatus' => 1])
								->whereRaw("DATEDIFF(NOW(), datereceived) BETWEEN 1 AND 7")
								->first()->totals;

		$data['twoweeks'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereNull('datedispatched')
								->whereYear('datereceived', date('Y'))
								->where(['flag' => 1, 'lab_id' => $lab_id, 'repeatt' => 0, 'receivedstatus' => 1])
								->whereRaw("DATEDIFF(NOW(), datereceived) BETWEEN 8 AND 14")
								->first()->totals;

		$data['threeweeks'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereNull('datedispatched')
								->whereYear('datereceived', date('Y'))
								->where(['flag' => 1, 'lab_id' => $lab_id, 'repeatt' => 0, 'receivedstatus' => 1])
								->whereRaw("DATEDIFF(NOW(), datereceived) BETWEEN 15 AND 28")
								->first()->totals;

		$data['aboveamonth'] = $sampleview_class::selectRaw('count(id) as totals')
								->whereNull('datedispatched')
								->whereYear('datereceived', date('Y'))
								->where(['flag' => 1, 'lab_id' => $lab_id, 'repeatt' => 0, 'receivedstatus' => 1])
								->whereRaw("DATEDIFF(NOW(), datereceived) > 28")
								->first()->totals;

		$client = new Client(['base_uri' => self::$base]);

		$response = $client->request('post', 'lablogs', [
            'http_errors' => false,
			'verify' => false,
			'headers' => [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . self::get_token(),
			],
			'json' => [
				'data' => $data,
				'lab_id' => $lab_id,
			],
		]);
	}

	public static function join_worksheets($type)
	{
		if($type == 'eid'){
	    	return function($query){
	    		return $query->join('worksheets', 'samples_view.worksheet_id', '=', 'worksheets.id');;
	    	};
		}
		else{
	    	return function($query){
	    		return $query->join('viralworksheets', 'viralsamples_view.worksheet_id', '=', 'viralworksheets.id');;
	    	};			
		}
	}




	public static function transfer_sample($type, $to_lab, $sample_ids)
	{
    	$sample_model = self::$synch_arrays[$type]['sample_class'];
    	$with_array = self::$synch_arrays[$type]['with_array'];

    	if(!$sample_ids){
    		session(['toast_message' => 'Please select the samples to transfer.', 'toast_error' => 1]);
    		return;
    	}

    	$samples = $sample_model::whereIn('id', $sample_ids)->with($with_array)->get();

		//$client = new Client(['base_uri' => self::$base]);
		$client = new Client(['base_uri' => self::$base,'verify' => false]);
		//dd($client);
		$response = $client->request('post', 'transfer', [
            'debug' => true,
			'verify' => false,
            'http_errors' => false,
			'headers' => [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . self::get_token(),
			],
			'json' => [
				'type' => $type,
				'samples' => $samples->toJson(),
				'lab_id' => env('APP_LAB', null),
				'to_lab' => $to_lab,
			],
		]);

		 //dd($response);

		$body = json_decode($response->getBody());

		$status_code = $response->getStatusCode();
		//dd($status_code);
		if($status_code < 400){
			$ok = $body->ok ?? null;

			if($ok) $sample_model::whereIn('id', $ok)->delete();
			session(['toast_message' => 'The transfer has been made.']);
		}
		else{
			session(['toast_message' => "An error has occured. Status code {$status_code}.", 'toast_error' => 1]);
		}
		return;
		// print_r($body);
	}


	public static function synch_covid()
	{
		$client = new Client(['base_uri' => self::$cov_base]);
		if(in_array(env('APP_LAB'), self::$covid_inaccessible)) $client = new Client(['base_uri' => self::$p3_base]);
		$today = date('Y-m-d');

		$double_approval = Lookup::$double_approval; 

		if(in_array(env('APP_LAB'), $double_approval)){
			$where_query = "( receivedstatus=2 OR  (result > 0 AND (repeatt = 0 or repeatt is null) AND ((approvedby IS NOT NULL AND approvedby2 IS NOT NULL) or (dateapproved IS NOT NULL AND dateapproved2 IS NOT NULL)) ))";
		}
		else{
			$where_query = "( receivedstatus=2 and repeatt=0 OR  (result > 0 AND (repeatt = 0 or repeatt is null) AND (approvedby IS NOT NULL OR dateapproved IS NOT NULL)) )";
		}

		$samples = CovidSample::whereRaw($where_query)->whereRaw("(synched=0 or synched is null or datedispatched is null)")->get();
		$today = date('Y-m-d');

		foreach ($samples as $key => $sample) {
			if($sample->parentid) $sample = $sample->parent;
			$sample->datedispatched = $sample->datedispatched ?? $today;
			$sample->set_tat();
			$sample->pre_update();

			foreach ($sample->child as $key => $child) {
				$child->datedispatched = $child->datedispatched ?? $today;
				$child->set_tat();
				$child->pre_update();
			}
		}

		$samples = CovidSample::whereRaw($where_query)->whereRaw("(synched=0)")->limit(60)->get();

		$post_path = 'covid_sample';
		if(in_array(env('APP_LAB'), self::$covid_inaccessible)) $post_path = 'nat_covid_sample';

		foreach ($samples as $key => $sample) {
			/*if($sample->parentid) $sample = $sample->parent;
			$sample->datedispatched = $sample->datedispatched ?? $today;
			$sample->set_tat();
			$sample->save();

			foreach ($sample->child as $key => $child) {
				$child->datedispatched = $child->datedispatched ?? $today;
				$child->set_tat();
				$child->save();
			}
			unset($sample->child);*/
			$sample->load(['patient.travel', 'child']);

			$token = self::get_covid_token();				

			$response = $client->request('post', $post_path, [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . $token,
				],
	            'http_errors' => false,
				'verify' => false,
				'json' => [
					'sample' => $sample->toJson(),
					'lab_id' => env('APP_LAB', null),
				],
			]);
			if($response->getStatusCode() > 399) dd($response);
			

			$body = json_decode($response->getBody());
			$sample_array = $body->sample;

			$sample->synched = 1;
			$sample->datesynched = $today;
			$sample->national_sample_id = $sample_array->{'sample_' . $sample->id} ?? null;
			$sample->save();


			$sample->patient->synched = 1;
			$sample->patient->datesynched = $today;
			$sample->patient->national_patient_id = $body->patient;
			$sample->patient->save();

			foreach ($sample->child as $key => $child) {
				$child->synched = 1;
				$child->datesynched = $today;
				$child->national_sample_id = $sample_array->{'sample_' . $child->id} ?? null;
				$child->save();
			}

			foreach ($sample->patient->travel as $key => $travel) {
				$travel->synched = 1;
				$travel->datesynched = $today;
				$travel->save();
			}

		}

		$sample_ids = CovidSample::select('covid_samples.id')
					->join('covid_patients', 'covid_patients.id', '=', 'covid_samples.patient_id')
					->where('covid_samples.synched', '>', 0)
					->where('covid_patients.synched', '=', 0)
					->get()->pluck('id')->flatten()->toArray();

		CovidSample::whereIn('id', $sample_ids)->update(['synched' => 0]);
	}


	public static function get_covid_samples($filters=null, $jitenge=false)
	{
		if(in_array(env('APP_LAB'), [1,2,3,6])){
			if(!$filters) $filters = ['patient_name' => null, 'identifier' => null, 'national_id' => null];
			extract($filters);
			$sql = '';
			$names = explode(' ', $patient_name);
			foreach ($names as $key => $name) {
				$n = addslashes($name);
				$sql .= "patient_name LIKE '%{$n}%' AND ";
			}
			$sql = substr($sql, 0, -4);

			$identifier_sql = null;			
			if(isset($national_id) && $national_id) $identifier_sql = "(identifier='{$national_id}' OR national_id='{$national_id}')";
			else if(isset($identifier) && $identifier) $identifier_sql .= "(identifier='{$identifier}' OR national_id='{$identifier}')";

			$samples = \App\CovidModels\CovidSample::where(['covid_samples.synched' => 0])
				->whereIn('lab_id', [11, 101])
				->where('covid_samples.created_at', '>', date('Y-m-d', strtotime('-5 days')))
				->whereNull('original_sample_id')
				->whereNull('receivedstatus')
				->when(($patient_name && !$identifier_sql), function($query) use ($sql){
					return $query->select('covid_samples.*')
					->join('covid_patients', 'covid_patients.id', '=', 'covid_samples.patient_id')
					->whereRaw($sql);
				})
				->when($identifier_sql, function($query) use($identifier_sql){
					return $query->whereRaw($identifier_sql);
				})
				->when($jitenge, function($query){
					return $query->where('lab_id', 101);
				})
				// ->orderBy('datecollected', 'desc')
				->orderBy('id', 'desc')
				->with(['patient'])->get();
			return $samples;
		}
		$client = new Client(['base_uri' => self::$cov_base]);
		// $client = new Client(['base_uri' => self::$base]);

		$response = $client->request('get', 'covid_sample/cif', [
			'headers' => [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . self::get_covid_token(),
				// 'Authorization' => 'Bearer ' . self::get_token(),
			],
			'verify' => false,
		]);

		$body = json_decode($response->getBody());
		return $body;
	}

	public static function set_covid_samples($samples)
	{
		if(in_array(env('APP_LAB'), [1,2,3,6])){
			$nat_samples = \App\CovidModels\CovidSample::where(['synched' => 0])->whereIn('lab_id', [11, 101])->where('created_at', '>', date('Y-m-d', strtotime('-5 days')))->whereNull('original_sample_id')->whereNull('receivedstatus')->whereIn('id', $samples)->get();

			foreach ($nat_samples as $key => $nat_sample) {
		        $nat_sample->lab_id = auth()->user()->lab_id;

		        $p = CovidPatient::where('national_patient_id', $nat_sample->patient->id)->first();
		        if(!$p){
		            $p = new CovidPatient;
		        }
		        $patient_details = $nat_sample->patient->toArray();
		        $p->national_patient_id = $patient_details['id'];
		        unset($patient_details['original_patient_id']);
		        // unset($patient_details['cif_patient_id']);
		        unset($patient_details['nhrl_patient_id']);
		        unset($patient_details['date_recovered']);
		        $p->fill($patient_details);
		        $p->save();

		        $nat_sample->patient->original_patient_id = $p->id;
		        $nat_sample->patient->save();
		        unset($nat_sample->patient);

		        $s = new CovidSample;
		        $s->fill($nat_sample->toArray());
		        $s->patient_id = $p->id;
		        $s->national_sample_id = $nat_sample->id;
		        unset($s->original_sample_id);
		        // unset($s->cif_sample_id);
		        unset($s->nhrl_sample_id);
		        unset($s->age_category);
		        $s->receivedstatus = 1;
		        $s->datereceived = date('Y-m-d');
		        $s->save();

		        $nat_sample->original_sample_id = $s->id;
		        $nat_sample->save();
			}
			return;
			// return $samples;
		}
		$client = new Client(['base_uri' => self::$cov_base]);
		// $client = new Client(['base_uri' => self::$base]);

		$response = $client->request('post', 'covid_sample/cif', [
			'headers' => [
				'Accept' => 'application/json',
				'Authorization' => 'Bearer ' . self::get_covid_token(),
				// 'Authorization' => 'Bearer ' . self::get_token(),
			],
			'verify' => false,
			'json' => [
				'samples' => $samples,
				'lab_id' => auth()->user()->lab_id,
			],
		]);

		$body = json_decode($response->getBody());
		return $body;
	}


	public static function match_eid_patients()
	{
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');
		$done = 0;
		$offset=0;

		while (true) {
			$patients = Patient::with(['mother'])
				->where('synched', '>', 0)
				->whereNull('national_patient_id')
				->limit(200)
				// ->offset($offset)
				->get();
			if($patients->isEmpty()) break;

			$response = $client->request('post', 'synch/patients', [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'patients' => $patients->toJson(),
					'lab_id' => env('APP_LAB', null),
				],
			]);

			$body = json_decode($response->getBody());
			$i=0;

			foreach ($body->patients as $key => $value) {
				// $update_data = get_object_vars($value);
				$update_data['national_patient_id'] = $value->id;
				$update_data['synched'] = 1;
				$update_data['datesynched'] = $today;
				// unset($update_data['id']);
				// unset($update_data['original_patient_id']);

				// $new_update_data['national_patient_id'] = $value->id;
				// $new_update_data['dob'] = $value->dob;
				// $new_update_data['sex'] = $value->sex;

				$update_data = [
					'national_patient_id' => $value->id,
					'synched' => 1,
					'datesynched' => $today,
				];
				$i++;

				Patient::where('id', $value->original_patient_id)->update($update_data);
			}

			$offset += (200 - ($i+1));

			foreach ($body->mothers as $key => $value) {
				// $update_data = get_object_vars($value);
				$update_data = [];
				$update_data['national_mother_id'] = $value->id;
				$update_data['synched'] = 1;
				$update_data['datesynched'] = $today;
				// unset($update_data['id']);
				// unset($update_data['original_mother_id']);

				Mother::where('id', $value->original_mother_id)->update($update_data);
			}

			$done+=200;
			echo "Matched {$done} eid patient records at " . date('d/m/Y h:i:s a', time()). "\n";
		}
	}

	public static function match_vl_patients()
	{
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');
		$done=0;
		$offset=0;

		while (true) {
			$patients = Viralpatient::where('synched', '>', 0)
				->whereNull('national_patient_id')
				->limit(200)
				// ->offset($offset)
				->get();
			if($patients->isEmpty()) break;

			$response = $client->request('post', 'synch/viralpatients', [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'patients' => $patients->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);

			$body = json_decode($response->getBody());
			$i=0;

			foreach ($body->patients as $key => $value) {
				// $update_data = get_object_vars($value);
				$update_data['national_patient_id'] = $value->id;
				$update_data['synched'] = 1;
				$update_data['datesynched'] = $today;
				// unset($update_data['id']);
				// unset($update_data['original_patient_id']);
				$i++;

				Viralpatient::where('id', $value->original_patient_id)->update($update_data);
			}

			$offset += (200 - ($i+1));

			$done+=200;
			echo "Matched {$done} vl patient records at " . date('d/m/Y h:i:s a', time()). "\n";
		}
	}

	public static function match_batches($type)
	{
		$classes = self::$synch_arrays[$type];

		$misc_class = $classes['misc_class'];
		$sample_class = $classes['sample_class'];
		$sampleview_class = $classes['sampleview_class'];
		$batch_class = $classes['batch_class'];

		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');
		$my = new $misc_class;
		$my->save_tat($sampleview_class, $sample_class);

		if($batch_class == "App\\Batch"){
			$url = 'synch/batches';
		}else{
			$url = 'synch/viralbatches';
		}
		$done=0;
		$offset=0;

		while (true) {
			$batches = $batch_class::with(['sample:id'])
				->where('synched', '>', 0)
				->whereNull('national_batch_id')
				->limit(200)
				->offset($offset)
				->get();
			if($batches->isEmpty()) break;

			$response = $client->request('post', $url, [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'batches' => $batches->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);
			$i=0;

			$body = json_decode($response->getBody());

			foreach ($body->batches as $key => $value) {
				$update_data = ['national_batch_id' => $value->national_batch_id, 'synched' => 1, 'datesynched' => $today,];
				$batch_class::where('id', $value->original_id)->update($update_data);
				$i++;
			}

			foreach ($body->samples as $key => $value) {
				$update_data = ['national_sample_id' => $value->national_sample_id, 'synched' => 1, 'datesynched' => $today,];
				// if($batch_class == "App\\Viralbatch"){
				// 	$update_data['age_category'] = $value->age_category;
				// 	$update_data['justification'] = $value->justification;
				// 	$update_data['prophylaxis'] = $value->prophylaxis;
				// }
				$sample_class::where('id', $value->original_id)->update($update_data);
			}
			
			$offset += (200 - ($i+1));

			$done+=200;
			echo "Matched {$done} {$type} batch records at " . date('d/m/Y h:i:s a', time()). "\n";
		}
	}

	public static function match_samples($type)
	{
		$classes = self::$synch_arrays[$type];

		$misc_class = $classes['misc_class'];
		$sample_class = $classes['sample_class'];
		$sampleview_class = $classes['sampleview_class'];
		$batch_class = $classes['batch_class'];

		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');
		// $my = new $misc_class;
		// $my->save_tat($sampleview_class, $sample_class);

		if($batch_class == "App\\Batch"){
			$url = 'synch/samples';
		}else{
			$url = 'synch/viralsamples';
		}
		$done=0;
		$offset=0;

		while (true) {
			$samples = $sample_class::with(['batch:id,national_batch_id,lab_id'])
				->where('synched', '>', 0)
				->whereNull('national_sample_id')
				->limit(200)
				->offset($offset)
				->get();
			if($samples->isEmpty()) break;

			$response = $client->request('post', $url, [
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'samples' => $samples->toJson(),
					'lab_id' => env('APP_LAB', null),
				],
			]);
			$i=0;

			$body = json_decode($response->getBody());

			foreach ($body->samples as $key => $value) {
				$update_data = ['national_sample_id' => $value->national_sample_id, 'synched' => 1];
				$i++;
				$sample_class::where('id', $value->original_id)->update($update_data);
			}
			
			$offset += (200 - ($i+1));

			$done+=200;
			echo "Matched {$done} {$type} sample records at " . date('d/m/Y h:i:s a', time()). "\n";
		}
	}

    public static function synch_facilities()
    {
        ini_set('memory_limit', '-1');
        $client = new Client(['base_uri' => self::$base]);
        $today = date('Y-m-d');

        $max_temp = FacilityChange::selectRaw("max(temp_facility_id) as maximum ")->first()->maximum ?? 1000000;

        while (true) {
            $facilities = Facility::where('synched', 0)->limit(30)->get();
            if($facilities->isEmpty()) break;

            $response = $client->request('post', 'facility', [
                'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . self::get_token(),
                ],
                'json' => [
                        'facilities' => $facilities->toJson(),
                        'lab_id' => env('APP_LAB', null),
                ],
            ]);

            $body = json_decode($response->getBody());

            $update_data = ['synched' => 1,];

            foreach ($body->facilities as $key => $value) {
                Facility::where('id', $value->old_facility_id)->update($update_data);
                if($value->new_facility_id != $value->old_facility_id){

                    $f = new FacilityChange;
                    $f->old_facility_id = $value->old_facility_id;
                    $f->new_facility_id = $value->new_facility_id;
                    $f->temp_facility_id = $max_temp++;
                    $f->save();

                    Common::change_facility_id($value->old_facility_id, $f->temp_facility_id, true);
                }
            }
        }

        $changes = FacilityChange::where(['implemented' => 0])->get();

        foreach ($changes as $f) {
            Common::change_facility_id($f->temp_facility_id, $f->new_facility_id, true);
            $f->implemented = 1;
            $f->save();
        }
    }

    public static function synch_updates_facilities()
    {
        ini_set('memory_limit', '-1');
        $client = new Client(['base_uri' => self::$base]);
        $today = date('Y-m-d');

        $facilities = Facility::where(['synched' => 1])->get();

        foreach ($facilities as $facility) {

            $response = $client->request('get', "facility/{$facility->id}", [
            	'http_errors' => false,
                'headers' => [
                        'Accept' => 'application/json',
                        'Authorization' => 'Bearer ' . self::get_token(),
                ],
            ]);

            if($response->getStatusCode() > 399){
            	echo "Facility {$facility->id} not found.\n";
            	continue;
            }
            
            $body = json_decode($response->getBody());

            $facility->district = $body->facility->district;
            $facility->partner = $body->facility->partner;
            $facility->save();
        }
    }

    public static function sendAllocationReview($allocationReactionCounts = null)
    {
    	$users = new User;
		$users = $users->notifiedAllocation()->orWhere('user_type_id', 0)->get();
		// dd($users);
		$lab = Lab::find(env('APP_LAB'));
		$date = date('Y-m-d');
		$fromAllocationDate = date('d M, Y', strtotime($date));
		$toAllocationDate = date('d M, Y', strtotime($date. ' + 14 days'));
		self::$allocationReactionCounts = $allocationReactionCounts;
		self::$users = $users;
		self::$lab = $lab;
		self::$from = $fromAllocationDate;
		self::$to = $toAllocationDate;
		// return self::$users;
		self::sendAllocationReviewSms();
		self::sendAllocationReviewEmail();
		// if (self::$allocationReactionCounts->approved > 0)
		// 	Mail::to(self::$users->pluck('email')->toArray())->send(new AllocationReview(self::$allocationReactionCounts, self::$lab, self::$from, self::$to, true, false));
		// 	// Mail::to(['bakasajoshua09@gmail.com'])->send(new AllocationReview($allocationReactionCounts, $lab, $from, $to, true, false));
		// if (self::$allocationReactionCounts->rejected > 0)
		// 	Mail::to(self::$users->pluck('email')->toArray())->send(new AllocationReview(self::$allocationReactionCounts, self::$lab, self::$from, self::$to, false, true));
		foreach ($users as $key => $user) {
			$user->allocation_notification_date = date('Y-m-d H:i:s');
			$user->save();
		}
		return true;
    }


	private static function sendAllocationReviewSms()
	{
		$message = "";
		$body = null;
		foreach (self::$users as $user) {
			if (null !== $user->telephone) {
				$labname = self::$lab->labname;
				$approved = self::$allocationReactionCounts->approved;
				$rejected = self::$allocationReactionCounts->rejected;
				$month = self::$allocationReactionCounts->month;
				$year = self::$allocationReactionCounts->year;
				$from = self::$from;
				$to = self::$to;
				if (self::$allocationReactionCounts->approved > 0)
					$message .= "{$labname}, {$approved} of your {$month} {$year} allocation have been approved. The commodities will be the delivered between {$from} and {$to} by KEMSA.\n\n";
				if (self::$allocationReactionCounts->rejected > 0)
					$message .= "{$labname}, {$rejected} of your {$month} {$year} allocation have been rejected. Kindly log into the system under the ‘Kits’ link to view the comments for your review then re-submit the allocation as soon as possible.";
				$client = new Client(['base_uri' => \App\Common::$sms_url]);

				$response = $client->request('post', '', [
					'auth' => [env('SMS_USERNAME'), env('SMS_PASSWORD')],
					'http_errors' => false,
					'json' => [
						'sender' => env('SMS_SENDER_ID'),
						'recipient' => $user->telephone,
						'message' => $message,
					],
				]);
				$body = json_decode($response->getBody());
			}
		}
		return $body;
	}

	public static function synchCovidConsumption()
	{
		$client = new Client(['base_uri' => self::$base]);
		$url = 'insert/covidconsumption';
		if (in_array(env('APP_LAB'), [9, 23])) {
			$client = new Client(['base_uri' => self::$p3_base]);
			$url = 'consumption/covid';
		}

		while (true) {
			$consumptions = CovidConsumption::with(['details.kit'])->where('synced', 0)->get();
			if($consumptions->isEmpty())
				break;
			
			$response = $client->request('post', $url, [
				'http_errors' => false,
				'debug' => false,
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'consumptions' => $consumptions->toJson(),
					'lab' => env('APP_LAB')
				],
			]);
			
			$body = json_decode($response->getBody());
			
			if (isset($body->error)) {
				$subject = "COVID allocation synch failed";
				Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail(null, $subject, json_encode($body)));
				print_r('Error: ');print_r($body->error);
				return false;
			} else {
				foreach ($body as $key => $consumption) {
					$covidconsumption = CovidConsumption::find($consumption->original_id);
					if (null !== $consumption->national_id){
						$covidconsumption->national_id = $consumption->national_id;
						$covidconsumption->synchComplete();
					}
				}
				return true;
			}
		}
		return false;
	}

	public static function synchMachineMapping()
	{
		$client = new Client(['base_uri' => self::$base]);
		$url = 'insert/machinemapping';

		while (true) {
			$mappings = LabEquipment::where('lab', env('APP_LAB'))->where('synched', 0)->get();
			if ($mappings->isEmpty())
				break;

			$response = $client->request('post', $url, [
				'http_errors' => false,
				'debug' => false,
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'mappings' => $mappings->toJson(),
					'lab' => env('APP_LAB')
				]
			]);
			
			$body = json_decode($response->getBody());
			if (isset($body->error)) {
				$subject = "Machine Mapping syncing failed for lab " . env('APP_LAB');
				Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail(null, $subject, json_encode($body)));
				print_r('Error: ');print_r($body->error);
				return false;
			} else {
				foreach($body as $key => $item) {
					// dd($item);
					$equipment = LabEquipment::findOrFail($item->original_id);
					$equipment->national_id = $item->national_id;
					$equipment->synched = 1;
					$equipment->datesynched = now();
					$equipment->save();
				}
				return true;
			}
		}
		return false;
	}

	public static function synchLabPerformanceTrackers()
	{
		$client = new Client(['base_uri' => self::$base]);
		$url = 'insert/labperformancetracker';

		while (true) {
			$performances = LabPerformanceTracker::with(['submitter'])->where('synched', 0)->limit(100)->get();
			if ($performances->isEmpty())
				break;

			$response = $client->request('post', $url, [
				'http_errors' => false,
				'debug' => false,
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'performances' => $performances->toJson(),
					'lab' => env('APP_LAB')
				]
			]);
			
			$body = json_decode($response->getBody());
			if (isset($body->error)) {
				$subject = "Lab Performance syncing failed for lab " . env('APP_LAB');
				Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail(null, $subject, json_encode($body)));
				print_r('Error: ');print_r($body->error);
				return false;
			} else {
				foreach($body as $key => $item) {
					$equipment = LabPerformanceTracker::findOrFail($item->original_id);
					$equipment->national_id = $item->national_id;
					$equipment->synched = 1;
					$equipment->datesynched = now();
					$equipment->save();
				}				
			}
		}
		return true;
	}

	public static function synchLabEquipmentTrackers()
	{
		$client = new Client(['base_uri' => self::$base]);
		$url = 'insert/labpequipmenttracker';

		while (true) {
			$trackings = LabEquipmentTracker::with(['equipment', 'submitter'])->where('synched', 0)->limit(100)->get();
			if ($trackings->isEmpty())
				break;

			$response = $client->request('post', $url, [
				'http_errors' => false,
				'debug' => false,
				'headers' => [
					'Accept' => 'application/json',
					'Authorization' => 'Bearer ' . self::get_token(),
				],
				'json' => [
					'trackings' => $trackings->toJson(),
					'lab' => env('APP_LAB')
				]
			]);
			
			$body = json_decode($response->getBody());
			if (isset($body->error)) {
				$subject = "Machine Mapping syncing failed for lab " . env('APP_LAB');
				Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail(null, $subject, json_encode($body)));
				print_r('Error: ');print_r($body->error);
				return false;
			} else {
				foreach($body as $key => $item) {
					$equipment = LabEquipmentTracker::findOrFail($item->original_id);
					$equipment->national_id = $item->national_id;
					$equipment->synched = 1;
					$equipment->datesynched = now();
					$equipment->save();
				}
			}
		}
		return true;
	}

	public static function synchPartnerFacilities()
	{
		$client = new Client(['base_uri' => self::$base]);
		$url = 'partnerfacilitycontacts';

		$response = $client->request('get', $url, [
			'verify' => false,
			'http_errors' => false,
			'debug' => false,
			'headers' => [
				'Accept' => 'application/json',
			]
		]);

		$body = json_decode($response->getBody());
		if (isset($body->error)) {
			$subject = "Synching facility partners failed " . env('APP_LAB');
			Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail(null, $subject, json_encode($body)));
			print_r('Error: ');print_r($body->error);
			return false;
		} else {
			foreach($body as $key => $item) {
				$partnerfacilities = PartnerFacilityContact::where('email', $item->email)->get();
				if ($partnerfacilities->isEmpty()) {
					$contact = new PartnerFacilityContact;
				} else {
					$contact = $partnerfacilities->first();
				}
				$contact->fill((array) $item);
				$contact->save();
			}
		}
		return true;
	}

	private static function sendAllocationReviewEmail()
	{
		Mail::to(['bakasajoshua09@gmail.com'])->send(new AllocationReview(self::$allocationReactionCounts));
	}
}
