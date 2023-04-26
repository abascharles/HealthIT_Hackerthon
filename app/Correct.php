<?php

namespace App;

use GuzzleHttp\Client;

use App\Sample;
use App\Batch;
use App\Patient;
use App\Mother;
use App\Worksheet;

use App\Viralsample;
use App\Viralbatch;
use App\Viralpatient;
use App\Viralworksheet;

class Correct
{
    private static $limit = 1000;

	public function correct_eid()
	{
        ini_set("memory_limit", "-1"); 
        $iterations = 0;
		$today = date('Y-m-d');

        $base = \App\Synch::$base;
        $client = new Client(['base_uri' => $base]);

        while(true)
        {
	        $samples = Sample::with(['patient.mother', 'batch'])->whereNull('national_sample_id')
		        				->limit(self::$limit)->get();

		    if($samples->isEmpty()) break;

			$response = $client->request('post', 'correct/eid', [
				'headers' => [
					'Accept' => 'application/json',
				],
				'json' => [
					'samples' => $samples->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);

			$body = json_decode($response->getBody());

			foreach ($body->mothers as $key => $value) {
				$update_data = ['national_mother_id' => $value->national_mother_id, 'synched' => 1, 'datesynched' => $today,];
				Mother::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->patients as $key => $value) {
				$update_data = ['national_patient_id' => $value->national_patient_id, 'synched' => 1, 'datesynched' => $today,];
				Patient::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->batches as $key => $value) {
				$update_data = ['national_batch_id' => $value->national_batch_id, 'synched' => 1, 'datesynched' => $today,];
				Batch::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->samples as $key => $value) {
				$update_data = ['national_sample_id' => $value->national_sample_id, 'synched' => 1, 'datesynched' => $today,];
				Sample::where('id', $value->original_id)->update($update_data);
			}

			$iterations++;
			if($iterations > 1000) break;
        }
	}

	public function correct_vl()
	{
        ini_set("memory_limit", "-1"); 
        $iterations = 0;
		$today = date('Y-m-d');

        $base = \App\Synch::$base;
        $client = new Client(['base_uri' => $base]);

        while(true)
        {
	        $samples = Viralsample::with(['patient', 'batch'])->whereNull('national_sample_id')
		        				->limit(self::$limit)->get();

		    if($samples->isEmpty()) break;

			$response = $client->request('post', 'correct/eid', [
				'headers' => [
					'Accept' => 'application/json',
				],
				'json' => [
					'samples' => $samples->toJson(),
					'lab_id' => env('APP_LAB', null),
				],

			]);

			$body = json_decode($response->getBody());

			foreach ($body->patients as $key => $value) {
				$update_data = ['national_patient_id' => $value->national_patient_id, 'synched' => 1, 'datesynched' => $today,];
				Viralpatient::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->batches as $key => $value) {
				$update_data = ['national_batch_id' => $value->national_batch_id, 'synched' => 1, 'datesynched' => $today,];
				Viralbatch::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->samples as $key => $value) {
				$update_data = ['national_sample_id' => $value->national_sample_id, 'synched' => 1, 'datesynched' => $today,];
				Viralsample::where('id', $value->original_id)->update($update_data);
			}

			$iterations++;
			if($iterations > 1000) break;
        }
	}
}
