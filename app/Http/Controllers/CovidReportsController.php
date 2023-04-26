<?php

namespace App\Http\Controllers;

use App\CovidSampleView;
use App\CovidSample;
use App\CovidWorksheet;
use App\Lab;
use App\MiscCovid;
use Carbon\Carbon;
use Excel;
use DB;
use Illuminate\Http\Request;
use App\Exports\CovidAntigenExport;

class CovidReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware('covid_allowed');   
    }
    
    private $quarters = ['Q1' => '1,2,3', 'Q2' => '4,5,6', 'Q3' => '7,8,9', 'Q4' => '10,11,12'];
	public function index()
	{
		return view('reports.covidreports');
	}

	public function generate(Request $request)
	{
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "720");
        
		// Get the dates
		$date = Carbon::parse($request->input('date_filter'))->format('Y-m-d');
		$date_to = $request->input('date_filter_to');

		if($request->input('types') == 'nphl_results_submission') return $this->nphl_upload($date);
		if($request->input('types') == 'nphl_results_submission_latest') return $this->nphl_upload_v3($date);
		if($request->input('types') == 'nphl_api_submission') return $this->nphl_api_download();
		if($request->input('types') == 'worksheet_machines') return $this->worksheet_report();
		if($request->input('types') == 'worksheet_report') return $this->worksheets_no_reruns($date, $date_to);
		if($request->input('types') == 'nphl_antigen_submission') return new CovidAntigenExport($request);

		
		// Get the data from the database
		$today_data = $this->get_model()->whereDate('datetested', $date)->orderBy('result', 'desc')->get();
		$last_update_data = $this->get_model()->whereRaw("DATE(datetested) < '{$date}'")->get();
		
		// Prepare the data to fill the excel
		$data = $this->prepareData($today_data, $last_update_data, $date, $date_to);

		return \App\MiscCovid::csv_download($data, 'DAILY COVID-19 LABORATORY RESULTS ' . $date, false);
	}

	private function get_model($lab_id = null)
	{
		$user = auth()->user();
		return CovidSampleView::where('repeatt', 0)
						->whereNotNull('result')
						->when($user, function ($query) use ($user, $lab_id) {
							if ($user->user_type_id == 12 && !$lab_id){
								return $query->where('lab_id', $user->lab_id);
							}
							if($lab_id) return $query->where('lab_id', $lab_id);
						})
						->when((env('APP_LAB') == 5), function($query){
							return $query->orderBy('worksheet_id', 'asc')
									->orderBy('run', 'desc')
									->orderBy('covid_sample_view.id', 'asc');
						});
	}

	private function filterDate($model, $request)
	{
		if ($request->input('types') == 'daily_results_submission') 
		{
			$model = $model->whereDate('datetested', Carbon::now()->toDateString());
		} else {
			if ($request->input('period') == 'annually')
				$model = $model->whereYear('datetested', $request->input('year'));

			if ($request->input('period') == 'monthly')
				$model = $model->whereYear('datetested', $request->input('year'))
							->whereMonth('datetested', $request->input('month'));

			if ($request->input('period') == 'quarterly')
				$model = $model->whereYear('datetested', $request->input('year'))
							->whereRaw("MONTH(datetested) IN ({$this->quarters[$request->input('quarter')]})");

			if ($request->input('period') == 'range')
				$model = $model->whereRaw("DATE(datetested) BETWEEN '{$request->input('fromDate')}' AND '{$request->input('toDate')}'");
		}
	

		return $model;
	}

	private function prepareData($today_data, $last_update_data, $date, $date_to=null)
	{
		$data = [[Lab::find(auth()->user()->lab_id)->labdesc . ' DAILY COVID-19 LABORATORY RESULTS SUBMISSION']];
		$data[] = [
			'Date', 'Testing Laboratory', 'Cumulative number of samples tested as at last update', 'Number of samples tested since last update', 'Cumulative number of samples tested to date ', 'Cumulative positive tests as at last update ', 'Number of new Positive tests', 'Cumulative Positive samples since onset of outbreak'
		];
		$data[] = $this->get_summary_data($today_data, $last_update_data, $date, $date_to);

		if(env('APP_LAB') == 1 && auth()->user()->lab_id == env('APP_LAB')){
			$labs = CovidSample::selectRaw('DISTINCT lab_id AS lab_id')->where('lab_id', '!=', env('APP_LAB'))->where('site_entry', '!=', 2)->get();

			foreach ($labs as $key => $value) {
				$today_data_other = $this->get_model($value->lab_id)					
					->when($date, function($query) use($date, $date_to){
						if($date_to) return $query->whereBetween('datetested', [$date, $date_to]);
						return $query->whereDate('datetested', $date);
					})
					->orderBy('result', 'desc')
					->get();
				$last_update_data_other = $this->get_model($value->lab_id)->whereDate("datetested", '<', $date)->get();
				$data[] = $this->get_summary_data($today_data_other, $last_update_data_other, $date, $date_to, $value->lab_id);
			}
		}

		for ($i=0; $i < 2; $i++) { 
			$data[] = [""];
		}

		foreach ($this->get_detailed_data($today_data) as $key => $value) {
			$data[] = $value;
		}
		// dd($data);
		
		return $data;
	}

	private function get_summary_data($today_data, $last_update_data, $date, $date_to=null, $lab_id=null)
	{
		if(!$lab_id) $lab_id = auth()->user()->lab_id;
		$lab = Lab::find($lab_id);
		// if($lab->id != 1) dd($lab);

		return [
			$date,
			$lab->labdesc,
			$last_update_data->count(),
			$today_data->count(),
			($last_update_data->count() + $today_data->count()),
			$last_update_data->whereIn('result', [2,8])->count(),
			$today_data->whereIn('result', [2,8])->count(),
			($last_update_data->whereIn('result', [2,8])->count() + $today_data->whereIn('result', [2,8])->count())
		];
	}

	private function get_detailed_data($alldata)
	{
		$data = [['Testing Lab', 'S/N', 'Lab ID', 'Name', 'Age', 'Sex', 'ID / Passport Number', 'National ID', 'Justification', 'Health Status',
				'Telephone Number', 'County of Residence', 'Sub-County', 'Travel History (Y/N)',
				'Where from', 'history of contact with confirmed case', 'Facility Name (Quarantine /health facility)', 'Name of Confirmed Case', 'Worksheet Number', 'Date Collected', 'Date Tested', 'Result', 'Test Type'
				]];
		$count = 1;
		$a = ['covid_justifications', 'health_statuses'];
		$lookups = [];
		foreach ($a as $value) {
			$lookups[$value] = DB::table($value)->get();
		}
		foreach ($alldata as $key => $row) {
			$data[] = $this->get_excel_samples($row, $count, $lookups);
			$count++;
		}
		return $data;
	}

	private function get_excel_samples($sample, $count, $lookups)
	{
		$travelled = 'N';
		$history = '';
		if (!$sample->patient->travel->isEmpty()){
			$travelled = 'Y';
			foreach ($sample->patient->travel as $key => $travel) {
				if(!$travel->town) continue;
				$history .= $travel->town->name . ', ' . $travel->town->country . '\n';
			}
		}
		return [
			// Lab::find(env('APP_LAB'))->labdesc,
			Lab::find($sample->lab_id)->labdesc,
			$count,
			$sample->id,
			$sample->patient_name,
			$sample->age,
			$sample->gender,
			$sample->identifier,
			$sample->national_id,
			$sample->get_prop_name($lookups['covid_justifications'], 'justification'),
			$sample->get_prop_name($lookups['health_statuses'], 'health_status'),
			$sample->phone_no ?? '',
			$sample->countyname ?? $sample->county,
			$sample->sub_county ?? $sample->subcountyname ?? $sample->subcounty ?? '',

			$travelled,
			$history,
			"",
			$sample->quarantine_site ?? $sample->facilityname ?? '',
			"",
			$sample->worksheet_id ?? '',
			$sample->datecollected ?? '',
			$sample->datetested ?? '',
			$sample->result_name,
			$sample->sampletype ?? ''
		];
	}


	private function nphl_upload($date)
	{
		$user = auth()->user();
		$lab = Lab::find(env('APP_LAB'))->labdesc;
		$samples = CovidSampleView::where('repeatt', 0)
						->whereIn('result', [1,2])
						->where('datedispatched', $date)
						->get();
						// ->when($user, function ($query) use ($user, $lab_id) {
						// 	if ($user->user_type_id == 12 && !$lab_id){
						// 		return $query->where('lab_id', $user->lab_id);
						// 	}							
						// })

		$data = [];

		$a = ['nationalities', 'covid_sample_types', 'covid_symptoms'];
		$lookups = [];
		foreach ($a as $value) {
			$lookups[$value] = DB::table($value)->get();
		}

		$data[] = [];

		$data[] = [
			'TESTING LAB', 'CASE ID', 'TYPE OF CASE (INITIAL/REPEAT)', 'SAMPLE NUMBER', 'NAME', 'ID/PASSPORT NUMBER', 'AGE', 'AGE UNIT (DAYS/MONTHS/YEAR)', 'GENDER (M/F)','PHONE NUMBER', 'OCCUPATION', 'NATIONALITY', 'COUNTY OF RESIDENCE', 'SUB COUNTY OF RESIDENCE', 'VILLAGE/ESTATE OF RESIDENCE', 'WARD', 'COUNTY OF DIAGNOSIS', 'HAS TRAVEL HISTORY (LAST 14 DAYS) Y/N', 'TRAVEL FROM', 'CONTACT WITH CASE (Y/N)', 'CONFIRMED CASE NAME', 'QUARANTINE FACILITY/HOSPITAL/HOMESTEAD', 'HAS SYMPTOMS (Y/N)', 'DATE OF ONSET OF SYMPTOMS', 'SYMPTOMS SHOWN (COUGH;FEVER;ETC)', 'SAMPLE TYPE (NP SWAB, OP SWAB, SERUM, SPUTUM ETC)', 'DATE OF SAMPLE COLLECTION', 'RESULT', 'LAB CONFIRMATION DATE',
		];

		// $symptoms_array = [];
		foreach ($lookups['covid_symptoms'] as $key => $value) {
			$symptoms_array[$key] = $value;
		}


		foreach ($samples as $key => $sample) {
			$travelled = 'N';
			$history = '';
			if (!$sample->patient->travel->isEmpty()){
				$travelled = 'Y';
				foreach ($sample->patient->travel as $key => $travel) {
					if(!$travel->town) continue;
					$history .= $travel->town->name . ', ' . $travel->town->country . '\n';
				}
			}

			$has_symptoms = 'N';
			$symptoms = '';
			if($sample->date_symptoms){
				$has_symptoms = 'Y';
				foreach ($sample->symptoms as $value) {
					$symptoms .= $symptoms_array[$value] . ';';
				}
			}
			$current_lab = null;
			if($sample->lab_id != env('APP_LAB')) $current_lab = Lab::find(env('APP_LAB'))->labdesc;

			$data[] = [
				$current_lab ?? $lab,
				$sample->identifier,
				$sample->sample_type == 1 ? 'Initial' : 'Repeat',
				$sample->id,
				$sample->patient_name,
				$sample->national_id,
				$sample->age,
				'Years',
				substr($sample->gender, 0, 1),
				$sample->phone_no,
				$sample->occupation,
				$sample->get_prop_name($lookups['nationalities'], 'nationality'),
				$sample->countyname ?? $sample->county,
				$sample->subcountyname ?? $sample->sub_county ?? $sample->subcounty ?? '',
				$sample->residence,
				'',
				$sample->countyname ?? $sample->county,
				$travelled,
				$history,
				'',
				'',
				$sample->quarantine_site ?? $sample->facilityname ?? '',
				$has_symptoms,
				$sample->date_symptoms,
				$symptoms,
				$sample->get_prop_name($lookups['covid_sample_types'], 'sample_type'),
				$sample->datecollected,
				$sample->result_name,
				$sample->datedispatched,
			];
		}
		return MiscCovid::csv_download($data, 'COVID-19 LABORATORY RESULTS FOR NPHL UPLOAD FOR ' . $date, false);
	}


	private function nphl_upload_v3($date)
	{
		$user = auth()->user();
		$lab = Lab::find(env('APP_LAB'))->labdesc;
		$samples = CovidSampleView::where('repeatt', 0)
						->whereIn('result', [1,2])
						->where('justification', 16)
						->where('datedispatched', $date)
						->get();
						// ->when($user, function ($query) use ($user, $lab_id) {
						// 	if ($user->user_type_id == 12 && !$lab_id){
						// 		return $query->where('lab_id', $user->lab_id);
						// 	}							
						// })

		$data = [];

		$a = ['nationalities', 'covid_sample_types', 'covid_symptoms'];
		$lookups = [];
		foreach ($a as $value) {
			$lookups[$value] = DB::table($value)->get();
		}

		$data[] = [];

		$data[] = [
			'REASON FOR TESTING', 'CASE ID', 'TYPE OF CASE (INITIAL/REPEAT)', 'SAMPLE NUMBER', 'NAME', 'ID/PASSPORT NUMBER', 'AGE', 'AGE UNIT (DAYS/MONTHS/YEAR)', 'GENDER (M/F)','PHONE NUMBER', 'OCCUPATION', 'NATIONALITY', 'COUNTY OF RESIDENCE', 'SUB COUNTY OF RESIDENCE', 'VILLAGE/ESTATE OF RESIDENCE', 'WARD', 'COUNTY OF DIAGNOSIS', 'HAS TRAVEL HISTORY (LAST 14 DAYS) Y/N', 'TRAVEL FROM', 'CONTACT WITH CASE (Y/N)', 'CONFIRMED CASE NAME', 'QUARANTINE FACILITY/HOSPITAL/HOMESTEAD', 'HAS SYMPTOMS (Y/N)', 'DATE OF ONSET OF SYMPTOMS', 'SYMPTOMS SHOWN (COUGH;FEVER;ETC)', 'SAMPLE TYPE (NP SWAB, OP SWAB, SERUM, SPUTUM ETC)', 'DATE OF SAMPLE COLLECTION', 'RESULT', 'LAB CONFIRMATION DATE', 'EMAIL ADDRESS',
		];

		// $symptoms_array = [];
		foreach ($lookups['covid_symptoms'] as $key => $value) {
			$symptoms_array[$key] = $value;
		}


		foreach ($samples as $key => $sample) {
			$travelled = 'N';
			$history = '';
			if (!$sample->patient->travel->isEmpty()){
				$travelled = 'Y';
				foreach ($sample->patient->travel as $key => $travel) {
					if(!$travel->town) continue;
					$history .= $travel->town->name . ', ' . $travel->town->country . '\n';
				}
			}

			$has_symptoms = 'N';
			$symptoms = '';
			if($sample->date_symptoms){
				$has_symptoms = 'Y';
				foreach ($sample->symptoms as $value) {
					$symptoms .= $symptoms_array[$value] . ';';
				}
			}
			$current_lab = null;
			if($sample->lab_id != env('APP_LAB')) $current_lab = Lab::find(env('APP_LAB'))->labdesc;

			$data[] = [
				// $current_lab ?? $lab,
				$sample->nphl_justification,
				$sample->identifier,
				$sample->sample_type == 1 ? 'Initial' : 'Repeat',
				$sample->id,
				$sample->patient_name,
				$sample->national_id,
				$sample->age,
				'Years',
				substr($sample->gender, 0, 1),
				$sample->phone_no,
				$sample->occupation,
				$sample->get_prop_name($lookups['nationalities'], 'nationality'),
				$sample->countyname ?? $sample->county,
				$sample->subcountyname ?? $sample->sub_county ?? $sample->subcounty ?? '',
				$sample->residence,
				'',
				$sample->countyname ?? $sample->county,
				$travelled,
				$history,
				'',
				'',
				$sample->quarantine_site ?? $sample->facilityname ?? '',
				$has_symptoms,
				$sample->date_symptoms,
				$symptoms,
				$sample->get_prop_name($lookups['covid_sample_types'], 'sample_type'),
				$sample->datecollected,
				$sample->result_name,
				$sample->datedispatched,
				$sample->email_address,
			];
		}
		return MiscCovid::csv_download($data, 'COVID-19 LABORATORY RESULTS FOR NPHL UPLOAD FOR ' . $date, false);
	}


	private function nphl_upload_antigen($date)
	{
		$user = auth()->user();
		$lab = Lab::find(env('APP_LAB'))->labdesc;
		$samples = CovidSampleView::whereIn('result', [1,2,5])
						->where(['repeatt' => 0, 'antigen' => true, 'datedispatched' => $date])
						->get();
						

		$data = [];

		$a = ['nationalities', 'covid_sample_types', 'covid_symptoms'];
		$lookups = [];
		foreach ($a as $value) {
			$lookups[$value] = DB::table($value)->get();
		}

		$data[] = [];

		$data[] = [
			'REASON FOR TESTING', 'CASE ID', 'TYPE OF CASE (INITIAL/REPEAT)', 'SAMPLE NUMBER', 'NAME', 'ID/PASSPORT NUMBER', 'AGE', 'AGE UNIT (DAYS/MONTHS/YEAR)', 'GENDER (M/F)','PHONE NUMBER', 'OCCUPATION', 'NATIONALITY', 'COUNTY OF RESIDENCE', 'SUB COUNTY OF RESIDENCE', 'VILLAGE/ESTATE OF RESIDENCE', 'WARD', 'COUNTY OF DIAGNOSIS', 'HAS TRAVEL HISTORY (LAST 14 DAYS) Y/N', 'TRAVEL FROM', 'CONTACT WITH CASE (Y/N)', 'CONFIRMED CASE NAME', 'QUARANTINE FACILITY/HOSPITAL/HOMESTEAD', 'HAS SYMPTOMS (Y/N)', 'DATE OF ONSET OF SYMPTOMS', 'SYMPTOMS SHOWN (COUGH;FEVER;ETC)', 'SAMPLE TYPE (NP SWAB, OP SWAB, SERUM, SPUTUM ETC)', 'DATE OF SAMPLE COLLECTION', 'RESULT', 'LAB CONFIRMATION DATE', 'EMAIL ADDRESS',
		];

		// $symptoms_array = [];
		foreach ($lookups['covid_symptoms'] as $key => $value) {
			$symptoms_array[$key] = $value;
		}


		foreach ($samples as $key => $sample) {
			$travelled = 'N';
			$history = '';
			if (!$sample->patient->travel->isEmpty()){
				$travelled = 'Y';
				foreach ($sample->patient->travel as $key => $travel) {
					if(!$travel->town) continue;
					$history .= $travel->town->name . ', ' . $travel->town->country . '\n';
				}
			}

			$has_symptoms = 'N';
			$symptoms = '';
			if($sample->date_symptoms){
				$has_symptoms = 'Y';
				foreach ($sample->symptoms as $value) {
					$symptoms .= $symptoms_array[$value] . ';';
				}
			}
			$current_lab = null;
			if($sample->lab_id != env('APP_LAB')) $current_lab = Lab::find(env('APP_LAB'))->labdesc;

			$data[] = [
				// $current_lab ?? $lab,
				$sample->nphl_justification,
				$sample->identifier,
				$sample->sample_type == 1 ? 'Initial' : 'Repeat',
				$sample->id,
				$sample->patient_name,
				$sample->national_id,
				$sample->age,
				'Years',
				substr($sample->gender, 0, 1),
				$sample->phone_no,
				$sample->occupation,
				$sample->get_prop_name($lookups['nationalities'], 'nationality'),
				$sample->countyname ?? $sample->county,
				$sample->subcountyname ?? $sample->sub_county ?? $sample->subcounty ?? '',
				$sample->residence,
				'',
				$sample->countyname ?? $sample->county,
				$travelled,
				$history,
				'',
				'',
				$sample->quarantine_site ?? $sample->facilityname ?? '',
				$has_symptoms,
				$sample->date_symptoms,
				$symptoms,
				$sample->get_prop_name($lookups['covid_sample_types'], 'sample_type'),
				$sample->datecollected,
				$sample->result_name,
				$sample->datedispatched,
				$sample->email_address,
			];
		}
		return MiscCovid::csv_download($data, 'COVID-19 LABORATORY RESULTS FOR NPHL UPLOAD FOR ' . $date, false);
	}

	public function nphl_api_download()
	{
		$samples = CovidSampleView::whereIn('result', [1,2])
						->where(['repeatt' => 0])
						->where('datedispatched', '>',  date('Y-m-d', strtotime('-3 days')))
						->with(['lab'])
						// ->limit(200)
						->get();

		$a = ['nationalities', 'covid_sample_types', 'covid_symptoms'];
		$lookups = $data = [];
		foreach ($a as $value) {
			$lookups[$value] = DB::table($value)->get();
		}

		foreach ($lookups['covid_symptoms'] as $key => $value) {
			$symptoms_array[$value->id] = $value->name;
		}

		foreach ($samples as $key => $sample) {
			$travelled = 'No';
			$history = '';
			if (!$sample->patient->travel->isEmpty()){
				$travelled = 'Yes';
				foreach ($sample->patient->travel as $key => $travel) {
					if(!$travel->town) continue;
					$history .= $travel->town->name . ', ' . $travel->town->country . ';';
				}
			}

			$has_symptoms = 'No';
			$symptoms = '';
			if($sample->date_symptoms){
				$has_symptoms = 'Yes';
				if($sample->symptoms && is_array($sample->symptoms)){
					foreach ($sample->symptoms as $value) {
						$symptoms .= $symptoms_array[$value] . ';';
					}
				}
			}

			$post_data = [
				'TESTING_LAB' => $sample->lab->code,

				'CASE_ID' => $sample->identifier,
				'CASE_TYPE' => $sample->test_type == 1 ? 'Initial' : 'Repeat',
				'SAMPLE_TYPE' => $sample->get_prop_name($lookups['covid_sample_types'], 'sample_type', 'name'),
				'SAMPLE_NUMBER' => $sample->id,
				'SAMPLE_COLLECTION_DATE' => $sample->datecollected->toDateString(),
				'RESULT' => $sample->result_name,
				'LAB_CONFIRMATION_DATE' => $sample->datedispatched->toDateString(),

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
				'NATIONALITY' => $sample->get_prop_name($lookups['nationalities'], 'nationality'),
				'NATIONAL_ID' => $sample->national_id ?? $sample->identifier,
				'COUNTY' => $sample->countyname ?? $sample->county,
				'SUB_COUNTY' => $sample->subcountyname ?? $sample->sub_county ?? $sample->subcounty ?? '',
				'WARD' => $sample->ward ?? $sample->residence,
				'VILLAGE' => $sample->residence,

				'HAS_TRAVEL_HISTORY' => $travelled,
				'TRAVEL_FROM' => $history,
				'CONTACT_WITH_CASE' => 'No',
				'CONFIRMED_CASE_NAME' => null,

				'SYMPTOMATIC' => $has_symptoms,
				'SYMPTOMS' => $symptoms,
				'SYMPTOMS_ONSET_DATE' => $sample->date_symptoms,
				'COUNTY_OF_DIAGNOSIS' => $sample->countyname ?? $sample->county,

				'QUARANTINED_FACILITY' => $sample->quarantine_site ?? $sample->facilityname ?? null,
				'HOSPITALIZED' => $sample->date_admission ? 'Yes' : 'Unknown',
				'ADMISSION_DATE' => $sample->date_admission,
			];

			$data[] = $post_data;
		}
		return MiscCovid::csv_download($data, 'COVID-19 LABORATORY RESULTS FOR NPHL API');
		// return Common::csv_download($data, 'nphl_download');
	}


	public function worksheet_report()
	{
		return \App\Random::covid_worksheets(date('Y'), true);
	}

	public function worksheets_no_reruns($date, $date_to=null)
	{
		$worksheets = CovidWorksheet::selectRaw('covid_worksheets.*, machines.machine, count(covid_samples.id) as sample_number')
			->join('machines', 'machines.id', '=', 'covid_worksheets.machine_type')
			->join('covid_samples', 'covid_samples.worksheet_id', '=', 'covid_worksheets.id')
			// ->where('parentid', 0)					
			->when($date, function($query) use($date, $date_to){
				if($date_to) return $query->whereBetween('daterun', [$date, $date_to]);
				return $query->whereDate('daterun', $date);
			})
			->groupBy('covid_worksheets.id')
			->having('sample_number', '>', 0)
			->get();

		$data = $worksheets->toArray();
		return MiscCovid::csv_download($data, 'worksheets-data');
	}

}
?>