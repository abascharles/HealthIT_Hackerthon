<?php

namespace App\Http\Controllers;

use App\CancerPatient;
use App\CancerSample;
use App\CancerSampleView;
use App\Lookup;
use App\ViewFacility;
use DB;
use Illuminate\Http\Request;

class CancerSampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($param=null)
    {
        $user = auth()->user();
        $facility_user = false;
        if ($user->facility_id)
            $facility_user = true;
        
        $samples = CancerSampleView::with(['facility', 'worksheet', 'user' => function($query) use ($facility_user) {
                        $query->when(!$facility_user, function($query) {
                                return $query->whereNotIn('users.user_type_id', [5]);
                        });
                    }])->when($facility_user, function($query) use ($user) {
                        return $query->whereRaw("(facility_id = {$user->facility_id} or user_id = {$user->id})");
                    })->when($param, function($query) use ($param, $facility_user){
                        if ($param == 1) {
                            return $query->whereNull('result')
                                ->when($facility_user, function($query){
                                    return $query->where('site_entry', 2);
                                });
                        } else {
                            return $query->whereNotNull('datedispatched')->orderBy('datedispatched', 'DESC');
                        }
                    })->orderBy('created_at', 'DESC')->paginate();
        
        $data['samples'] = $samples;
        $data['param'] = $param;
        $data['facility_user'] = $facility_user;
        
        return view('tables.cancer_samples', $data)->with('pageTitle', 'HPV Samples');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = Lookup::cancersample_form();
        return view('forms.cancersamples', $data)->with('pageTitle', 'Add Cervical Cancer Sample');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $submit_type = $request->input('submit_type');
        $user = auth()->user();
        $patient_string = $request->input('patient');
        
        DB::beginTransaction();
        try {
            $cancerpatient = CancerPatient::existing($request->input('facility_id'), $patient_string)->first();
            if(!$cancerpatient) 
                $cancerpatient = new CancerPatient;

            $data = $request->only(['facility_id', 'patient', 'patient_name',
                                'dob', 'entry_point', 'hiv_status']);
            if(!$data['dob'])
                $data['dob'] = Lookup::calculate_dob($request->input('datecollected'), $request->input('age'), 0);
            $cancerpatient->fill($data);
            $cancerpatient->sex = 2;
            $cancerpatient->patient = $patient_string;
            $cancerpatient->pre_update();

            $data = $request->only(['facility_id', 'sampletype', 'datecollected', 'justification', 'datereceived', 'receivedstatus', 'rejectedreason', 'age']);
            if (!isset($data['age'])){
                $diff = abs(strtotime($request->input('datecollected')) - strtotime($request->input('dob')));
                $data['age'] = floor($diff / (365*60*60*24));
            }
            
            $cancersample = new CancerSample;
            $cancersample->fill($data);
            $cancersample->sample_type = $data['sampletype'];
            if (auth()->user()->user_type_id != 5){
                $cancersample->site_entry = 0;
                $cancersample->lab_id = env('APP_LAB');
            }
            
            $cancersample->patient_id = $cancerpatient->id;
            $cancersample->user_id = $user->id;
            if ($cancersample->receivedstatus == 2)
                $cancersample->datedispatched = date('Y-m-d');
            
            $cancersample->setTATs();

            DB::commit();
            if ($submit_type == 'add') {
                session(['toast_message' => "Cervical Cancer Sample added successfully."]);
                return back();
            } else if ($submit_type == 'release') {
                session(['toast_message' => "Cervical Cancer Sample added successfully."]);
                return redirect('cancersample');
            }
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
            // session(['toast_error' => true, 'toast_message' => "An error occured while saving cervical cancer sample. {$e}"]);
            // return back();
        }
    }

    public function save_result(Request $request, CancerSample $sample)
    {
        $data = $request->only(["approvedby", "approvedby2", "dateapproved", "dateapproved2",
        "datemodified", "datetested", "lab_id", "result", "action"]);
        $sample->fill($data);
        $sample->datedispatched = $data['datetested'];
        $sample->dateapproved = $data['datetested'];
        $sample->dateapproved2 = $data['datetested'];
        $sample->pre_update();
        $sample->setTATs();

        session(['toast_message' => 'Cancer Result sample updated successfully']);
        return redirect('cancersample');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $sample = CancerSampleView::find($id);
        $s = CancerSample::find($id);
        $samples = CancerSample::runs($s)->get();
        $patient = $s->patient;

        $data = Lookup::cancer_lookups();
        $data['sample'] = $sample;
        $data['samples'] = $samples;
        $data['patient'] = $patient;
        
        return view('tables.cancersample_search', $data)->with('pageTitle', 'Cancer Sample Summary');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Lookup::cancersample_form();
        $data['sample'] = CancerSample::find($id);
        return view('forms.cancersamples', $data)->with('pageTitle', 'Edit Cervical Cancer Sample');
    }

    /**
     * Show the form for updating the specified resource.
     *
     * @param  CancerSample  $sample
     * @return \Illuminate\Http\Response
     */
    public function edit_result(CancerSample $sample)
    {
        $sample->load(['patient', 'facility']);
        $data = Lookup::cancer_lookups();
        $data['sample'] = $sample;
        return view('forms.cancer_result', $data)->with('pageTitle', 'Edit Cancer Result');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $cancersample = CancerSample::find($id);
            $cancerpatient = $cancersample->patient;
            $patient_string = $request->input('patient');
            
            if($cancerpatient->patient != $request->input('patient')){
                $cancerpatient = CancerPatient::existing($request->input('facility_id'), $request->input('patient'))->first();
                $different_patient = true;

                if(!$cancerpatient){
                    $cancerpatient = new CancerPatient;
                    $created_patient = true;
                }
            }
            $patient_update_data = $request->only(['facility_id', 'patient', 'patient_name',
                                'dob', 'sex', 'entry_point', 'hiv_status']);

            if(!$patient_update_data['dob'])
                $patient_update_data['dob'] = Lookup::calculate_dob($request->input('datecollected'), $request->input('age'), 0);
            $cancerpatient->fill($patient_update_data);
            $cancerpatient->patient = $patient_string;
            $cancerpatient->pre_update();

            $data = $request->only(['facility_id', 'sampletype', 'datecollected', 'justification', 'datereceived', 'receivedstatus', 'rejectedreason', 'age']);
            if (!isset($data['age'])){
                $diff = abs(strtotime($request->input('datecollected')) - strtotime($request->input('dob')));
                $data['age'] = floor($diff / (365*60*60*24));
            }
            
            $cancersample->fill($data);
            $cancersample->sample_type = $cancersample->sampletype;
            unset($cancersample->sampletype);
            $cancersample->patient_id = $cancerpatient->id;
            $cancersample->save();
            $cancersample->setTATs();

            DB::commit();
            session(['toast_message' => "Cervical Cancer Sample updated successfully."]);
            return redirect('cancersample');
        } catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($sample)
    {
        $sample = CancerSample::find($sample);
        // dd($sample);
        if($sample->result == NULL && $sample->run < 2 && $sample->worksheet_id == NULL && !$sample->has_rerun){
            $sample->delete();
            session(['toast_message' => 'The sample has been deleted.']);
        }  
        else{
            session(['toast_message' => 'The sample has not been deleted.']);
            session(['toast_error' => 1]);
        }      
        return back();
    }

    /**
     * Print the specified resource.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function print(CancerSample $sample)
    {
        $data = Lookup::cancer_lookups();
        $sample->load(['patient', 'facility']);
        $data['samples'] = [$sample];
        // dd($data);
        return view('exports.mpdf_cancersamples', $data)->with('pageTitle', 'Individual Samples');
    }

    public function sample_dispatch()
    {
        return $this->get_rows();
    }

    public function get_rows($sample_list=NULL)
    {
        ini_set('memory_limit', '-1');
        
        $batches = CancerSampleView::select('batches.*', 'facility_contacts.email', 'facilitys.name')
            ->leftJoin('facilitys', 'facilitys.id', '=', 'batches.facility_id')
            ->leftJoin('facility_contacts', 'facilitys.id', '=', 'facility_contacts.facility_id')
            ->when($sample_list, function($query) use ($batch_list){
                return $query->whereIn('batches.id', $batch_list);
            })
            ->where('batch_complete', 2)
            ->where('lab_id', env('APP_LAB'))
            ->when((env('APP_LAB') == 9), function($query){
                return $query->limit(10);
            })            
            ->get();

        $batch_ids = $batches->pluck(['id'])->toArray();

        $subtotals = Misc::get_subtotals($batch_ids);
        $rejected = Misc::get_rejected($batch_ids);
        $date_modified = Misc::get_maxdatemodified($batch_ids);
        $date_tested = Misc::get_maxdatetested($batch_ids);

        $batches->transform(function($batch, $key) use ($subtotals, $rejected, $date_modified, $date_tested){
            $neg = $subtotals->where('batch_id', $batch->id)->where('result', 1)->first()->totals ?? 0;
            $pos = $subtotals->where('batch_id', $batch->id)->where('result', 2)->first()->totals ?? 0;
            $failed = $subtotals->where('batch_id', $batch->id)->where('result', 3)->first()->totals ?? 0;
            $redraw = $subtotals->where('batch_id', $batch->id)->where('result', 5)->first()->totals ?? 0;
            $noresult = $subtotals->where('batch_id', $batch->id)->where('result', 0)->first()->totals ?? 0;

            $rej = $rejected->where('batch_id', $batch->id)->first()->totals ?? 0;
            $total = $neg + $pos + $failed + $redraw + $noresult + $rej;

            $dm = $date_modified->where('batch_id', $batch->id)->first()->mydate ?? '';
            $dt = $date_tested->where('batch_id', $batch->id)->first()->mydate ?? '';

            $batch->negatives = $neg;
            $batch->positives = $pos;
            $batch->failed = $failed;
            $batch->redraw = $redraw;
            $batch->noresult = $noresult;
            $batch->rejected = $rej;
            $batch->total = $total;
            $batch->date_modified = $dm;
            $batch->date_tested = $dt;
            return $batch;
        });

        return view('tables.dispatch', ['batches' => $batches, 'pending' => $batches->count(), 'batch_list' => $batch_list, 'pageTitle' => 'Batch Dispatch']);
    }

    public function facility($facility)
    {
        ini_set("memory_limit", "-1");
        $user = auth()->user();
        $facility_user = false;
        if ($user->facility_id)
            $facility_user = true;
        $data['param'] = false;
        $data['samples'] = CancerSampleView::with(['facility', 'worksheet', 'user'])
                            ->where('facility_id', $facility)
                            ->orderBy('created_at', 'DESC')->paginate(20);
                            
        return view('tables.cancer_samples', $data)->with('pageTitle', 'HPV Facility Search Samples');
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        
        $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";

        $samples = CancerSampleView::select('id')
            ->whereRaw("id like '" . $search . "%'")
            ->when($facility_user, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->paginate(10);

        $samples->setPath(url()->current());
        return $samples;
    }
}
