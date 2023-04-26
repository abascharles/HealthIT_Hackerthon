<?php

namespace App\Http\Controllers;

use App\Sample;
use App\SampleView;
use App\Patient;
use App\Mother;
use App\Batch;
use App\Facility;
use App\Viralpatient;
use App\Lookup;
use App\Misc;

use App\Http\Requests\SampleRequest;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function list_poc($param=null)
    {
        $user = auth()->user();
        $string = "1";
        if($user->user_type_id == 5) 
            $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";
        
        $data = Lookup::get_lookups();

        $samples = SampleView::with(['facility'])
            ->when($param, function($query){
                return $query->whereNull('result')->where(['receivedstatus' => 1]);
            })
            ->whereRaw($string)
            ->where(['site_entry' => 2])
            ->orderBy('id', 'desc')
            ->paginate(50);
        
        $samples->setPath(url()->current());
        $data['samples'] = $samples;
        $data['pre'] = '';
        return view('tables.poc_samples', $data)->with('pageTitle', 'Eid POC Samples');
    }

    public function list_sms()
    {
        $user = auth()->user();
        $string = "1";
        if($user->user_type_id == 5) $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";

        $data = Lookup::get_lookups();
        $samples = SampleView::whereRaw($string)->whereNotNull('time_result_sms_sent')->orderBy('datedispatched', 'desc')->paginate(50);
        $samples->setPath(url()->current());
        $data['samples'] = $samples;
        $data['pre'] = '';
        return view('tables.sms_log', $data)->with('pageTitle', 'Eid Patient SMS Log');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = Lookup::samples_form();
        return view('forms.samples', $data)->with('pageTitle', 'Add Sample');
    }

    public function create_poc()
    {
        $data = Lookup::samples_form();
        $data['poc'] = true;
        return view('forms.samples', $data)->with('pageTitle', 'Add POC Sample');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SampleRequest $request)
    {
        $samples_arrays = Lookup::samples_arrays();
        $submit_type = $request->input('submit_type');
        $user = auth()->user();

        $site_entry = $request->input('site_entry');
        $batch = session('batch');
        if($batch){
            $batch = Batch::find($batch->id);
            if($site_entry && $batch->site_entry != $site_entry) $batch = null;
        }

        if($submit_type == "cancel"){
            if($batch) $batch->premature();
            $this->clear_session();
            if(!$batch) return back();
            session(['toast_message' => "The batch {$batch->id} has been released."]);
            return redirect("batch/{$batch->id}");
        }   

        $data_existing = $request->only(['facility_id', 'patient', 'datecollected']);
        if(!isset($data_existing['facility_id'])){
            session(['toast_message' => "Please set the facility before submitting.", 'toast_error' => 1]);
            return back();   
        }

        $patient_string = trim($request->input('patient'));
        if(env('APP_LAB') == 4){
            $fac = \App\ViewFacility::find($data_existing['facility_id']);
            $str = $fac->facilitycode;
            if($request->input('automatic_slash')) $str .= '/';
            if(!\Str::startsWith($patient_string, $str) && $request->input('automatic_mfl') && $fac->county_id != 47){
                if(\Str::startsWith($patient_string, $fac->facilitycode)){
                    $code = str_after($patient_string, $fac->facilitycode);
                    $patient_string = $str . $code;
                }
                else{
                    $patient_string = $str . $patient_string;
                }
            }
        }

        $data_existing['patient'] = $patient_string;

        $existing = SampleView::existing( $data_existing )->get()->first();
        if($existing && !$request->input('reentry')){
            session(['toast_error' => 1, 'toast_message' => "The sample already exists in batch {$existing->batch_id} and has therefore not been saved again"]);
            return back();            
        }
        
        
        if(!$batch){
            $facility_id = $request->input('facility_id');
            $facility = Facility::find($facility_id);
            session(['facility_name' => $facility->name, 'batch_total' => 0]);

            $batch = Batch::eligible($facility_id, $request->input('datereceived'), $site_entry)->first();

            if(!$batch) $batch = new Batch;
            $batch->user_id = $user->id;
            $batch->lab_id = $user->lab_id;

            if($user->is_lab_user()){
                $batch->received_by = $user->id;
                $batch->site_entry = 0;
                $batch->time_received = date('Y-m-d H:i:s');
            }
            else{
                $batch->site_entry = 1;
            }
        }

        $data = $request->only($samples_arrays['batch']);
        $batch->fill($data);
        $batch->save();
        session(['batch' => $batch]);

        $new_patient = $request->input('new_patient');
        $last_result = $request->input('last_result');
        $mother_last_result = $request->input('mother_last_result');

        $patient = Patient::existing($request->input('facility_id'), $patient_string)->first();
        if(!$patient) $patient = new Patient;
        $data = $request->only($samples_arrays['patient']);
        $patient->fill($data);
        $patient->patient = $patient_string;

        $mother = $patient->mother;
        if(!$mother) $mother = new Mother;
        $data = $request->only($samples_arrays['mother']);
        $mother->mother_dob = Lookup::calculate_dob($request->input('datecollected'), $request->input('mother_age')); 
        $mother->fill($data);

        if(env('APP_LAB') == 4 && !\Str::startsWith($mother->ccc_no, $fac->facilitycode)) $mother->ccc_no = $fac->facilitycode . '/' . $mother->ccc_no;

        $viralpatient = Viralpatient::existing($mother->facility_id, $mother->ccc_no)->first();
        if($viralpatient) $mother->patient_id = $viralpatient->id;

        $mother->pre_update();

        $patient->mother_id = $mother->id;
        $patient->pre_update();


        /*if($new_patient == 0){
            $patient_id = $request->input('patient_id');
            $repeat_test = Sample::where(['patient_id' => $patient_id, 'batch_id' => $batch->id])->first();

            if($repeat_test){
                session(['toast_message' => 'The sample already exists in the batch and has therefore not been saved again']);
                session(['toast_error' => 1]);
                return redirect()->route('sample.create');
            }

            $patient = Patient::find($patient_id);
            if(!$patient) $patient = Patient::existing($request->input('facility_id'), $request->input('patient'))->first();
            if(!$patient) $patient = new Patient;
            $data = $request->only($samples_arrays['patient']);
            $patient->fill($data);

            $data = $request->only($samples_arrays['mother']);
            $mother = Mother::find($patient->mother_id);
            if(!$mother) $mother = new Mother;
            $mother->mother_dob = Lookup::calculate_dob($request->input('datecollected'), $request->input('mother_age')); 
            $mother->fill($data);

            $viralpatient = Viralpatient::existing($mother->facility_id, $mother->ccc_no)->get()->first();
            if($viralpatient) $mother->patient_id = $viralpatient->id;

            $mother->pre_update();

            $patient->mother_id = $mother->id;
            $patient->pre_update();
        }

        else{

            $data = $request->only($samples_arrays['mother']);
            $mother = Mother::existing($data['facility_id'], $data['ccc_no'])->get()->first();
            if(!$mother) $mother = new Mother;
            
            $mother->mother_dob = Lookup::calculate_dob($request->input('datecollected'), $request->input('mother_age'));
            $mother->fill($data);

            $viralpatient = Viralpatient::existing($mother->facility_id, $mother->ccc_no)->get()->first();
            if($viralpatient) $mother->patient_id = $viralpatient->id;

            $mother->save();

            $data = $request->only($samples_arrays['patient']);
            $patient = new Patient;
            $patient->fill($data);
            $patient->mother_id = $mother->id;
            $patient->save();
        }*/

        $data = $request->only($samples_arrays['sample']);
        $sample = new Sample;
        $sample->fill($data);
        $sample->batch_id = $batch->id;
        $sample->patient_id = $patient->id;

        if($last_result){
            $sample->mother_last_result = $last_result;
            $sample->mother_last_rcategory = 1;
        }
        else if($mother_last_result){
            $sample->mother_last_result = $mother_last_result;
            $my = new \App\MiscViral;
            $res = $my->set_rcategory($mother_last_result);
            $sample->mother_last_rcategory = $res['rcategory'] ?? null;
        }

        $sample->age = Lookup::calculate_age($request->input('datecollected'), $request->input('dob'));
        $sample->save();

        $sample_count = Sample::where('batch_id', $batch->id)->get()->count();

        session(['toast_message' => "The sample has been created in batch {$batch->id}.", 'batch_total' => $sample_count, 'last_patient' => $patient->patient]);

        $submit_type = $request->input('submit_type');

        if($submit_type == "release" || $batch->site_entry == 2 || $sample_count > 9){
            if($sample_count > 9) $batch->full_batch(); 
            $this->clear_session();
            if($submit_type == "release" || $batch->site_entry == 2) $batch->premature();
            else{
                $batch->full_batch();
                session(['toast_message' => "The batch {$batch->id} is full and no new samples can be added to it."]);
            }
            if($batch->site_entry == 2) return back();
            // Misc::check_batch($batch->id); 

            if($user->is_lab_user()){

                $work_samples = Misc::get_worksheet_samples(2);
                if($work_samples['count'] > 21) session(['toast_message' => 'You now have ' . $work_samples['count'] . ' samples that are eligible for testing.']);

            }

            return redirect("batch/{$batch->id}");
        }

        /*if($sample_count == 10){
            $this->clear_session();
            $batch->full_batch();
            Misc::check_batch($batch->id); 
            return redirect("batch/{$batch->id}");
        }*/

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function show(SampleView $sample)
    {
        // $samples->load(['patient.mother']);
        // $batch->load(['facility', 'receiver', 'creator']);
        // $data = Lookup::get_lookups();
        // $data['batch'] = $batch;
        // $data['samples'] = $samples;

        $s = Sample::find($sample->id);
        $samples = Sample::runs($s)->get();

        $patient = $s->patient; 

        $data = Lookup::get_lookups();
        $data['sample'] = $sample;
        $data['samples'] = $samples;
        $data['patient'] = $patient;
        return view('tables.sample_search', $data)->with('pageTitle', 'Sample Summary');

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function edit(Sample $sample)
    {
        // $sample->load(['patient.mother', 'batch.facility']);
        $data = Lookup::samples_form();
        $data['sample'] = $sample;
        return view('forms.samples', $data)->with('pageTitle', 'Samples');
    }

    /**
     * Show the form for editing the specified resource (poc).
     *
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function edit_poc(Sample $sample)
    {
        $sample->load(['patient', 'batch.facility_lab']);
        if($sample->batch->site_entry != 2) abort(409, 'This sample is not a POC sample.');
        $data = Lookup::get_lookups();
        $data['sample'] = $sample;
        $data['pre'] = '';
        return view('forms.poc_result', $data)->with('pageTitle', 'Edit Result');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sample $sample)
    {
        $submit_type = $request->input('submit_type');
        $user = auth()->user();

        $batch = $sample->batch;

        if($batch->site_entry == 1 && !$sample->receivedstatus && $user->is_lab_user()){
            $sample->sample_received_by = $user->id;
        }

        $samples_arrays = Lookup::samples_arrays();
        $data = $request->only($samples_arrays['sample']);
        $sample->fill($data);

        
        $last_result = $request->input('last_result');
        $mother_last_result = $request->input('mother_last_result');

        // $new_batch = false;

        /*if($submit_type == "new_batch" && $batch->facility_id != $request->input('facility_id')){
            $batch = new Batch;
            $new_batch = true;

            $facility_id = $request->input('facility_id');
            $facility = Facility::find($facility_id);
            session(['facility_name' => $facility->name, 'batch_total' => 0]);

            $batch = new Batch;
            $batch->user_id = $user->id;
            $batch->lab_id = $user->lab_id;

            if($user->user_type_id == 1 || $user->user_type_id == 4){
                $batch->received_by = $user->id;
                $batch->site_entry = 0;
            }
            else{
                $batch->site_entry = 1;
            }
        }*/

        $data = $request->only($samples_arrays['batch']);
        $batch->fill($data);
        if(!$batch->received_by && $user->is_lab_user()){
            $batch->received_by = $user->id;
            $batch->time_received = date('Y-m-d H:i:s');
        }
        $batch->pre_update();

        $patient = $sample->patient;

        if($patient->patient != $request->input('patient')){
            $patient = Patient::existing($request->input('facility_id'), $request->input('patient'))->first();
            $different_patient = true;

            if(!$patient){
                $patient = new Patient;
                $created_patient = true;
            }
        }



        $data = $request->only($samples_arrays['patient']);
        $patient->fill($data);

        if(isset($created_patient)){
            $mother = new Mother;
        }else{
            $mother = $patient->mother;
        }

        $data = $request->only($samples_arrays['mother']);
        $mother->fill($data);

        $viralpatient = Viralpatient::existing($mother->facility_id, $mother->ccc_no)->get()->first();
        if($viralpatient) $mother->patient_id = $viralpatient->id;

        $mother->pre_update();

        $patient->mother_id = $mother->id;
        $patient->pre_update();

        


        // $new_patient = $request->input('new_patient');

        // if($new_patient == 0){
        
        //     $data = $request->only($samples_arrays['patient']);
        //     $patient = Patient::find($sample->patient_id);
        //     $patient->fill($data);
        //     $patient->pre_update();

        //     $data = $request->only($samples_arrays['mother']);
        //     $mother = Mother::find($patient->mother_id);
        //     $mother->mother_dob = Lookup::calculate_dob($request->input('datecollected'), $request->input('mother_age'));
        //     $mother->fill($data);

        //     $viralpatient = Viralpatient::existing($mother->facility_id, $mother->ccc_no)->get()->first();
        //     if($viralpatient) $mother->patient_id = $viralpatient->id;

        //     $mother->pre_update();
        // }
        // else
        // {
        //     $data = $request->only($samples_arrays['mother']);
        //     $mother = new Mother;
        //     $mother->mother_dob = Lookup::calculate_dob($request->input('datecollected'), $request->input('mother_age'));
        //     $mother->fill($data);

        //     $viralpatient = Viralpatient::existing($mother->facility_id, $mother->ccc_no)->get()->first();
        //     if($viralpatient) $mother->patient_id = $viralpatient->id;

        //     $mother->pre_update();
            
        //     $data = $request->only($samples_arrays['patient']);
        //     $patient = new Patient;
        //     $patient->fill($data);
        //     $patient->mother_id = $mother->id;
        //     $patient->pre_update();
        // }
        
        if($last_result){
            $sample->mother_last_result = $last_result;
            $sample->mother_last_rcategory = 1;
        }
        else if($mother_last_result){
            $sample->mother_last_result = $mother_last_result;
            $my = new \App\MiscViral;
            $res = $my->set_rcategory($mother_last_result);
            $sample->mother_last_rcategory = $res['rcategory'];
        }
        
        $sample->age = Lookup::calculate_age($request->input('datecollected'), $request->input('dob'));
        $sample->patient_id = $patient->id; 
        $sample->batch_id = $batch->id; 

        session(['toast_message' => 'The sample has been updated.']);

        if($sample->receivedstatus == 2 && $sample->getOriginal('receivedstatus') == 1 && $sample->worksheet_id){
            /*$worksheet = $sample->worksheet;
            if($worksheet->status_id == 1){
                $d = Misc::get_worksheet_samples($worksheet->machine_type, 1);
                $s = $d['samples']->first();
                if($s){
                    $sample->worksheet_id = null;
                    $replacement = Sample::find($s->id);

                    $replacement->worksheet_id = $worksheet->id;
                    $replacement->save();
                    session(['toast_message' => 'The sample has been rejected and it has been replaced in worksheet ' . $worksheet->id]);
                }
                else{
                    session([
                        'toast_message' => 'The sample has been rejected but no sample could be found to replace it in the worksheet.',
                        'toast_error' => 1
                    ]);
                }
            }
            else{
                session([
                    'toast_message' => 'The worksheet has already been run.',
                    'toast_error' => 1
                ]);
            }*/
            $sample->worksheet_id = null;
            $sample->result = null;
            $sample->interpretation = null;
            $sample->repeatt = 0;
        }

        if($sample->receivedstatus == 1 && $sample->getOriginal('receivedstatus') == 2){
            if($batch->batch_complete == 1) $transfer = true;
            else if($batch->batch_complete == 2){
                $batch->batch_complete = 0;
                $batch->pre_update();
            }            
        }

        $sample->pre_update(); 

        if(isset($transfer)){
            $url = $batch->transfer_samples([$sample->id], 'new_facility', true);
            $sample->refresh();
            $batch = $sample->batch;
            session(['toast_message' => 'The sample has been tranferred to a new batch because the batch it was in has already been dispatched.']);
        }

        if(isset($different_patient)){
            if($sample->run == 1 && $sample->has_rerun){
                $children = $sample->child;

                foreach ($children as $kid) {
                    $kid->patient_id = $patient->id;
                    $kid->pre_update();
                }
            }
            else if($sample->run > 1){
                $parent = $sample->parent;
                $parent->pre_update();
                
                $children = $parent->child;

                foreach ($children as $kid) {
                    $kid->patient_id = $patient->id;
                    $kid->pre_update();
                }
            }
        }

        Misc::check_batch($batch->id);  

        if($sample->receivedstatus == 1 && $user->is_lab_user()){            
            $work_samples = Misc::get_worksheet_samples(2);
            if($work_samples['count'] > 21) session(['toast_message' => 'The sample has been accepted.<br />You now have ' . $work_samples['count'] . ' samples that are eligible for testing.']);
        }

        /*if($new_batch){
            session(['batch' => $batch, 'batch_total' => 1,
                'toast_message' => 'The sample has been saved to batch number ' . $batch->id]);
            return redirect('sample/create');
        } */    

        if($sample->receivedstatus && !$sample->getOriginal('receivedstatus') && $batch->site_entry == 1){
            return redirect('batch/site_approval_group/' . $batch->id);
        }

        $site_entry_approval = session()->pull('site_entry_approval');

        if($site_entry_approval){
            session(['toast_message' => 'The site entry sample has been approved.']);
            return redirect('batch/site_approval/' . $batch->id);
        }

        return redirect('batch/' . $batch->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function save_poc(Request $request, Sample $sample)
    {
        if($sample->result){
            $mintime = strtotime('now -5days');
            if($sample->datemodified && strtotime($sample->datemodified) < $mintime){
                session(['toast_message' => 'The result cannot be changed as it was first updated long ago.', 'toast_error' => 1]);
                return back();
            }
            else if(strtotime($sample->datetested) < $mintime){
                session(['toast_message' => 'The result cannot be changed as it was first updated long ago.', 'toast_error' => 1]);
                return back();
            }
        }

        $sample->fill($request->except(['_token', 'lab_id']));
        $sample->pre_update();
        Misc::check_batch($sample->batch_id);
        Misc::check_worklist(SampleView::class, $sample->worksheet_id);

        $batch = $sample->batch;
        $batch->lab_id = $request->input('lab_id');
        if($batch->batch_complete == 2){
            $batch->datedispatched = date('Y-m-d');
            $batch->batch_complete = 1;
        }
        $batch->pre_update();
        session(['toast_message' => 'The sample has been updated.']);

        return redirect('sample/list_poc');        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sample $sample)
    {
        if($sample->result == NULL && $sample->run < 2 && $sample->worksheet_id == NULL && !$sample->has_rerun){
            $batch = $sample->batch;
            $sample->delete();
            $samples = $batch->sample;
            if($samples->isEmpty()) $batch->delete();
            else{
                Misc::check_batch($batch->id);
            }
            session(['toast_message' => 'The sample has been deleted.']);
        }  
        else{
            session(['toast_message' => 'The sample has not been deleted.']);
            session(['toast_error' => 1]);
        }      
        return back();
    }


    public function new_patient(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $patient = $request->input('patient');

        if(!$facility_id || $facility_id == '') return null;

        if(env('APP_LAB') == 4){
            $fac = Facility::find($facility_id);
            $str = $fac->facilitycode . '/';
            if(!\Str::contains($patient, $str)) $patient = $str . $patient;
        }

        // Add check for in process sample

        $patient = Patient::where(['facility_id' => $facility_id, 'patient' => $patient])->first();

        $data;
        if($patient){
            $patient->most_recent();
            $mother = $patient->mother;
            $mother->calc_age();

            $viralpatient = $mother->viral_patient;

            if($viralpatient){
                $viralpatient->last_test();
                if($viralpatient->recent){
                    if($viralpatient->recent->rcategory == 1) $mother->recent_test = 0;
                    else{
                        $mother->recent_test = $viralpatient->recent->result;
                    }
                }
            }

            $data[0] = 0;
            $data[1] = $patient->toArray();
            $data[2] = $mother->toArray();


            $prev_samples = Sample::where(['patient_id' => $patient->id, 'repeatt' => 0])->orderBy('datetested', 'asc')->get();
            $previous_positive = 0;
            $recommended_pcr = 1;
            $message = null;
            $error_message = null;
            $age = $patient->age;

            if($prev_samples->count() > 0){
                $pos_sample = $prev_samples->where('result', 2)->first();
                if($pos_sample){
                    $previous_positive = 1;
                    $recommended_pcr = 4;

                    $bool = false;
                    foreach ($prev_samples as $key => $sample) {
                        if($sample->result == 2) $bool = true;
                        if($bool && $sample->result == 1) $recommended_pcr = 5;
                    }
                }
                else{
                    if($age < 12){
                        $recommended_pcr = 2;
                    }
                    else{
                        $recommended_pcr = 3;
                    }
                }                
            }

            if($age > 24) $error_message = "The patient is over age.";

            $data[3] = ['previous_positive' => $previous_positive, 'recommended_pcr' => $recommended_pcr, 'message' => $message, 'error_message' => $error_message];

            $data[4] = 0;
            if($patient->most_recent){
                $data[4] = "The date collected for the most recent test of the patient is " . $patient->most_recent->my_date_format('datecollected') . " in batch number " . $patient->most_recent->batch_id;
            }
        }
        else{
            $data[0] = 1;
        }
        return $data;
    }


    public function transfer(Sample $sample)
    {
        $sample->sample_received_by = auth()->user()->id;
        $sample->save();
        session(['toast_message' => "The sample has been tranferred to your account."]);
        return back();
    }

    public function runs(Sample $sample)
    {
        $samples = Sample::runs($sample)->get();

        $patient = $sample->patient; 
        return view('tables.sample_runs', ['patient' => $patient, 'samples' => $samples]); 
    }

    /**
     * Print the specified resource.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function individual(Sample $sample)
    {
        $data = Lookup::get_lookups();
        $sample->load(['patient.mother', 'approver', 'batch.lab', 'batch.facility', 'batch.receiver', 'batch.creator']);
        $data['samples'] = [$sample];

        return view('exports.mpdf_samples', $data)->with('pageTitle', 'Individual Sample');
    }

    public function send_sms(SampleView $sample)
    {
        Misc::send_sms($sample);
        session(['toast_message' => 'The sms has been sent.']);
        return back();
    }

    public function return_for_testing(Sample $sample)
    {
        if($sample->result != 5 || $sample->repeatt == 1 || $sample->age_in_months > 3){
            session(['toast_error' => 1, 'toast_message' => 'The sample cannot be returned for testing.']);
            return back();
        }

        $sample->repeatt = 1;
        $sample->save();
        
        $rerun = Misc::save_repeat($sample->id);

        $batch = $sample->batch;

        if($batch->batch_complete == 0){
            session(['toast_message' => 'The sample has been returned for testing.']);
            return back();
        }
        else{
            $batch->transfer_samples([$rerun->id], 'new_facility');
            $rerun->refresh();
            $batch = $rerun->batch;
            // $batch->fill(['batch_complete' => 0, 'datedispatched' => null, 'tat5' => null, 'dateindividualresultprinted' => null, 'datebatchprinted' => null, 'dateemailsent' => null, 'sent_email' => 0]);
            $batch->return_for_testing();
            $batch->save();
            session(['toast_message' => 'The sample has been returned for testing and tranferred to a new batch.']);
            return redirect('batch/' . $batch->id);
        }
    }

    public function release_redraw(Sample $sample)
    {
        $batch = $sample->batch;
        if($sample->run == 1 || $batch->batch_complete != 0 ){
            session(['toast_message' => 'The sample cannot be released as a redraw.']);
            session(['toast_error' => 1]);
            return back();
        } 
        else if($sample->run == 2){
            // $prev_sample = Sample::find($sample->parentid);
            $prev_sample = $sample->parent;
        }
        else{
            $run = $sample->run - 1;
            $prev_sample = Sample::where(['parentid' => $sample->parentid, 'run' => $run])->get()->first();
        }
        
        $sample->delete();

        $prev_sample->labcomment = "Failed Test";
        $prev_sample->repeatt = 0;
        $prev_sample->result = 5;
        $prev_sample->approvedby = auth()->user()->id;
        $prev_sample->approvedby2 = auth()->user()->id;
        $prev_sample->dateapproved = date('Y-m-d');
        $prev_sample->dateapproved2 = date('Y-m-d');

        $prev_sample->save();
        Misc::check_batch($prev_sample->batch_id);
        session(['toast_message' => 'The sample has been released as a redraw.']);
        return back();
    }

    public function release_redraws(Request $request)
    {
        $samples_input = $request->input('samples');
        // DB::table('samples')->whereIn('id', $samples_input)->update(['repeatt' => 0, 'result' => 5, 'approvedby' => auth()->user()->id, 'dateapproved' => date('Y-m-d')]);

        $samples = Sample::whereIn('id', $samples_input)->get();

        foreach ($samples as $key => $sample) {
            $this->release_redraw($sample);
        }
        return back();
    }

    public function unreceive(Sample $sample)
    {
        if($sample->worksheet_id || $sample->run > 1 || $sample->synched){
            session(['toast_error' => 1, 'toast_message' => 'The sample cannot be set to unreceived']);
        }
        else{
            $sample->fill(['sample_received_by' => null, 'receivedstatus' => null, 'rejectedreason' => null]);
            $sample->save();
            session(['toast_message' => 'The sample has been unreceived.']);
        }
        return back();
    }

    public function approve_edarp(Request $request)
    {
        $samples = $request->input('samples');
        $submit_type = $request->input('submit_type');
        $user = auth()->user();

        $batches = Sample::selectRaw("distinct batch_id")->whereIn('id', $samples)->get();

        if($submit_type == "release"){
            Sample::whereIn('id', $samples)->update(['synched' => 0, 'approvedby' => $user->id]);
        }
        else{
            Sample::whereIn('id', $samples)->delete();
        }

        foreach ($batches as $key => $value) {
            Misc::check_batch($value->batch_id);
        } 
        return back();
    }

    public function site_sample_page()
    {
        return view('forms.upload_site_samples', ['url' => 'sample'])->with('pageTitle', 'Upload Facility Samples');
    }

    public function upload_site_samples(Request $request)
    {
        $file = $request->upload->path();
        $path = $request->upload->store('public/site_samples/eid');

        $problem_rows = 0;
        $created_rows = 0;

        $handle = fopen($file, "r");
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE){

            $facility = Facility::locate($row[5])->get()->first();
            if(!$facility) continue;
            $datecollected = Lookup::other_date($row[1]);
            $datereceived = Lookup::other_date($row[20]);
            if(!$datereceived) $datereceived = date('Y-m-d');
            $existing = SampleView::existing(['facility_id' => $facility->id, 'patient' => $row[3], 'datecollected' => $datecollected])->get()->first();

            // if($existing) continue;

            $site_entry = Lookup::get_site_entry($row[19]);

            $batch = Batch::withCount(['sample'])
                                    ->where('received_by', auth()->user()->id)
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
                $batch = new Batch;
                $batch->user_id = $facility->facility_user->id;
                $batch->facility_id = $facility->id;
                $batch->received_by = auth()->user()->id;
                $batch->time_received = date('Y-m-d H:i:s');
                $batch->lab_id = auth()->user()->lab_id;
                $batch->datereceived = $datereceived;
                $batch->site_entry = $site_entry;
                $batch->save();
            }

            $patient = Patient::existing($facility->id, $row[3])->get()->first();
            if(!$patient){
                $patient = new Patient;
                $mother = new Mother;
            }
            else{
                $mother = $patient->mother;
            }
            $dob = Lookup::other_date($row[8]);
            if (!$dob && strlen($row[8]) == 4) $dob = $row[8] . '-01-01';

            $mother->facility_id = $facility->id;
            $mother->ccc_no = $row[15];
            $mother->mother_dob = Lookup::calculate_dob($datecollected, $row[14]);
            $mother->save();

            if($dob) $patient->dob = $dob;            
            $patient->facility_id = $facility->id;
            $patient->mother_id = $mother->id;
            $patient->patient = $row[3];
            $patient->patient_name = $row[2];
            $patient->entry_point = $row[10];
            $patient->sex = $row[9];
            $patient->ccc_no = $row[13];
            $patient->pre_update();

            $sample = new Sample;
            $sample->batch_id = $batch->id;
            $sample->patient_id = $patient->id;
            $sample->datecollected = $datecollected;
            $sample->age = Lookup::calculate_age($datecollected, $patient->dob);
            $sample->redraw = $row[7];
            $sample->pcrtype = $row[6];
            $sample->regimen = Lookup::eid_regimen($row[11]);
            $sample->feeding = $row[12];
            $sample->mother_age = $row[14];
            $sample->mother_prophylaxis = Lookup::eid_intervention($row[16]);
            $sample->mother_last_result = $row[17];

            $my = new \App\MiscViral;
            $res = $my->set_rcategory($sample->mother_last_result);
            $sample->mother_last_rcategory = $res['rcategory'] ?? null;

            $sample->spots = $row[18];

            $sample->receivedstatus = $row[21];
            if(is_numeric($row[22])) $sample->rejectedreason = $row[22];
            $sample->save();
            $created_rows++;
        }
        session(['toast_message' => "{$created_rows} samples have been created."]);
        return redirect('/home');        
    }


    public function transfer_samples_form($facility_id=null)
    {
        $samples = SampleView::whereNull('receivedstatus')
                    ->where('site_entry', '!=', 2)
                    ->when($facility_id, function($query) use($facility_id){
                        return $query->where('facility_id', $facility_id);
                    })
                    ->whereNull('datetested')
                    ->where(['repeatt' => 0])
                    ->where('created_at', '>', date('Y-m-d', strtotime("-3 months")))
                    ->paginate(25);

        $samples->setPath(url()->current());

        if($facility_id) $facility = \App\Facility::find($facility_id);

        $data = [
            'samples' => $samples,
            'labs' => \App\Lab::all(),
            'facility' => $facility ?? null,
            'pre' => '',
        ];

        return view('forms.transfer_samples', $data);
    }

    public function transfer_samples(Request $request)
    {
        $samples = $request->input('samples');
        $lab = $request->input('lab');
        // dd($samples);
        \App\Synch::transfer_sample('eid', $lab, $samples);
        return back();
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        // $string = "(batches.facility_id='{$user->facility_id}' OR batches.user_id='{$user->id}')";
        $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";

        // $samples = Sample::select('samples.id')
        //     ->whereRaw("samples.id like '" . $search . "%'")
        //     ->when($facility_user, function($query) use ($string){
        //         return $query->join('batches', 'samples.batch_id', '=', 'batches.id')->whereRaw($string);
        //     })
        //     ->paginate(10);
        $samples = SampleView::select('id')
            ->whereRaw("id like '" . $search . "%'")
            ->when($facility_user, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->paginate(10);

        $samples->setPath(url()->current());
        return $samples;
    }

    public function ord_no(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        $string = "(facility_id='{$user->facility_id}' OR user_id='{$user->id}')";

        $samples = SampleView::select(['id', 'order_no', 'patient'])
            ->whereRaw("order_no like '%" . $search . "%'")
            ->when($facility_user, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->paginate(10);

        $samples->setPath(url()->current());
        return $samples;
    }

    public function similar(Request $request)
    {
        $facility_id = $request->input('facility_id');
        $patient = $request->input('patient');
        $sex = $request->input('sex');

        $samples = SampleView::where('created_at', '>', date('Y-m-d', strtotime('-2months')))
            ->where(['repeatt' => 0, 'facility_id' => $facility_id, 'sex' => $sex, ])
            ->limit(10);

        return $samples;
    }



    private function clear_session(){
        session()->forget('batch');
        session()->forget('facility_name');
        session()->forget('batch_total');
        session()->forget('last_patient');
        // session()->forget('batch_dispatch');
        // session()->forget('batch_dispatched');
        // session()->forget('batch_received');
        // session()->forget('facility_id');
    }
}
