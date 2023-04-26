<?php

namespace App;

use DB;
use Str;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Mail;

use App\Mail\TestMail;
use Carbon\Carbon;
use Exception;

class Covid
{

	public static function synch_to_nphl()
	{
		$samples = Traveller::whereIn('result', [1,2])
						// ->where(['datedispatched' => date('Y-m-d', strtotime('-1 day')), 'sent_to_nphl' => 0])
						->where(['sent_to_nphl' => 0, ])
						->where('datedispatched', '>', date('Y-m-d', strtotime('-6 days')))
						// ->where('datedispatched', '>=', '2020-10-01')
						->limit(30)
						->get();

		$client = new Client(['base_uri' => env('NPHL_URL')]);

		foreach ($samples as $key => $sample) {
			$travelled = 'No';
			$history = '';

			$has_symptoms = 'No';
			$symptoms = '';

			$post_data = [
				'USERNAME' => env('NPHL_USERNAME'),
				'PASSWORD' => env('NPHL_PASSWORD'),
				'TESTING_LAB' => $lab->nphl_code ?? '00024',

				'CASE_ID' => $sample->id_passport,
				'CASE_TYPE' => 'Initial',
				'SAMPLE_TYPE' => 'OP Swab',
				'SAMPLE_NUMBER' => 'IGM/IGG-' . $sample->id,
				'SAMPLE_COLLECTION_DATE' => $sample->datecollected ?? $sample->datetested,
				'RECEIVED_ON' => $sample->datereceived ?? $sample->datetested,
				'RESULT' => $sample->result_name,
				'LAB_CONFIRMATION_DATE' => $sample->datedispatched ?? $sample->datetested,

				'FIRST_FOLLOW_UP_DATE' => null,
				'FIRST_FOLLOW_UP_RESULT' => null,
				'SECOND_FOLLOW_UP_DATE' => null,
				'SECOND_FOLLOW_UP_RESULT' => null,
				'THIRD_FOLLOW_UP_DATE' => null,
				'THIRD_FOLLOW_UP_RESULT' => null,

				'PATIENT_NAMES' => $sample->patient_name,
				'PATIENT_PHONE' => $sample->phone_no,
				'AGE' => $sample->age ?? 0,
				'AGE_UNIT' => $sample->age_unit ?? 'Years',
				'GENDER' => substr($sample->gender, 0, 1),
				'OCCUPATION' => $sample->occupation,
				'NATIONALITY' => $sample->citizenship,
				'NATIONAL_ID' => $sample->id_passport ?? $sample->identifier,
				'COUNTY' => $sample->countyname ?? $sample->county ?? 'None',
				'SUB_COUNTY' => $sample->subcountyname ?? $sample->sub_county ?? $sample->subcounty ?? 'None',
				'WARD' => $sample->ward ?? $sample->residence,
				'VILLAGE' => $sample->residence,

				'HAS_TRAVEL_HISTORY' => 'No',
				'TRAVEL_FROM' => '',
				'CONTACT_WITH_CASE' => 'No',
				'CONFIRMED_CASE_NAME' => null,

				'SYMPTOMATIC' => 'No',
				'SYMPTOMS' => '',
				'SYMPTOMS_ONSET_DATE' => $sample->date_symptoms,
				'COUNTY_OF_DIAGNOSIS' => $sample->countyname ?? $sample->county,

				'QUARANTINED_FACILITY' => $sample->quarantine_site ?? $sample->facilityname ?? null,
				'HOSPITALIZED' => $sample->date_admission ? 'Yes' : 'Unknown',
				'ADMISSION_DATE' => $sample->date_admission,
			];

			// dd(self::get_token($lab));
			$response = $client->request('post', '', [
				'http_errors' => false,
				'verify' => false,
				'form_params' => $post_data,
			]);

			

			$body = json_decode($response->getBody());
			// dd($body);
			if($response->getStatusCode() < 400){
				if($body->status == 'SUCCESS'){
					$sample->sent_to_nphl = 1;
					$sample->time_sent_to_nphl = date('Y-m-d H:i:s');
					$sample->save();
					echo 'Status code ' . $response->getStatusCode() . "\n";
				}
				if($body->status == 'ERROR'){
					if($sample->time_sent_to_nphl) continue;
					$sample->sent_to_nphl = 2;
					if(\Str::contains($body->message, ['SAMPLE_NUMBER'])) $sample->sent_to_nphl = 1;
					$sample->save();
					print_r($body);
					continue;
				}

			}else{
				dd($body);
			}
		}
	}


}