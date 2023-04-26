<?php

namespace App;

use Illuminate\Support\Facades\Mail;
use App\Mail\DrugResistanceResult;
use App\Mail\DrugResistance;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use DB;
use Str;

class MiscDr extends Common
{

	// public static $hyrax_url = 'https://sangerv2.api.exatype.com/sanger/v2'; 
	public static $hyrax_url = 'https://production121120v0-sanger.api.exatype.com/sanger/v2'; 
	public static $ui_url = 'https://sanger.exatype.com';

    public static $call_array = [
        'LC' => [
            'resistance' => 'Low Coverage',
            'resistance_colour' => "#595959",
            'cells' => [],
        ],
        'R' => [
            'resistance' => 'Resistant',
            'resistance_colour' => "#ff0000",
            'cells' => [],
        ],
        'I' => [
            'resistance' => 'Intermediate Resistance',
            'resistance_colour' => "#ff9900",
            'cells' => [],
        ],
        'S' => [
            'resistance' => 'Susceptible',
            'resistance_colour' => "#00ff00",
            'cells' => [],
        ],
        'L' => [
            'resistance' => 'Low Level',
            'resistance_colour' => "#00ff00",
            'cells' => [],
        ],
        'PL' => [
            'resistance' => 'Potential Low Level',
            'resistance_colour' => "#00ff00",
            'cells' => [],
        ],
    ];

    public static function get_drug_score($score)
    {
    	$c = self::$call_array;
    	// susceptible
    	if($score <= 10) return $c['S'];
    	else if($score < 15) return $c['PL'];
    	else if($score < 30) return $c['L'];
    	else if($score < 60) return $c['I'];
    	else if($score > 59) return $c['R'];
    	else{
    		return $c['LC'];
    	}
    }

    public static function dump_log($postData, $encode_it=true)
    {
    	if(!is_dir(storage_path('app/logs/'))) mkdir(storage_path('app/logs/'), 0777);

		if($encode_it) $postData = json_encode($postData, JSON_PRETTY_PRINT);
		
		$file = fopen(storage_path('app/logs/' . 'dr_logs3' .'.txt'), "a");
		if(fwrite($file, $postData) === FALSE) fwrite("Error: no data written");
		fwrite($file, "\r\n");
		fclose($file);
    }

	public static function get_hyrax_key()
	{
		if(Cache::has('dr_api_v2_token')){}
		else{
			self::login();
		}
		return Cache::get('dr_api_v2_token');
	}

	public static function login()
	{
		Cache::forget('dr_api_v2_token');
		$client = new Client(['base_uri' => self::$hyrax_url]);

		$response = $client->request('POST', 'authorisations', [
            // 'debug' => true,
            'http_errors' => false,
            'connect_timeout' => 15,
			'headers' => [
				// 'Accept' => 'application/json',
			],
			'json' => [
				'data' => [
					'type' => 'authorisations',
					'attributes' => [
						'email' => env('DR_USERNAME'),
						'password' => env('DR_PASSWORD'),
					],
				],
			],
		]);

		// dd($response->getBody());		

		if($response->getStatusCode() < 400)
		{
			$body = json_decode($response->getBody());
			$key = $body->data->attributes->api_key ?? null;
			if(!$key) dd($body);
			Cache::put('dr_api_v2_token', $key, 600);
			return;
		}
		else{
			dd($response->getStatusCode());
			$body = json_decode($response->getBody());
			dd($body);
		}
	}


	public static function create_plate($worksheet)
	{
		ini_set('memory_limit', '-1');
		$client = new Client(['base_uri' => self::$hyrax_url]);

		$files = self::upload_worksheet_files($worksheet);

		$sample_data = $files['sample_data'];
		$errors = $files['errors'];

		if($errors){
			session(['toast_error' => 1, 'toast_message' => 'The upload has errors.']);
			return $errors;
		}

		$postData = [
				'data' => [
					'type' => 'job-create-v2',
					'attributes' => [
						'job_name' => "job_plate_{$worksheet->id}",
						'assay' => 'tf_cdc_pr_rt_in',
						'plates' => [
							// {
							'plate_name' => "plate_{$worksheet->id}",
							// }
						],
						'samples' => $sample_data,
					],
				],
			];

		// self::dump_log($postData);

		$response = $client->request('POST', 'jobs', [
            'http_errors' => false,
            // 'debug' => true,
			'headers' => [
				// 'Accept' => 'application/json',
				// 'x-hyrax-daemon-apikey' => self::get_hyrax_key(),
				'X-Hyrax-Apikey' => self::get_hyrax_key(),
			],
			'json' => $postData,
		]);

		return self::processResponse($worksheet, $response);
	}

	public static function upload_worksheet_files($worksheet)
	{
		$path = storage_path('app/public/results/dr/' . $worksheet->id . '/');

		// $samples = $worksheet->sample_view;
		$samples = $worksheet->sample;
		// $samples->load(['result']);

		$primers = ['F1', 'F2', 'F3', 'R1', 'R2', 'R3'];

		$sample_data = [];
		$print_data = [];
		$errors = [];

		$contigs = [
			'PRRT' => [
				'contig_array' => [
					'contig_name' => 'PRRT',
					'contig_alias' => 'PRRT',
					'contig_code' => 'PRRT',
					'plate_name' => "plate_{$worksheet->id}",
					'ab1s' => null,
				],
				'primers' => ['F1', 'F2', 'F3', 'R1', 'R2', 'R3']
			],
			'INT' => [
				'contig_array' => [
					'contig_name' => 'IN',
					'contig_alias' => 'INT',
					'contig_code' => 'IN',
					'plate_name' => "plate_{$worksheet->id}",
					'ab1s' => null,
				],
				'primers' => ['F1', 'F2', 'R1', 'R2']
			],
		];

		foreach ($samples as $key => $sample) {
			foreach ($contigs as $contig_name => $contig) {

				$s = [
					'sample_name' => "{$sample->mid}",
					'sample_type' => 'data',
					'contigs' => $contig['contig_array'],
				];

				if($sample->control == 1) $s['sample_type'] = 'negative';
				if($sample->control == 2) $s['sample_type'] = 'positive';

				$abs = [];
				$abs2 = [];

				foreach ($primers as $primer) {
					$sample_file = $sample->ab_file()->where(['primer' => $primer])->first();
					if(!$sample_file)
						$sample_file = self::find_ab_file($path, $sample, $primer, $contig_name);
					// if($ab) $abs[] = $ab;
					if($sample_file){
						$abs[] = [
							'primer_name' => $sample_file->primer_name,
							'file_link_id' => $sample_file->exatype_file_id,
							'path' => $sample_file->file_name,
						];
					}
					else{
						// $errors[] = "Sample {$sample->id} ({$sample->mid}) Primer {$primer} could not be found.";
						if(env('APP_LAB') == 1) $errors[] = "Sample {$sample->id} ({$sample->mid}) Primer {$primer} could not be found.";
						else{
							$errors[] = "Sample {$sample->id} ({$sample->nat}) Primer {$primer} could not be found.";
						}
					}
				}
				if(!$abs) continue;
				$s['contigs']['ab1s'] = $abs;
				$sample_data[] = $s;
			}
		}
		// self::dump_log($print_data);
		// die();
		return ['sample_data' => $sample_data, 'errors' => $errors];
	}

	public static function find_ab_file($path, $sample, $primer, $contig_name)
	{
		$files = scandir($path);
		if(!$files) return null;

		ini_set('memory_limit', '-1');
		$client = new Client(['base_uri' => self::$hyrax_url]);

		foreach ($files as $file) {
			if($file == '.' || $file == '..') continue;

			$new_path = $path . '/' . $file;
			if(is_dir($new_path)){
				$a = self::find_ab_file($new_path, $sample, $primer, $contig_name);

				if(!$a) continue;
				return $a;
			}
			else{
				if(Str::startsWith($file, [$sample->mid . '-', $sample->mid . '_']) && Str::contains($file, $primer))
				{
					$ab_file = fopen($new_path, 'r');
					$response = $client->request('POST', 'file-link/upload/', [
						'headers' => [
							'X-Hyrax-Apikey' => self::get_hyrax_key(),
						],
						'body' => $ab_file
					]);

					$body = json_decode($response->getBody());
					$sample_file = DrSampleFile::firstOrCreate(
						[
							'sample_id' => $sample->id, 
							'primer' => $primer
						], 
						[
							'sample_id' => $sample->id, 
							'primer' => $primer, 
							'exatype_file_id' => $body->attributes->key, 
							'file_name' => $file,
							'contig' => $contig_name,
						]);

					return $sample_file;
				}
				continue;
			}
		}
		return false;
	}

	public static function processResponse($worksheet, $response)
	{
		$body = json_decode($response->getBody());

		if($response->getStatusCode() >= 400){
			session(['toast_error' => 1, 'toast_message' => 'Something went wrong. Status code ' . $response->getStatusCode()]);
			return false;			
		}

		$worksheet->exatype_job_id = $body->data->attributes->data->id;
		$worksheet->plate_id = $body->data->attributes->data->plates[0]->id;
		$worksheet->time_sent_to_exatype = date('Y-m-d H:i:s');
		$worksheet->status_id = 5;
		$worksheet->save();

		foreach ($body->data->attributes->data->samples as $key => $value) {

			if(env('APP_LAB') == 100){
				$patient = \App\Viralpatient::where('patient', $value->sample_name)
					->whereRaw("id IN (SELECT patient_id FROM dr_samples WHERE worksheet_id={$worksheet->id})")
					->first();
				$sample = $patient->dr_sample()->first();
				if(!$sample){
					echo 'Cannot find ' . $value->sample_name . "\n";
					continue;
				}
				$sample->exatype_id = $value->id;
				$sample->save();
			}
			else{
				$sample_id = Str::after($value->sample_name, env('DR_PREFIX', ''));
				$sample = DrSample::find($sample_id);
				if($sample->worksheet->id != $worksheet->id){
					if(env('APP_LAB') != 1) continue;
					$sample = DrSample::where(['worksheet_id' => $worksheet->id, 'parentid' => Str::after($value->sample_name, env('DR_PREFIX', ''))])->first();
					if(!$sample) continue;
				}

				$sample->exatype_id = $value->id;
				$sample->save();
			}

			foreach ($value->contigs as $contig) {
				$sample->contig()->create([
					'exatype_id' => $contig->id,
					'contig' => $contig->contig_alias,
				]);
			}
		}
		session(['toast_message' => 'The worksheet has been successfully created at Exatype.']);
		return $body;
	}



	public static function get_plate_result($worksheet)
	{
		ini_set('memory_limit', '-1');
		$client = new Client(['base_uri' => self::$hyrax_url]);

		$response = $client->request('GET', "jobs/{$worksheet->exatype_job_id}", [
			'headers' => [
				// 'Accept' => 'application/json',
				'X-Hyrax-Apikey' => self::get_hyrax_key(),
			],
		]);

		$body = json_decode($response->getBody());

		/*$included = print_r($body, true);

		$file = fopen(public_path('dr_res.json'), 'w+');
		fwrite($file, $included);
		fclose($file);
		die();*/

		// dd($body);

		if($response->getStatusCode() != 200){
			session(['toast_error' => 1, 'toast_message' => 'Something went wrong. Status code ' . $response->getStatusCode()]);
			return false;
		}

		foreach ($body->included as $key => $value) {
			if($value->type == 'sanger-plate'){
				$worksheet->exatype_status_id = MiscDr::get_worksheet_status($value->status->id);
				$worksheet->save();
			}
			else if($value->type == 'basecall-result'){
				$contig = DrContig::where(['exatype_id' => $value->id])->first();
				foreach ($value->attributes->warnings as $warning) {
					self::create_warning(2, $contig, $warning, 0);
				}
				foreach ($value->attributes->warnings as $warning) {
					self::create_warning(2, $errors, $error, 1);
				}
			}
			else if($value->type == 'contig'){
				$contig = DrContig::where(['exatype_id' => $value->id])->first();
				$contig->exatype_status_id = MiscDr::get_contig_status($value->status->id);
				$contig->chromatogram_id = $value->chromatogram_id;
				$contig->save();
			}
			else if($value->type == 'drug-call-result'){
				$sample = DrSample::where(['exatype_id' => $value->id])->first();
				foreach ($value->attributes->drug_calls as $drug_call) {	
					$c = DrCall::firstOrCreate(['sample_id' => $sample->id, 'drug_class' => $drug_call->drug_class,], [
						'sample_id' => $sample->id,
						'drug_class' => $drug_call->drug_class,
						'drug_class_id' => self::get_drug_class($drug_call->drug_class),
					]);

					if(isset($call->mutations) && $call->mutations){
						$sample->has_mutations = true;
						$c->mutations = $call->mutations ?? [];
						$c->save();

						foreach ($c->mutations as $mutation) {
							$drMutation = DrMutation::firstOrCreate([
								'drug_class_id' => $c->drug_class_id,
								'mutation' => $mutation,
							]);
							$sample->sample_mutation()->firstOrCreate([
								'mutation_id' => $drMutation->id,
							]);
						}
					}

					foreach ($drug_call->drugs as $drug) {
						$d = DrCallDrug::firstOrNew(['call_id' => $c->id, 'short_name' => $drug->short_name]);				
						$d->fill([
							'call_id' => $c->id,
							'short_name' => $drug->short_name,
							'short_name_id' => MiscDr::get_short_name_id($drug->short_name),
							'call' => $drug->call,
							'score' => $drug->score ?? null,
						]);
						$d->save();
					}
				}
			}
			else if($value->type == 'sample-qc'){
				$sample = DrSample::where(['exatype_id' => $value->id])->first();

			}
			else if($value->type == 'sanger-sample'){
				$sample = DrSample::where(['exatype_id' => $value->attributes->id])->first();
				$sample->pdf_download_link = $value->attributes->pdf_download->generate;
				$sample->status_id = MiscDr::get_sample_status($value->status->id);
				$sample->save();

			}
			else if($value->type == 'aligner-result'){
				$sample = DrSample::where(['exatype_id' => $value->id])->first();
				foreach ($value->attributes->aligned_sequence as $aligned_sequence) {
					$a = $aligned_sequence->read;
				}

			}
			else if($value->type == 'job-qc'){
				$sample = DrSample::where(['exatype_id' => $value->id])->first();

			}
		}
		session(['toast_message' => 'The worksheet results have been successfully retrieved from Exatype.']);
		return $body;

		// dd($body);
	}



	public static function create_warning($type, $model, $warning, $is_error = 0)
	{
		if($type == 1){
			$class = DrWorksheetWarning::class;
			$column = 'worksheet_id';
		}
		else if($type == 2){
			$class = DrContigWarning::class;
			$column = 'contig_id';					
		}
		else{
			$class = DrWarning::class;
			$column = 'sample_id';			
		}

		$e = $class::firstOrCreate([
			$column => $model->id,
			'warning_id' => self::get_sample_warning($warning, $is_error),
			'detail' => $warning->message ?? '',
		]);

		if(!$e->warning_id){
			$e->detail .= " error_name " . $warning->title;
			$e->save();
		}
		return $e;
	}

	public static function get_sample_warning($warning, $is_error)
	{
		$warning_id = DB::table('dr_warning_codes')->where(['code' => $warning->code])->first()->id ?? 0;
		if(!$warning_id){
			// DB::table('dr_warning_codes')->insert(['code' => $warning->code, 'type' => $warning->type, 'message' => $warning->message, 'error' => $is_error]);
			// return self::get_sample_warning($warning);
			$drWarningCode = DrWarningCode::create(['code' => $warning->code, 'type' => $warning->type, 'message' => $warning->message, 'error' => $is_error]);
			return $drWarningCode->id;
		}else{
			return $warning_id;
		}
	}
	

	public static function get_job_status($id)
	{
		return DB::table('dr_job_statuses')->where(['name' => $id])->first()->id;
	}

	public static function get_worksheet_status($id)
	{
		return DB::table('dr_plate_statuses')->where(['name' => $id])->first()->id;
	}

	public static function get_sample_status($id)
	{
		return DB::table('dr_sample_statuses')->where(['other_id' => $id])->first()->id;
	}

	public static function get_contig_status($id)
	{
		return DB::table('dr_contig_statuses')->where(['other_id' => $id])->first()->id;
	}

	public static function get_drug_class($id)
	{
		return DB::table('regimen_classes')->where(['drug_class' => $id])->first()->drug_class_id ?? null;
	}

	public static function get_short_name_id($id)
	{
		return DB::table('regimen_classes')->where(['short_name' => $id])->first()->id ?? null;
	}

	

	/*
		Start of Console Commands
	*/

	public static function send_to_exatype()
	{
		$worksheets = DrWorksheet::where(['status_id' => 2])->get();
		// $worksheets = DrBulkRegistration::where(['status_id' => 2])->get();
		foreach ($worksheets as $key => $worksheet) {
			self::create_plate($worksheet);
		}
	}

	public static function fetch_results()
	{
		$max_time = date('Y-m-d H:i:s', strtotime('-10 minutes'));
		$worksheets = DrWorksheet::where(['status_id' => 5])->where('time_sent_to_exatype', '<', $max_time)->get();
		// $worksheets = DrBulkRegistration::where(['status_id' => 5])->where('time_sent_to_exatype', '<', $max_time)->get();
		foreach ($worksheets as $key => $worksheet) {
			echo "Getting results for {$worksheet->id} \n";
			self::get_plate_result($worksheet);
		}

		$max_time = date('Y-m-d H:i:s', strtotime('-1 hour'));
		$worksheets = DrWorksheet::where(['status_id' => 6, 'exatype_status_id' => 5])->where('time_sent_to_exatype', '<', $max_time)->get();
		// $worksheets = DrBulkRegistration::where(['status_id' => 6, 'exatype_status_id' => 5])->where('time_sent_to_exatype', '<', $max_time)->get();
		foreach ($worksheets as $key => $worksheet) {
			self::get_plate_result($worksheet);
		}
	}

	public static function send_completed_results()
	{
		$drSamples = DrSample::whereNull('dateemailsent')->where(['status_id' => 1])->get();
		foreach ($drSamples as $drSample) {
			self::send_email($drSample);
		}
	}

	public static function send_email($drSample)
	{
		$mail_array = $drSample->facility->email_array;
		if(env('APP_LAB') == 1) $mail_array[] = 'eid-nairobi@googlegroups.com';
		$new_mail = new DrugResistanceResult($drSample);
		Mail::to($mail_array)->send($new_mail);
		if(!$drSample->dateemailsent) $drSample->dateemailsent = date('Y-m-d');
		$drSample->save();
	}

	public function save_dr_tat()
	{
		$samples = DrSample::whereNotNull('datedispatched')->whereNull('tat1')->get();
		foreach ($samples as $key => $sample) {
			$sample->tat1 = self::get_days($sample->datecollected, $sample->datereceived);
			$sample->tat2 = self::get_days($sample->datereceived, $sample->datetested);
			$sample->tat3 = self::get_days($sample->datetested, $sample->datedispatched);
			$sample->tat4 = self::get_days($sample->datecollected, $sample->datedispatched);
			$sample->save();
		}
	}


	public static function set_current_drug()
	{
		$samples = DrSample::all();

		foreach ($samples as $sample) {
			$viralregimen = DB::table('viralregimen')->where('id', $sample->prophylaxis)->first();
			if(!$viralregimen) continue;
			foreach ($sample->dr_call as $dr_call) {
				foreach ($dr_call->call_drug as $call_drug) {
					$r = [$viralregimen->regimen1_class_id, $viralregimen->regimen2_class_id, $viralregimen->regimen3_class_id, $viralregimen->regimen4_class_id, $viralregimen->regimen5_class_id, ];
					if(in_array($call_drug->short_name_id, $r)){
						$call_drug->current_drug = 1;
						$call_drug->save();
					}else{
						$call_drug->current_drug = 0;
						$call_drug->save();						
					}
				}
			}
		}
	}

	public static function set_fields()
	{
		$misc = new MiscViral;
		$samples = DrSample::whereNull('age_category')->get();

		foreach ($samples as $key => $sample) {
			$sample->age_category = $misc->set_age_cat($sample->age);
			$sample->save();
		}
	}


	public static function create_mutations()
	{
		$dr_calls = DrCall::get();

		foreach ($dr_calls as $key => $dr_call) {
			if(!$dr_call->mutations) continue;

			foreach ($dr_call->mutations as $key => $mutation) {
				$dr_mutation = DrMutation::firstOrCreate(['drug_class_id' => $dr_call->drug_class_id, 'mutation' => $mutation]);
				DrSampleMutation::firstOrCreate(['sample_id' => $dr_call->sample_id, 'mutation_id' => $dr_mutation->id]);
			}
		}
	}


	


	public static function get_extraction_worksheet_samples($limit=48)
	{
		$samples = DrSampleView::whereNull('worksheet_id')
		->whereNull('extraction_worksheet_id')
		->where('datereceived', '>', date('Y-m-d', strtotime('-2 months')))
		->where(['receivedstatus' => 1, 'control' => 0])
		->orderBy('run', 'desc')
		->orderBy('datereceived', 'asc')
		->orderBy('id', 'asc')
		->limit($limit)
		->get();

		$valid_samples = [];

		if(env('APP_LAB') == 700){
			foreach ($samples as $key => $drSample) {
		        $vl_sample = Viralsample::where($drSample->only(['datecollected', 'patient_id']))->first();
		        if($vl_sample && is_numeric($vl_sample->result) && $vl_sample->result > 500) $valid_samples[] = $samples;
			}
			return ['samples' => $valid_samples, 'create' => true, 'limit' => $limit];
		}

		/*if($samples->count() == $limit || in_array(env('APP_LAB'), [7]) ){
			return ['samples' => $samples, 'create' => true, 'limit' => $limit];
		}*/
		return ['samples' => $samples, 'create' => true, 'limit' => $limit];
	}

	// public static function get_worksheet_samples($extraction_worksheet_id)
	public static function get_worksheet_samples($sample_ids=[], $limit=null)
	{
		$samples = DrSampleView::whereNull('worksheet_id')
		// ->where(['passed_gel_documentation' => true, 'extraction_worksheet_id' => $extraction_worksheet_id])
		->when($sample_ids, function($query) use ($sample_ids){
			return $query->whereIn('id', $sample_ids);
		})
		->where('datereceived', '>', date('Y-m-d', strtotime('-3 months')))
		->where(['receivedstatus' => 1, 'control' => 0])
		->orderBy('control', 'desc')
		->orderBy('run', 'desc')
		->orderBy('id', 'asc')
		->when($limit, function($query) use ($limit){
			return $query->limit($limit);
		})
		->get();

		$create = false;
		if($samples->count() > 0) $create = true;

		return ['samples' => $samples, 'create' => $create];
	}


	public static function get_bulk_registration_samples($sample_ids=[], $limit=null)
	{
		$samples = DrSampleView::whereNull('bulk_registration_id')
		->where('datereceived', '>', date('Y-m-d', strtotime('-1 year')))
		->where(['receivedstatus' => 1, 'control' => 0])
		->when($sample_ids, function($query) use ($sample_ids){
			return $query->whereIn('id', $sample_ids);
		})
		->orderBy('run', 'desc')
		->orderBy('datereceived', 'asc')
		->orderBy('id', 'asc')
		->when($limit, function($query) use ($limit){
			return $query->limit($limit);
		})
		->get();

		if($samples->count() > 0){
			return ['samples' => $samples, 'create' => true];
		}
		return ['samples' => $samples, 'create' => false];
	}
}
