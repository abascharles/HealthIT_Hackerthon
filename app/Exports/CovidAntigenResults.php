<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

use DB;

use App\Lookup;
use App\CovidSampleView;

class CovidAntigenResults implements FromArray, WithTitle
{

	protected $from_date;
	protected $to_date;

    function __construct($from_date, $to_date)
    {
    	$this->from_date = $from_date;
    	$this->to_date = $to_date;
    }


    public function array(): array
    {
    	$user = auth()->user();
    	$lab = $user->lab;
    	$data = [];
    	$data[] = ["COVID-19 Antigen Results Upload Template
Note: Fields in red must be filled", '', '', '', '', '', '', '', '', '', 'Facility Name:', $lab->labdesc];
    	$data[] = ['', 'Sample No.', 'Type of case (Initial / Repeat)', 'Date of sample collection (DD/MM/YYYY)', 'Client Name                                                                 (First, Middle, Last)', 'Age', 'Age Unit (Days / Months / Years)', 'Sex', 'Telephone Number',  'ID / Passport Number', 'Occupation', 'Nationality', 'County of Residence', 'Sub-county of Residence', 'Village / Estate of Residence', 'Sample Type', 'Reason for Testing', 'Have Symptoms', 'Date of Onset of Symptoms
 (DD/MM/YYYY)', 'Symptoms shown', 'Assay Kit Name', 'Lot No.', 'Antigen  Result', 'Action Taken', 'Date of PCR Sample collection', 'PCR Result', 'Tester Initials', 'Remarks'];
 		$data[] = [];
 		$a = array_fill(0, 15, '');
 		$data[] = array_merge($a, ['Sample Type']);
 		$b = array_fill(0, 18, '');
 		$data[] = array_merge($b, ['If more than one separate with a slash (/) eg 1/5/8', '', 'Expiry
          /        /']);
 		$c = array_fill(0, 15, '');
 		$data[] = array_merge($c, ['(NP swab, OP Swab, NP/OP Swabs, Sputum, Serum)', ' (Refer to Codes)', ' (Y / N)', '', ' (Refer to Codes)', '', '', '(N, P, I)', ' (Refer to Codes)', '(DD/MM/YYYY) or NA)', '(N / P)']);
 		$d = [''];

 		for ($l=1; $l < 27; $l++) { 
 			$d[] = '(' . chr(96 + $l) . ')';
 		}
 		for ($l=1; $l < 3; $l++) { 
 			$d[] = '(a' . chr(96 + $l) . ')';
 		}
 		$data[] = $d;

    	$from_date = $this->from_date;
    	$to_date = $this->to_date;


		$samples = CovidSampleView::whereIn('result', [1,2,5])
						->where(['repeatt' => 0, 'antigen' => true, 'datedispatched' => $date])
						->when(true, function($query) use($from_date, $to_date){
							if($to_date) return $query->whereBetween('datedispatched', [$from_date, $to_date]);
							return $query->where('datedispatched', $from_date);
						})						
						->get();

		$a = ['nationalities', 'covid_sample_types', 'covid_symptoms', 'covid_justifications'];
		$lookups = [];
		foreach ($a as $value) {
			$lookups[$value] = DB::table($value)->get();
		}

		$symptoms_array = [];
		foreach ($lookups['covid_symptoms'] as $key => $value) {
			$symptoms_array[$key] = $value;
		}

		foreach ($samples as $sample) {
			$has_symptoms = 'N';
			$symptoms = '';
			if($sample->date_symptoms){
				$has_symptoms = 'Y';
				foreach ($sample->symptoms as $value) {
					// $symptoms .= $symptoms_array[$value] . '/';
				}
			}

			$data[] = [
				'',
				$sample->id,
				$sample->sample_type == 1 ? 'Initial' : 'Repeat',
				date('d/m/Y', strtotime($sample->datecollected)),
				$sample->patient_name,
				$sample->age,
				'Years',
				substr($sample->gender, 0, 1),
				$sample->phone_no,
				$sample->national_id,
				$sample->occupation,
				$sample->get_prop_name($lookups['nationalities'], 'nationality'),
				$sample->countyname ?? $sample->county,
				$sample->subcountyname ?? $sample->sub_county ?? $sample->subcounty ?? '',
				$sample->residence,
				$sample->get_prop_name($lookups['covid_sample_types'], 'sample_type', 'nphl_name'),
				$sample->get_prop_name($lookups['covid_justifications'], 'justification', 'nphl_code'),
				$has_symptoms,
				date('d/m/Y', strtotime($sample->date_symptoms)),
				$symptoms,
				$sample->assay_kit_name,
				$sample->lot_no . ' ' . date('d/m/Y', strtotime($sample->kit_expiry)),
				$sample->nphl_result_name,
				'',
				'',
				'',
				substr($user->surname, 0, 1) . '.' . substr($user->oname, 0, 1),
				$sample->labcomment,
			];
		}

    }

    public function title(): string
    {
    	return 'COVID Antigen Test Register';
    }
}
