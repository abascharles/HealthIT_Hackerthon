<?php

namespace App;

use Illuminate\Support\Facades\Mail;
use App\Mail\DrugResistanceResult;
use App\Mail\DrugResistance;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use DB;
use Str;

use App\Imports\DrWorksheetsImport;
use Excel;

class MiscDrOld extends Common
{

	// public static $hyrax_url = 'https://sanger20181106v2-sanger.hyraxbio.co.za';
	// public static $ui_url = 'http://sangelamerkel.exatype.co.za';

	public static $hyrax_url = 'https://sanger.api.exatype.com'; 
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
		if(Cache::has('dr_api_token')){}
		else{
			self::login();
		}
		return Cache::get('dr_api_token');
	}

	public static function login()
	{
		Cache::forget('dr_api_token');
		$client = new Client(['base_uri' => self::$hyrax_url]);

		$response = $client->request('POST', 'sanger/authorisations', [
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
			Cache::put('dr_api_token', $key, 60);
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

		$files = self::get_worksheet_files($worksheet);

		$sample_data = $files['sample_data'];
		$errors = $files['errors'];

		if($errors){
			session(['toast_error' => 1, 'toast_message' => 'The upload has errors.']);
			return $errors;
		}

		$postData = [
				'data' => [
					'type' => 'plate_create',
					'attributes' => [
						'plate_name' => "{$worksheet->id}",
					],
				],
				'included' => $sample_data,
			];

		// self::dump_log($postData);

		$response = $client->request('POST', 'sanger/plate', [
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

	public static function processResponse($worksheet, $response)
	{
		$body = json_decode($response->getBody());

		if($response->getStatusCode() < 400)
		{
			$worksheet->plate_id = $body->data->id;
			$worksheet->time_sent_to_exatype = date('Y-m-d H:i:s');
			$worksheet->status_id = 5;
			$worksheet->save();

			foreach ($body->data->attributes->samples as $key => $value) {

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
					$sample_id = \Str::after($value->sample_name, env('DR_PREFIX', ''));
					$sample = DrSample::find($sample_id);
					if($sample->worksheet->id != $worksheet->id){
						if(env('APP_LAB') != 1) continue;
						$sample = DrSample::where(['worksheet_id' => $worksheet->id, 'parentid' => \Str::after($value->sample_name, env('DR_PREFIX', ''))])->first();
						if(!$sample) continue;
					}

					$sample->exatype_id = $value->id;
					$sample->save();
				}
			}
			session(['toast_message' => 'The worksheet has been successfully created at Exatype.']);
			return $body;
		}
		else{
			session(['toast_error' => 1, 'toast_message' => 'Something went wrong. Status code ' . $response->getStatusCode()]);
			return false;
		}

	}


	public static function get_worksheet_files($worksheet)
	{
		$path = storage_path('app/public/results/dr/' . $worksheet->id . '/');

		// $samples = $worksheet->sample_view;
		$samples = $worksheet->sample;
		// $samples->load(['result']);

		$primers = ['F1', 'F2', 'F3', 'R1', 'R2', 'R3'];

		$sample_data = [];
		$print_data = [];
		$errors = [];

		foreach ($samples as $key => $sample) {

			$s = [
				'type' => 'sample_create',
				'attributes' => [
					'sample_name' => "{$sample->mid}",
					// 'sample_name' => "{$sample->nat}",
					'pathogen' => 'hiv',
					'assay' => 'thermo_PR_RT',
					// 'assay' => 'cdc-hiv',
					'enforce_recall' => false,
					'sample_type' => 'data',
				],
			];

			if($sample->control == 1) $s['attributes']['sample_type'] = 'negative';
			if($sample->control == 2) $s['attributes']['sample_type'] = 'positive';

			$abs = [];
			$abs2 = [];

			foreach ($primers as $primer) {
				$ab = self::find_ab_file($path, $sample, $primer);
				// if($ab) $abs[] = $ab;
				if($ab){
					$abs[] = $ab;
					// $abs2[] = ['file_name' => $ab['file_name']];
				}
				else{
					// $errors[] = "Sample {$sample->id} ({$sample->mid}) Primer {$primer} could not be found.";
					if(env('APP_LAB') == 1) $errors[] = "Sample {$sample->id} ({$sample->mid}) Primer {$primer} could not be found.";
					else{
						$errors[] = "Sample {$sample->id} (NAT {$sample->patient->nat}, CCC {$sample->patient->patient}) Primer {$primer} could not be found.";
					}
				}
			}
			if(!$abs) continue;
			$s['attributes']['ab1s'] = $abs;
			$sample_data[] = $s;

			// $s['attributes']['ab1s'] = $abs2;
			// $print_data[] = $s;
		}
		// self::dump_log($print_data);
		// die();
		return ['sample_data' => $sample_data, 'errors' => $errors];
	}

	public static function find_ab_file($path, $sample, $primer)
	{
		$files = scandir($path);
		if(!$files) return null;

		foreach ($files as $file) {
			if($file == '.' || $file == '..') continue;

			$new_path = $path . '/' . $file;
			if(is_dir($new_path)){
				$a = self::find_ab_file($new_path, $sample, $primer);

				if(!$a) continue;
				return $a;
			}
			else{
				// if(\Str::startsWith($file, $sample->mid . $primer)){
				if(\Str::startsWith($file, [$sample->mid . '-', $sample->mid . '_']) && \Str::contains($file, $primer))
				// if(\Str::startsWith($file, $sample->nat . '-') && \Str::contains($file, $primer))
				{
					$a = [
						'file_name' => $file,
						'data' => base64_encode(file_get_contents($new_path)),
					];
					return $a;
				}
				continue;
			}
		}
		return false;
	}

	public static function create_warning($type, $model, $error)
	{
		if($type == 1){
			$class = \App\DrWorksheetWarning::class;
			$column = 'worksheet_id';
		}
		else{
			$class = \App\DrWarning::class;
			$column = 'sample_id';			
		}

		$e = $class::firstOrCreate([
			$column => $model->id,
			'warning_id' => self::get_sample_warning($error->title),
			'system_field' => $error->system ?? '',
			'detail' => $error->detail ?? '',
		]);

		if(!$e->warning_id){
			$e->detail .= " error_name " . $error->title;
			$e->save();
		}
		return $e;
	}

	public static function get_plate_result($worksheet)
	{
		ini_set('memory_limit', '-1');
		$client = new Client(['base_uri' => self::$hyrax_url]);

		$response = $client->request('GET', "sanger/plate/result/{$worksheet->plate_id}", [
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

		if($response->getStatusCode() == 200)
		{
			$w = $body->data->attributes;
			$worksheet->exatype_status_id = self::get_worksheet_status($w->status);
			$worksheet->plate_controls_pass = $w->plate_controls_pass;
			$worksheet->qc_run = $w->plate_qc_run;
			$worksheet->qc_pass = $w->plate_qc->pass ?? 0;
			$worksheet->qc_distance_pass = $w->plate_qc->distance_pass ?? 0;

			if($worksheet->exatype_status_id == 4) return null;

			if($worksheet->exatype_status_id != 5){

				if($w->errors){
					foreach ($w->errors as $error) {
						self::create_warning(1, $worksheet, $error);
					}
				}

				if($w->warnings){
					foreach ($w->warnings as $error) {
						self::create_warning(1, $worksheet, $error);
					}
				}
			}

			$worksheet->status_id = 6;
			$worksheet->save();

			// dd($body->included);

			foreach ($body->included as $key => $value) {

				$sample = DrSample::where(['exatype_id' => $value->id])->first();

				if(!$sample) continue;
				if(in_array($sample->status_id, [1])) continue;

				// echo " {$sample->id} ";

				// if($worksheet->exatype_status_id == 5 && !$worksheet->plate_controls_pass && !$sample->control) continue;

				$s = $value->attributes;
				$sample->status_id = self::get_sample_status($s->status_id);	

				if($sample->status_id == 3)	$sample->qc_pass = 0;			

				if(isset($s->sample_qc_pass)){
					$sample->qc_pass = $s->sample_qc_pass ?? null;

					$sample->qc_stop_codon_pass = $s->sample_qc->stop_codon_pass ?? null;
					$sample->qc_plate_contamination_pass = $s->sample_qc->plate_contamination_pass ?? null;
					$sample->qc_frameshift_codon_pass = $s->sample_qc->frameshift_codon_pass ?? null;
				}

				if(isset($s->sample_qc_distance)){
					$sample->qc_distance_to_sample = $s->sample_qc_distance[0]->to_sample_id ?? null;
					$sample->qc_distance_from_sample = $s->sample_qc_distance[0]->from_sample_id ?? null;
					$sample->qc_distance_difference = $s->sample_qc_distance[0]->difference ?? null;
					$sample->qc_distance_strain_name = $s->sample_qc_distance[0]->strain_name ?? null;
					$sample->qc_distance_compare_to_name = $s->sample_qc_distance[0]->compare_to_name ?? null;
					$sample->qc_distance_sample_name = $s->sample_qc_distance[0]->sample_name ?? null;
				}

				if(isset($s->errors) && $s->errors){
					$sample->has_errors = true;

					foreach ($s->errors as $error) {
						self::create_warning(2, $sample, $error);
					}
				}

				if(isset($s->warnings) && $s->warnings){
					$sample->has_warnings = true;

					foreach ($s->warnings as $error) {
						self::create_warning(2, $sample, $error);
					}
				}

				if(isset($s->calls) && $s->calls){
					// $sample->has_calls = true;

					foreach ($s->calls as $call) {
						// $c = DrCall::where(['sample_id' => $sample->id, 'drug_class' => $call->drug_class])->first();
						// if(!$c) $c = new DrCall;

						// $c->fill([
						// 	'sample_id' => $sample->id,
						// 	'drug_class' => $call->drug_class,
						// 	'other_mutations' => $call->other_mutations,
						// 	'major_mutations' => $call->major_mutations,
						// ]);

						// $c->save();

						// dd($call);

						$c = DrCall::firstOrCreate([
							'sample_id' => $sample->id,
							'drug_class' => $call->drug_class,
							'drug_class_id' => self::get_drug_class($call->drug_class),
							// 'mutations' => $call->mutations ?? [],
							// 'other_mutations' => self::escape_null($call->other_mutations),
							// 'major_mutations' => self::escape_null($call->major_mutations),
						]);

						if(isset($call->mutations) && $call->mutations){
							$sample->has_mutations = true;
							$c->mutations = $call->mutations ?? [];
							$c->save();
						}

						foreach ($call->drugs as $drug) {
							$d = DrCallDrug::firstOrCreate([
								'call_id' => $c->id,
								'short_name' => $drug->short_name,
								'short_name_id' => self::get_short_name_id($drug->short_name),
								'call' => $drug->call,
								'score' => $drug->score ?? null,
							]);
						}
					}
				}

				if(isset($s->genotype) && $s->genotype){
					// $sample->has_genotypes = true;

					foreach ($s->genotype as $genotype) {
						$g = DrGenotype::firstOrCreate([
							'sample_id' => $sample->id,
							'locus' => $genotype->locus,
						]);

						foreach ($genotype->residues as $residue) {
							$r = DrResidue::firstOrCreate([
								'genotype_id' => $g->id,
								'residue' => $residue->residues[0] ?? null,
								'position' => $residue->position,
							]);
						}
					}
				}

				if($s->pending_action == "PendChromatogramManualIntervention"){
					$sample->pending_manual_intervention = true;
				}

				if(!$s->pending_action && $sample->pending_manual_intervention){
					$sample->pending_manual_intervention = false;
					$sample->had_manual_intervention = true;
				}				

				$sample->assembled_sequence = $s->assembled_sequence ?? '';
				$sample->chromatogram_url = $s->chromatogram_url ?? '';
				$sample->exatype_version = $s->exatype_version ?? '';
				$sample->algorithm = $s->algorithm ?? '';
				// $sample->pdf_download_link = $s->sample_pdf_download->signed_url ?? '';
				$sample->save();

				// echo " {$sample->id} ";
			
			}
			session(['toast_message' => 'The worksheet results have been successfully retrieved from Exatype.']);
			return $body;
		}
		else{
			session(['toast_error' => 1, 'toast_message' => 'Something went wrong. Status code ' . $response->getStatusCode()]);
			return false;			
		}

		return $body;

		// dd($body);
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

	public static function get_sample_warning($error_name)
	{
		// if(!DB::table('dr_warning_codes')->where(['name' => $error_name])->first()) dd($id);
		$warning_id = DB::table('dr_warning_codes')->where(['name' => $error_name])->first()->id ?? 0;
		if(!$warning_id){
			$error = 1;
			if(\Str::startsWith($error_name, 'Wrn')) $error = 0;
			DB::table('dr_warning_codes')->insert(['name' => $error_name, 'error' => $error]);
			return self::get_sample_warning($error_name);
		}else{
			return $warning_id;
		}
	}

	public static function get_drug_class($id)
	{
		return DB::table('regimen_classes')->where(['drug_class' => $id])->first()->drug_class_id ?? null;
	}

	public static function get_short_name_id($id)
	{
		return DB::table('regimen_classes')->where(['short_name' => $id])->first()->id ?? null;
	}

	public static function escape_null($var)
	{
		if($var) return $var;
		return null;
	}

	public static function set_drug_classes()
	{
		ini_set('memory_limit', '-1');

		$calls = DrCall::all();

		foreach ($calls as $key => $value) {
			$value->drug_class_id = self::get_drug_class($value->drug_class);
			$value->save();
		}

		$calls = DrCallDrug::all();

		foreach ($calls as $key => $value) {
			$value->short_name_id = self::get_short_name_id($value->short_name);
			$value->save();
		}
	}


	public static function get_extraction_worksheet_samples($limit=48)
	{
		$samples = DrSampleView::whereNull('worksheet_id')
		->whereNull('extraction_worksheet_id')
		->where('datereceived', '>', date('Y-m-d', strtotime('-1 year')))
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



	/*

		public static function generate_samples()
		{
			$potential_patients = \App\DrPatient::where('status_id', 1)->limit(300)->get();

			foreach ($potential_patients as $patient) {
		        $data = $patient->only(['patient_id', 'dr_reason_id']);
		        $data['user_id'] = 0;
		        $data['receivedstatus'] = 1;
		        $data['datecollected'] = date('Y-m-d', strtotime('-2 days'));
		        $data['datereceived'] = date('Y-m-d');
		        // $sample = DrSample::create($data);
		        $sample = new DrSample;
		        $sample->fill($data);
		        $facility = $sample->patient->facility;
		        $sample->facility_id = $facility->id;
		        $sample->save();      

		        $patient->status_id=2;
		        $patient->save();
			}
		}


		public static function regimens()
		{
			$calls = \App\DrCallView::all();

			foreach ($calls as $key => $value) {
				$reg = DB::table('regimen_classes')->where(['drug_class' => $value->drug_class, 'short_name' => $value->short_name])->first();

				if(!$reg){
					DB::table('regimen_classes')->insert(['drug_class' => $value->drug_class, 'short_name' => $value->short_name]);
				}
			}
		}


		public static function seed()
		{		
	    	$e = \App\DrExtractionWorksheet::create(['lab_id' => env('APP_LAB'), 'createdby' => 2, 'date_gel_documentation' => date('Y-m-d')]);

	    	$w = \App\DrWorksheet::create(['extraction_worksheet_id' => $e->id]);

	    	DB::table('dr_samples')->insert([
	    		['id' => 1, 'control' => 1, 'patient_id' => 1, 'worksheet_id' => $w->id, 'extraction_worksheet_id' => $e->id, 'created_at' => date('Y-m-d H:i:s')],
	    		['id' => 2, 'control' => 2, 'patient_id' => 1, 'worksheet_id' => $w->id, 'extraction_worksheet_id' => $e->id, 'created_at' => date('Y-m-d H:i:s')],
	    	]);

	    	$samples = [6, 10, 14, 17, 20, 22, 99, 2009695759, 2012693909, 2012693911, 2012693943, 3005052934, 3005052959, ];

	    	foreach ($samples as $key => $sample) {
	    		$s = DrSample::create(['id' => $sample, 'patient_id' => 1, 'worksheet_id' => $w->id, 'extraction_worksheet_id' => $e->id, 'lab_id' => env('APP_LAB')]);
	    	}
		}

		public static function seed_nhrl()
		{		
	    	$u = \App\User::where('user_type_id', 0)->first();
	    	$e = \App\DrExtractionWorksheet::create(['lab_id' => env('APP_LAB'), 'createdby' => $u->id, 'date_gel_documentation' => date('Y-m-d')]);

	    	$w = \App\DrWorksheet::create(['lab_id' => env('APP_LAB'), 'extraction_worksheet_id' => $e->id, 'createdby' => $u->id, ]);

	    	$samples = [1, 2, 3, 4, 5, 6, 7, 9, 10, 14, 17, 20, 22, ];

	    	foreach ($samples as $key => $sample) {
	    		$s = DrSample::create(['id' => $sample, 'patient_id' => 1, 'worksheet_id' => $w->id, 'extraction_worksheet_id' => $e->id, 'lab_id' => env('APP_LAB')]);
	    	}

	    	DB::table('dr_samples')->insert([
	    		['id' => 23, 'control' => 1, 'patient_id' => 1, 'worksheet_id' => $w->id, 'extraction_worksheet_id' => $e->id, 'created_at' => date('Y-m-d H:i:s'), 'lab_id' => env('APP_LAB')],
	    		['id' => 24, 'control' => 2, 'patient_id' => 1, 'worksheet_id' => $w->id, 'extraction_worksheet_id' => $e->id, 'created_at' => date('Y-m-d H:i:s'), 'lab_id' => env('APP_LAB')],
	    	]);
		}
	*/

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

	public static function clean_warning_codes()
	{
		$current_id = 0;
		$warning_classes = [
			DrWorksheetWarning::class,
			DrContigWarning::class,
			DrWarning::class,
		];

		while(true){
			$drWarningCode = DrWarningCode::where('id', '>', $current_id)->first();
			if(!$drWarningCode) return;

			foreach($warning_classes as $warning_class){
				$warning_class::whereIn('warning_id', DrWarningCode::select('id')
					->where('name', $drWarningCode->name))
				->update(['warning_id' => $drWarningCode->id]);
			}
			DrWarningCode::where('name', $drWarningCode->name)
				->where('id', '!=', $drWarningCode->id)
				->delete();

			$current_id = $drWarningCode->id;
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

	public static function clean_duplicate_calls()
	{
		$rows = DB::select("select call_id, short_name_id, count(id) AS total from dr_call_drugs group by call_id, short_name_id having total > 1;");

		foreach ($rows as $row) {
			$call_drugs = DrCallDrug::where([
				'call_id' => $row->call_id,
				'short_name_id' => $row->short_name_id,
			])->get();

			foreach ($call_drugs as $key => $call_drug) {
				if(!$key) continue;
				$call_drug->delete();
			}
		}
	}

	public static $matches = [
		'6255' => 'CCC1412006255',
		'CCC13218002765' => 'CCC13218_002765',
		'CCC136041554' => 'CCC1360401554',
		'CCC1601200633' => 'CCC633',

		'CCC1307701837' => 'CCC13077-01837',
		'CCC13642467' => 'CCC136042467',
		'CCC1410311244' => 'CCC 11244',
		'CCC141031997' => 'CCC1997',
		'CCC141034788' => 'CCC4788',
		'CCC141034878' => 'CCC4878',
		'CCC141038263' => 'CCC8263',
		'CCC21478CH2' => 'CCC21478-CH2',
		'CCCTI1947CH355' => 'CCCT.I947(CH355)'
	];

	public static function nhrl_worksheets()
	{
		ini_set('memory_limit', '-1');

		$path = storage_path('app/public/results/dr_man');
		$exFiles = scandir($path);
		$primers = ['F1', 'F2', 'F3', 'R1', 'R2', 'R3'];
		$client = new Client(['base_uri' => self::$hyrax_url]);

		$user = User::where('email', 'like', 'joelkith%')->first();

		$matches = self::$matches;

		// Iterating through the root folder
		// E.g. Worksheet 1
		/*foreach ($exFiles as $exFile) {
			if(in_array($exFile, ['.', '..',])) continue;

			$extractionWorksheetPath = $path . '/' . $exFile;
			$seqFolders = scandir($extractionWorksheetPath);*/

			$extractionWorksheetPath = $path;
			$seqFolders = scandir($path);

			$drExtractionWorksheet = DrExtractionWorksheet::create(['status_id' => 1, 'lab_id' => env('APP_LAB'), 'createdby' => $user->id, 'date_gel_documentation' => date('Y-m-d')]);

			// Iterating through sequencing folders
			foreach ($seqFolders as $seqFolder) {
				if(in_array($seqFolder, ['.', '..', ])) continue;

				$seq_path = $extractionWorksheetPath . '/' . $seqFolder;
				if(!is_dir($seq_path)) continue;
				$seq_files = scandir($seq_path);

				$drWorksheet = DrWorksheet::create(['status_id' => 1, 'lab_id' => env('APP_LAB'), 'dateuploaded' => date('Y-m-d'), 'createdby' => $user->id, 'extraction_worksheet_id' => $drExtractionWorksheet->id]);

				$identifiers = $sample_data = $errors = [];

				// Iterate over Sequencing Worksheet files (ab1)
				foreach ($seq_files as $seq_file) {
					if(in_array($seq_file, ['.', '..', ])) continue;

					if(Str::contains($seq_file, ['phd.1', 'scf', 'seq'])) continue;
					if(!Str::contains($seq_file, $primers)) continue;

					$identifier = explode('-', $seq_file);
					$identifier = $identifier[0];

					$lowered_identifier = strtolower($identifier);

					if(in_array($identifier, $identifiers)) continue;
					$identifiers[] = $identifier;

					$id = str_replace('ccc', '', $lowered_identifier);
					$id = str_replace('nat', '', $id);
					$id = str_replace('cnt', '', $id);

					$patient=null;

					if(isset($matches[$identifier])) $patient = Viralpatient::where('patient', $matches[$identifier])->first();
					else if(Str::contains($lowered_identifier, 'ccc')) $patient = Viralpatient::where('patient', 'like', "%{$id}%")->first();
					else if(Str::contains($lowered_identifier, 'nat')) $patient = Viralpatient::where('nat', 'like', "%{$id}%")->first();
					else if(Str::contains($lowered_identifier, 'cnt')){
						$patient = Viralpatient::where('patient', 'like', "%{$id}%")->first();
						if(!$patient) $patient = Viralpatient::where('nat', 'like', "%{$id}%")->first();
					}
					else{
						$patient = Viralpatient::where('patient', 'like', "%ccc{$id}%")->first();
						if(!$patient) $patient = Viralpatient::where('patient', 'like', "%cnt{$id}%")->first();
						// if(!$patient) $patient = Viralpatient::where('patient', 'like', "%{$id}%")->first();
					}

					if(!$patient) continue;
					// if(!$patient) dd('Patient ' . $seq_file . ' ID ' . $id . ' not found');

					$sample = $patient->dr_sample()->whereNull('worksheet_id')->where(['repeatt' => 0])->first();
					if(!$sample) continue;	
					// $sample = $patient->dr_sample()->first();	
					$sample->extraction_worksheet_id = $drExtractionWorksheet->id;
					$sample->worksheet_id = $drWorksheet->id;
					$sample->save();			

					// if(!$sample) dd('Sample ' . $seq_file . ' ID ' . $id . ' not found');

					$s = [
						'type' => 'sample_create',
						'attributes' => [
							'sample_name' => "{$sample->mid}",
							// 'sample_name' => "{$sample->nat}",
							'pathogen' => 'hiv',
							'assay' => 'thermo_PR_RT',
							// 'assay' => 'cdc-hiv',
							'enforce_recall' => false,
							'sample_type' => 'data',
						],
					];

					$abs = [];

					foreach ($primers as $primer) {
						$ab = self::find_ab_file_two($extractionWorksheetPath, $identifier, $primer);
						if($ab) $abs[] = $ab;
						else{
							$errors[] = "Sample {$sample->id} ({$seq_file}) Primer {$primer} could not be found.";
						}
					}
					if(!$abs) continue;
					$s['attributes']['ab1s'] = $abs;
					$sample_data[] = $s;
				}
				// End of Iterating over directory with ab1 files 

				// dd($sample_data);
				// dd($errors);
				// dd($sample_data);

				if(!$drWorksheet->sample->count()){
					$drWorksheet->delete();
					continue;
				}

				$postData = [
					'data' => [
						'type' => 'plate_create',
						'attributes' => [
							'plate_name' => "{$drWorksheet->id}",
						],
					],
					'included' => $sample_data,
				];


				$response = $client->request('POST', 'sanger/plate', [
		            'http_errors' => false,
		            // 'debug' => true,
					'headers' => [
						// 'Accept' => 'application/json',
						// 'x-hyrax-daemon-apikey' => self::get_hyrax_key(),
						'X-Hyrax-Apikey' => self::get_hyrax_key(),
					],
					'json' => $postData,
				]);

				self::processResponse($drWorksheet, $response);
			}
			// End of Iterating through sequencing folders
		// }
		// End of iterating through root folder
		return false;
	}

	public static function find_ab_file_two($path, $identifier, $primer)
	{
		// dd($identifier);
		$files = scandir($path);
		if(!$files) return null;

		foreach ($files as $file) {
			if($file == '.' || $file == '..') continue;

			$new_path = $path . '/' . $file;
			if(is_dir($new_path)){
				$a = self::find_ab_file_two($new_path, $identifier, $primer);

				if(!$a) continue;
				return $a;
			}
			else{
				if(\Str::startsWith($file, [$identifier]) && \Str::contains($file, $primer))
				{
					$a = [
						'file_name' => $file,
						'data' => base64_encode(file_get_contents($new_path)),
					];
					return $a;
				}
				continue;
			}
		}
		return false;
	}

	public static function import_worksheets()
	{
		$path = public_path('hiv_dr_worksheets.xlsx');
        Excel::import(new DrWorksheetsImport, $path);
        dd(session('duplicates'));
	}

	public static function clean_patient_ccc()
	{
		$patients = Viralpatient::where('patient', 'like', 'ccc%')->get();

		foreach ($patients as $key => $p) {
			$p->patient = trim(Str::after($p->patient, 'ccc'));
			$p->save();
		}
	}


	public static function clean_patient_cnt()
	{
		$patients = Viralpatient::where('patient', 'like', '%cnt%')->get();

		foreach ($patients as $key => $p) {

			$p->cnt = self::clean_identifier(Str::after($p->patient, 'cnt'));
			$p->patient = self::clean_identifier(Str::before($p->patient, 'cnt'));

			$p->save();
		}
	}


	public static function clean_identifier($str)
	{
		$str = str_replace('(', '', $str);
		$str = str_replace(')', '', $str);
		$str = str_replace(',', '', $str);
		return trim($str);
	}
}
