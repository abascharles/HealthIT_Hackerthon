<?php

namespace App;
use Excel;
use DB;
use App\Facility;
use App\Lookup;

use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use Mpdf\Mpdf;

class Random
{
	protected static $taqmanKits = [
		['EIDname'=>"Ampliprep, HIV-1 Qualitative Test kits HIVQCAP", 'VLname'=>"Ampliprep, HIV-1 Quantitative Test kits HIVQCAP", 'alias'=>'qualkit', 'unit'=>'48 Tests' ,'factor'=>1, 'testFactor' => ['EID'=>44,'VL'=>42]],
		['name'=>"Ampliprep Specimen Pre-Extraction Reagent", 'alias'=>'spexagent', 'unit'=>'350 Tests' ,'factor'=>0.15, 'testFactor' => 0.15],
		['name'=>"Ampliprep Input S-tube", 'alias'=>'ampinput', 'unit'=>'12 * 24' ,'factor'=>0.2, 'testFactor' => 0.2],
		['name'=>"Ampliprep SPU", 'alias'=>'ampflapless', 'unit'=>'12 * 24' ,'factor'=>0.2, 'testFactor' => 0.2],
		['name'=>"Ampliprep K-Tips", 'alias'=>'ampktips', 'unit'=>'5.1L' ,'factor'=>0.15, 'testFactor' => 0.15],
		['name'=>"Ampliprep Wash Reagent", 'alias'=>'ampwash', 'unit'=>'1.2mm, 12 * 36' ,'factor'=>0.5, 'testFactor' => 0.5],
		['name'=>"TAQMAN K-Tubes", 'alias'=>'ktubes', 'unit'=>'12 * 96Pcs' ,'factor'=>0.05, 'testFactor' => 0.05],
		['name'=>"CAP/CTM Consumable Bundles", 'alias'=>'consumables', 'unit'=>'2 * 2.5ml' ,'factor'=>0.5, 'testFactor' => 0.5]
					];
	protected static $abbottKits = [
		['EIDname'=>"ABBOTT RealTime HIV-1 Qualitative Amplification Reagent Kit", 'VLname'=>"ABBOTT RealTime HIV-1 Quantitative Amplification Reagent Kit", 'alias'=>'qualkit','factor'=>['EID'=>1,'VL'=>1], 'testFactor' => ['EID'=>94,'VL'=>93]],
		['name'=>"ABBOTT m2000rt Optical Calibration Kit", 'alias'=>'calibration','factor'=>['EID'=>0,'VL'=>0], 'testFactor' => ['EID'=>0,'VL'=>0]],
		['name'=>"ABBOTT RealTime HIV-1 Quantitative Control Kit", 'alias'=>'control', 'factor'=>['EID'=>(2*(2/24)),'VL'=>(3/24)], 'testFactor' => ['EID'=>(2*(2/24)),'VL'=>(3/24)]],
		['name'=>"Bulk mLysisDNA Buffer (for DBS processing only)", 'alias'=>'buffer','factor'=>['EID'=>1,'VL'=>1], 'testFactor' => ['EID'=>1,'VL'=>1]],
		['name'=>"ABBOTT mSample Preparation System RNA", 'alias'=>'preparation','factor'=>['EID'=>1,'VL'=>1], 'testFactor' => ['EID'=>1,'VL'=>1]],
		['name'=>"ABBOTT Optical Adhesive Covers", 'alias'=>'adhesive','factor'=>['EID'=>(2/100),'VL'=>(1/100)], 'testFactor' => ['EID'=>(2/100),'VL'=>(1/100)]],
		['name'=>"ABBOTT 96-Deep-Well Plate", 'alias'=>'deepplate','factor'=>['EID'=>(2*(2/4)),'VL'=>(3/4)], 'testFactor' => ['EID'=>(2*(2/4)),'VL'=>(3/4)]],
		['name'=>"Saarstet Master Mix Tube", 'alias'=>'mixtube','factor'=>['EID'=>(2*(1/25)),'VL'=>(1/25)], 'testFactor' => ['EID'=>(2*(1/25)),'VL'=>(1/25)]],
		['name'=>"Saarstet 5ml Reaction Vessels", 'alias'=>'reactionvessels','factor'=>['EID'=>(192/500),'VL'=>(192/500)], 'testFactor' => ['EID'=>(192/500),'VL'=>(192/500)]],
		['name'=>"200mL Reagent Vessels", 'alias'=>'reagent','factor'=>['EID'=>(2*(5/6)),'VL'=>(6/6)], 'testFactor' => ['EID'=>(2*(5/6)),'VL'=>(6/6)]],
		['name'=>"ABBOTT 96-Well Optical Reaction Plate", 'alias'=>'reactionplate','factor'=>['EID'=>(192/500),'VL'=>(1/20)], 'testFactor' => ['EID'=>(192/500),'VL'=>(1/20)]],
		['name'=>"1000 uL Eppendorf (Tecan) Disposable Tips (for 1000 tests)", 'alias'=>'1000disposable','factor'=>['EID'=>(2*(421/192)),'VL'=>(841/192)], 'testFactor' => ['EID'=>(2*(421/192)),'VL'=>(841/192)]],
		['name'=>"200 ML Eppendorf (Tecan) Disposable Tips", 'alias'=>'200disposable','factor'=>['EID'=>(2*(48/192)),'VL'=>(96/192)], 'testFactor' => ['EID'=>(2*(48/192)),'VL'=>(96/192)]]
					];
	public static function site_entry_samples($type)
	{
		$classes = \App\Synch::$synch_arrays[$type];

		$sample_class = $classes['sampleview_class'];
		$table = 'samples_view';
		if($type == 'vl') $table = 'viralsamples_view';

		$data = $sample_class::join('users', 'users.id', '=', "{$table}.user_id")
			->join('view_facilitys', 'view_facilitys.id', '=', "users.facility_id")
			->selectRaw("view_facilitys.facilitycode AS `MFL Code`, county AS `County`, Subcounty AS `Subcounty`, view_facilitys.name AS `Facility`, COUNT(DISTINCT {$table}.facility_id) AS `Facilities Supported`,  COUNT({$table}.id) AS `Samples Entered` ")
			->where(['site_entry' => 1, 'parentid' => 0, 'user_type_id' => 5, ])
			->groupBy("{$table}.user_id")
			->get();

		$file = $type . '_facilities_doing_remote_entry';

		$rows = [];

		foreach ($data as $key => $value) {
			$rows[] = $value->toArray();
			// dd($value->toArray());
		}

		Excel::create($file, function($excel) use($rows){
			$excel->sheet('Sheetname', function($sheet) use($rows) {
				$sheet->fromArray($rows);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com'])->send(new TestMail($data));
	}

    public static function county_backlog()
    {
        $dates = ['feb' => '2021-02-21', 'mar' => '2021-03-21', 'apr' => '2021-04-21'];

        foreach ($dates as $key => $value) {
            $data = ViralsampleView::selectRaw("county, COUNT(viralsamples_view.id) AS sample_count ")
                ->join('view_facilitys', 'view_facilitys.id', '=', 'viralsamples_view.facility_id')
                ->where(['repeatt' => 0, 'receivedstatus' => 1, 'lab_id' => env('APP_LAB')]) 
                ->where('datereceived', '<', $value)
                ->whereNull('datetested')
                ->groupBy('county')
                ->orderBy('county', 'ASC')
                ->get()
                ->toArray();

            $file = $key . '_county_backlog';
            Common::csv_download($data, $file, true, true);
        }

    }

    public static function fix_ccc()
    {
	ini_set('memory_limit', '-1');
        $c = new \App\Imports\AmpathMflImport;
        Excel::import($c, base_path('ampath_fixed_ccc.xlsx'));
    }

    public static function covid_linelist()
    {
        $data = CovidSample::select('covid_samples.id', 'identifier', 'national_id', 'worksheet_id', 'covid_patients.county', 'covid_patients.subcounty', 'covid_justifications.name AS justification', 'datecollected', 'datetested', 'interpretation', 'result')
            ->join('covid_patients', 'covid_patients.id', '=', 'covid_samples.patient_id')
            ->join('covid_worksheets', 'covid_worksheets.id', '=', 'covid_samples.worksheet_id')
            ->join('covid_justifications', 'covid_justifications.id', '=', 'covid_patients.justification')
            ->whereBetween('datetested', ['2021-01-01', '2021-03-31'])
            ->where(['covid_samples.result' => 2, 'covid_worksheets.machine_type' => 2])
            ->get()->toArray();

        Common::csv_download($data, 'abbott-positive-2021-Q1', true, true);
    }

    public static function backlog_report($type)
    {
        $class = \App\Synch::$synch_arrays[$type]['sampleview_class'];
        $view_table = \App\Synch::$synch_arrays[$type]['view_table'];

        $file_name = $type . '_backlog_line_list';
        $fp = fopen(storage_path("exports/{$file_name}.csv"), 'w');
        fputcsv($fp, ['Patient', 'Facility', 'MFL Code', 'Subcounty', 'County', 'Batch Number', 'Gender', 'Age', 'Regimen', 'Date Collected', 'Date Received']);

        $class::select($view_table . '.*', 'view_facilitys.facilitycode', 'view_facilitys.name', 'subcounty', 'county', 'viralregimen.name AS regimen')
            ->join('view_facilitys', 'view_facilitys.id', '=', $view_table. '.facility_id')
            ->join('viralregimen', 'viralregimen.id', '=', $view_table. '.prophylaxis')
            ->where(['repeatt' => 0, 'receivedstatus' => 1, 'lab_id' => env('APP_LAB')])
            ->whereBetween('datecollected', ['2021-02-01', '2021-03-31'])
            ->whereNull('datetested')
            ->chunk(100, function($samples) use($fp){
                foreach ($samples as $key => $sample) {
                    $row = [
                        'Patient' => $sample->patient,
                        'Facility' => $sample->name,
                        'MFL Code' => $sample->facilitycode,
                        'Subcounty' => $sample->subcounty,
                        'County' => $sample->county,
                        'Batch Number' => $sample->batch_id,
                        'Gender' => $sample->gender,
                        'Age' => $sample->age,
                        'Age' => $sample->age,
                        'Date Collected' => $sample->datecollected,
                        'Date Received' => $sample->datereceived,
                    ];
                    fputcsv($fp, $row);
                }
            });
        fclose($fp);
    }

    /*public static function backlog_report($type)
    {
        $class = \App\Synch::$synch_arrays[$type]['sampleview_class'];
        $view_table = \App\Synch::$synch_arrays[$type]['view_table'];

        $offset = 0; $limit = 100;
        $data = [];

        while (true) {
            $samples = $class::select($view_table . '.*', 'view_facilitys.facilitycode', 'name', 'subcounty', 'county')
                ->join('view_facilitys', 'view_facilitys.id', '=', $view_table. '.facility_id')
                ->where(['repeatt' => 0, 'receivedstatus' => 1, 'lab_id' => env('APP_LAB')])
                ->whereBetween('datecollected', ['2021-02-01', '2021-03-31'])
                ->whereNull('datetested')
                ->limit($limit)
                ->offset($offset)
                ->get();

            if(!$samples->count()) break;

            foreach ($samples as $key => $sample) {
                $data[] = [
                    'Patient' => $sample->patient,
                    'Facility' => $sample->name,
                    'MFL Code' => $sample->facilitycode,
                    'Subcounty' => $sample->subcounty,
                    'County' => $sample->county,
                    'Batch Number' => $sample->batch_id,
                    'Gender' => $sample->gender,
                    'Age' => $sample->age,
                    'Date Collected' => $sample->datecollected,
                    'Date Received' => $sample->datereceived,
                ];
            }
            $offset += $limit;
        }

        $lab = Lab::find(env('APP_LAB'));

        $file = $type . '_backlog_line_list';
        Common::csv_download($data, $file, true, true);
    }*/

    public static function download_covid_excel()
    {
        ini_set("memory_limit", "-1");
        $samples = CovidSampleView::select('covid_sample_view.*', 'machines.machine')
            ->where('repeatt', 0)
            ->leftJoin('covid_worksheets', 'covid_worksheets.id', '=', 'covid_sample_view.worksheet_id')
            ->leftJoin('machines', 'machines.id', '=', 'covid_worksheets.machine_type')
            ->get();

        extract(Lookup::covid_form());

        $rows = [];

        foreach ($samples as $key => $sample) {
            $row = [
                'Lab ID' => $sample->id,
                'Identifier' => $sample->identifier,
                'National ID' => $sample->national_id,
                'Patient Name' => $sample->patient_name,
                'Phone Number' => $sample->phone_no,
                'County' => $sample->countyname ?? $sample->county,
                'Subcounty' => $sample->subcountyname ?? $sample->sub_county ?? $sample->subcounty ?? '',
                'Age' => $sample->age,
                'Gender' => $sample->get_prop_name($gender, 'sex', 'gender_description'),
                'Quarantine Site / Facility' => $sample->quarantine_site ?? $sample->facilityname,
                'Justification' => $sample->get_prop_name($covid_justifications, 'justification'),
                'Test Type' => $sample->get_prop_name($covid_test_types, 'test_type'),
                'Worksheet Number' => $sample->worksheet_id,
                'Machine' => $sample->machine,
                'Date Collected' => $sample->my_date_format('datecollected'),
                'Date Received' => $sample->my_date_format('datereceived'),
                'Date Tested' => $sample->my_date_format('datetested'),
                'TAT (Receipt to Testing)' => ($sample->datetested && $sample->datereceived) ? $sample->datetested->diffInDays($sample->datereceived) : '',
                'TAT (Receipt to Testing, Weekdays Only)' => ($sample->datetested && $sample->datereceived) ? $sample->datetested->diffInWeekdays($sample->datereceived) : '',
                'Received Status' => $sample->get_prop_name($receivedstatus, 'receivedstatus'),
                'Result' => $sample->get_prop_name($results, 'result'),
                'Entered By' => $sample->creator->full_name ?? null,
                'Date Entered' => $sample->my_date_format('created_at'),
            ];
            if(env('APP_LAB') == 1) $row['Kemri ID'] = $sample->kemri_id;
            if(env('APP_LAB') == 25) $row['AMREF ID'] = $sample->kemri_id;
            $rows[] = $row;
        }

        $file = 'all_covid_samples';

        Common::csv_download($rows, $file, true, true);
        // storage_path("exports/" . $file . ".csv");

        $attachments = [storage_path("exports/" . $file . ".csv")];

        Mail::to(['joelkith@gmail.com', 'cchiera@ampath.or.ke'])->send(new TestMail($attachments));
    }


    public static function export_facilities()
    {
        $data = DB::table('hcm.view_facilitys')->selectRaw("DHIScode, facilitycode AS `MFL Code`, NAME, is_surge, WardDHISCode, wardname, SubCountyDHISCode, subcounty, CountyDHISCode, countyname AS `County`, partnername AS `Partner`")->get();

        $file = 'facilities';

		$rows = [];

		foreach ($data as $key => $value) {
			$rows[] = get_object_vars($value);
		}

		Excel::create($file, function($excel) use($rows){
			$excel->sheet('Sheetname', function($sheet) use($rows) {
				$sheet->fromArray($rows);
			});
		})->store('csv');

		$data = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com'])->send(new TestMail($data));
    }


    public static function delete_site_entry()
    {
        $min_time = date('Y-m-d', strtotime("-28 days"));

        $batches = \App\Viralbatch::selectRaw("viralbatches.*, COUNT(viralsamples.id) AS sample_count ")
            ->leftJoin('viralsamples', 'viralbatches.id', '=', 'viralsamples.batch_id')
            ->whereNull('receivedstatus')
            ->where('site_entry', 1)
            ->where('viralbatches.created_at', '<', $min_time)
            ->groupBy('viralbatches.id')
            ->get();

        foreach ($batches as $key => $batch) {
        	$sample = \App\Viralsample::where('batch_id', $batch->id)->whereNotNull('receivedstatus')->first();
        	if(!$sample){
        		$batch->datereceived = null;
        		$batch->save();
        		$batch->batch_delete();
        	}
        }
    }

    public static function recollection_report()
    {
        $d = [
            'EID' => [
                'model' => \App\SampleView::class,
            ],
            'VL' => [
                'model' => \App\ViralsampleView::class,
            ],
        ];
        $rows = [];

        $sql = "id, patient_id, datereceived";

        foreach ($d as $type => $value) {
            $m = $value['model'];
            for($year=2016; $year < 2020; $year++){
                $rejected_samples = $m::selectRaw($sql)
                    ->where(['repeatt' => 0, 'receivedstatus' => 2, 'batch_complete' => 1])
                    ->where('site_entry', '!=', 2)
                    ->whereBetween('datereceived', [$year . '-01-01', $year . '-12-31'])
                    ->get();
                $recollected = 0;
                foreach ($rejected_samples as $rejected_sample) {
                    $s = $m::selectRaw($sql)
                    ->where(['repeatt' => 0, 'receivedstatus' => 1, 'patient_id' => $rejected_sample->patient_id])
                    ->where('site_entry', '!=', 2)
                    ->where('datereceived', '>', $rejected_sample->datereceived)
                    ->first();
                    if($s) $recollected++;
                }
                $rows[] = [
                    'Test' => $type,
                    'Year' => $year,
                    'Rejected' => $rejected_samples->count(),
                    'Recollected' => $recollected,
                ];
            }
        }

        $file = 'recollection_report';

        Excel::create($file, function($excel) use($rows){
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->fromArray($rows);
            });
        })->store('csv');

    }

    public static function county_tat_report($year)
    {
        $d = [
            'Eid' => [
                'model' => \App\SampleView::class,
                'table' => 'samples_view',
            ],
            'VL' => [
                'model' => \App\ViralsampleView::class,
                'table' => 'viralsamples_view',
            ],
        ];
        $files = [];

        $divs = ['County', 'Subcounty'];

        $sql = ", AVG(tat1) AS `Collection to Receipt at the Lab (TAT 1)`, AVG(tat2) AS `Receipt to Testing (TAT 2)`, AVG(tat3) AS `Testing to Dispatch (TAT 3)`, AVG(tat4) AS `Collection to Dispatch (TAT 4)`, AVG(tat5) AS `Receipt to Dispatch (Lab TAT)`, COUNT(id) AS `Number of Samples` ";

        foreach ($d as $key => $value) {
            $m = $value['model'];

            foreach ($divs as $div) {
                $query = $div . str_replace('id', $value['table'] . '.id', $sql);
                if($div == 'Subcounty') $query = 'County, ' . $query;

                $data = $m::selectRaw($query)
                ->join('view_facilitys', 'view_facilitys.id', '=', $value['table'] . '.facility_id')
                ->where(['repeatt' => 0, 'receivedstatus' => 1, 'batch_complete' => 1])
                ->where('site_entry', '!=', 2)
                ->whereBetween('datetested', [$year . '-01-01', $year . '-12-31'])
                ->groupBy($div)
                ->orderBy($div, 'asc')
                ->get();
                $rows = [];

                foreach ($data as $row) {
                    // $row['Month'] = date('M', strtotime("2019-{$row['Month']}-01"));
                    $rows[] = $row->toArray();
                }

                $file = $key . '_' . $div . '_tat_data';

                Excel::create($file, function($excel) use($rows){
                    $excel->sheet('Sheetname', function($sheet) use($rows) {
                        $sheet->fromArray($rows);
                    });
                })->store('csv');

                $files[] = storage_path("exports/" . $file . ".csv");
            }
        }  

        Mail::to(['joelkith@gmail.com'])->send(new TestMail($files));
    }

    public static function tat_report($year, $month)
    {
    	$d = [
    		'eid' => [
    			'model' => \App\SampleView::class,
    		],
    		'vl' => [
    			'model' => \App\ViralsampleView::class,
    		],
    	];
    	$files = [];

    	$sql = "year(datetested) AS `Year`, month(datetested) AS `Month`, AVG(tat1) AS `Collection to Receipt at the Lab (TAT 1)`, AVG(tat2) AS `Receipt to Testing (TAT 2)`, AVG(tat3) AS `Testing to Dispatch (TAT 3)`, AVG(tat4) AS `Collection to Dispatch (TAT 4)`, AVG(tat5) AS `Receipt to Dispatch (Lab TAT)`, COUNT(id) AS `Number of Samples` ";

    	foreach ($d as $key => $value) {
    		$m = $value['model'];

    		$data = $m::selectRaw($sql)->where('datetested', '>=', "{$year}-{$month}-01")
    		->where(['repeatt' => 0, 'receivedstatus' => 1, 'batch_complete' => 1])
    		->where('site_entry', '!=', 2)
    		->groupBy('year', 'month')->orderBy('year', 'asc')->orderBy('month', 'asc')
    		->get();
    		$rows = [];

    		foreach ($data as $row) {
    			// $row['Month'] = date('M', strtotime("2019-{$row['Month']}-01"));
    			$rows[] = $row->toArray();
    		}

    		$file = $key . '_tat_data';

			Excel::create($file, function($excel) use($rows){
				$excel->sheet('Sheetname', function($sheet) use($rows) {
					$sheet->fromArray($rows);
				});
			})->store('csv');

			$files[] = storage_path("exports/" . $file . ".csv");
    	}  

        Mail::to(['joelkith@gmail.com'])->send(new TestMail($files));
    }

    public static function tat_data()
    {
    	$months = [9, 10, 11];
    	$files = [];
    	$d = [
    		'eid' => [
    			'model' => \App\SampleView::class,
    			'tat' => 3,
    			'failed_result' => 5,
    		],
    		'vl' => [
    			'model' => \App\ViralsampleView::class,
    			'tat' => 7,
    			'failed_result' => 'Collect New Sample',
    		],
    	];

    	foreach ($d as $key => $value) {
    		$m = $value['model'];
    		$rows = [];
    		foreach ($months as $month) {
	    		$row['Year'] = 2019;
	    		$row['Month'] = date('M', strtotime("2019-{$month}-01"));
	    		$row['Samples Meeting TAT (' . $value['tat'] . ' days)'] = $m::selectRaw('COUNT(id) AS my_count')->whereYear('datetested', 2019)->whereMonth('datetested', $month)->where(['repeatt' => 0, 'lab_id' => env('APP_LAB')])->where('tat5', '<', $value['tat'] + 1)->first()->my_count;
	    		$row['Failed Samples'] = $m::selectRaw('COUNT(id) AS my_count')->whereYear('datetested', 2019)->whereMonth('datedispatched', $month)->where(['repeatt' => 0, 'lab_id' => env('APP_LAB'), 'result' => $value['failed_result']])->first()->my_count;
	    		$row['Total Samples Tested'] = $m::selectRaw('COUNT(id) AS my_count')->whereYear('datetested', 2019)->whereMonth('datetested', $month)->where(['repeatt' => 0, 'lab_id' => env('APP_LAB')])->first()->my_count;
	    		$rows[] = $row;
    		}

    		$file = $key . '_data';

			Excel::create($file, function($excel) use($rows){
				$excel->sheet('Sheetname', function($sheet) use($rows) {
					$sheet->fromArray($rows);
				});
			})->store('csv');

			$files[] = storage_path("exports/" . $file . ".csv");
    	}		

		Mail::to(['joelkith@gmail.com'])->send(new TestMail($files));
    }

    public static function rerun_data()
    {
    	$samples = \App\SampleView::where('run', '>', 2)->where('datedispatched', '>', '2017-01-01')->get();

    	$rows = $parent_ids = [];

    	$sql = "id AS `Lab ID`, parentid AS `Original Lab ID`, run, patient as `HEI Number`, facilitycode as `MFL Code`, facilityname as `Facility`, batch_id AS `Batch`, worksheet_id AS `Worksheet`, datetested AS `Date Tested`, interpretation as `Raw Result`, result, repeatt as `Final Result`, datedispatched AS `Date Dispatched` ";

    	foreach ($samples as $samp) {
    		if(in_array($samp->parentid, $parent_ids)) continue;
    		$runs = \App\SampleView::selectRaw($sql)->whereRaw("(id={$samp->parentid} OR parentid={$samp->parentid})")->get();

    		foreach ($runs as $run) {
    			$r = $run->toArray();
    			$r['result'] = $run->result_name;
    			if($r['Final Result']) $r['Final Result'] = 'No';
    			else{
    				$r['Final Result'] = 'Yes';
    			}
    			
    			$rows[] = $r;
    		}
    	}

		$file = 'multiple_runs_data';

		Excel::create($file, function($excel) use($rows){
			$excel->sheet('Sheetname', function($sheet) use($rows) {
				$sheet->fromArray($rows);
			});
		})->store('csv');

		$files = [storage_path("exports/" . $file . ".csv")];

		Mail::to(['joelkith@gmail.com'])->send(new TestMail($files));
    }

    public static function baseline_data()
    {
        $sql = "YEAR(datetested) AS `Year`, MONTH(datetested) AS `Month`, count(id) AS `Number of Valid Baseline Tests` ";

        $rows = \App\ViralsampleView::selectRaw($sql)
            ->where(['justification' => 10, 'repeatt' => 0, 'receivedstatus' => 1, 'lab_id' => env('APP_LAB')])
            ->whereBetween('rcategory', [1,4])
            ->where('datetested', '>', '2014-12-31')
            ->groupBy('Year', 'Month')
            ->orderBy('Year', 'asc')
            ->orderBy('Month', 'asc')
            ->orderBy('Month')
            ->get()->toArray();

        $file = 'baseline_data';

        Excel::create($file, function($excel) use($rows){
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->fromArray($rows);
            });
        })->store('csv');

        $files = [storage_path("exports/" . $file . ".csv")];

        Mail::to(['joelkith@gmail.com'])->send(new TestMail($files));
    }

    public static function confirmatory_data()
    {
        $sql = "YEAR(datetested) AS `Year`, MONTH(datetested) AS `Month`, count(id) AS `Number of Valid Baseline Tests` ";

        $rows = \App\SampleView::selectRaw($sql)
            ->where(['pcrtype' => 4, 'result' => 2, 'repeatt' => 0, 'receivedstatus' => 1, 'lab_id' => env('APP_LAB')])
            ->where('datetested', '>', '2014-12-31')
            ->groupBy('Year', 'Month')
            ->orderBy('Year', 'asc')
            ->orderBy('Month', 'asc')
            ->orderBy('Month')
            ->get()->toArray();

        $file = 'eid_data';

        Excel::create($file, function($excel) use($rows){
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->fromArray($rows);
            });
        })->store('csv');

        $files = [storage_path("exports/" . $file . ".csv")];

        Mail::to(['joelkith@gmail.com'])->send(new TestMail($files));
    }



    public static function to_ampath()
    {
        $path = public_path('afya_transitioned_sites.csv');
        config(['excel.import.heading' => true]);
        $facilities = Excel::load($path, function($reader){})->get();

        $rows = [];
        $un = [];

        foreach ($facilities as $fac) {
            $f = \App\Facility::locate($fac->mfl_code)->first();
            if(!$f){
                $un[] = $fac;
                continue;
                // dd($fac);
            }
            
            $b = \App\Batch::create(['site_entry' => 0, 'user_id' => 0, 'facility_id' => $f->id, 'lab_id' => env('APP_LAB')]);
            $row = [];
            foreach ($fac as $key => $value) {
                $row[$key] = $value;
            }
            $row['Batch Number'] = $b->id;
            $rows[] = $row;
        }
        // dd($un);

        $file = "afya_transitioned_sites_with_batch_numbers";
        
        Excel::create($file, function($excel) use($rows){
            $excel->sheet('Sheetname', function($sheet) use($rows) {
                $sheet->fromArray($rows);
            });
        })->store('csv');

        $data = [storage_path("exports/" . $file . ".csv")];

        Mail::to(['joelkith@gmail.com'])->send(new TestMail($data, 'Afya Transitioned Sites With Batch Numbers'));

        // dd($facilities);

    }

    public static function merge_patients()
    {
        $path = public_path('duplicate_clients.csv');
        config(['excel.import.heading' => true]);
        $patients = Excel::load($path, function($reader){})->get();

        $rows = [];
        $un = [];

        $skip = 0;

        foreach ($patients as $key => $pat1) {
            if($skip){
                $skip--;
                continue;
            }

            $ps = $counts = [];
            $ps[] = $pat1;
            $current_key = $key;

            while(true){
                $current_key++;
                $pat = $patients[$current_key] ?? null;
                if(!$pat || $pat->person_id != $pat1->person_id) break;
                $skip++;
                $ps[] = $pat;
            }

            foreach ($ps as $p) {
                $counts[] = \App\Viralsample::where(['patient_id' => $p->id, 'repeatt' => 0])->count();
            }

            $max_key = array_keys($ps, max($ps))[0];
            $patient = \App\Viralpatient::where(['patient' => $ps[$max_key]->patient_id])->first();

            if(!$patient) {
                // echo "\nError at {$key}: ";
                // print_r($ps);
                continue;
            }

            foreach ($ps as $pkey => $p) {
                if($pkey == $max_key) continue;

                $old_patient = \App\Viralpatient::where(['patient' => $p->patient_id])->first();

                if(!$old_patient){
                    // echo "\nError at {$key}: Could not find patient {$p->patient_id}.";
                    continue;
                }

                $samples = \App\Viralsample::where(['patient_id' => $old_patient->id])->get();
                foreach ($samples as $s) {
                    $s->patient_id = $patient->id;
                    $s->pre_update();
                }
            }
        }

        /*foreach ($patients as $key => $pat1) {
            $pat2 = $patients[$key+1] ?? null;
            if(!$pat2) break;

            $p1 = \App\Viralpatient::where(['patient' => $pat1->patient_id])->first();
            $p2 = \App\Viralpatient::where(['patient' => $pat2->patient_id])->first();

            // if(!$p1 || !$p2 || $p1->facility_id != $p2->facility_id) continue;
            if(!$p1 || !$p2 || $pat1->person_id != $pat2->person_id) continue;

            $s1 = \App\Viralsample::where(['patient_id' => $p1->id, 'repeatt' => 0])->count();
            $s2 = \App\Viralsample::where(['patient_id' => $p2->id, 'repeatt' => 0])->count();

            $patient_id = null;

            if($s1 > $s2){
                $samples = \App\Viralsample::where(['patient_id' => $p2->id])->get();
                $patient_id = $p1->id;
            }
            else{
                $samples = \App\Viralsample::where(['patient_id' => $p1->id])->get();
                $patient_id = $p2->id;

            }

            foreach ($samples as $s) {
                $s->patient_id = $patient_id;
                $s->pre_update();
            }
        }*/
        // dd($un);
    }

    public static function enter_samples()
    {
    	$file = public_path('machakos.csv');
    	$handle = fopen($file, "r");
    	while (($row = fgetcsv($handle, 1000, ",")) !== FALSE){

            $facility = Facility::locate($row[3])->get()->first();
            if(!$facility) continue;
            $datecollected = Lookup::other_date($row[8]);
            $datereceived = Lookup::other_date($row[15]);
            if(!$datereceived) $datereceived = date('Y-m-d');
            $existing = \App\ViralsampleView::existing(['facility_id' => $facility->id, 'patient' => $row[1], 'datecollected' => $datecollected])->first();

            if($existing){
                $sampletype = (int) $row[7];
                if(in_array($existing->sampletype, [3, 4]) && in_array($sampletype, [1,2,5])){
                    $s = \App\Viralsample::find($existing->id);
                    $s->delete();
                }
                else{
                	if($existing->received_by != 7939){
	                    $b = \App\Viralbatch::find($existing->batch_id);
	                    $b->received_by = 7939;
	                    $b->save();
                	}
                    continue;                        
                }
            }

            $site_entry = Lookup::get_site_entry($row[14]);

            $batch = \App\Viralbatch::withCount(['sample'])
                                    ->where('received_by', 7939)
                                    ->where('datereceived', $datereceived)
                                    ->where('input_complete', 0)
                                    ->where('site_entry', $site_entry)
                                    ->where('facility_id', $facility->id)
                                    ->get()->first();

            if($batch){
                if($batch->sample_count > 9){
                    unset($batch->sample_count);
                    $batch->full_batch();
                    $batch = null;
                }
            }

            if(!$batch){
                $batch = new \App\Viralbatch;
                $batch->user_id = $facility->facility_user->id;
                $batch->facility_id = $facility->id;
                $batch->received_by = 7939;
                $batch->time_received = date('Y-m-d H:i:s');
                $batch->lab_id = env('APP_LAB');
                $batch->datereceived = $datereceived;
                $batch->site_entry = $site_entry;
                $batch->save();
            }

            $patient = \App\Viralpatient::existing($facility->id, $row[1])->get()->first();
            if(!$patient){
                $patient = new \App\Viralpatient;
            }
            $dob = Lookup::other_date($row[5]);
            if (!$dob) {
                if(strlen($row[5]) == 4) $dob = $row[5] . '-01-01';
            }
            if($dob) $patient->dob = $dob;            
            $patient->facility_id = $facility->id;
            $patient->patient = $row[1];
            $patient->sex = Lookup::get_gender($row[4]);
            $patient->initiation_date = Lookup::other_date($row[9]);
            if(!$patient->dob && $row[6]) $patient->dob = Lookup::calculate_dob($datecollected, $row[6]); 
            $patient->pre_update();

            $sample = new \App\Viralsample;
            $sample->batch_id = $batch->id;
            $sample->patient_id = $patient->id;
            $sample->datecollected = $datecollected;
            $sample->age = $row[6];
            if(!$sample->age) $sample->age = Lookup::calculate_viralage($datecollected, $patient->dob);
            $sample->prophylaxis = Lookup::viral_regimen($row[10]);
            $sample->dateinitiatedonregimen = Lookup::other_date($row[11]);
            $sample->justification = Lookup::justification($row[12]);
            $sample->sampletype = (int) $row[7];
            if($sample->sampletype == 5) $sample->sampletype = 1;
            $sample->pmtct = $row[13];
            $sample->receivedstatus = $row[16];
            if(is_numeric($row[17])) $sample->rejectedreason = $row[17];
            $sample->save();

    	}
    }

    public static function oldest($type)
    {
		$sampleview_class = \App\Synch::$synch_arrays[$type]['sampleview_class'];

		$m = $sampleview_class::selectRaw('MIN(datereceived) as mindate')
								->where('datereceived', '>', date('Y-m-d', strtotime("-1 year")))
								->whereNull('worksheet_id')
								->whereNull('approvedby')
								->whereNull('datedispatched')
								// ->where('receivedstatus', '!=', 2)
								->where('site_entry', '!=', 2)
								->whereRaw("(result is null or result=0)")
								->where(['receivedstatus' => 1, 'flag' => 1, 'input_complete' => 1, 'lab_id' => env('APP_LAB', null)])
								->get()->first();
		return $m;
    }


	public static function switch_amrs()
	{
		ini_set("memory_limit", "-1");
		$samples = \App\ViralsampleView::whereBetween('datereceived', ['2018-09-01', '2018-10-24'])->whereRaw("(amrs_location is not null and amrs_location != 0)")->get();
		foreach ($samples as $sample) {
			$s = \App\Viralsample::find($sample->id);
			$s->amrs_location = Lookup::get_mrslocation($s->amrs_location);
			$s->pre_update();
		}
	}

	public static function facilitys()
	{
		self::alter_facilitys();
		self::poc_sites();
		self::mlab_sites();
	}

	public static function alter_facilitys()
	{
		DB::statement('ALTER TABLE facilitys ADD COLUMN `poc` TINYINT UNSIGNED DEFAULT 0 after latitude;');
		Facility::where('id', '>', 0)->update(['smsprinter' => 0]);
	}

	public static function correct_ampath_data()
	{
		ini_set("memory_limit", "-1");
        config(['excel.import.heading' => true]);
		$path = public_path('justification.csv');

		$data = Excel::load($path, function($reader){})->get();

		foreach ($data as $row) {
			$s = \App\Viralsample::find($row->system_id);
			if($s->age < 3) continue;
			$s->justification = 1;
			$s->synched = 2;
			$s->save();
		}
	}

	public static function poc_sites()
	{
		ini_set("memory_limit", "-1");
        config(['excel.import.heading' => true]);
		$path = public_path('poc_hubs_list.csv');
		$data = Excel::load($path, function($reader){

		})->get();

		foreach ($data as $row) {
			Facility::where(['facilitycode' => $row->code])->update(['poc' => 1]);
		}
	} 

	public static function mlab_sites()
	{
		ini_set("memory_limit", "-1");
        config(['excel.import.heading' => true]);
		$path = public_path('mlab_facilities.csv');
		$data = Excel::load($path, function($reader){

		})->get();

		foreach ($data as $row) {
			Facility::where(['facilitycode' => $row->code])->update(['smsprinter' => 1]);
		}
	} 

    public static function update_turkana()
    {
        $handle = fopen(public_path('turkana_samples.csv'), "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE){
            if(!is_numeric($data[0])) continue;

            $sample = Viralsample::where(['national_sample_id' => $data[0]])->first();
            if(!$sample) continue;

            $patient = Viralpatient::where(['patient' => $data[2]])->first();

            if(!$patient){
                $patient = $sample->patient;
                $patient->patient = $data[2];
                $patient->pre_update();
            }else{
                $sample->patient_id = $patient->id;
                $sample->pre_update();
            }            

        }
    }

	public static function locations()
	{
		$locations = '
			[
				{
					"location_id" : 1,
					"name" : "MTRH Module 1",
					"description" : "Moi Teaching and Referral Hospital - Module 1"
				},
				{
					"location_id" : 2,
					"name" : "Mosoriot",
					"description" : "Mosoriot Outpatient Center"
				},
				{
					"location_id" : 3,
					"name" : "Turbo",
					"description" : "Turbo heath center Clinic"
				},
				{
					"location_id" : 4,
					"name" : "Burnt Forest",
					"description" : "Burnt Forest RHDC Clinic"
				},
				{
					"location_id" : 5,
					"name" : "Amukura",
					"description" : "Amukura Health Center"
				},
				{
					"location_id" : 6,
					"name" : "Naitiri",
					"description" : "Naitiri Health center"
				},
				{
					"location_id" : 7,
					"name" : "Chulaimbo",
					"description" : "Chulaimbo Sub-district hospital (Clinic)"
				},
				{
					"location_id" : 8,
					"name" : "Webuye",
					"description" : "Webuye Hospital"
				},
				{
					"location_id" : 9,
					"name" : "Mt. Elgon",
					"description" : "Mount Elgon Clinic (Kapsokwony)"
				},
				{
					"location_id" : 10,
					"name" : "Kapenguria",
					"description" : "Kapenguria Clinic"
				},
				{
					"location_id" : 11,
					"name" : "Kitale",
					"description" : "Kitale Clinic"
				},
				{
					"location_id" : 12,
					"name" : "Teso",
					"description" : "Teso Clinic"
				},
				{
					"location_id" : 13,
					"name" : "MTRH Module 2",
					"description" : "Moi Teaching and Referral Hospital - Module 2"
				},
				{
					"location_id" : 14,
					"name" : "MTRH Module 3",
					"description" : "Moi Teaching and Referral Hospital - Module 3"
				},
				{
					"location_id" : 15,
					"name" : "MTRH Module 4",
					"description" : "Moi Teaching and Referral Hospital - Module 4"
				},
				{
					"location_id" : 16,
					"name" : "Unknown",
					"description" : "Unknown Location"
				},
				{
					"location_id" : 17,
					"name" : "Iten",
					"description" : "Iten Clinic"
				},
				{
					"location_id" : 18,
					"name" : "Kabarnet",
					"description" : "Kabarnet Clinic"
				},
				{
					"location_id" : 19,
					"name" : "Busia",
					"description" : "Busia Clinic"
				},
				{
					"location_id" : 20,
					"name" : "Port Victoria",
					"description" : "Port Victoria AMPATH clinic"
				},
				{
					"location_id" : 21,
					"name" : "Non AMPATH Site",
					"description" : "All clinical locations outside the AMPATH system."
				},
				{
					"location_id" : 22,
					"name" : "None",
					"description" : "No location."
				},
				{
					"location_id" : 23,
					"name" : "Khunyangu",
					"description" : "Khunyangu District Hospital"
				},
				{
					"location_id" : 24,
					"name" : "Chulaimbo Module 1",
					"description" : "Chulaimbo Adult Clinic"
				},
				{
					"location_id" : 25,
					"name" : "Chulaimbo Module 2",
					"description" : "Chulaimbo Pediatric Clinic"
				},
				{
					"location_id" : 26,
					"name" : "Busia Module 1",
					"description" : "Busia Module 1"
				},
				{
					"location_id" : 27,
					"name" : "Busia Module 2",
					"description" : "Busia Module 2"
				},
				{
					"location_id" : 28,
					"name" : "Ziwa",
					"description" : "Ziwa Clinic"
				},
				{
					"location_id" : 30,
					"name" : "Anderson",
					"description" : "Anderson Clinic"
				},
				{
					"location_id" : 31,
					"name" : "Uasin Gishu District Hospital",
					"description" : "Uasin Gishu District Hospital (DH)"
				},
				{
					"location_id" : 32,
					"name" : "Eldoret Catholic Church(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 33,
					"name" : "Eldoret Police Station(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 34,
					"name" : "Majengo (Our Lady) Church(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 35,
					"name" : "Turbo Police Station",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 36,
					"name" : "Nakuru(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 37,
					"name" : "Nairobi(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 38,
					"name" : "Eldoret Showground(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 39,
					"name" : "Yamumbi (IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 40,
					"name" : "Matharu Center(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 41,
					"name" : "Munyaka PCEA Church(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 42,
					"name" : "Maji Mazuri(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 43,
					"name" : "Kamara(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 44,
					"name" : "Eldamaravine Police Station(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 45,
					"name" : "Moisbridge(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 46,
					"name" : "Langas police station(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 47,
					"name" : "Timboroa Police Station",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 48,
					"name" : "Bishop Muge(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 49,
					"name" : "Kipkenyo(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 50,
					"name" : "Endebes(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 51,
					"name" : "Kachibora(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 52,
					"name" : "Cherangany(IDP)",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 53,
					"name" : "Nzioa Scheme",
					"description" : "Internally Displaced AMPATH Patients"
				},
				{
					"location_id" : 54,
					"name" : "Plateau Mission Hospital",
					"description" : "Burnt Forest Satellite Clinic"
				},
				{
					"location_id" : 55,
					"name" : "Bumala A",
					"description" : "Bumala \"A\" Health Center(Busia Satellite Clinic)"
				},
				{
					"location_id" : 56,
					"name" : "Eldoret Prison",
					"description" : "Satellite Clinic of MTRH Module 3"
				},
				{
					"location_id" : 57,
					"name" : "Kitale Prison",
					"description" : "Satellite Clinic of Kitale"
				},
				{
					"location_id" : 58,
					"name" : "Ngeria Prison",
					"description" : "Satellite Clinic of MTRH Module 3"
				},
				{
					"location_id" : 59,
					"name" : "Mautuma",
					"description" : "Satellite Clinic of Turbo"
				},
				{
					"location_id" : 60,
					"name" : "Chepsaita",
					"description" : "Chepsaita Dispensary(Turbo Satellite Clinic)"
				},
				{
					"location_id" : 61,
					"name" : "Kaptagat",
					"description" : "Satellite Clinic of Burnt Forest"
				},
				{
					"location_id" : 62,
					"name" : "Kesses",
					"description" : "Satellite Clinic of Burnt Forest"
				},
				{
					"location_id" : 63,
					"name" : "Lukolis",
					"description" : "Lukolis Dispensary(Amukura satellite clinic)"
				},
				{
					"location_id" : 64,
					"name" : "Bokoli",
					"description" : "Bokoli Hospital(Webuye satellite clinic)"
				},
				{
					"location_id" : 65,
					"name" : "Angurai",
					"description" : "Angurai Health Center(Teso satellite clinic)"
				},
				{
					"location_id" : 66,
					"name" : "Cheptais",
					"description" : "Cheptais Sub-District Hospital(Mt. Elgon Satellite Clinic)"
				},
				{
					"location_id" : 67,
					"name" : "Cheskaki",
					"description" : "Mt. Elgon Satellite Clinic"
				},
				{
					"location_id" : 68,
					"name" : "Marigat",
					"description" : "Satellite Clinic of Kabarnet"
				},
				{
					"location_id" : 69,
					"name" : "Huruma SDH",
					"description" : "Satellite Clinic of Uasin Gishu District Hospital"
				},
				{
					"location_id" : 70,
					"name" : "Pioneer Sub-District Hospital",
					"description" : "Satellite clinic for Mosoriot Health Centre"
				},
				{
					"location_id" : 71,
					"name" : "Moi\'s Bridge",
					"description" : "Moi\'s Bridge Clinic"
				},
				{
					"location_id" : 72,
					"name" : "Moi University",
					"description" : "Moi University  Main Campus clinic"
				},
				{
					"location_id" : 73,
					"name" : "Soy",
					"description" : "Soy Clinic"
				},
				{
					"location_id" : 74,
					"name" : "Mihuu",
					"description" : "Mihuu Dispensary(Webuye satellite clinic)"
				},
				{
					"location_id" : 75,
					"name" : "Sinoko",
					"description" : "Sinoko Dispensary(Bungoma East)"
				},
				{
					"location_id" : 76,
					"name" : "Milo",
					"description" : "Milo Health Center (Satellite clinic to Webuye)"
				},
				{
					"location_id" : 77,
					"name" : "Moiben",
					"description" : "Satellite Clinic of Ziwa"
				},
				{
					"location_id" : 78,
					"name" : "Mukhobola",
					"description" : "Mukhobola Clinic"
				},
				{
					"location_id" : 79,
					"name" : "Nambale",
					"description" : "Nambale Clinic"
				},
				{
					"location_id" : 80,
					"name" : "MOI BARRACKS",
					"description" : "Satellite Clinic of Module 3"
				},
				{
					"location_id" : 81,
					"name" : "Busia Prison",
					"description" : "Busia Satellite Clinic"
				},
				{
					"location_id" : 82,
					"name" : "Saboti",
					"description" : "Kitale satellite clinic"
				},
				{
					"location_id" : 83,
					"name" : "Bumala B",
					"description" : "Bumala \"B\" Health Center (Khunyangu Satellite clinic)"
				},
				{
					"location_id" : 84,
					"name" : "Moi Teaching and Referral Hospital",
					"description" : "Primary Health Care Clinic Location"
				},
				{
					"location_id" : 85,
					"name" : "Makutano",
					"description" : "Satellite Clinic Site for Naitiri"
				},
				{
					"location_id" : 86,
					"name" : "Kaptama ( Friends) Dispensary",
					"description" : "Satellite clinic of Mount Elgon(Kapsokwony)"
				},
				{
					"location_id" : 87,
					"name" : "Sio Port",
					"description" : "Sio port"
				},
				{
					"location_id" : 88,
					"name" : "Tulwet",
					"description" : "Satellite clinic of Kitale"
				},
				{
					"location_id" : 89,
					"name" : "Kopsiro",
					"description" : "Satellite Clinic of Mt. elgon."
				},
				{
					"location_id" : 90,
					"name" : "Changara",
					"description" : "Teso Satellte Clinic"
				},
				{
					"location_id" : 91,
					"name" : "Malaba",
					"description" : "Satellite clinic of Teso"
				},
				{
					"location_id" : 92,
					"name" : "Amase",
					"description" : "Amase Dispensary(Amukura satellite clinic)"
				},
				{
					"location_id" : 93,
					"name" : "Obekai",
					"description" : "Obekai Dispensary(Amukura satellite clinic)"
				},
				{
					"location_id" : 94,
					"name" : "Tambach",
					"description" : "Satellite Clinic to Iten"
				},
				{
					"location_id" : 95,
					"name" : "Tenges",
					"description" : "Satellite clinic to Kabarnet."
				},
				{
					"location_id" : 96,
					"name" : "Kibisi",
					"description" : "Satellite clinic to Naitiri"
				},
				{
					"location_id" : 97,
					"name" : "Sango",
					"description" : "Satellite clinic to Naitiri."
				},
				{
					"location_id" : 98,
					"name" : "AIC Diguna Royal Toto Children\'s Home,Ngechek",
					"description" : "Mosoriot satellite clinic"
				},
				{
					"location_id" : 99,
					"name" : "Lupida",
					"description" : "Nambale Satellite Clinic"
				},
				{
					"location_id" : 100,
					"name" : "Osieko",
					"description" : "A satellite to Port Victoria"
				},
				{
					"location_id" : 101,
					"name" : "Room 7",
					"description" : "Casualty"
				},
				{
					"location_id" : 102,
					"name" : "Elgeyo Border",
					"description" : "These is a health centre"
				},
				{
					"location_id" : 103,
					"name" : "Riat",
					"description" : "This is a dispensary and its Chulaimbo\'s Satellites"
				},
				{
					"location_id" : 104,
					"name" : "Sunga",
					"description" : "This is a dispensary and its Chulaimbo\'s Satellites"
				},
				{
					"location_id" : 105,
					"name" : "Siriba",
					"description" : "This is a dispensary and its Chulaimbo\'s Satellites"
				},
				{
					"location_id" : 106,
					"name" : "Kamolo",
					"description" : "satelite at Kamolo Dispensary,to be run by Teso, AMPATH clinic"
				},
				{
					"location_id" : 107,
					"name" : "Kapteren Health Center",
					"description" : "A satellite of Iten Clinic"
				},
				{
					"location_id" : 108,
					"name" : "Madende Health Center",
					"description" : "A satellite of Nambale"
				},
				{
					"location_id" : 109,
					"name" : "Rai Plywoods",
					"description" : "Satellite clinic to UGDH"
				},
				{
					"location_id" : 110,
					"name" : "Mogoget",
					"description" : "Dispensary in Kosirai Division"
				},
				{
					"location_id" : 111,
					"name" : "Birbiriet",
					"description" : "Dispensary in Kosirai Division"
				},
				{
					"location_id" : 112,
					"name" : "Itigo",
					"description" : "Dispensary in Kosirai Division"
				},
				{
					"location_id" : 113,
					"name" : "Lelmokwo",
					"description" : "Dispensary in Kosirai Division"
				},
				{
					"location_id" : 114,
					"name" : "Kokwet",
					"description" : "Dispensary in Kosirai Division"
				},
				{
					"location_id" : 115,
					"name" : "Ngechek",
					"description" : "Dispensary in Kosirai Division"
				},
				{
					"location_id" : 116,
					"name" : "Cheramei",
					"description" : "Dispensary in Turbo Division"
				},
				{
					"location_id" : 117,
					"name" : "Murgusi",
					"description" : "Dispensary in Turbo Division"
				},
				{
					"location_id" : 118,
					"name" : "Cheplaskei",
					"description" : "Dispensary in Turbo Division"
				},
				{
					"location_id" : 119,
					"name" : "Sigot",
					"description" : "Dispensary in Turbo Division"
				},
				{
					"location_id" : 120,
					"name" : "Sugoi A",
					"description" : "Dispensary in Turbo Division"
				},
				{
					"location_id" : 121,
					"name" : "Sugoi B",
					"description" : "Dispensary in Turbo Division"
				},
				{
					"location_id" : 122,
					"name" : "Chepkemel",
					"description" : "Dispensary in Turbo Division"
				},
				{
					"location_id" : 123,
					"name" : "Chepkemel",
					"description" : "Dispensary in Turbo Division"
				},
				{
					"location_id" : 124,
					"name" : "Akichelesit",
					"description" : "Dispensary in Teso Division"
				},
				{
					"location_id" : 125,
					"name" : "Aboloi",
					"description" : "Dispensary in Teso Division"
				},
				{
					"location_id" : 126,
					"name" : "Moding",
					"description" : "Dispensary in Teso Division"
				},
				{
					"location_id" : 127,
					"name" : "Sambut",
					"description" : "Sambut - Dispensary in Turbo division"
				},
				{
					"location_id" : 128,
					"name" : "Ngenyilel",
					"description" : "Dispensary in Turbo division"
				},
				{
					"location_id" : 129,
					"name" : "Sosiani",
					"description" : "Health Centre in Turbo division"
				},
				{
					"location_id" : 130,
					"name" : "Matayos Health Centre",
					"description" : "New site from Aphia"
				},
				{
					"location_id" : 131,
					"name" : "Chebaiywa",
					"description" : "Used by CDM team and their forms being entered to AMRS"
				},
				{
					"location_id" : 132,
					"name" : "Kapsara Sub-District Hospital",
					"description" : "New location in Kitale"
				},
				{
					"location_id" : 133,
					"name" : "Chepterit",
					"description" : "A dispensary in Mosoriot Division"
				},
				{
					"location_id" : 134,
					"name" : "Kapyemit",
					"description" : "A dispensary in Turbo division and Uasin Gishu county"
				},
				{
					"location_id" : 135,
					"name" : "Kaborom",
					"description" : "Dispensary - a satellite of Mt Elgon."
				},
				{
					"location_id" : 136,
					"name" : "Murgor Hills",
					"description" : "A dispensary in Turbo"
				},
				{
					"location_id" : 137,
					"name" : "Osorongai",
					"description" : "A dispensary in Turbo"
				},
				{
					"location_id" : 138,
					"name" : "Family Health Care Options Kenya - Eldoret",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 139,
					"name" : "Elgon View Hospital",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 140,
					"name" : "Cedar Clinical Associates",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 141,
					"name" : "Glory Health Centre and Chemists",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 142,
					"name" : "Amani Health Centre",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 143,
					"name" : "Gynocare Health Centre",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 144,
					"name" : "St. Marys Health Centre - Kapsoya",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 145,
					"name" : "SOS Medical Centre - Eldoret",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 146,
					"name" : "Imani Hospital",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 147,
					"name" : "Fountain Health Centre",
					"description" : "Private Hospital in Eldoret"
				},
				{
					"location_id" : 148,
					"name" : "St. Luke\'s",
					"description" : "A Private Hospital in Eldoret"
				},
				{
					"location_id" : 149,
					"name" : "Eldoret Hospital",
					"description" : "A private hospital in Eldoret."
				},
				{
					"location_id" : 150,
					"name" : "Sisenye Dispensary",
					"description" : "Dispensary in Bunyala sub-county"
				},
				{
					"location_id" : 151,
					"name" : "Rukala Dispensary",
					"description" : "Dispensary in Bunyala Sub-County."
				},
				{
					"location_id" : 152,
					"name" : "Budalangi Dispensary",
					"description" : "A dispensary in Bunyala Sub-county"
				},
				{
					"location_id" : 153,
					"name" : "Reale Hospital",
					"description" : "Private Hospital in Eldoret\r\nunder the Private Sector Engagement program\r\n(formally PPP)"
				},
				{
					"location_id" : 154,
					"name" : "Sokyot",
					"description" : "A community based in Turbo"
				},
				{
					"location_id" : 155,
					"name" : "Turbo/Kaptebee",
					"description" : "A community in Turbo"
				},
				{
					"location_id" : 156,
					"name" : "Ngechek",
					"description" : "A community In Kosirai Division"
				},
				{
					"location_id" : 157,
					"name" : "Tuigoin",
					"description" : "A community Unit in Turbo Division"
				},
				{
					"location_id" : 158,
					"name" : "Leseru",
					"description" : "A community Unit in Turbo Division"
				},
				{
					"location_id" : 159,
					"name" : "Kosirai",
					"description" : "A community Unit in Kosirai Division"
				},
				{
					"location_id" : 160,
					"name" : "Mutwot",
					"description" : "A Community unit in Kosirai Division"
				},
				{
					"location_id" : 161,
					"name" : "Laikipia",
					"description" : "An amrs site in Laikipia County"
				},
				{
					"location_id" : 162,
					"name" : "Sirimba Mission Hospital",
					"description" : "A health Facility in Busia County"
				},
				{
					"location_id" : 163,
					"name" : "Nasewa Health Centre",
					"description" : "A Health Facility in Nasewa"
				},
				{
					"location_id" : 164,
					"name" : "Mabunge",
					"description" : "A community unit in Nasewa"
				},
				{
					"location_id" : 165,
					"name" : "Buyama",
					"description" : "A community unit in Nasewa"
				},
				{
					"location_id" : 166,
					"name" : "Lung\'a",
					"description" : "A community unit in Nasewa"
				},
				{
					"location_id" : 167,
					"name" : "Nasewa",
					"description" : "A community unit in Nasewa"
				},
				{
					"location_id" : 168,
					"name" : "Sikarira Dispensary",
					"description" : "A health facility in Sikarira"
				},
				{
					"location_id" : 169,
					"name" : "Bulwani",
					"description" : "A Community Unit in Bwaliro"
				},
				{
					"location_id" : 170,
					"name" : "Kanjala",
					"description" : "A community unit in Sikarira"
				},
				{
					"location_id" : 171,
					"name" : "Sirimba Mission Hospital",
					"description" : "A health Facility in Busia County"
				},
				{
					"location_id" : 172,
					"name" : "Ruambwa",
					"description" : "A community unit in Sirimba"
				},
				{
					"location_id" : 173,
					"name" : "Ikonzo Dispensary",
					"description" : "A Health Facility in Busia"
				},
				{
					"location_id" : 174,
					"name" : "Namwitsula",
					"description" : "A community unit in Ikonzo"
				},
				{
					"location_id" : 175,
					"name" : "Ikonzo",
					"description" : "A community Unit in Ikonzo"
				},
				{
					"location_id" : 176,
					"name" : "West Clinic Health Centre",
					"description" : "A health facility in Uasin Gishu"
				},
				{
					"location_id" : 177,
					"name" : "Kibulgeng",
					"description" : "A community facility in Uasin Gishu"
				},
				{
					"location_id" : 178,
					"name" : "Bujumba Dispensary",
					"description" : "Is a health Facility in Bujumba"
				},
				{
					"location_id" : 179,
					"name" : "Bujumba",
					"description" : "Is a community Facility in Bujumba"
				},
				{
					"location_id" : 183,
					"name" : "Ikonzo Dispensary",
					"description" : "A dispensary in Busia"
				},
				{
					"location_id" : 184,
					"name" : "Ikonzo Dispensary",
					"description" : "A dispensary in Busia"
				},
				{
					"location_id" : 185,
					"name" : "Sikarira",
					"description" : "A community unit in Ikonzo"
				},
				{
					"location_id" : 186,
					"name" : "MTRH Memorial Hospital",
					"description" : "PPP Clinic"
				},
				{
					"location_id" : 187,
					"name" : "Chep\'ngoror Dispensary",
					"description" : "A dispensary in Burnt Forest"
				},
				{
					"location_id" : 188,
					"name" : "Matunda Health Centre",
					"description" : "A community unit in Matunda"
				},
				{
					"location_id" : 189,
					"name" : "Endebes Health Centre",
					"description" : "A Health Centre in Trans nzoia"
				},
				{
					"location_id" : 190,
					"name" : "Kwanza Health Centre",
					"description" : "A Health Facility in Trans Nzoia"
				},
				{
					"location_id" : 191,
					"name" : "Anderson",
					"description" : "A health centre in transzoia"
				},
				{
					"location_id" : 192,
					"name" : "Kapsoya Health Centre",
					"description" : "A health centre in Kapsoya"
				},
				{
					"location_id" : 193,
					"name" : "Sister Freda Medical Centre",
					"description" : "A Health facility in Trans Nzoia"
				},
				{
					"location_id" : 194,
					"name" : "St. Ladislaus Dispensary",
					"description" : "A health facility in Uasin Gishu County"
				},
				{
					"location_id" : 195,
					"name" : "Location Test",
					"description" : "This is a test location for POC Testers."
				},
				{
					"location_id" : 196,
					"name" : "Mediheal Hospital",
					"description" : "A ppp Clinic in Eldoret"
				},
				{
					"location_id" : 197,
					"name" : "MTRH MCH",
					"description" : "Used to collect PMTCT data."
				},
				{
					"location_id" : 198,
					"name" : "MTRH Adolescent Clinic",
					"description" : "Moi Teaching and Referral Hospital Adolescent Clinic."
				},
				{
					"location_id" : 199,
					"name" : "MTRH Nyayo Ward",
					"description" : "MTRH clinic Nyayo Ward"
				},
				{
					"location_id" : 200,
					"name" : "MTRH Mother & Baby Ward",
					"description" : "MTRH Mother & Baby Ward"
				},
				{
					"location_id" : 201,
					"name" : "MTRH Pediatric Ward",
					"description" : "MTRH Pediatric Ward"
				},
				{
					"location_id" : 202,
					"name" : "MTRH Other",
					"description" : "MTRH Other"
				},
				{
					"location_id" : 203,
					"name" : "Langas",
					"description" : "Facility"
				},
				{
					"location_id" : 204,
					"name" : "MTRH Oncology",
					"description" : "Moi Teaching and Referral Hospital - \r\nOncology"
				},
				{
					"location_id" : 205,
					"name" : "Busagwa Dispensary",
					"description" : "Busagwa Dispensary"
				},
				{
					"location_id" : 206,
					"name" : "MTRH ACTG",
					"description" : "MTRH ACTG"
				},
				{
					"location_id" : 207,
					"name" : "MTRH-Oncology",
					"description" : "Handles patients screened and treated with breast and cervical cancer"
				},
				{
					"location_id" : 208,
					"name" : "Huruma MCH",
					"description" : "Huruma MCH"
				},
				{
					"location_id" : 209,
					"name" : "Kakamega",
					"description" : "Is a Kakamega County Referral hospital"
				},
				{
					"location_id" : 210,
					"name" : "Homabay",
					"description" : "Oncology site"
				},
				{
					"location_id" : 211,
					"name" : "Alphima Medical Clinic",
					"description" : "A Private Hospital in Eldoret"
				},
				{
					"location_id" : 212,
					"name" : "Jaramogi Oginga Odinga TRH",
					"description" : "Jaramogi Oginga Odinga Training and Referral Hospital."
				},
				{
					"location_id" : 213,
					"name" : "Bomet",
					"description" : "Oncology clinic at Bomet"
				},
				{
					"location_id" : 214,
					"name" : "Kapenguria County Referral Hospital",
					"description" : "Referral hospital in Kapenguria."
				},
				{
					"location_id" : 215,
					"name" : "Hamisi Sub County Hospital",
					"description" : "A sub county hospital in Hamisi"
				},
				{
					"location_id" : 216,
					"name" : "BUTERE",
					"description" : "An Oncology Clinic"
				},
				{
					"location_id" : 217,
					"name" : "Turbo CCC",
					"description" : "A CDM comprehensive Care center"
				},
				{
					"location_id" : 218,
					"name" : "Huruma CCC",
					"description" : "A CDM Comprehensive Care center"
				},
				{
					"location_id" : 219,
					"name" : "St. Elizabeth Lwak Mission Health center",
					"description" : "A health Center in Siaya County"
				},
				{
					"location_id" : 220,
					"name" : "Madiany sub county hospital",
					"description" : "Madiany sub county in Siaya County- Oncology study"
				},
				{
					"location_id" : 221,
					"name" : "Bungoma County Referral Hospital",
					"description" : "A referral hospital in Bungoma county"
				},
				{
					"location_id" : 222,
					"name" : "Nyahururu District Hospital",
					"description" : "A district Hospital in Laikipia County"
				},
				{
					"location_id" : 223,
					"name" : "MTRH-TB",
					"description" : "TB Clinic at MTRH"
				},
				{
					"location_id" : 224,
					"name" : "Chemundu Dispensary",
					"description" : "A dispensary in Nandi County."
				},
				{
					"location_id" : 225,
					"name" : "AIC Kapsowar Mission Hospital",
					"description" : "Mission Hospital in Kapsowar."
				},
				{
					"location_id" : 226,
					"name" : "Vihiga County Referral Hospital.",
					"description" : "Referral Hospital in Vihiga county."
				},
				{
					"location_id" : 227,
					"name" : "Iten MCH",
					"description" : "An mch facility"
				},
				{
					"location_id" : 228,
					"name" : "Webuye Group 1",
					"description" : "This group is a GISHE group that meets in the Webuye Area."
				},
				{
					"location_id" : 229,
					"name" : "Kitale MCH",
					"description" : "mch clinic at kitale"
				},
				{
					"location_id" : 230,
					"name" : "Busia MCH",
					"description" : "mch clinic at busia."
				},
				{
					"location_id" : 231,
					"name" : "Chulaimbo MCH",
					"description" : "mch clinic at chulaimbo."
				}
			]';

		DB::statement("ALTER TABLE amrslocations MODIFY COLUMN id smallint UNSIGNED AUTO_INCREMENT;");
		DB::statement("ALTER TABLE samples MODIFY COLUMN amrs_location smallint UNSIGNED;");
		DB::statement("ALTER TABLE viralsamples MODIFY COLUMN amrs_location smallint UNSIGNED;");

		if(env('APP_LAB') == 5) DB::statement("ALTER TABLE cd4samples MODIFY COLUMN amrs_location smallint UNSIGNED;");
	
		$locations = json_decode($locations);

		foreach ($locations as $location) {
			$loc = DB::table('amrslocations')->where('identifier', $location->location_id)->first();
			if(!$loc){
				$loc2 = DB::table('amrslocations')->where('id', $location->location_id)->first();

				if(!$loc2){
					DB::table('amrslocations')->insert(['id' => $location->location_id, 'identifier' => $location->location_id, 'name' => $location->name]);
				}
			}
		}

		foreach ($locations as $location) {
			$loc = DB::table('amrslocations')->where('identifier', $location->location_id)->first();
			if(!$loc){
				DB::table('amrslocations')->insert(['identifier' => $location->location_id, 'name' => $location->name]);
			}
		}
	}

	public static function nyumbani()
	{
		ini_set("memory_limit", "-1");
		$file = public_path('vl_22-01-2019.csv');

		$handle = fopen($file, "r");

		$worksheet = \App\Viralworksheet::create([
			'machine_type' => 2,
			'lab_id' => env('APP_LAB'),
			'datereviewed' => '2019-01-22',
			'dateuploaded' => '2019-01-22',
			'daterun' => '2019-01-22',
			'status_id' => 3,
			'sampletype' => 2,
		]);

		$batches = [];

        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE){
            $facility = Facility::locate($row[4])->get()->first();
            if(!$facility || !is_numeric($row[4])) continue;

            $datecollected = Lookup::other_date($row[9]);
            $datereceived = Lookup::other_date($row[13]);
            if(!$datereceived) $datereceived = date('Y-m-d');
            $patient_string = $row[2];
            $existing = \App\ViralsampleView::where(['facility_id' => $facility->id, 'patient' => $patient_string, 'datecollected' => $datecollected])->get()->first();

            if($existing){
                // $existing_rows[] = $existing->toArray();
                continue;
            }

            $batch = \App\Viralbatch::withCount(['sample'])
                                    // ->where('received_by', auth()->user()->id)
                                    ->where('datereceived', $datereceived)
                                    ->where('input_complete', 0)
                                    ->where('site_entry', 1)
                                    ->where('facility_id', $facility->id)
                                    ->get()->first();

            if($batch){
                if($batch->sample_count > 9){
                    unset($batch->sample_count);
                    $batch->full_batch();
                    $batch = null;
                }
            }

            if(!$batch){
                $batch = new \App\Viralbatch;
                // $batch->user_id = auth()->user()->id;
                $batch->facility_id = $facility->id;
                // $batch->received_by = auth()->user()->id;
                $batch->lab_id = env('APP_LAB');
                $batch->datereceived = $datereceived;
                $batch->site_entry = 1;
                $batch->save();

                $batches[] = $batch->id;
            }

            $patient = \App\Viralpatient::existing($facility->id, $patient_string)->first();
            if(!$patient) $patient = new \App\Viralpatient;

            $patient->patient = $patient_string;
            $patient->facility_id = $facility->id;
            $patient->dob = Lookup::calculate_dob($datecollected, $row[7]);
            $patient->sex = Lookup::get_gender($row[6]);
            $patient->initiation_date = Lookup::other_date($row[11]);
            $patient->save();


            $sample = new \App\Viralsample;
            $sample->batch_id = $batch->id;
            $sample->patient_id = $patient->id;
            $sample->datecollected = $datecollected;
            $sample->age = $row[7];
            if(\Str::contains(strtolower($row[8]), ['edta'])) $sample->sampletype = 2; 

            $sample->areaname = $row[5];
            $sample->label_id = $row[1];
            $sample->prophylaxis = Lookup::viral_regimen($row[10]);
            $sample->justification = Lookup::justification($row[12]);
            $sample->pmtct = 3;
            $sample->receivedstatus = 1;
            $sample->worksheet_id = $worksheet->id;
            $sample->datetested = $sample->dateapproved = '2019-01-22';
            $results = \App\MiscViral::sample_result($row[14]);
            $sample->fill($results);

            $sample->save();

            // $created_rows++;
        }
        \App\Viralbatch::whereIn('id', $batches)->update(['batch_complete' => 1, 'datedispatched' => '2019-01-22']);
	}

	public static function __getLablogsData($year, $month = null, $check_email_sent = true)
    {
		$performance = LabPerformanceTracker::where('year', $year)
							->when($month, function($query) use($month){
								return $query->where('month', $month);
							})->when($check_email_sent, function($query) {
                                return $query->whereNull('dateemailsent');  
                            })->get();
                
        $eidcount = Sample::selectRaw("count(*) as tests")->whereYear('datetested', $year)
							->when($month, function($query) use ($month){
								return $query->whereMonth('datetested', $month);
							})->where('flag', '=', 1)->first()->tests;
		$eidrejected = SampleView::selectRaw('distinct rejectedreasons.name')->join('rejectedreasons', 'rejectedreasons.id', '=', 'samples_view.rejectedreason')->where('receivedstatus', '=', 2)->whereYear('samples_view.datereceived', $year)->when($month, function($query) use ($month){
								return $query->whereMonth('samples_view.datereceived', $month);
							})->get();

		$vlplasmacount = Viralsample::selectRaw("count(*) as tests")->whereYear('datetested', $year)
							->when($month, function($query) use ($month){
								return $query->whereMonth('datetested', $month);
							})->where('flag', 1)->whereBetween('sampletype', [1,2])->first()->tests;
		$vlplasmarejected = ViralsampleView::selectRaw('distinct rejectedreasons.name')->join('rejectedreasons', 'rejectedreasons.id', '=', 'viralsamples_view.rejectedreason')->where('receivedstatus', '=', 2)->whereBetween('sampletype', [1,2])->whereYear('viralsamples_view.datereceived', $year)
							->when($month, function($query) use ($month){
								return $query->whereMonth('viralsamples_view.datereceived', $month);
							})->get();

		$vldbscount = Viralsample::selectRaw("count(*) as tests")->whereYear('datetested', $year)
							->when($month, function($query) use ($month){
								return $query->whereMonth('datetested', $month);
							})->where('flag', 1)->whereBetween('sampletype', [3,4])->first()->tests;
		$vldbsrejected = ViralsampleView::selectRaw('distinct rejectedreasons.name')->join('rejectedreasons', 'rejectedreasons.id', '=', 'viralsamples_view.rejectedreason')->where('receivedstatus', '=', 2)->whereBetween('sampletype', [3,4])->whereYear('viralsamples_view.datereceived', $year)
							->when($month, function($query) use ($month){
								return $query->whereMonth('viralsamples_view.datereceived', $month);
							})->get();
		
		$equipment = LabEquipmentTracker::where('year', $year)
							->when($month, function($query) use ($month){
								return $query->where('month', $month);
							})->when($check_email_sent, function($query) {
                                return $query->whereNull('dateemailsent');  
                            })->get();

        // dd($equipment);
        // if ($performance->isEmpty() && $equipment->isEmpty())
        //     return false;
		return (object)['performance' => $performance, 'equipments' => $equipment, 'year' => $year, 'month' => $month, 'eidcount' => $eidcount, 'vlplasmacount' => $vlplasmacount, 'vldbscount' => $vldbscount, 'eidrejected' => $eidrejected, 'vlplasmarejected' => $vlplasmarejected, 'vldbsrejected' => $vldbsrejected];
	}

	public static function adjust_deliveries($plartform, $id, $quantity, $damaged) {
		if ($plartform == 1) {
			$deliveries = Taqmandeliveries::class;
			$kits = (object)self::$taqmanKits;
		} else if ($plartform == 2) {
			$deliveries = Abbotdeliveries::class;
			$kits = (object)self::$abbottKits;
		}

		$getdeliveries = $deliveries::where('id', '=', $id)->first();
		foreach ($kits as $key => $kit) {
			$alias = $kit['alias'];
			$received = $alias.'received';
			$columndamaged = $alias.'damaged';
			if ($kit['alias'] == 'qualkit'){
				$getdeliveries->$received = $quantity;
				$getdeliveries->$columndamaged = $damaged;
			} else {
				if ($plartform == 1) {
					$insertquantity = (is_nan(@((int)$quantity * $kit['factor']))) ? 0 : @((int)$quantity * $kit['factor']);
					$insertdamaged = (is_nan(@((int)$damaged * $kit['factor']))) ? 0 : @((int)$damaged * $kit['factor']);
					$getdeliveries->$received = $insertquantity;
					$getdeliveries->$columndamaged = $insertdamaged;
				} else if ($plartform == 2) {
					if ($getdeliveries->testtype == 1)
						$factor = $kit['factor']['EID'];
					else 
						$factor = $kit['factor']['VL'];
					$insertquantity = (is_nan((int)$quantity * $factor)) ? 0 : $quantity * $factor;
					$insertdamaged = (is_nan((int)$damaged * $factor)) ? 0 : $damaged * $factor;
					$getdeliveries->$received = $insertquantity;
					$getdeliveries->$columndamaged = $insertdamaged;
				}
			}
		}
		$getdeliveries->save();
	}


	public static function tat5()
	{
		DB::statement("ALTER TABLE `batches` ADD `tat5` tinyint unsigned NULL AFTER `datedispatched`");
		DB::statement("ALTER TABLE `viralbatches` ADD `tat5` tinyint unsigned NULL AFTER `datedispatched`");

        DB::statement("
        CREATE OR REPLACE VIEW samples_view AS
        (
          SELECT s.*, b.national_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.tat5, b.site_entry, b.batch_complete, b.lab_id, b.user_id, b.received_by, b.entered_by, f.facilitycode, f.name as facilityname, b.facility_id, b.input_complete, b.datedispatchedfromfacility,  p.national_patient_id, p.patient, p.sex, p.dob, p.mother_id, p.entry_point, p.patient_name, p.patient_phone_no, p.preferred_language, p.dateinitiatedontreatment,
          p.hei_validation, p.enrollment_ccc_no, p.enrollment_status, p.referredfromsite, p.otherreason

          FROM samples s
            JOIN batches b ON b.id=s.batch_id
            JOIN patients p ON p.id=s.patient_id
            LEFT JOIN facilitys f ON f.id=b.facility_id
        );
        ");

        DB::statement("
        CREATE OR REPLACE VIEW viralsamples_view AS
        (
          SELECT s.*, b.national_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.tat5, b.site_entry, b.batch_complete, b.lab_id, b.user_id, b.received_by, b.entered_by, f.facilitycode, f.name as facilityname, b.facility_id, b.input_complete, b.datedispatchedfromfacility,
          p.national_patient_id, p.patient, p.initiation_date, p.sex, p.dob, p.patient_name, p.patient_phone_no, p.preferred_language

          FROM viralsamples s
            JOIN viralbatches b ON b.id=s.batch_id
            JOIN viralpatients p ON p.id=s.patient_id
            LEFT JOIN facilitys f ON f.id=b.facility_id
        );
        ");

        \App\Common::save_tat5('eid');
        \App\Common::save_tat5('vl');
	}


	public static function time_received()
	{
		\App\Batch::where('id', '>', 1000)->update(['time_received' => null]);
		\App\Viralbatch::where('id', '>', 10000)->update(['time_received' => null]);

		DB::statement("ALTER TABLE `batches` MODIFY COLUMN `time_received` datetime NULL");
		DB::statement("ALTER TABLE `viralbatches` MODIFY COLUMN `time_received` datetime NULL");

        DB::statement("
        CREATE OR REPLACE VIEW samples_view AS
        (
          SELECT s.*, b.national_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.tat5, b.time_received, b.site_entry, b.batch_complete, b.lab_id, b.user_id, b.received_by, b.entered_by, f.facilitycode, f.name as facilityname, b.facility_id, b.input_complete,  p.national_patient_id, p.patient, p.sex, p.dob, p.mother_id, p.entry_point, p.patient_name, p.patient_phone_no, p.preferred_language, p.dateinitiatedontreatment,
          p.hei_validation, p.enrollment_ccc_no, p.enrollment_status, p.referredfromsite, p.otherreason

          FROM samples s
            JOIN batches b ON b.id=s.batch_id
            JOIN patients p ON p.id=s.patient_id
            LEFT JOIN facilitys f ON f.id=b.facility_id
        );
        ");

        DB::statement("
        CREATE OR REPLACE VIEW viralsamples_view AS
        (
          SELECT s.*, b.national_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.tat5, b.time_received, b.site_entry, b.batch_complete, b.lab_id, b.user_id, b.received_by, b.entered_by, f.facilitycode, f.name as facilityname, b.facility_id, b.input_complete,
          p.national_patient_id, p.patient, p.initiation_date, p.sex, p.dob, p.patient_name, p.patient_phone_no, p.preferred_language

          FROM viralsamples s
            JOIN viralbatches b ON b.id=s.batch_id
            JOIN viralpatients p ON p.id=s.patient_id
            LEFT JOIN facilitys f ON f.id=b.facility_id
        );
        ");
	}

	public static function clean_batches()
	{
		$batches = DB::select("select b.id, b.created_at, count(s.id) as s_count from viralbatches b left join viralsamples s on b.id=s.batch_id where s.repeatt=1 and b.id IN (select b.id from viralbatches b left join viralsamples s on b.id=s.batch_id group by b.id having count(s.id)=1 ) and date(b.created_at) > '2019-02-01' group by b.id having s_count=1;");

		foreach ($batches as $key => $batch) {
			$b = \App\Viralbatch::find($batch->id);
			$s = $b->sample->first();
			$c = $s->child->first();
			$c->batch_id = $b->id;
			$c->save();

			echo "Cleaned batch {$b->id} \n";
		}
	}


	public static function facility_tables()
	{
		DB::statement("
			CREATE TABLE IF NOT EXISTS `facility_changes` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `old_facility_id` int(10) unsigned NOT NULL,
			  `new_facility_id` int(10) unsigned NOT NULL,
			  `temp_facility_id` int(10) unsigned NOT NULL,
			  `implemented` tinyint unsigned NOT NULL DEFAULT 0,
			  `created_at` timestamp NULL DEFAULT NULL,
			  `updated_at` timestamp NULL DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  KEY `old_facility_id` (`old_facility_id`),
			  KEY `new_facility_id` (`new_facility_id`),
			  KEY `temp_facility_id` (`temp_facility_id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
	}

    public static function worksheets_data()
    {
        $data = [];
        $data[] = self::eid_worksheets();
        $data[] = self::vl_worksheets();
        $data[] = self::covid_worksheets();

        Mail::to(['joelkith@gmail.com'])->send(new TestMail($data));
    }

	public static function eid_worksheets($year = null, $download=true)
	{
		if(!$year) $year = date('Y');
		$data = SampleView::selectRaw("year(daterun) as year, month(daterun) as month, machine_type, result, count(*) as tests ")
			->join('worksheets', 'worksheets.id', '=', 'samples_view.worksheet_id')
			->where('site_entry', '!=', 2)
			->whereYear('daterun', $year)
			->where(['samples_view.lab_id' => env('APP_LAB')])
			->groupBy('year', 'month', 'machine_type', 'result')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('machine_type', 'asc')
            ->orderBy('result', 'asc')
			->get();

		$results = [1 => 'Negative', 2 => 'Positive', 3 => 'Failed', 4 => 'Unknown', 5 => 'Collect New Sample'];
		$machines = [1 => 'Roche', 2 => 'Abbott'];

		$rows = [];

		for ($i=1; $i < 13; $i++) { 
			foreach ($machines as $mkey => $mvalue) {
				$row = ['Year of Testing' => $year, 'Month of Testing' => date('F', strtotime("{$year}-{$i}-1")), ];
				$row['Machine'] = $mvalue;
				$total = 0;

				foreach ($results as $rkey => $rvalue) {
					$row[$rvalue] = $data->where('result', $rkey)->where('machine_type', $mkey)->where('month', $i)->first()->tests ?? 0;
					$total += $row[$rvalue];
				}

				$row['Total'] = $total;
				$rows[] = $row;
			}
			if($year == date('Y') && $i == date('m')) break;
		}

		$file = 'eid_worksheets_data';

        Common::csv_download($rows, $file, true, true);

		return storage_path("exports/" . $file . ".csv");
	}

	public static function vl_worksheets($year = null, $download=true)
	{
		if(!$year) $year = date('Y');
		$data = ViralsampleView::selectRaw("year(daterun) as year, month(daterun) as month, machine_type, rcategory, count(*) as tests ")
			->join('viralworksheets', 'viralworksheets.id', '=', 'viralsamples_view.worksheet_id')
			->where('site_entry', '!=', 2)
			->whereYear('daterun', $year)
			->where(['viralsamples_view.lab_id' => env('APP_LAB'), 'repeatt' => 0,])
			->groupBy('year', 'month', 'machine_type', 'rcategory')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('machine_type', 'asc')
            ->orderBy('rcategory', 'asc')
			->get();

        $data2 = ViralsampleView::selectRaw("year(daterun) as year, month(daterun) as month, machine_type, count(*) as tests ")
            ->join('viralworksheets', 'viralworksheets.id', '=', 'viralsamples_view.worksheet_id')
            ->where('site_entry', '!=', 2)
            ->whereYear('daterun', $year)
            ->where(['viralsamples_view.lab_id' => env('APP_LAB'), 'repeatt' => 1, 'rcategory' => 5])
            ->groupBy('year', 'month', 'machine_type')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('machine_type', 'asc')
            ->get();

		$results = [1 => 'LDL & <=400', 2 => '>400 & <= 1000', 3 => '> 1000 & <= 4000', 4 => '> 4000', 5 => 'Collect New Sample', 0 => 'Not Yet Dispatched'];
		$machines = [1 => 'Roche', 2 => 'Abbott', 3 => 'C8800'];

		$rows = [];

		for ($i=1; $i < 13; $i++) { 
			foreach ($machines as $mkey => $mvalue) {
				$row = ['Year of Testing' => $year, 'Month of Testing' => date('F', strtotime("{$year}-{$i}-1")), ];
				$row['Machine'] = $mvalue;
				$total = 0;

				foreach ($results as $rkey => $rvalue) {
					$row[$rvalue] = $data->where('rcategory', $rkey)->where('machine_type', $mkey)->where('month', $i)->first()->tests ?? 0;
					$total += $row[$rvalue];
				}

                $row['Failed'] = $data2->where('machine_type', $mkey)->where('month', $i)->first()->tests ?? 0;
                $total += $row['Failed'];

				$row['Total'] = $total;
				$rows[] = $row;
			}
			if($year == date('Y') && $i == date('m')) break;
		}

		$file = 'vl_worksheets_data';

        Common::csv_download($rows, $file, true, true);

        return storage_path("exports/" . $file . ".csv");
	}

    public static function covid_worksheets($year = null, $download=true)
    {
        if(!$year) $year = date('Y');
        $data = CovidSample::selectRaw("year(daterun) as year, month(daterun) as month, machine_type, result, count(*) as tests ")
            ->join('covid_worksheets', 'covid_worksheets.id', '=', 'covid_samples.worksheet_id')
            ->where('site_entry', '!=', 2)
            ->whereYear('daterun', $year)
            ->where(['covid_samples.lab_id' => env('APP_LAB')])
            ->groupBy('year', 'month', 'machine_type', 'result')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('machine_type', 'asc')
            ->orderBy('result', 'asc')
            ->get();

        $results = [1 => 'Negative', 2 => 'Positive', 3 => 'Failed', 4 => 'Unknown', 5 => 'Collect New Sample'];
        $machines = [0 => 'Manual', 1 => 'Roche', 2 => 'Abbott', 3 => 'C8800'];

        $rows = [];

        for ($i=1; $i < 13; $i++) { 
            foreach ($machines as $mkey => $mvalue) {
                $row = ['Year of Testing' => $year, 'Month of Testing' => date('F', strtotime("{$year}-{$i}-1")), ];
                $row['Machine'] = $mvalue;
                $total = 0;

                foreach ($results as $rkey => $rvalue) {
                    $row[$rvalue] = $data->where('result', $rkey)->where('machine_type', $mkey)->where('month', $i)->first()->tests ?? 0;
                    if($rkey == 3) $row[$rvalue] += $data->where('result', null)->where('machine_type', $mkey)->where('month', $i)->first()->tests ?? 0;
                    $total += $row[$rvalue];
                }

                $row['Total'] = $total;
                $rows[] = $row;
            }
            if($year == date('Y') && $i == date('m')) break;
        }

        $file = 'covid_worksheets_data';

        if($download) return Common::csv_download($rows, $file);

        Common::csv_download($rows, $file, true, true);

        return storage_path("exports/" . $file . ".csv");
    }

    private static function computeQualkits($deliveries, $prevConsumption, $procClass, $prefices, $used, $wasted, $posAdj, $negAdj, $requested)
    {
        $consumption = $procClass->consumption;
        $beginning = $prevConsumption->endingqualkit;

        $received = (isset($deliveries)) ? ($deliveries->qualkitreceived - $deliveries->qualkitdamaged) : 0;
        $positive = (int) $posAdj;$negative = (int) $negAdj;$wasted = (int) $wasted;$used = (int) $used;$requested = (int) $requested;

        $ending = (int) (($beginning + $received + $positive)-($negative + $wasted + $used));
        // return "Beginning -->" . $beginning . "     Received -->" . $received . "     Positive -->" . $positive;
        $data = ['wasted' => $wasted,'issued' => $negative,'pos' => $positive,'ending' => $ending,'request' => $requested];
        foreach ($prefices as $key => $prefix) {
            $column = $prefix.'qualkit';
            $consumption->$column = $data[$prefix];
        }
//        dd($consumption);
        return $consumption;
    }

    private static function computeOtherKits($prefices, $kits, $consumption)
    {
        foreach ($prefices as $key => $prefix) {
            $qualkit = 0;
            foreach ($kits as $key => $kit) {
                $kit = (object)$kit;
                $column = $prefix.$kit->alias;
                if ($kit->alias == 'qualkit'){
                    $qualkit = $consumption->$column;
                }
                $factor = $kit->factor;
                $classname = get_class($consumption);
                if ($classname == "App\Abbotprocurement"){
                    if (!($factor = $kit->factor['EID']))
                        $factor = $kit->factor['VL'];
                }
                $consumption->$column = round(($qualkit * $factor),2);
            }
        }
        return $consumption;
    }

    private static function getTests($testtype, $platform, $year, $month)
    {
        if ($testtype == 2)
            $tests = Viralsample::selectRaw("count(*) as `tests`")->join("viralworksheets", "viralworksheets.id", "=", "viralsamples.worksheet_id")->where('viralworksheets.lab_id', '=', env('APP_LAB'))
                            ->where('receivedstatus', '=', '1')
                            ->when($platform, function($query) use ($platform) {
                                if ($platform == 2)
                                    return $query->where("viralworksheets.machine_type", "=", 2);
                                if ($platform == 1)
                                    return $query->whereIn("viralworksheets.machine_type", [1,3]);
                            });
        if ($testtype == 1)
            $tests = Sample::selectRaw("count(*) as `tests`")->join("worksheets", "worksheets.id", "=", "samples.worksheet_id")->where('worksheets.lab_id', '=', env('APP_LAB'))
                            ->where('receivedstatus', '=', '1')
                            ->when($platform, function($query) use ($platform) {
                                if ($platform == 2)
                                    return $query->where("worksheets.machine_type", "=", 2);
                                if ($platform == 1)
                                    return $query->whereIn("worksheets.machine_type", [1,3]);
                            });

        $tests->whereYear('datetested', $year);
        $tests->whereMonth('datetested', $month);
        return $tests->first()->tests;
    }

    public static function backdateprocurement($plartform, $testtype, $month, $year, $used, $wasted, $posAdj, $negAdj, $requested) {
        $procClass = self::getProcurementClass($plartform);
        echo "==> Checking existing record\n";
        if ($procClass->consumption::where('month', $month)->where('year', $year)->where('testtype', $testtype)->get()->isEmpty()){
            if ($month == 1){
                $prevMonth = 12;
                $prevYear = ($year - 1);
            } else {
                $prevMonth = ($month - 1);
                $prevYear = $year;
            }            
            echo "\t Getting previous month consumptions\n";
            $prevConsumption = $procClass->consumption::where('month', $prevMonth)->where('year', $prevYear)->where('testtype', $testtype)->first();
            echo "\t Getting deliveries made\n";
            $deliveries = $procClass->deliveries::whereMonth('datereceived', $month)->whereYear('datereceived', $year)->where('testtype', $testtype)->first();
            $prefices = ['wasted','issued','request','pos','ending'];
            echo "\t Get test data\n";
            $consumptionClass = $procClass->consumption;
            $testsModel = self::getTests($testtype, $plartform, $year, $month);
            echo "\t Computing qual kits\n";
            $kit = (object)collect($procClass->kits)->first();
            $typetest = ($testtype == 1) ? 'EID' : 'VL';
            $testFactor = $kit->testFactor[$typetest];
            $consumption = self::computeQualkits($deliveries, $prevConsumption, $procClass, $prefices, round(@($testsModel/$testFactor)), $wasted, $posAdj, $negAdj, $requested);
            // dd($consumption);
            echo "\t Computing other kits\n";
            $consumption = self::computeOtherKits($prefices, $procClass->kits, $consumption);
            $consumption->year = $year;
            $consumption->month = $month;
            $consumption->testtype = $testtype;
            $consumption->tests = $testsModel;
            $consumption->datesubmitted = date('Y-m-d');
            $consumption->submittedBy = 88;
            $consumption->lab_id = env('APP_LAB');
            // dd($consumption);
            echo "\t Saving consumption\n"; 
            $consumption->save();
        } else {
            echo "==> Record exists\n";
        }
        echo "==> Done back-dating";
    }

	public static function adjust_procurement($id, $ending = null, $wasted = null, $issued = null, $request = null, $pos = null) {
		// $procClass = self::getProcurementClass($plartform);
		$consumption = Consumption::findOrFail($id);
		if ((int)$ending > 0) 
			self::adjust_procurement_numbers($consumption, $ending, 'ending_balance');
		// if ((int)$wasted > 0)
		// 	self::adjust_procurement_numbers($consumptions, $wasted, $procClass->kits, 'wasted');
		// if ((int)$issued > 0) 
		// 	self::adjust_procurement_numbers($consumptions, $issued, $procClass->kits, 'issued');
		// if ((int)$request > 0)
		// 	self::adjust_procurement_numbers($consumptions, $request, $procClass->kits, 'request');
		// if ((int)$pos > 0)
		// 	self::adjust_procurement_numbers($consumptions, $pos, $procClass->kits, 'pos');
		// if($consumptions->isDirty());
		// 	$consumptions->pre_update();
	}

    protected static function getProcurementClass($plartform) {
        if ($plartform == 1) {
            $consumption = new Taqmanprocurement;
            $deliveries = new Taqmandeliveries;
            $kits = (object)self::$taqmanKits;
        } else if ($plartform == 2) {
            $consumption = new Abbotprocurement;
            $deliveries = new Abbotdeliveries;
            $kits = (object)self::$abbottKits;
        }
        return (object)['consumption' => $consumption, 'deliveries' => $deliveries, 'kits' => $kits,];
    }

	protected static function adjust_procurement_numbers($model, $qualquantity, $type)
    {
        // $kits = $model->platform->kits;
        foreach ($model->details as $key => $detail) {
            $detail->$type = $qualquantity * $detail->kit->factor;
            $detail->save();
        }
		// $qualkitvalue = 0;
		// foreach($kits as $kit) {
		// 	$kit = (object)$kit;
		// 	$column = $type.$kit->alias;
		// 	if ($kit->alias == 'qualkit')
		// 		$qualkitvalue = $qualquantity;

		// 	$factor = $kit->factor;
		// 	$classname = get_class($model);
		// 	if ($classname == "App\Abbotprocurement"){
		// 		if (!($factor = $kit->factor['EID']))
		// 			$factor = $kit->factor['VL'];
		// 	}
		// 	$model->$column = $qualkitvalue * $factor;
		// }
        return true;
	}


	public static function create_attachments_table()
	{
		DB::statement("
			CREATE TABLE `attachments` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `email_id` int(10) unsigned NOT NULL ,
		  `attachment_path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		  `download_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
		  `created_at` timestamp NULL DEFAULT NULL,
		  `updated_at` timestamp NULL DEFAULT NULL,
		  `deleted_at` timestamp NULL DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `attachments_email_id_index` (`email_id`)
		) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
		");
	}

	public static function temp_correct_repeats() {
		$new_array = [];
		$samples_id = [
						// ['patient' => '17190-2019-0001', 'batch_id' => 33516],
						// ['patient' => '16152-2018-025', 'batch_id' => 33903],
						// ['patient' => '16066/2019/0008', 'batch_id' => 33702],
						// ['patient' => '16061-2019-0002', 'batch_id' => 33823],
						// ['patient' => '16027-2019-0003', 'batch_id' => 32807],
						// ['patient' => '15969-2019-0012', 'batch_id' => 33577],
						// ['patient' => '15969-2019-0008', 'batch_id' => 33413],
						// ['patient' => '15940-2019-0006', 'batch_id' => 33976],
						// ['patient' => '15874/2019/007', 'batch_id' => 33804],
						// ['patient' => '15833/2018/0027', 'batch_id' => 32673],
						// ['patient' => '15823-2019-0013', 'batch_id' => 33933],
						// ['patient' => '15795/2018/0057', 'batch_id' => 33054],
						['patient' => '14512-2019-0003', 'batch_id' => 30774] //Belongs to AMPATH
						// ['patient' => '1793120180031', 'batch_id' => 33064],
						// ['patient' => '1585020180006', 'batch_id' => 33961],
						// ['patient' => '1582620180006', 'batch_id' => 32064],
						// ['patient' => '158712018015', 'batch_id' => 33411]
					];

		foreach ($samples_id as $key => $sample) {
			$db_sample = SampleView::where($sample)->get();
			if(!$db_sample->isEmpty()){
				$update_sample = Sample::find($db_sample->first()->id);
				$update_sample->pcrtype = 2;
				$update_sample->pre_update();
			} else {
				$new_array[] = $sample;
			}
		}
		echo "<pre>";print_r($new_array);
	}

	public static function import_edarp_samples_excel($received_by) {
		$nofacility = [];
		$dataArray = [];
        echo "==>Upload Begin\n";
        $file = 'public/docs/knh-28-2-2019.xlsx';
		// $file = 'public/docs/knh-28-2-2019.xlsx';
        $batch = null;
        $lookups = Lookup::get_viral_lookups();
        // dd($lookups);
        echo "\t Fetching excel data\n";
        $excelData = Excel::load($file, function($reader){
            $reader->toArray();
        })->get();
        $excelsheetvalue = collect($excelData->values()->all());
        echo "\t Inserting sample data\n";
        $dataArray = [];
        $dataArray = ['Viral Batches'];
        $countItem = $excelsheetvalue->count();
        $counter = 0;
        $loop = 1;
        if (!$excelsheetvalue->isEmpty()){
            foreach ($excelsheetvalue as $samplekey => $samplevalue) {
            	$counter++;
            	echo $loop . "   ";
            	$loop++;
                $facility = Facility::where('facilitycode', '=', $samplevalue[5])->first();
                if (!isset($facility)){
                    $nofacility[] = $samplevalue;
                    continue;
                }
                $existing = Viralpatient::existing($facility->id, $samplevalue[3])->first();
                
                if ($existing)
                    $patient = $existing;
                else {
                    $patient = new Viralpatient();
                    $patient->patient = $samplevalue[3];
                    $patient->facility_id = $facility->id;
                    $patient->sex = $lookups['genders']->where('gender', $samplevalue[6])->first()->id;
                    $patient->dob = $samplevalue[9];
                    // $patient->initiation_date = $samplevalue[14];
                    $patient->save();
                }
                

                $existingSample = ViralsampleView::existing(['facility_id' => $facility->id, 'patient' => $patient->patient, 'datecollected' => $samplevalue[11]])->first();
                
                if ($existingSample) {
                	$batch = Viralbatch::find($existingSample->batch_id);
                	if ($batch->count() == 10)
                		$counter = 0;
                    continue;
                }
                if ($counter == 1) {                    
                    $batch = new Viralbatch();
                    $batch->user_id = $received_by;
                    $batch->lab_id = $samplevalue[2];
                    $batch->received_by = $received_by;
                    $batch->site_entry = 0;
                    $batch->entered_by = $received_by;
                    $batch->datereceived = $samplevalue[16];
                    $batch->facility_id = $facility->id;
                    $batch->save();
                }
                $existingSampleCheck = ViralsampleView::existing(['facility_id' => $facility->id, 'patient' => $patient->patient, 'datecollected' => $samplevalue[11]])->first();
                if ($existingSampleCheck) {
                	$dataArray[] = $existingSampleCheck->batch_id;
                	continue;
                }
                $sample = new Viralsample();
                $sample->batch_id = $batch->id;
                $sample->receivedstatus = $samplevalue[18];
                $sample->age = $samplevalue[8];
                $sample->patient_id = $patient->id;
                $sample->pmtct = $samplevalue[7];
                $sample->dateinitiatedonregimen = $samplevalue[14];
                $sample->datecollected = $samplevalue[11];
                $sample->regimenline = $samplevalue[13];
                $sample->prophylaxis = $lookups['prophylaxis']->where('category', $samplevalue[12])->first()->id ?? 15;
                $sample->justification = $lookups['justifications']->where('rank', $samplevalue[15])->first()->id ?? 8;
                $sample->sampletype = $samplevalue[10];
                $sample->save();

                $sample_count = $batch->sample->count();
                echo ".";
                $countItem -= 1;
                if($counter == 10) {
                    $dataArray[] = $batch->id;
                    $batch->full_batch();
                    $batch = null;
                    $counter = 0;
                } 

                if ($countItem == 1) {
                    $sample_count = $batch->sample->count();
                    if ($sample_count != 10) {
                        $batch->premature();
                        $dataArray[] = $batch->id;
                    }
                }
                // echo "<pre>";print_r("Close Batch {$batch}");echo "</pre>"; // Close batch
            }
            echo "\n\t Creating uploaded batches and missing facilities excel\n";
            $file = 'EDARP Samples uploaded to ' . Lab::find(env('APP_LAB'))->labdesc . date('Y_m_d H_i_s');

            Excel::create($file, function($excel) use($dataArray, $file){
                $excel->setTitle($file);
                $excel->setCreator('Joshua Bakasa')->setCompany($file);
                $excel->setDescription($file);

                $excel->sheet('Sheetname', function($sheet) use($dataArray) {
                    $sheet->fromArray($dataArray);
                });
            })->store('csv');

            $file2 = 'EDARP Samples uploaded to ' . Lab::find(env('APP_LAB'))->labdesc . ' without facilitycode'.date('Y_m_d H_i_s');

            Excel::create($file2, function($excel) use($nofacility, $file2){
                $excel->setTitle($file2);
                $excel->setCreator('Joshua Bakasa')->setCompany($file2);
                $excel->setDescription($file2);

                $excel->sheet('Sheetname', function($sheet) use($nofacility) {
                    $sheet->fromArray($nofacility);
                });
            })->store('csv');
            echo "\t Emailing uploaded batches and missing facilities excel\n";
            $data = [storage_path("exports/" . $file . ".csv"), storage_path("exports/" . $file2 . ".csv")];

            Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail($data));

            // $title = "EDARP Samples uploaded to KEMRI";
            // Excel::create($title, function($excel) use ($dataArray, $title) {
            //     $excel->setTitle($title);
            //     $excel->setCreator(Auth()->user()->surname.' '.Auth()->user()->oname)->setCompany('WJ Gilmore, LLC');
            //     $excel->setDescription($title);

            //     $excel->sheet('Sheet1', function($sheet) use ($dataArray) {
            //         $sheet->fromArray($dataArray, null, 'A1', false, false);
            //     });

            // })->download('csv');
            echo "==>Upload Complete";
        }
	}

	public static function export_edarp_results() {
        // echo "==> Retrival Begin \n";
		$file = 'public/docs/MISSING RESULTS.xlsx';
        // $batch = null;
        // $lookups = Lookup::get_viral_lookups();
        // dd($lookups);
		// $file = 'public/docs/EDARP_samples_on_KEMRI_752019.xlsx';// KEMRI
		// $file = 'public/docs/knh-28-2-2019.xlsx';// KNH

		/***  KEMRI Results File ***/
		// $rfiles = ['public/docs/15722.CSV',
		// 			'public/docs/15723.CSV',
		// 			'public/docs/15724.CSV',
		// 			'public/docs/15725.CSV',
		// 			'public/docs/15726.CSV'];
		$rfiles = ['public/docs/Edarp1.xlsx',
					'public/docs/Edarp2.xlsx',
					'public/docs/Edarp3.xlsx'];

        echo "==> Fetching Excel Data \n";
		$rdata = [];
		foreach ($rfiles as $key => $value) {
			$rexcelData = Excel::load($value, function($reader){
				$reader->toArray();
			})->get();
			foreach ($rexcelData as $datakey => $datavalue) {
				$rdata[] = $datavalue;
			}
		}
		$rdata = collect($rdata);
		/***  KEMRI Results File ***/

        $excelData = Excel::load($file, function($reader){
            $reader->toArray();
        })->get();
        $data = $excelData;
        $newData = [];
        $newData[] = ['Test Type','TestingLab','SpecimenLabelID','SpecimenClientCode','FacilityName','MFLCode','Sex','PMTCT','Age','DOB','SampleType','DateCollected','CurrentRegimen','regimenLine','ART Init Date','Justification','DateReceived','loginDate','ReceivedStatus','RejectedReason','ReasonforRepeat','LabComment','Datetested','DateDispatched','Results','Edited'];
        // dd($data);
        // echo "==> Getting Results \n";
        $count = 0;
        $availablecount = 0;
        $worksheet = [];
        $today = $datetested = date("Y-m-d");
        $my = new MiscViral;
        $sample_array = $doubles = [];
        // Loop through the worksheet data
        // foreach ($rdata as $key => $excelworksheet) {
        // 	# code...
        // }
        foreach ($data as $key => $sample) {
            $dbsamples = ViralsampleView::where('patient', '=', $sample[3])->where('datecollected', '=', $sample[11])->get();
            if ($dbsamples){
            	foreach ($dbsamples as $key => $samplefound) {
			        $nc = $nc_int = $lpc = $lpc_int = $hpc = $hpc_int = $nc_units = $hpc_units = $lpc_units =  NULL;
            		$samplefound = Viralsample::find($samplefound->id);
            		$excelResult = $rdata->where(2, $samplefound->id)->first();
            		// Update worksheet data if found
            		if ($excelResult){
            			$wsheet = $samplefound->worksheet;
            			$date_tested=date("Y-m-d", strtotime($excelResult[9]));
		                $datetested = MiscViral::worksheet_date($date_tested, $wsheet->created_at);

		                $interpretation = $excelResult[5];
		                $error = $excelResult[6];

			            MiscViral::dup_worksheet_rows($doubles, $sample_array, $samplefound->id, $interpretation);

			            $result_array = MiscViral::sample_result($interpretation, $error);

		                $sample_type = $excelResult[0];

		                if($sample_type == "NC"){
		                    $nc = $result_array['result'];
		                    $nc_int = $result_array['interpretation']; 
		                    $nc_units = $result_array['units']; 
		                }
		                else if($sample_type == "HPC"){
		                    $hpc = $result_array['result'];
		                    $hpc_int = $result_array['interpretation'];
		                    $hpc_units = $result_array['units'];
		                }
		                else if($sample_type == "LPC"){
		                    $lpc = $result_array['result'];
		                    $lpc_int = $result_array['interpretation'];
		                    $lpc_units = $result_array['units'];
		                }

		                $data_array = array_merge(['datemodified' => $today, 'datetested' => $datetested], $result_array);

		                $samplefound->fill($data_array);
		                if ($samplefound->national_sample_id && $samplefound->synched == 1){
                			$samplefound->synched = 2;
                		}
                		$samplefound->save();
                		$batch = $samplefound->batch;
                		if ($batch->national_batch_id && $batch->synched == 1){
                			$batch->synched = 2;
                		}
                		$batch->lab_id = 10;
                		$batch->datedispatched = date('Y-m-d', strtotime("- 25 days"));
                		$batch->save();

                		Viralsample::where(['worksheet_id' => $wsheet->id])->where('run', 0)->update(['run' => 1]);
				        Viralsample::where(['worksheet_id' => $wsheet->id])->whereNull('repeatt')->update(['repeatt' => 0]);
				        Viralsample::where(['worksheet_id' => $wsheet->id])->whereNull('result')->update(['repeatt' => 1]);

				        if (!in_array($wsheet->id, $worksheet)){
				        	$wsheet->status_id = 3;
	            			$wsheet->lab_id = 10;
					        $wsheet->neg_units = $nc_units;
					        $wsheet->neg_control_interpretation = $nc_int;
					        $wsheet->neg_control_result = $nc;

					        $wsheet->hpc_units = $hpc_units;
					        $wsheet->highpos_control_interpretation = $hpc_int;
					        $wsheet->highpos_control_result = $hpc;

					        $wsheet->lpc_units = $lpc_units;
					        $wsheet->lowpos_control_interpretation = $lpc_int;
					        $wsheet->lowpos_control_result = $lpc;

					        $wsheet->daterun = $datetested;
					        $wsheet->uploadedby = 41;

					        $wsheet->save();

					        $worksheet[] = $wsheet->id;
				        }

					    MiscViral::requeue($wsheet->id);       			
            		}
            		
            	}
            }
            // $dbsample = $dbsamples->last();
            
            // if(empty($worksheet) || !in_array($dbsample->worksheet_id, $worksheet) )
            // 	$worksheet[] = $dbsample->worksheet_id;

            // /* File worksheet reagion */
            // $excelResult = $rdata->where(5, 'S')->where(4, $dbsample->id)->first();
            // // dd($excelResult);
            // if (!$excelResult)
            // 	continue;
            // $excelResult = $excelResult->toArray();
            // /* File worksheet reagion */

            // if ($dbsample)
            // 	$availablecount++;
            // else
            // 	$count++;
            // $sample[19] = $dbsample->rejectedreason ?? null;
            // $sample[20] = $dbsample->reason_for_repeat ?? null;
            // $sample[21] = $dbsample->labcomment ?? $excelResult[12] ?? null;
            // $sample[22] = (isset($dbsample->datetested)) ? date('m/d/Y', strtotime($dbsample->datetested)) : $excelResult[11] ?? null;
            // $sample[23] = (isset($dbsample->datedispatched)) ? date('m/d/Y', strtotime($dbsample->datedispatched)) : $excelResult[11] ?? null;
            // // $sample[22] = $dbsample->datetested;
            // // $sample[23] = $dbsample->datedispatched;
            // $sample[24] = $dbsample->result ?? $excelResult[8] ?? null;
            // $newData[] = $sample->toArray();
        }
        // echo "\t";
        // print_r($worksheet);
        // echo "\n";
        echo "==> Available Results - " . $availablecount . "; Unavailable - " . $count;
        // echo "\n==> Building excel results \n";

        // $file = 'KEMRI2EDARP'.date('Y_m_d H_i_s');

        // Excel::create($file, function($excel) use($newData, $file){
        //     $excel->setTitle($file);
        //     $excel->setCreator('Joshua Bakasa')->setCompany($file);
        //     $excel->setDescription($file);

        //     $excel->sheet('Sheetname', function($sheet) use($newData) {
        //         $sheet->fromArray($newData);
        //     });
        // })->store('csv');

        // $data = [storage_path("exports/" . $file . ".csv")];

        // Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail($data));

        echo "==>Retrival Complete";
	}

	public static function export_edarp_results_worksheet() {
		echo "==> Retrival Begin\n";
		$file = 'public/docs/Lst28KEMRI.xlsx';
		$rfiles = [['file' => 'public/docs/15668.xlsx', 'id' => 15668],
					['file' => 'public/docs/15671.xlsx', 'id' => 15671],
					['file' => 'public/docs/15675.xlsx', 'id' => 15675],
					['file' => 'public/docs/15679.xlsx', 'id' => 15679]];

        echo "==> Fetching Excel Data \n";
        $newData = [];
		$excelData = Excel::load($file, function($reader){
            $reader->toArray();
        })->get();

		foreach ($rfiles as $key => $value) {
			$rexcelData = Excel::load($value['file'], function($reader){
				$reader->toArray();
			})->get();
			$worksheet = Viralworksheet::find($value['id']);
	        $today = $datetested = date("Y-m-d");
	        $my = new MiscViral;
	        $sample_array = $doubles = [];
			$nc = $nc_int = $lpc = $lpc_int = $hpc = $hpc_int = $nc_units = $hpc_units = $lpc_units =  NULL;
			foreach ($rexcelData as $datakey => $datavalue) {
				$sample = Viralsample::find($datavalue[2]);
				$date_tested=date("Y-m-d", strtotime($datavalue[12]));
		        $datetested = MiscViral::worksheet_date($date_tested, $worksheet->created_at);

		        $interpretation = $datavalue[5];
                $error = $datavalue[6];
                if ($sample)
	            	MiscViral::dup_worksheet_rows($doubles, $sample_array, $sample->id, $interpretation);

	            $result_array = MiscViral::sample_result($interpretation, $error);

		        $sample_type = $datavalue[0];

                if($sample_type == "NC"){
                    $nc = $result_array['result'];
                    $nc_int = $result_array['interpretation']; 
                    $nc_units = $result_array['units']; 
                }
                else if($sample_type == "HPC"){
                    $hpc = $result_array['result'];
                    $hpc_int = $result_array['interpretation'];
                    $hpc_units = $result_array['units'];
                }
                else if($sample_type == "LPC"){
                    $lpc = $result_array['result'];
                    $lpc_int = $result_array['interpretation'];
                    $lpc_units = $result_array['units'];
                }

                $data_array = array_merge(['datemodified' => $today, 'datetested' => $datetested], $result_array);

                if ($sample) {
                	$sample->fill($data_array);
	                if ($sample->national_sample_id && $sample->synched == 1){
            			$sample->synched = 2;
            		}
            		$sample->save();
            		$batch = $sample->batch;
            		if ($batch->national_batch_id && $batch->synched == 1){
            			$batch->synched = 2;
            		}
            		$batch->lab_id = 10;
            		$batch->datedispatched = date('Y-m-d', strtotime("- 25 days"));
            		$batch->save();

            		 /* File worksheet reagion */
            		$patient = $sample->patient;
		            $sampleExcel = $excelData->where(3, $patient->patient)->first();
		            // dd($excelResult);
		            if (!$sampleExcel)
		            	continue;
		            $sampleExcel = $sampleExcel->toArray();
		            /* File worksheet reagion */

		            $sampleExcel[19] = $sample->rejectedreason ?? null;
		            $sampleExcel[20] = $sample->reason_for_repeat ?? null;
		            $sampleExcel[21] = $sample->labcomment ?? null;
		            $sampleExcel[22] = (isset($sample->datetested)) ? date('m/d/Y', strtotime($sample->datetested)) : null;
		            $sampleExcel[23] = (isset($sample->datedispatched)) ? date('m/d/Y', strtotime($sample->datedispatched)) : null;
		            // $sample[22] = $dbsample->datetested;
		            // $sample[23] = $dbsample->datedispatched;
		            $sampleExcel[24] = $sample->result ?? null;
		            $newData[] = $sampleExcel;
                }
			}
			Viralsample::where(['worksheet_id' => $worksheet->id])->where('run', 0)->update(['run' => 1]);
	        Viralsample::where(['worksheet_id' => $worksheet->id])->whereNull('repeatt')->update(['repeatt' => 0]);
	        Viralsample::where(['worksheet_id' => $worksheet->id])->whereNull('result')->update(['repeatt' => 1]);

        	$worksheet->status_id = 3;
			$worksheet->lab_id = 10;
	        $worksheet->neg_units = $nc_units;
	        $worksheet->neg_control_interpretation = $nc_int;
	        $worksheet->neg_control_result = $nc;

	        $worksheet->hpc_units = $hpc_units;
	        $worksheet->highpos_control_interpretation = $hpc_int;
	        $worksheet->highpos_control_result = $hpc;

	        $worksheet->lpc_units = $lpc_units;
	        $worksheet->lowpos_control_interpretation = $lpc_int;
	        $worksheet->lowpos_control_result = $lpc;

	        $worksheet->daterun = $datetested;
	        $worksheet->uploadedby = 41;

	        $worksheet->save();

		    MiscViral::requeue($worksheet->id);
		}

		echo "\n==> Building excel results \n";

        $file = 'KEMRI2EDARP'.date('Y_m_d H_i_s');

        Excel::create($file, function($excel) use($newData, $file){
            $excel->setTitle($file);
            $excel->setCreator('Joshua Bakasa')->setCompany($file);
            $excel->setDescription($file);

            $excel->sheet('Sheetname', function($sheet) use($newData) {
                $sheet->fromArray($newData);
            });
        })->store('csv');

        $data = [storage_path("exports/" . $file . ".csv")];

        Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail($data));

        echo "==>Retrival Complete";
	}

	public static function delete_edarp_imported_batches() {
		echo "==> Deleting Samples\n";
		$batches = [];
		// $batches = collect($excelData->toArray())->first();
        Viralsample::whereIn('batch_id', $batches)->delete();
        Viralbatch::whereIn('id', $batches)->delete();
        echo "==> Deletion Complete\n";
	}

	public static function confirm_edarp_upload($received_by) {
		echo "==>Check Begin\n";
		$file = 'public/docs/EDARP_samples_being_referred_to _KNH_CCC_laboratory.xlsx';
        // $batch = null;
        // $lookups = Lookup::get_viral_lookups();
        // // dd($lookups);
        $addedcount = 0;
        $missingcount = 0;
        echo "\t Fetching excel data\n";
        $excelData = Excel::load($file, function($reader){
            $reader->toArray();
        })->get();
        $excelsheetvalue = collect($excelData->values()->all());
        $countItem = $excelsheetvalue->count();
        $counter = 0;
        echo "\t Beginning Count\n";
        if (!$excelsheetvalue->isEmpty()){
            foreach ($excelsheetvalue as $samplekey => $samplevalue) {
            	$facility = Facility::where('facilitycode', '=', $samplevalue[5])->first();
				$patient = Viralpatient::existing($facility->id, $samplevalue[3])->first();
            	$existingSampleCheck = ViralsampleView::existing(['facility_id' => $facility->id, 'patient' => $patient->patient, 'datecollected' => $samplevalue[11]])->first();
                if ($existingSampleCheck) {
                	$addedcount++;
                	$countItem -= 1;
                } else {
                	print_r($samplevalue);
     //            	$counter++;
					// $nofacility = [];
					// $dataArray = [];

			  //       $lookups = Lookup::get_viral_lookups();
     //            	if ($counter == 1) {                    
	    //                 $batch = new Viralbatch();
	    //                 $batch->user_id = $received_by;
	    //                 $batch->lab_id = env('APP_LAB');
	    //                 $batch->received_by = $received_by;
	    //                 $batch->site_entry = 0;
	    //                 $batch->entered_by = $received_by;
	    //                 $batch->datereceived = $samplevalue[16];
	    //                 $batch->facility_id = $facility->id;
	    //                 $batch->save();
	    //             }
	    //             $existingSampleCheck = ViralsampleView::existing(['facility_id' => $facility->id, 'patient' => $patient->patient, 'datecollected' => $samplevalue[11]])->first();
	    //             if ($existingSampleCheck) {
	    //             	$dataArray[] = $existingSampleCheck->batch_id;
	    //             	continue;
	    //             }
	    //             $sample = new Viralsample();
	    //             $sample->batch_id = $batch->id;
	    //             $sample->receivedstatus = $samplevalue[18];
	    //             $sample->age = $samplevalue[8];
	    //             $sample->patient_id = $patient->id;
	    //             $sample->pmtct = $samplevalue[7];
	    //             $sample->dateinitiatedonregimen = $samplevalue[14];
	    //             $sample->datecollected = $samplevalue[11];
	    //             $sample->regimenline = $samplevalue[13];
	    //             $sample->prophylaxis = $lookups['prophylaxis']->where('category', $samplevalue[12])->first()->id ?? 15;
	    //             $sample->justification = $lookups['justifications']->where('rank', $samplevalue[15])->first()->id ?? 8;
	    //             $sample->sampletype = $samplevalue[10];
	    //             $sample->save();

	    //             $sample_count = $batch->sample->count();
	    //             echo ".";
	    //             $countItem -= 1;
	    //             if($counter == 10) {
	    //                 $dataArray[] = $batch->id;
	    //                 $batch->full_batch();
	    //                 $batch = null;
	    //                 $counter = 0;
	    //             } 

	    //             if ($countItem == 1) {
	    //                 $sample_count = $batch->sample->count();
	    //                 if ($sample_count != 10) {
	    //                     $batch->premature();
	    //                     $dataArray[] = $batch->id;
	    //                 }
	    //             }
                	$missingcount++;
                }
            }
        }
        echo "\t Count complete data\n";
        echo "==> Check complete with available " . $addedcount . " and missing " . $missingcount;
	}
	public static function delete_duplicates () {
		echo "==>Check Begin\n";
		$file = 'public/docs/EDARP_samples_being_referred_to _KNH_CCC_laboratory.xlsx';
        echo "\t Fetching excel data\n";
        $excelData = Excel::load($file, function($reader){
            $reader->toArray();
        })->get();
        $excelsheetvalue = collect($excelData->values()->all());
        echo "\t Checking duplicates Count\n";
        if (!$excelsheetvalue->isEmpty()){
            foreach ($excelsheetvalue as $samplekey => $samplevalue) {
            	$facility = Facility::where('facilitycode', '=', $samplevalue[5])->first();
            	$patient = Viralpatient::existing($facility->id, $samplevalue[3])->first();
            	$samples = $patient->sample;
            	if ($samples->count() > 1) {
            		$firstSample = $samples->first();
            		foreach ($samples as $key => $sample) {
            			if (($firstSample->id != $sample->id) && ($sample->facility_id == $firstSample->facility_id && $sample->datecollected == $firstSample->datecollected)) {
            				if ($sample->batch->count() == 0)
            					$sample->batch->delete();
            				$sample->delete();
            			}
            		}
            	}
            }
        }
        echo "==> Complete";
	}

	public static function delete_uploads(){
		echo "==> Reading File\n";
		$file = 'public/docs/KemriToBeRemoved.csv';
		$excelData = Excel::load($file, function($reader){
            $reader->toArray();
        })->get();
        $batches = [];
        echo "==> Starting samples delete\n";
        foreach ($excelData as $key => $sample) {
        	$dbsample = ViralsampleView::where('patient', '=',  $sample[3])->whereNull('result')->get()->last();
        	$sample = Viralsample::find($dbsample->id);
        	$batch = $sample->batch;
        	$sample->delete();
        	echo ".";
        	if($batch->fresh('sample')->count() < 1){
        		$batch->delete();
        		echo "\n..\n";
        	}
        	$batches[] = $batch->id;
        }
        echo "\n==> Saving deleted Batches\n";
        $file = 'KEMRI2EDARPDeletedBatches'.date('Y_m_d H_i_s');
        
        Excel::create($file, function($excel) use($batches, $file){
            $excel->setTitle($file);
            $excel->setCreator('Joshua Bakasa')->setCompany($file);
            $excel->setDescription($file);

            $excel->sheet('Sheetname', function($sheet) use($batches) {
                $sheet->fromArray($batches);
            });
        })->store('csv');
        echo "==> Deletion Complete";
	}

    public static function checkMbNo(){
    	$files = [['file' =>'public/docs/NHRL_MBNo/eid_data_Exsting.csv', 'name' => 'eid data Exsting First'],
    			['file' =>'public/docs/NHRL_MBNo/eid_data_Exsting_part2.csv', 'name' => 'eid data Exsting First Part 2'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataSecondPart1.csv', 'name' => 'eid data Exsting Second Part 1'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataSecondPart2.csv', 'name' => 'eid data Exsting Second Part 2'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataThirdPart1.csv', 'name' => 'eid data Exsting Third Part 1'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataThirdPart2.csv', 'name' => 'eid data Exsting Third Part 2'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataFourthPart1.csv', 'name' => 'eid data Exsting Fourth Part 1'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataFourthPart2.csv', 'name' => 'eid data Exsting Fourth Part 2'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataFifthPart1.csv', 'name' => 'eid data Exsting Fifth Part 1'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataFifthPart2.csv', 'name' => 'eid data Exsting Fifth Part 2'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataSixthPart1.csv', 'name' => 'eid data Exsting Sixth Part 1'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataSixthPart2.csv', 'name' => 'eid data Exsting Sixth Part 2'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataSeventhPart1.csv', 'name' => 'eid data Exsting Seventh Part1'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataSeventhPart2.csv', 'name' => 'eid data Exsting Seventh Part2'],
    			['file' =>'public/docs/NHRL_MBNo/eidDataEighthPart1.csv', 'name' => 'eid data Exsting Eighth']
    		];
    	// $files = [['file' => 'public/docs/eidTest.xlsx', 'name' => 'EID Test Data']];
    	echo "==> Fetching Excel Data (". date('Y-m-d H:i:s') . ") \n";
    	ini_set("memory_limit", "-1");
    	foreach ($files as $key => $file) {
    		echo "====> Getting Excel Data (". date('Y-m-d H:i:s') . " - " . $file['name'] . ") \n";
    		$excelData = Excel::load($file['file'], function($reader){
	            $reader->toArray();
	        })->get();
    		// dd($excelData->toArray());
	        foreach ($excelData as $key => $value) {
	        	$dbData[] = [
	        		'c_posted' => $value[0],
	        		'label_id' => $value[1],
	        		'login_date' => $value[2],
	        	];
	        }
	        echo "====> Saving Excel Data (". date('Y-m-d H:i:s') . " - " . $file['name'] . ") \n";
	        Nhrl::insert($dbData);
	        echo "====> Saved Excel Data (". date('Y-m-d H:i:s') . " - " . $file['name'] . ") \n";
    	}
    	echo "==> All Files completed(". date('Y-m-d H:i:s') . ")";
        // $excelData = Excel::import($file, function($reader){
        //     $reader->toArray();
        // })->get();
        // $data = $excelData;
        // echo "==> Getting MB No \n";
        // dd($data);
        // foreach ($data as $key => $sample) {
        // 	$dbsample = Sample::where('comment', '=', $sample[3])->get()->last();
        // }
    }

    public static function consolidate() {
    	$missing = 'public/docs/MISSING RESULTS.xlsx';
    	$result = 'public/docs/kemri-28-2-2019.xlsx';
    	echo "==> Reading Excel files\n";
    	$missingData = Excel::load($missing, function($reader){
            $reader->toArray();
        })->get();
        $resultData = Excel::load($result, function($reader){
            $reader->toArray();
        })->get();
        $newData[] = ['Test Type','TestingLab','SpecimenLabelID','SpecimenClientCode','FacilityName','MFLCode','Sex','PMTCT','Age','DOB','SampleType','DateCollected','CurrentRegimen','regimenLine','ART Init Date','Justification','DateReceived','loginDate','ReceivedStatus','RejectedReason','ReasonforRepeat','LabComment','Datetested','DateDispatched','Results','Edited'];
        echo "==> Matching Excel results\n";
        foreach ($missingData as $key => $missing) {
        	$result = $resultData->where(2, $missing[2])->first();
        	$missing[21] = $result[21] ?? null;
    		$missing[22] = $result[22] ?? null;
    		$missing[23] = $result[23] ?? null;
    		$missing[24] = $result[24] ?? null;
    		$newData[] = $missing->toArray();
        }
        echo "==> Writing data to csv\n";
        $file = 'KEMRI2EDARP'.date('Y_m_d H_i_s');
        // dd($newData);
        Excel::create($file, function($excel) use($newData, $file){
            $excel->setTitle($file);
            $excel->setCreator('Joshua Bakasa')->setCompany($file);
            $excel->setDescription($file);

            $excel->sheet('Sheetname', function($sheet) use($newData) {
                $sheet->fromArray($newData);
            });
        })->store('csv');
        echo "==> Data consolidation complete\n";
    }

    public static function run_ken_request() {
    	$data = [];
    	echo "==> Getting Patients\n";
    	ini_set("memory_limit", "-1");
    	$patients = Viralpatient::select('id')->get();
    	// echo "==> Getting Patients Samples\n";
    	// $excelColumns = ['Patient', 'Current Regimen', 'Recent Result', 'Age Category'];
    	// ini_set("memory_limit", "-1");
    	// foreach ($patients as $key => $patient) {
    		// $samples = ViralsampleCompleteView::where('patient_id', $patient->id)->orderBy('datetested', 'desc')->limit(2)->get();
    		// if ($samples->count() == 2) {
    		// 	$newsamples = $samples->whereIn('rcategory', [3,4]);
    		// 	if ($newsamples->count() == 2){
    		// 		echo ".";
    		// 		$newsample = $newsamples->first();
    		// 		$data[] = [
    		// 			'patient' => $patient->patient,
    		// 			'regimen' => $newsample->prophylaxis_name,
    		// 			'result' => $newsample->result,
    		// 			'agecategory' => self::getMakeShiftAgeCategory($newsample->age),
    		// 		];
    		// 	}
    		// }
    	// }
    	$file = 'VL_Line_List_TLD_TX_LAST2';
    	
    	// New TLD patients
    	ini_set("memory_limit", "-1");
    	// $patientsGroups = Viralpatient::select('id')->get()->split(10600);
    	echo "==> Getting patients' data\n";
    	foreach ($patients as $key => $patient) {
    		echo "\tGetting patients` batch {$key}\n";
    		// echo "==> Getting tests \n";
    		$tests = ViralsampleCompleteView::selectRaw("patient_id,viralsample_complete_view.id,batch_id,patient,labdesc,county,subcounty,partner,view_facilitys.name,view_facilitys.facilitycode,gender_description,dob,age,sampletype,datecollected,justification_name,datereceived,datetested,datedispatched,initiation_date,receivedstatus_name,reason_for_repeat,rejected_name,prophylaxis_name, regimenline,pmtct_name,result, month(datetested) as testmonth")
    		// $dataArray = SampleCompleteView::select('sample_complete_view.id','patient','original_batch_id','labdesc','county','subcounty','partner','view_facilitys.name','view_facilitys.facilitycode','gender_description','dob','age','pcrtype','enrollment_ccc_no','datecollected','datereceived','datetested','datedispatched','regimen_name','receivedstatus_name','labcomment','reason_for_repeat','spots','feeding_name','entry_points.name as entrypoint','results.name as infantresult','mother_prophylaxis_name','motherresult','mother_age','mother_ccc_no','mother_last_result')
    						->where('repeatt', 0)
    						// ->whereIn('rcategory', [1,2,3,4])
    						->where('patient_id', $patient->id)
    						// ->whereYear('datetested', 2019)
    						->whereIn('rcategory', [3,4])
    						->where('regimen', 18)
    						// ->whereRaw("month(datetested) IN (4, 5, 6)")
    						->join('labs', 'labs.id', '=', 'viralsample_complete_view.lab_id')
    						->join('view_facilitys', 'view_facilitys.id', '=', 'viralsample_complete_view.facility_id')
    						// ->join('results', 'results.id', '=', 'sample_complete_view.result')
    						// ->join('entry_points', 'entry_points.id', '=', 'sample_complete_view.entry_point')
    						->orderBy('datetested', 'desc')->limit(2)->get();
    		// dd($tests);
    		// if ($tests->count() == 2) {
    			foreach ($tests as $key => $test) {
	    			$data[] = $test->toArray();
	    		}
    		// }
    	}

    	echo "=> Creating excel\n";
    	Excel::create($file, function($excel) use($data)  {
		    $excel->sheet('Sheetname', function($sheet) use($data) {
		        $sheet->fromArray($data);
		    });
		})->store('csv');
		$data = [storage_path("exports/" . $file . ".csv")];
		echo "==> Mailing excel";
		Mail::to(['bakasajoshua09@gmail.com', 'joshua.bakasa@dataposit.co.ke'])->send(new TestMail($data));
    }

    private static function getMakeShiftAgeCategory($dob) {
		$date2 = date('Y-m-d');
		$diff = abs(strtotime($date2) - strtotime($dob));
		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		$age = $years + (($months + ($days / 365)) / 12);

		// dd("%d years, %d months, %d days\n", $years, $months, $days);
    	if ($age < 1)
    		return '0-1';
    	if ($age > 0.9999 && $age < 5)
    		return '1- <5';
    	if ($age > 5.9999 && $age < 10)
    		return '5-<10';
    }

    public static function linelist(){
    	$dataArray = [];
    	$data = [];
    	$months = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
    	echo "==> Getting Unique patients\n";
    	ini_set("memory_limit", "-1");
    	$patientsGroups = Viralsample::selectRaw('distinct patient_id')->whereYear('datetested', '=', '2018')->get()->split(10600);
    	echo "==> Getting patients' data\n";
    	foreach ($patientsGroups as $key => $patients) {
    		echo "\tGetting patients` batch {$key}\n";
    		// echo "==> Getting tests \n";
    		$tests = ViralsampleCompleteView::selectRaw("distinct patient_id,viralsample_complete_view.id,batch_id,patient,labdesc,county,subcounty,partner,view_facilitys.name,view_facilitys.facilitycode,gender_description,dob,age,sampletype,datecollected,justification_name,datereceived,datetested,datedispatched,initiation_date,receivedstatus_name,reason_for_repeat,rejected_name,prophylaxis_name, regimenline,pmtct_name,result, month(datetested) as testmonth")
    		// $dataArray = SampleCompleteView::select('sample_complete_view.id','patient','original_batch_id','labdesc','county','subcounty','partner','view_facilitys.name','view_facilitys.facilitycode','gender_description','dob','age','pcrtype','enrollment_ccc_no','datecollected','datereceived','datetested','datedispatched','regimen_name','receivedstatus_name','labcomment','reason_for_repeat','spots','feeding_name','entry_points.name as entrypoint','results.name as infantresult','mother_prophylaxis_name','motherresult','mother_age','mother_ccc_no','mother_last_result')
    						->where('repeatt', 0)
    						// ->whereIn('rcategory', [1,2,3,4])
    						->whereIn('patient_id', $patients->toArray())
    						->whereYear('datetested', 2018)
    						// ->whereRaw("month(datetested) IN (4, 5, 6)")
    						->join('labs', 'labs.id', '=', 'viralsample_complete_view.lab_id')
    						->join('view_facilitys', 'view_facilitys.id', '=', 'viralsample_complete_view.facility_id')
    						// ->join('results', 'results.id', '=', 'sample_complete_view.result')
    						// ->join('entry_points', 'entry_points.id', '=', 'sample_complete_view.entry_point')
    						->orderBy('datetested', 'desc')->get();
    		// dd($tests);
    		foreach ($tests as $key => $test) {
    			$data[] = $test;
    		}
    	}
    	echo "==> Splitting to months\n";
    	$data = collect($data);
    	foreach ($months as $key => $value) {
    		$dataArray = array_values(collect($data)->whereIn('testmonth', $value)->flatten(1)->toArray());
    		// dd($dataArray);
    		echo "\tPreparing excel {$value}\n";
	    	$file = 'New VL Line List 2018 Unique patients ' . $value;
	    	// return (new NhrlExport($data, $excelColumns))->store("$file.csv");
	    	Excel::create($file, function($excel) use($dataArray)  {
			    $excel->sheet('Sheetname', function($sheet) use($dataArray) {
			        $sheet->fromArray($dataArray);
			    });
			})->store('csv');
			$data = [storage_path("exports/" . $file . ".csv")];
			Mail::to(['bakasajoshua09@gmail.com', 'joshua.bakasa@dataposit.co.ke'])->send(new TestMail($data));
			echo "\t Completed month {$value}\n";
			// dd('Only the first');
    	}
    	echo "==> Completed everything";

    	// dd($dataArray);
    	
    }

    public static function getElvis()
    {
        // New TLD patients
        ini_set("memory_limit", "-1");
        $data = [['Facility', 'MFL Code', 'Tests', 'Positives', 'Positivity', 'Rejected Samples', 'Collection to Receipt', 'Receipt to Processing', 'Processing to Dispatch', 'Collection to Dispatch']];
        echo "==> Getting patient level data\n";
        $model = SampleCompleteView::selectRaw("sample_complete_view.patient_id AS `uniqueOf`, sample_complete_view.id, sample_complete_view.result, sample_complete_view.receivedstatus, sample_complete_view.tat1, sample_complete_view.tat2, sample_complete_view.tat3, sample_complete_view.tat4, vf.name AS `facility`, vf.facilitycode")
            ->join('view_facilitys as vf', 'vf.id', '=', 'sample_complete_view.facility_id')
            ->whereRaw("DATE(datetested) BETWEEN '2018-07-01' AND '2019-06-30' AND sample_complete_view.repeatt = 0")->get()->unique('uniqueOf');
        echo "==> Getting the unique facilities\n";
        $facilities = $model->pluck('facilitycode');

        echo "==> Getting facilites data of {$facilities->count()}\n";

        foreach ($facilities as $key => $value) {
            echo "{$key} - ";
            $facilityData = $model->where('facilitycode', $value);
            $facility = $facilityData->first()->facility;
            $totalTests = $facilityData->count();
            $totalPositives = $facilityData->where('result', 2)->count();
            $totalRejected = $facilityData->where('receivedstatus', 2)->count();
            $tat1 = $facilityData->pluck('tat1')->avg();
            $tat2 = $facilityData->pluck('tat2')->avg();
            $tat3 = $facilityData->pluck('tat3')->avg();
            $tat4 = $facilityData->pluck('tat4')->avg();
            // dd($facilityData);
            $data[] = [
                $facility,
                $value,
                $totalTests,
                $totalPositives,
                round($totalPositives/$totalTests, 2),
                $totalRejected,
                round($tat1, 2),
                round($tat2, 2),
                round($tat3, 2),
                round($tat4, 2)
            ];
        }
        
        $file = "excel export";
        echo "\n==> Creating excel\n";
        Excel::create($file, function($excel) use($data)  {
            $excel->sheet('Sheetname', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->store('csv');
        $data = [storage_path("exports/" . $file . ".csv")];
        echo "==> Mailing excel";
        Mail::to(['bakasajoshua09@gmail.com', 'joshua.bakasa@dataposit.co.ke'])->send(new TestMail($data));
    }

    public static function rectify()
    {
        $correction_classes = [
                [
                    'incomingclass' => \App\CorruptModels\Batch::class, 
                    'restclass' => Batch::class, 
                    'check_columns' => ['facility_id', 'datereceived', 'lab_id'],
                ],
                [
                    'incomingclass' => \App\CorruptModels\Viralbatch::class, 
                    'restclass' => Viralbatch::class, 
                    'check_columns' => ['facility_id', 'datereceived', 'lab_id'],
                ],
                [
                    'incomingclass' => \App\CorruptModels\Sample::class,
                    'restclass' => Sample::class, 
                    'check_columns' => ['patient_id', 'batch_id', 'created_at'],
                ],
                [
                    'incomingclass' => \App\CorruptModels\Viralsample::class,
                    'restclass' => Viralsample::class, 
                    'check_columns' => ['patient_id', 'batch_id', 'created_at'],
                ],
                [
                    'incomingclass' => \App\CorruptModels\Abbotdeliveries::class,
                    'restclass' => Abbotdeliveries::class,
                    'check_columns' => ['year', 'quarter', 'testtype']],
                
                [
                    'incomingclass' => \App\CorruptModels\Abbotprocurement::class,
                    'restclass' => Abbotprocurement::class,
                    'check_columns' => ['year', 'month', 'testtype']],
                [
                    'incomingclass' => \App\CorruptModels\Abbotdeliveries::class,
                    'restclass' => Abbotdeliveries::class,
                    'check_columns' => ['year', 'quarter', 'testtype']],
                [
                    'incomingclass' => \App\CorruptModels\Taqmanprocurement::class,
                    'restclass' => Taqmanprocurement::class,
                    'check_columns' => ['year', 'month', 'testtype']],
                [
                    'incomingclass' => \App\CorruptModels\Taqmandeliveries::class,
                    'restclass' => Taqmandeliveries::class,
                    'check_columns' => ['year', 'quarter', 'testtype']],
                [
                    'incomingclass' => \App\CorruptModels\Taqmandeliveries::class,
                    'restclass' => Taqmandeliveries::class,
                    'check_columns' => ['year', 'quarter', 'testtype']],
                [
                    'incomingclass' => \App\CorruptModels\LabEquipmentTracker::class,
                    'restclass' => LabEquipmentTracker::class,
                    'check_columns' => ['year', 'month', 'equipment_id']],
                [
                    'incomingclass' => \App\CorruptModels\LabPerformanceTracker::class,
                    'restclass' => LabPerformanceTracker::class,
                    'check_columns' => ['year', 'month', 'testtype', 'sampletype']],
                [
                    'incomingclass' => \App\CorruptModels\Patient::class,
                    'restclass' => Patient::class,
                    'check_columns' => ['facility_id', 'patient']
                ],
                [
                    'incomingclass' => \App\CorruptModels\Viralpatient::class,
                    'restclass' => Viralpatient::class,
                    'check_columns' => ['facility_id', 'patient']
                ], 
                [
                    'incomingclass' => \App\CorruptModels\Viralworksheet::class,
                    'restclass' => Viralworksheet::class,
                    'check_columns' => ['createdby', 'created_at']
                ],
                [
                    'incomingclass' => \App\CorruptModels\Worksheet::class,
                    'restclass' => Worksheet::class,
                    'check_columns' => ['createdby', 'created_at']
                ],
                [
                    'incomingclass' => \App\CorruptModels\Mother::class,
                    'restclass' => Mother::class
                ],
        ];

        echo "==>Begin transaction\n";

        echo "==> Inserting the data\n";
        foreach ($correction_classes as $key => $class) {
            echo "==> Processing {$class['incomingclass']}\n";
            $objects = self::process_incoming_model($class);
            echo "\t{$class['incomingclass']} {$objects->count()} objects found\n";
            $insertclass = $class['incomingclass'];          
            foreach ($objects as $key => $model) {
                $model->old_id = $model->id;
                unset($model->id);
                $save = self::format_for_and_save($model, $class, $insertclass);
            }
        }
        echo "==> Correcting the reference ids\n";
        echo "\tViralsample correction\n";
        foreach (\App\CorruptModels\Viralsample::whereNotNull('old_id')->get() as $key => $sample) {
            $sample->corrupt_version();
        }

        echo "\tSample correction\n";
        foreach (\App\CorruptModels\Sample::whereNotNull('old_id')->get() as $key => $sample) {
            $sample->corrupt_version();
        }

        echo "\tPatient correction\n";
        foreach (\App\CorruptModels\Patient::whereNotNull('old_id')->get() as $key => $patient) {
            $patient->corrupt_version();
        }
        echo "==> End transaction\n";
    }

    private static function process_incoming_model($class) {
        $reference_date = '2020-03-25';
        $modelclass = $class['restclass'];
        $oldmodels = $modelclass::whereRaw("date(created_at) > '{$reference_date}'")->whereNull('old_id')->get();
        return $oldmodels;
    }

    private static function format_for_and_save($model, $class, $insertclass) 
    {
        // $existingColumns = [];
        // foreach ($class['check_columns'] as $key => $value) {
        //     $existingColumns[] = $model->$value;
        // }
        // $existing = $insertclass::existing(...$existingColumns)->get();
        // if ($existing->isEmpty()) {
            self::save_missing_model($insertclass, $model);
        // }
        return true;
    }

    private static function save_missing_model($class, $model) {
        // dd($class);
        return $class::create($model->toArray());
    }

    public static function ampath_pmtct()
    {
        $file = public_path('ampath_pmtct.csv');
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            if($data[0] == 'Lab ID') continue;
            $s = Viralsample::find($data[0]);
            if(!$s) continue;

            // if(\Str::startsWith($data[3], 'N')) $s->pmtct = 3;
            if(\Str::startsWith($data[3], 'P')) $s->pmtct = 1;
            else if(\Str::startsWith($data[3], 'B')) $s->pmtct = 2;
            else{
                $s->pmtct = 2;
            }
            $s->pre_update();
        }
    }


    public static function wrp_covid_correction()
    {
        $file = public_path('original_wrp_samples.csv');
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            if($data[0] == 'Lab ID') continue;
            $s = CovidSampleView::find($data[0]);
            if(!$s) continue;

            if($s->patient_name != $data[3]){
                $patient = CovidPatient::find($s->patient_id);
                $count = $patient->sample()->where(['repeatt' => 0])->count();
                if($count > 1){
                    $patient = $patient->replicate(['national_patient_id', 'synched', 'datesynced']);                    
                }
                $patient->fill([
                    'patient_name' => $data[3],
                    'identifier' => $data[1],
                    'phone_no' => $data[4],
                    'county' => $data[5],
                    'subcounty' => $data[6],
                    'sex' => $data[8],
                ]);
                $patient->pre_update();

                $sample = CovidSample::find($s->id);
                $sample->fill([
                    'patient_id' => $patient->id,
                    'age' => $data[7],
                ]);
            }
        }
    }

    
    public static function knh_samples()
    {
        // ALTER TABLE `covid_samples` ADD `justification` tinyint(4) NULL AFTER `test_type`;
        $file = public_path('knh-samples.csv');
        $handle = fopen($file, "r");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            if($data[0] == 'Sample NO.') continue;
            $p = CovidPatient::create([
                'identifier' => $data[3],
                'facility_id' => 3475,
                'justification' => $data[8],
            ]);

            $s = CovidSample::create([
                'patient_id' => $p->id,
                'site_entry' => 1,
                'test_type' => $data[9],
                'sample_type' => $data[10],
                'datecollected' => $data[11],
            ]);
        }
    }

    

    public static function busia_study()
    {
        $file = public_path('busia.csv');
        $handle = fopen($file, "r");
        $rows = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            if($data[0] == 'study_id'){
                $rows[] = $data;
                continue;
            }
            $national_id = rtrim($data[4]);
            $identifier = rtrim($data[1]);
            $phone_no = rtrim($data[5]);
            /*$p = CovidPatient::where('national_id', $national_id)->first();
            if(!$p){
                $data[8] = 'Patient Not Found';
                $rows[] = $data;
                continue;
            }*/
            $datecollected = date('Y-m-d', strtotime($data[7]));

            $sample = CovidSampleView::where(['repeatt' => 0])
                ->where('national_id', 'like', "%{$national_id}%")
                ->whereBetween('datecollected', [date('Y-m-d', strtotime($datecollected . ' -3days')), date('Y-m-d', strtotime($datecollected . ' +3days'))])
                ->first();

            if(!$sample){
                $sample = CovidSampleView::where(['repeatt' => 0])
                    ->where('identifier', 'like', "%{$identifier}%")
                    ->whereBetween('datecollected', [date('Y-m-d', strtotime($datecollected . ' -3days')), date('Y-m-d', strtotime($datecollected . ' +3days'))])
                    ->first();
            }

            if(!$sample){
                $sample = CovidSampleView::where(['repeatt' => 0])
                    ->where('phone_no', 'like', "%{$phone_no}%")
                    ->whereBetween('datecollected', [date('Y-m-d', strtotime($datecollected . ' -3days')), date('Y-m-d', strtotime($datecollected . ' +3days'))])
                    ->first();
            }


            if(!$sample){
                $data[8] = 'Sample Not Found';
                $rows[] = $data;
                continue;
            }
            $data[8] = $sample->result_name;
            $rows[] = $data;
        }
        $file = 'busia-study';
        Common::csv_download($rows, $file, false, true);
        Mail::to(['joel.kithinji@dataposit.co.ke'])->send(new TestMail([storage_path("exports/" . $file . ".csv")]));
    }

    public static function bungoma_samples()
    {
        $file = public_path('bungoma_dec.csv');
        $handle = fopen($file, "r");
        $identifiers = $national_ids = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            if($data[3] == 'NAME') continue;

            if($data[2]) $identifiers[] = $data[2];
            if($data[5]) $national_ids[] = $data[5];
        }

        $samples_list = CovidSampleView::where(function($query) use($identifiers, $national_ids) {
            return $query->whereIn('national_id', $national_ids)->orWhereIn('identifier', $identifiers);
        })->where(['repeatt' => 0])->where('datetested', '>', '2020-12-01')->get();

        $samples = CovidSample::whereIn('id', $samples_list->pluck('id')->toArray())->get();
        $path = storage_path('app/batches/covid/bungoma_samples.pdf');

        $mpdf = new Mpdf();
        $data = Lookup::covid_form();
        $data['samples'] = $samples;
        $view_data = view('exports.mpdf_covid_samples', $data)->render();
        ini_set("pcre.backtrack_limit", "500000000");
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($path, \Mpdf\Output\Destination::FILE);

        Mail::to(['joel.kithinji@dataposit.co.ke'])->send(new TestMail([$path]));

    }


    public static function knh_switch_list()
    {
        ini_set('memory_limit', '-1');
        $file = public_path('knh_switch_list.csv');
        $handle = fopen($file, "r");
        $rows = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            if($data[0] == 'Index'){
                $rows[] = $data;
                continue;
            }

            $patient = Viralpatient::where(['patient' => $data[3]])->first();

            if(!$patient){
                $patient = Viralpatient::where(['patient' => $data[1]])->first();
                if(!$patient && $data[2]) $patient = Viralpatient::where(['patient' => $data[2]])->first();
                if(!$patient){
                    $data[4] = 'Patient Not Found';
                    continue;
                }
                $patient->patient = $data[3];
                $patient->pre_update();
            }

            $data[4] = $patient->id;

            $other_patients_array = [$data[1]];
            if($data[2]) $other_patients_array[] = [$data[2]];

            $other_patients = Viralpatient::whereIn('patient', $other_patients_array)->get();

            $data[5] = json_encode($other_patients->pluck('id')->toArray());
            $other_samples = Viralsample::whereIn('patient_id', $other_patients->pluck('id')->toArray())->get();

            foreach ($other_samples as $key => $other_sample) {
                $other_sample->patient_id = $patient->id;
                $other_sample->pre_update();
            }

            $rows[] = $data;

            if(($data[0] % 20) == 0) echo "At row {$data[0]} at ".date('Y-m-d H:i:s')." \n";
        }
        $file = 'knh-switch';
        Common::csv_download($rows, $file, false, true);
        // Mail::to(['joelkith@gmail.com'])->send(new TestMail([storage_path("exports/" . $file . ".csv")]));
        Mail::to(['joel.kithinji@dataposit.co.ke'])->send(new TestMail([storage_path("exports/" . $file . ".csv")]));
    }

    public static function knh_update_ccc()
    {
        ini_set('memory_limit', '-1');
        $file = public_path('knh_ccc_list.csv');
        $handle = fopen($file, "r");
        $rows = [];
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
        {
            if($data[0] == 'Patient CCC No'){
                $rows[] = $data;
                continue;
            }

            $patients = Viralpatient::where(['patient' => $data[0]])->get();

            if($patients->count() == 1){
                $patient = $patients->first();
                $patient->patient = $data[1];
                $patient->pre_update();
                continue;
            }
            /*else if(!$patients->count()){
                $data[2] = 'Patient not found.';
            }*/
            else if($patients->count() > 1){
                $patient = $patients->where('facility_id', 3475)->first();
                $patient->patient = $data[1];
                $patient->pre_update();

                /*$other_patient = $patients->where('facility_id', 50001)->first();

                foreach ($other_patient->sample as $key => $other_sample) {
                    $other_sample->patient_id = $patient->id;
                    $other_sample->pre_update();
                }*/


                // $data[2] = $patients->count() . ' patients were found.';
            }

            $rows[] = $data;

        }
        if($rows){
            $file = 'knh-ccc-list';
            Common::csv_download($rows, $file, false, true);
            // Mail::to(['joelkith@gmail.com'])->send(new TestMail([storage_path("exports/" . $file . ".csv")]));
            Mail::to(['joel.kithinji@dataposit.co.ke'])->send(new TestMail([storage_path("exports/" . $file . ".csv")]));
        }
    }

    public static function old_id_column()
    {
        DB::statement('ALTER TABLE samples ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE viralsamples ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE patients ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE viralpatients ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE viralbatches ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE batches ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE worksheets ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE viralworksheets ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE abbotdeliveries ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE abbotprocurements ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE taqmandeliveries ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE taqmanprocurements ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE lab_equipment_trackers ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE lab_performance_trackers ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
        DB::statement('ALTER TABLE mothers ADD COLUMN `old_id` INT(10) DEFAULT NULL after `id`;');
    }

    public static function covid_consumption_allowed()
    {
        DB::statement("ALTER TABLE `users` ADD COLUMN `covid_consumption_allowed` TINYINT NOT NULL DEFAULT '0' AFTER `last_access`");

    }

    public static function standardizeCCCNo()
    {
        $c = new \App\Imports\StandardizeCCCNumbersImport();
        Excel::import($c, public_path('TujengeJamiiStandardizedCCCNumbersampath.csv'));
        return true;
    }

    public static function delete_by_worksheet($id)
    {
        $worksheet = Viralworksheet::find($id);
        $samples = $worksheet->sample;
        $batches_ids = $samples->pluck('batch_id');
        $batches_to_be_deleted = Viralbatch::whereIn('id', $batches_ids)->get();
        foreach ($batches_to_be_deleted as $key => $batch) {
            $batch->delete();
        }
        foreach ($samples as $key => $sample) {
            $sample->delete();
        }
        $worksheet->delete();
        return true;
    }


	/*
	Function fixes a mistaken nullification of all HPV samples on 29 Nov 2021
	public static function mess()
	{
		$path = "app/public/results/hpv/abbott";
		$files = scandir(storage_path($path));
		
		echo "*********************** Starting imports ***********************\n";
		foreach ($files as $filekey => $file) {
			if (!($file == "." || $file == ".." || str_contains($file, '.xml'))) {
				echo "==> Importing file {$file}" . date('H:i:s') . " \n";
				$import_class = new \App\Imports\TempImport();
				Excel::import($import_class, storage_path("{$path}/{$file}"));
				echo "==> Finished importing file {$file} at " . date('H:i:s') . " \n";
			}
		}
		echo "*********************** Finished imports ***********************\n";

		return true;
	}*/
}
