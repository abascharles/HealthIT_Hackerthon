<?php

namespace App\Http\Controllers;

use App\DrSample;
use App\DrSampleView;
use App\DrPatient;
use App\Viralpatient;
use App\User;
use App\Lookup;
use App\MiscDr;

use DB;
use Excel;
use Mpdf\Mpdf;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\DrugResistance;

// use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DrSusceptabilityExport;
use App\Exports\DrDetailedReportExport;



class DrReportController extends Controller
{
/*
	public function date_filter($request, $column)
	{
		return function($query) use($request, $column){
			if($request->input('specificDate')){
				return $query->where($column, $request->input('specificDate'));
			}
			else if (null !== $request->input('period') || $request->input('fromDate')){
				if ($request->input('period') == 'range' || $request->input('fromDate')){
					return $query->whereBetween($column, [$request->input('fromDate'), $request->input('toDate')]);
				}
				else if ($request->input('period') == 'monthly'){
					$date_range = \App\Lookup::date_range_month($request->input('year'), $request->input('month'));
					return $query->whereBetween($column, $date_range);
				}
				else if ($request->input('period') == 'quarterly'){
					$quarters = [[], [1, 3], [4, 6], [7, 9], [10, 12]];
					$year = $request->input('year');
					$q = $quarters[$request->input('quarter')];
					$date_range = \App\Lookup::get_date_range($year, $q[0], $year, $q[1]);
					return $query->whereBetween($column, $date_range);
				}
				else if ($request->input('period') == 'annually'){
					return $query->whereBetween($column, \App\Lookup::date_range_month($request->input('year')));
				}
			}
		};
	}

	public function divisions_filter($request)
	{
		$param = $column = null;
        if ($request->input('category') == 'county') {
            $param = $request->input('county');
            $column = 'view_facilitys.county_id';
        } else if ($request->input('category') == 'subcounty') {
            $param = $request->input('district');
            $column = 'view_facilitys.subcounty_id';
        } else if ($request->input('category') == 'facility') {
            $param = $request->input('facility');
            $column = 'view_facilitys.id';
        } else if ($request->input('category') == 'partner') {
            $param = $request->input('partner');
            $column = 'view_facilitys.partner_id';
        }

		return function($query) use($param, $column){
			if(!$param) return null;
			if(is_array($param)) return $query->whereIn($column, $param);
			return $query->where($column, $param);			
		};
	}
*/
	public function reports(Request $request)
	{
		// return $this->susceptability($request);
        $type = $request->input('types');

        if($type == 'susceptibility'){
            return new DrSusceptabilityExport($request);
        }
        else if($type == 'detailed'){
            return new DrDetailedReportExport($request);
        }

	}

    public function susceptability($request)
    {
        $call_array = MiscDr::$call_array;
        $regimen_classes = DB::table('regimen_classes')->get();
        $date_column = "datedispatched";
        $user = auth()->user();
        $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}')";

        $samples = DrSample::select('dr_samples.*')
            ->where(['status_id' => 1, 'control' => 0, 'repeatt' => 0])
            ->leftJoin('viralpatients', 'dr_samples.patient_id', '=', 'viralpatients.id')
            ->leftJoin('view_facilitys', 'viralpatients.facility_id', '=', 'view_facilitys.id')
            ->with(['dr_call.call_drug', 'patient'])
            ->when(($user->user_type_id == 5), function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->when(true, $this->date_filter($request, $date_column))
            ->when(true, $this->divisions_filter($request))
            ->get();

        $top = ['', 'Drug Classes', ];
        $second = ['Sequence ID', 'Original Sample ID', ];

        foreach ($regimen_classes as $key => $value) {
            $top[] = $value->drug_class;
            $second[] = $value->short_name;
        }

        $rows[0] = $top;
        $rows[1] = $second;

        foreach ($samples as $sample_key => $sample) {
            $patient_string = $sample->patient->patient ?? '';
            $row = [$sample->id, $patient_string];

            foreach ($regimen_classes as  $regimen_key => $regimen) {
                $call = '';

                foreach ($sample->dr_call as $dr_call) {
                    foreach ($dr_call->call_drug as $call_drug) {
                        if($call_drug->short_name_id == $regimen->id){
                            $call = $call_drug->call;
                            $call_array[$call]['cells'][] = chr(64 + 3 + $regimen_key) . ($sample_key + 4);
                            
                            // $beginning = '';

                            // $char_key = $regimen_key + 3;
                            // if($char_key > 26){
                            //     $a = (int) ($char_key / 26);
                            //     $beginning = chr(64 + $a);
                            //     $char_key = $char_key % 26;
                            // }

                            // $call_array[$call]['cells'][] = $beginning . chr(64 + $char_key) . ($sample_key + 4);
                        }
                    }
                }
                $row[] = $call;
            }
            $rows[] = $row;
        }

        // dd($rows);
        // dd($call_array);

        Excel::create("susceptability_report", function($excel) use($rows, $call_array) {
            $excel->sheet('Sheetname', function($sheet) use($rows, $call_array) {
                $sheet->fromArray($rows);

                foreach ($call_array as $my_call) {
                    foreach ($my_call['cells'] as $my_cell) {
                        $sheet->cell($my_cell, function($cell) use ($my_call) {
                            $cell->setBackground($my_call['resistance_colour']);
                        });
                    }
                }
            });
        })->download('xlsx');
    }


}
