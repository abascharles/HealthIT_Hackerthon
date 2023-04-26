<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Controllers\BaseController;
use App\Api\V1\Requests\EidRequest;
use App\Api\V1\Requests\BlankRequest;
use App\Api\V1\Requests\WeeklyAlertRequest;
use DB;

use App\Lookup;

use App\SampleView;
use App\ViralsampleView;
use App\Cd4SampleView;
use App\CragSampleView;

class FunctionController extends BaseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('jwt:auth', []);
    }

    public function eid(EidRequest $request)
    {
        $code = $request->input('mflCode');
        $facility = Lookup::facility_mfl($code);

    }

    public function data_functions(BlankRequest $request)
    {     
        $test = $request->input('test');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $date_dispatched_start = $request->input('date_dispatched_start');
        $date_dispatched_end = $request->input('date_dispatched_end');
        $patients = $request->input('patient_id');
        $facilities = $request->input('facility_code');
        $orders = $request->input('order_numbers');
        $sample_status = $request->input('sample_status');
        $location = $request->input('location'); 
        $dispatched = $request->input('dispatched');   
        $ids = $request->input('ids');   



        if($test == 1) $class = SampleView::class;
        else if($test == 2) $class = ViralsampleView::class;
        else if($test == 3){
            $class = Cd4SampleView::class;
            if(env('APP_LAB') != 5) return $this->response->errorBadRequest("This lab does not do CD4 tests.");
        }else{
            return $this->response->errorBadRequest("The selected test is not valid.");
        }
        // else if($test == 4) $class = CragSampleView::class;

        if($patients){
            $patients = str_replace(' ', '', $patients);
            $patients = explode(',', $patients);
        }
        if($orders){
            $orders = str_replace(' ', '', $orders);
            $orders = explode(',', $orders);
        } 
        if($ids){
            $ids = str_replace(' ', '', $ids);
            $ids = explode(',', $ids);
        }
        if($facilities){
            $facilities = str_replace(' ', '', $facilities);
            $facilities = explode(',', $facilities);
            if(sizeof($facilities) > 10) $facilities = [];
        }
 
        $result = $class::when(($facilities), function($query) use($facilities){
                return $query->whereIn('facilitycode', $facilities);
            })
            ->when($dispatched, function($query){
                // return $query->whereNotNull('datedispatched');
                return $query->whereRaw("(datedispatched is not null OR (dateapproved is not null and dateapproved2 is not null))");
            })
            ->when(($sample_status && in_array($test, [3, 4])), function($query) use($sample_status){
                return $query->where('status_id', $sample_status);
            })
            ->when($patients, function($query) use($patients, $test){
                if($test == 3) return $query->whereIn('medicalrecordno', $patients);
                if($test == 4) return $query->whereIn('patient_number', $patients);
                return $query->whereIn('patient', $patients);
            })
            ->when($orders, function($query) use($orders){
                return $query->whereIn('order_no', $orders);
            })
            ->when($ids, function($query) use($ids){
                return $query->whereIn('id', $ids);
            })
            ->when($location, function($query) use($location){
                return $query->where('amrs_location', $location);
            })
            ->when(($start_date && $end_date), function($query) use($start_date, $end_date){
                return $query->whereBetween('datecollected', [$start_date, $end_date]);
            })
            ->when(($date_dispatched_start && $date_dispatched_end), function($query) use($date_dispatched_start, $date_dispatched_end){
                return $query->whereBetween('datedispatched', [$date_dispatched_start, $date_dispatched_end]);
            })
            ->when(true, function($query) use ($test){
                if($test < 3) return $query->where(['repeatt' => 0]);
            })            
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        $result->transform(function ($sample, $key) use ($test){
            // return ['patient age' => $item->age];

            if($sample->receivedstatus == 2){
                $sample->sample_status = "Rejected";
            }
            else{
                if($test == 1){
                    if(($sample->approvedby || $sample->dateapproved) && ($sample->result > 0 || $sample->result) && $sample->repeatt == 0){
                        $sample->sample_status = "Complete";
                    }
                    else{
                        $sample->sample_status = "Incomplete";
                    }
                }
                else if($test == 2){
                    if(($sample->approvedby || $sample->dateapproved) && ($sample->result > 0 || $sample->result) && $sample->repeatt == 0){
                        $sample->sample_status = "Complete";
                    }
                    else{
                        $sample->sample_status = "Incomplete";
                    }
                }
                else if($test == 3) $sample->sample_status = Lookup::get_cd4_status($sample->status_id);
            }
            $approved = false;
            if($sample->approvedby) $approved = true;

            $r = [
                'id' => $sample->id,
                'order_number' => $sample->order_no,                
                'provider_identifier' => $sample->provider_identifier,
                'facility_code' => $sample->facilitycode,
                'AMRs_location' => Lookup::get_mrslocation($sample->amrs_location),
                'full_names' => $sample->patient_name,
                'date_collected' => $sample->datecollected,
                'date_received' => $sample->datereceived,
                'date_tested' => $sample->datetested,
                'interpretation' => $sample->interpretation,
                'result' => $sample->result,
                'date_dispatched' => $sample->datedispatched,
                'sample_status' => $sample->sample_status,
            ];

            if($test == 1) $r['result'] = Lookup::get_result($sample->result);
            if($test == 2){
                $r['result_log'] = null;
                if(is_numeric($sample->result)) {
                    $r['result_log'] = round(log10($sample->result), 1);                
                }
            }

            if($sample->receivedstatus == 2){
                $r['rejected_reason'] = Lookup::get_rejected_reason($test, $sample->rejectedreason);
            }

            if($sample->patient) $r = array_merge($r, ['patient' => $sample->patient]);
            // if($sample->THelperSuppressorRatio){
            if($test == 3){
                $r = array_merge($r, [
                    'THelperSuppressorRatio' => $sample->THelperSuppressorRatio,
                    'AVGCD3percentLymph' => $sample->AVGCD3percentLymph,
                    'AVGCD3AbsCnt' => $sample->AVGCD3AbsCnt,
                    'AVGCD3CD4percentLymph' => $sample->AVGCD3CD4percentLymph,
                    'AVGCD3CD4AbsCnt' => $sample->AVGCD3CD4AbsCnt,
                    'AVGCD3CD8percentLymph' => $sample->AVGCD3CD8percentLymph,
                    'AVGCD3CD8AbsCnt' => $sample->AVGCD3CD8AbsCnt,
                    'AVGCD3CD4CD8percentLymph' => $sample->AVGCD3CD4CD8percentLymph,
                    'AVGCD3CD4CD8AbsCnt' => $sample->AVGCD3CD4CD8AbsCnt,
                    'CD45AbsCnt' => $sample->CD45AbsCnt,
                ]);
            }
            return $r;
        });

        return $result;
    }

    public function weekly_alerts(WeeklyAlertRequest $request)
    {     
        $lab = $request->input('lab', env('APP_LAB'));

        $w = \App\WeeklyAlert::where(['lab_id' => $lab])
            // ->whereBetween('end_date', [date('Y-m-d'), date('Y-m-d', strtotime('+2 days'))])
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();

        if(!$w) return ['message' => 'Send Later'];

        $w->fill($request->except(['lab']));
        $w->save();

        return $w;
    }

    

    /*public function api(BlankRequest $request)
    {
        $test = $request->input('test');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $date_dispatched_start = $request->input('date_dispatched_start');
        $date_dispatched_end = $request->input('date_dispatched_end');
        $patients = $request->input('patient_id');
        $facility = $request->input('facility_code');
        $orders = $request->input('order_numbers');
        $location = $request->input('location');

        $batch_array = ['b.national_batch_id', 'b.datereceived', 'b.datedispatched', 'f.facilitycode', 'p.national_patient_id', 'p.patient', 'patient_name'];
 
        if($test == 1){
            $table_name = 'samples AS s';
            $batch_name = 'batches AS b';
            $patient_name = 'patients AS p';
            $select_array = array_merge(['s.*'], $batch_array);
        } 
        if($test == 2){
            $table_name = 'viralsamples AS s';
            $batch_name = 'viralbatches AS b';
            $patient_name = 'viralpatients AS p';
            $select_array = array_merge(['s.*'], $batch_array);
        } 

        if($patients){
            $patients = str_replace(' ', '', $patients);
            $patients = explode(',', $patients);
        }
        if($orders){
            $orders = str_replace(' ', '', $orders);
            $orders = explode(',', $orders);
        } 
 
        $result = DB::table($table_name)
            ->select($select_array)
            ->join($batch_name, 'b.id', '=', 's.batch_id')
            ->join($patient_name, 's.patient_id', '=', 'p.id')
            ->leftJoin('facilitys AS f', 'f.id', '=', 'b.facility_id')
            ->when($facility, function($query) use($facility){
                return $query->where('f.facilitycode', $facility);
            })
            ->when($patients, function($query) use($patients){
                return $query->whereIn('p.patient', $patients);
            })
            ->when($orders, function($query) use($orders){
                return $query->whereIn('s.order_no', $orders);
            })
            ->when($location, function($query) use($location){
                return $query->where('s.amrs_location', $location);
            })
            ->when(($start_date && $end_date), function($query) use($start_date, $end_date){
                return $query->whereBetween('s.datecollected', [$start_date, $end_date]);
            })
            ->when(($date_dispatched_start && $date_dispatched_end), function($query) use($date_dispatched_start, $date_dispatched_end){
                return $query->whereBetween('b.datedispatched', [$date_dispatched_start, $date_dispatched_end]);
            })
            ->paginate(20);


        $result->transform(function ($sample, $key) use ($test){
            // return ['patient age' => $item->age];

            if($sample->receivedstatus == 2){
                $sample->sample_status = "Rejected";
            }
            else{
                if($test == 1){
                    if($sample->approvedby && ($sample->result > 0 || $sample->result) && $sample->repeatt == 0){
                        $sample->sample_status = "Complete";
                    }
                    else{
                        $sample->sample_status = "Incomplete";
                    }
                }
                if($test == 2){
                    if($sample->approvedby && ($sample->result > 0 || $sample->result) && $sample->repeatt == 0){
                        $sample->sample_status = "Complete";
                    }
                    else{
                        $sample->sample_status = "Incomplete";
                    }
                }
            }
            $approved = false;
            if($sample->approvedby) $approved = true;

            return [
                'id' => $sample->id,
                'order_number' => $sample->order_no,
                'patient' => $sample->patient,
                'provider_identifier' => $sample->provider_identifier,
                'facility_code' => $sample->facilitycode,
                'AMRs_location' => $sample->amrs_location,
                'full_names' => $sample->patient_name,
                'date_collected' => Lookup::my_date_format($sample->datecollected),
                'date_received' => Lookup::my_date_format($sample->datereceived),
                'date_tested' => Lookup::my_date_format($sample->datetested),
                'interpretation' => $sample->interpretation,
                'result' => $sample->result,
                'date_dispatched' => Lookup::my_date_format($sample->datedispatched),
                'sample_status' => $sample->sample_status
            ];
        });

        return $result; 
    }*/



}
