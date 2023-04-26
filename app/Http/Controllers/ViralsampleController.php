<?php

namespace App\Http\Controllers;

use App\Facility;
use App\Http\Requests\ViralsampleRequest;
use App\Imports\ViralInterLabSampleImport;
use App\Lookup;
use App\Machine;
use App\MiscViral;
use App\User;
use App\Viralbatch;
use App\Viralpatient;
use App\Viralsample;
use App\ViralsampleView;
use Excel;
use DB;
use Illuminate\Http\Request;



class ViralsampleController extends Controller
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

    public function nhrl_samples(Request $request)
    {
        $samples = Viralsample::where('synched', 5)->with(['batch.facility', 'patient'])->get(); 
        $data['samples'] = $samples;
        return view('tables.confirm_viralsamples', $data)->with('pageTitle', 'Confirm Samples');
    }

    public function list_poc($param=null)
    {
        $user = auth()->user();
        $string = "1";
        if($user->user_type_id == 5) $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";
        
        $data = Lookup::get_viral_lookups();

        $samples = ViralsampleView::with(['facility'])
            ->when($param, function($query){
                return $query->whereRaw("((result IS NULL ) OR (result ='0' ))")->where(['receivedstatus' => 1]);
            })
            ->whereRaw($string)
            ->where(['site_entry' => 2])
            ->orderBy('id', 'desc')
            // ->get();
            ->paginate(50);
        
        $samples->setPath(url()->current());
        $data['samples'] = $samples;
        $data['pre'] = 'viral';
        return view('tables.poc_samples', $data)->with('pageTitle', 'VL POC Samples');
    }
    public function list_vl_upi_line_list()
    {
        $user = auth()->user();
        $string = "1";
        if ($user->user_type_id == 5) $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";
        $dt = date('Y-m-d', strtotime('-300 days'));
        $patients = Viralpatient::where('upi_no','!=', null)
            ->join('facilitys', 'viralpatients.facility_id', '=', 'facilitys.id')
            ->get();
//        dd($patients);


        $data['pre'] = 'viral';
        $data['patients'] = $patients;
        return view('tables.vl_upi_verification_line_list', $data)->with('pageTitle', 'VL patients verification list');
    }

    public function list_sms()
    {
        $user = auth()->user();
        $string = "1";
        if($user->user_type_id == 5) $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";
        
        $data = Lookup::get_viral_lookups();
        $samples = ViralsampleView::whereRaw($string)->whereNotNull('time_result_sms_sent')->orderBy('datedispatched', 'desc')->paginate(50);
        $samples->setPath(url()->current());
        $data['samples'] = $samples;
        $data['pre'] = 'viral';
        return view('tables.sms_log', $data)->with('pageTitle', 'VL Patient SMS Log');
    }

    public function potential_dr()
    {
        $data = Lookup::get_viral_lookups();
        $data['samples'] = ViralsampleView::selectRaw('viralsamples_view.*')
            ->join('view_facilitys', 'viralsamples_view.facility_id', '=', 'view_facilitys.id')
            ->whereNotNull('datedispatched')
            ->where(['repeatt' => 0, 'run' => 1, 'lab_id' => auth()->user()->lab_id])
            ->whereRaw("patient_id NOT IN (SELECT DISTINCT patient_id FROM dr_samples)")
            ->where(function($query){
                return $query->where('result', '>', 5000)
                    ->orWhere(\DB::raw("(county_id IN (16,18) AND justification=2)"));
            })
            ->orderBy('datecollected', 'desc')
            ->paginate(20);
        $data['samples']->setPath(url()->current());

        return view('tables.potential_dr', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($sampletype=false)
    {
        $data = Lookup::viralsample_form();
        $data['form_sample_type'] = $sampletype;
        $data['excelusers'] = User::where('user_type_id', '<>', 5)->get();
        return view('forms.viralsamples', $data)->with('pageTitle', 'Add Sample');
    }

    public function create_poc()
    {
        $data = Lookup::viralsample_form();
        $data['poc'] = true;
        return view('forms.viralsamples', $data)->with('pageTitle', 'Add POC Sample');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function deleteexcelupload(Request $request) {
        if ($request->method() == "GET") {
            return view('forms.viralsamplesexceldelete')->with('pageTitle', 'Add Sample');
        } else {
            $file = $request->excelupload->path();
            $path = $request->excelupload->store('public/samples/otherlab/delete');
            $excelData = Excel::load($file, function($reader){
                $reader->toArray();
            })->get();

            $batches = collect($excelData->toArray())->first();
            Viralsample::whereIn('batch_id', $batches)->delete();
            Viralbatch::whereIn('id', $batches)->delete();
            return back();
        }
    }

    function getPatientDetails(){
        //search db based on ccc
        $ccc = file_get_contents('php://input');

        $patient = DB::table('viralpatients')->where(['patient'=> $ccc])->first();

        if(!empty($patient)){
            $gender = DB::table('gender')->where('id','=', $patient->sex)->first();
            $patient->gender = $gender->gender_description;

            $dob = date('Y', strtotime($patient->dob));
            $date = date('Y');

            $patient->age = $date -$dob;

            $response = array(
                "status"=>'success',
                "data"=>$patient
            );
        }else{
            $response = array(
                "status" => "error"
            );
        }

        return json_encode($response);

        ///return json

    }
    public function getRecPatient()
    {
        $rec = file_get_contents('php://input');
        $recSample = DB::table('viralsamples')->where(['recency_number' => $rec])->first();

        if (!empty($recSample)) {
            $response = array(
                "status" => 'success',
                "data" => $recSample
            );
        } else {
            $response = array(
                "status" => 'error',
            );
        }

        return json_encode($response);
    }

    public function excelupload(Request $request) {
        if ($request->method() == "GET") {
            $data['excelusers'] = User::where('user_type_id', '<>', 5)->get();
            $data['machines'] = Machine::where('id', '<>', 0)->get();
            $data['sampletypes'] = Lookup::get_viral_lookups()["sample_types"];
            return view('forms.viralsamplesexcel', $data)->with('pageTitle', 'Add Sample');
        } else {
            ini_set('memory_limit', '-1');
            $file = $request->excelupload->path();
            $path = $request->excelupload->store('public/samples/otherlab');
            $importclass = new ViralInterLabSampleImport($request);            
            $worksheets = Excel::import($importclass, $path);

            $data = Lookup::worksheet_lookups();
            $data['worksheets'] = session('temp_worksheets');
            
            return view('tables.viralworksheetsexcel', $data);
            
            // return back();
            // $batch = null;
            // $lookups = Lookup::get_viral_lookups();
            // dd($lookups);
            // $excelData = Excel::load($file, function($reader){
            //     $reader->toArray();
            // })->get();
            // $excelsheetvalue = collect($excelData->flatten(1)->values()->all());
            // $dataArray = [];
            // $dataArray = ['Viral Batches'];
            // $countItem = $excelsheetvalue->count();
            // $counter = 0;
            // if (!$excelsheetvalue->isEmpty()){
            //     foreach ($excelsheetvalue as $samplekey => $samplevalue) {
            //         // $counter++;
            //         // $facility = Facility::where('facilitycode', '=', $samplevalue[5])->first();
            //         // // if (!isset($facility)){
            //         // //     $nofacility[] = $samplevalue;
            //         // //     continue;
            //         // // }
            //         // $existing = Viralpatient::existing($facility->id, $samplevalue[3])->first();
                    
            //         // if ($existing)
            //         //     $patient = $existing;
            //         // else {
            //         //     $patient = new Viralpatient();
            //         //     $patient->patient = $samplevalue[3];
            //         //     $patient->facility_id = $facility->id;
            //         //     $patient->sex = $lookups['genders']->where('gender', $samplevalue[6])->first()->id;
            //         //     $patient->dob = $samplevalue[9];
            //         //     // $patient->initiation_date = $samplevalue[14];
            //         //     $patient->save();
            //         // }
                    
            //         if ($counter == 1) {
            //             $batch = new Viralbatch();
            //             $existingSample = ViralsampleView::existing(['facility_id' => $facility->id, 'patient' => $patient->patient, 'datecollected' => $samplevalue[11]])->first();
                        
            //             if ($existingSample)
            //                 continue;
            //             $batch->user_id = $request->input('receivedby');
            //             $batch->lab_id = env('APP_LAB');
            //             $batch->received_by = $request->input('receivedby');
            //             $batch->site_entry = 0;
            //             $batch->entered_by = $request->input('receivedby');
            //             $batch->datereceived = $samplevalue[16];
            //             $batch->facility_id = $facility->id;
            //             $batch->save();
            //         }

            //         $sample = new Viralsample();
            //         $sample->batch_id = $batch->id;
            //         $sample->receivedstatus = $samplevalue[18];
            //         $sample->age = $samplevalue[8];
            //         $sample->patient_id = $patient->id;
            //         $sample->pmtct = $samplevalue[7];
            //         $sample->dateinitiatedonregimen = $samplevalue[14];
            //         $sample->datecollected = $samplevalue[11];
            //         $sample->regimenline = $samplevalue[13];
            //         $sample->prophylaxis = $lookups['prophylaxis']->where('category', $samplevalue[12])->first()->id ?? 15;
            //         $sample->justification = $lookups['justifications']->where('rank', $samplevalue[15])->first()->id ?? 8;
            //         $sample->sampletype = $samplevalue[10];
            //         $sample->save();

            //         $sample_count = $batch->sample->count();

            //         $countItem -= 1;
            //         if($counter == 10) {
            //             $dataArray[] = $batch->id;
            //             $batch->full_batch();
            //             $batch = null;
            //             $counter = 0;
            //         } 

            //         if ($countItem == 1) {
            //             $sample_count = $batch->sample->count();
            //             if ($sample_count != 10) {
            //                 $batch->premature();
            //                 $dataArray[] = $batch->id;
            //             }
            //         }
            //         // echo "<pre>";print_r("Close Batch {$batch}");echo "</pre>"; // Close batch
            //     }

                // $file = 'EDARP Samples uploaded to KEMRI';

                // Excel::create($file, function($excel) use($rows, $file){
                //     $excel->setTitle($file);
                //     $excel->setCreator('Joshua Bakasa')->setCompany($file);
                //     $excel->setDescription($file);

                //     $excel->sheet('Sheetname', function($sheet) use($rows) {
                //         $sheet->fromArray($rows);
                //     });
                // })->store('csv');

                // $data = [storage_path("exports/" . $file . ".csv")];

                // Mail::to(['bakasajoshua09@gmail.com'])->send(new TestMail($data));

                // $title = "EDARP Samples uploaded to KEMRI";
                // Excel::create($title, function($excel) use ($dataArray, $title) {
                //     $excel->setTitle($title);
                //     $excel->setCreator(Auth()->user()->surname.' '.Auth()->user()->oname)->setCompany('WJ Gilmore, LLC');
                //     $excel->setDescription($title);

                //     $excel->sheet('Sheet1', function($sheet) use ($dataArray) {
                //         $sheet->fromArray($dataArray, null, 'A1', false, false);
                //     });

                // })->download('csv');
            // }
        }
        
    }

    public function store(ViralsampleRequest $request)
    {
        $viralsamples_arrays = Lookup::viralsamples_arrays();
        $submit_type = $request->input('submit_type');
        $user = auth()->user();

        $site_entry = $request->input('site_entry');

        $batch = session('viral_batch');
        if($batch){
            $batch = Viralbatch::find($batch->id);
            if($site_entry && $batch->site_entry != $site_entry) $batch = null;
            if($batch && $batch->sample->count() > 9){
                $batch->full_batch();
                $batch = null;
            }
        }



        if($submit_type == "cancel"){
            if($batch) $batch->premature();
            $this->clear_session();
            if(!$batch) return back();
            session(['toast_message' => "The batch {$batch->id} has been released."]);
            return redirect("viralbatch/{$batch->id}");
        }

        $data_existing = $request->only(['facility_id', 'patient', 'datecollected']);
        if(!isset($data_existing['facility_id'])){
            session(['toast_message' => "Please set the facility before submitting.", 'toast_error' => 1]);
            return back();   
        }

        $patient_string = trim($request->input('patient'));
        if(env('APP_LAB') == 4){
            $fac = \App\ViewFacility::find($data_existing['facility_id']);
            // $patient_string = $fac->facilitycode . '/' . $patient_string;
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

        $existing = ViralsampleView::existing( $data_existing )->first();
        if($existing && !$request->input('reentry')){
            session(['toast_message' => "The sample already exists in batch {$existing->batch_id} and has therefore not been saved again"]);
            session(['toast_error' => 1]);
            return back();            
        }

        $highpriority = $request->input('highpriority');

        if($highpriority == 1)
        {
            $batch = new Viralbatch;
            $batch->user_id = $user->id;
            $batch->lab_id = $user->lab_id;

            if($user->is_lab_user()){
                $batch->received_by = auth()->user()->id;
                $batch->time_received = date('Y-m-d H:i:s');
                $batch->site_entry = 0;
            }

            if($user->user_type_id == 5) $batch->site_entry = 1;

            $data = $request->only($viralsamples_arrays['batch']);
            $batch->fill($data);

            $batch->save();
            $message = 'The high priority sample has been saved in batch no ' . $batch->id . '.';

            session(['toast_message' => $message]);
        }

        if(!$batch){
            $facility_id = $request->input('facility_id');
            $facility = Facility::find($facility_id);
            session(['viral_facility_name' => $facility->name, 'viral_batch_total' => 0]);

            $batch = Viralbatch::eligible($facility_id, $request->input('datereceived'), $site_entry)->first();

            if(!$batch) $batch = new Viralbatch;
            $batch->user_id = $user->id;
            $batch->lab_id = $user->lab_id;

            if($user->is_lab_user()){
                $batch->received_by = auth()->user()->id;
                $batch->time_received = date('Y-m-d H:i:s');
                $batch->site_entry = 0;
            }

            if($user->user_type_id == 5){
                $batch->site_entry = 1;
            }
        }

        $data = $request->only($viralsamples_arrays['batch']);
        $batch->fill($data);

        $batch->save();
        session(['viral_batch' => $batch]);

        $new_patient = $request->input('new_patient');
        $viralpatient = Viralpatient::existing($request->input('facility_id'), $patient_string)->first();
        if(!$viralpatient) $viralpatient = new Viralpatient;

        /*if($new_patient == 0){

            $patient_id = $request->input('patient_id');
            $repeat_test = Viralsample::where(['patient_id' => $patient_id, 'batch_id' => $batch->id])->first();

            if($repeat_test){
                session(['toast_message' => 'The sample already exists in the batch and has therefore not been saved again']);
                session(['toast_error' => 1]);
                return redirect()->route('viralsample.create');
            }

            $viralpatient = Viralpatient::find($patient_id);
            if(!$viralpatient) $viralpatient = Viralpatient::existing($request->input('facility_id'), $request->input('patient'))->first();
            if(!$viralpatient) $viralpatient = new Viralpatient;
        }
        else{
            $viralpatient = new Viralpatient;
        }*/

        $data = $request->only($viralsamples_arrays['patient']);
        if(!$data['dob'])
            $data['dob'] = Lookup::calculate_dob($request->input('datecollected'), $request->input('age'), 0);
        $viralpatient->fill($data);
        $viralpatient->patient = $patient_string;
        if(!$viralpatient->patient) $viralpatient->patient = '';
        $viralpatient->pre_update();
        

        $data = $request->only($viralsamples_arrays['sample']);
        $viralsample = new Viralsample;
        $viralsample->fill($data);
        if($request->input('dateseparated') && $request->input('separating_hour')){
            $viralsample->dateseparated .= ' ' . $request->input('separating_hour') . ':00:00';
        }
        if(env('APP_LAB') == 8){
            $viralsample->areaname = $request->input('areaname');
            $viralsample->label_id = $request->input('label_id');
        }
        $viralsample->patient_id = $viralpatient->id;
        $viralsample->age = Lookup::calculate_viralage($request->input('datecollected'), $viralpatient->dob);
        if($viralsample->age > 2 && $viralsample->justification == 10){
            session(['toast_error' => 1, 'toast_message' => 'The sample was not saved. This is because the justification cannot be baseline when the client is over 2 years of age.']);
            return back();
        }
        if($viralsample->age < 12 && in_array($viralsample->pmtct, [1,2])){
            session(['toast_error' => 1, 'toast_message' => 'The sample was not saved. This is because the patient is underage but pmtct is set.']);
            return back();
        }
        if($viralsample->age > 60 && in_array($viralsample->pmtct, [1,2])){
            session(['toast_error' => 1, 'toast_message' => 'The sample was not saved. This is because the patient is overage but pmtct is set.']);
            return back();
        }
        if($viralsample->age < 16 && $viralsample->justification == 12){
            $viralsample->receivedstatus = 2;
            $viralsample->rejectedreason = 14;
            $viralsample->labcomment = 'Underage for recency testing.';
        }
        $viralsample->batch_id = $batch->id;
        $viralsample->save();


        $sample_count = Viralsample::where('batch_id', $batch->id)->get()->count();

        session(['toast_message' => "The sample has been created in batch {$batch->id}.", 'viral_batch_total' => $sample_count, 'viral_last_patient' => $viralpatient->patient]);

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
            // MiscViral::check_batch($batch->id);

            if($user->is_lab_user()){           
                $work_samples_dbs = MiscViral::get_worksheet_samples(2, false, 1);
                $work_samples_edta = MiscViral::get_worksheet_samples(2, false, 2);

                $str = '';

                if($work_samples_dbs['count'] > 92) $str .= 'You now have ' . $work_samples_dbs['count'] . ' DBS samples that are eligible for testing.<br />';

                if($work_samples_edta['count'] > 20) $str .= 'You now have ' . $work_samples_edta['count'] . ' Plasma / EDTA samples that are eligible for testing.';

                if($str != '') session(['toast_message' => $str]);                
            }

            return redirect("viralbatch/{$batch->id}");
        }

        if($sample_count == 10){
            $this->clear_session();
            $batch->full_batch();
            MiscViral::check_batch($batch->id);
            session(['toast_message' => "The batch {$batch->id} is full and no new samples can be added to it."]);
            return redirect("viralbatch/{$batch->id}");
        }

        session(['toast_message' => 'The sample has been created.']);

        $stype = $request->input('form_sample_type');

        if($stype) return redirect('viralsample/create/' . $stype);

        return redirect()->route('viralsample.create');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Viralsample  $viralsample
     * @return \Illuminate\Http\Response
     */
    public function show(ViralsampleView $viralsample)
    {
        $s = Viralsample::find($viralsample->id);
        $samples = Viralsample::runs($s)->get();

        $patient = $s->patient; 

        $data = Lookup::get_viral_lookups();
        $data['sample'] = $viralsample;
        $data['samples'] = $samples;
        $data['patient'] = $patient;
        
        return view('tables.viralsample_search', $data)->with('pageTitle', 'Sample Summary');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Viralsample  $viralsample
     * @return \Illuminate\Http\Response
     */
    public function edit(Viralsample $viralsample)
    {
        // $viralsample->load(['patient', 'batch.facility']);
        $data = Lookup::viralsample_form();
        $data['viralsample'] = $viralsample;
        // dd($data);
        return view('forms.viralsamples', $data)->with('pageTitle', 'Edit Sample');
    }

    /**
     * Show the form for editing the specified resource (poc).
     *
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function edit_poc(Viralsample $sample)
    {
        $sample->load(['patient', 'batch.facility_lab']);
        // if($sample->batch->site_entry != 2) abort(409, 'This sample is not a POC sample.');
        $data = Lookup::get_lookups();
        $data['sample'] = $sample;
        $data['pre'] = 'viral';
        return view('forms.poc_result', $data)->with('pageTitle', 'Edit Result');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Viralsample  $viralsample
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Viralsample $viralsample)
    {
        $viralsamples_arrays = Lookup::viralsamples_arrays();
        $user = auth()->user();
        
        $batch = Viralbatch::find($viralsample->batch_id);

        if($batch->site_entry == 1 && !$viralsample->receivedstatus && $user->is_lab_user()){
            $viralsample->sample_received_by = $user->id;
        }

        $data = $request->only($viralsamples_arrays['sample']);
        $viralsample->fill($data);

        $data = $request->only($viralsamples_arrays['batch']);
        $batch->fill($data);
        if(!$batch->received_by && $user->is_lab_user()){
            $batch->received_by = $user->id;
            $batch->time_received = date('Y-m-d H:i:s');
        }
        $batch->pre_update();

        $data = $request->only($viralsamples_arrays['patient']);

        /*$new_patient = $request->input('new_patient');

        if($new_patient == 0){            
            $viralpatient = Viralpatient::find($viralsample->patient_id);
        }
        else{
            $viralpatient = new Viralpatient;
        }*/

        $viralpatient = $viralsample->patient;

        if($viralpatient->patient != $request->input('patient') && $request->justification != 12){
            $viralpatient = Viralpatient::existing($request->input('facility_id'), $request->input('patient'))->first();
            $different_patient = true;

            if(!$viralpatient){
                $viralpatient = new Viralpatient;
                $created_patient = true;
            }
        }
        

        if(!$data['dob'] || $data['dob'] == '') $data['dob'] = Lookup::calculate_dob($request->input('datecollected'), $request->input('age'), 0);
        $viralpatient->fill($data);
        if(!$viralpatient->patient) $viralpatient->patient = '';
        $viralpatient->pre_update();


        if($request->input('dateseparated') && $request->input('separating_hour')){
            $viralsample->dateseparated .=  ' ' . $request->input('separating_hour') . ':00:00';
        }
        $viralsample->age = Lookup::calculate_viralage($request->input('datecollected'), $viralpatient->dob);
        $viralsample->patient_id = $viralpatient->id;

        session(['toast_message' => 'The sample has been updated.']);

        if($viralsample->receivedstatus == 2 && $viralsample->getOriginal('receivedstatus') == 1 && $viralsample->worksheet_id){
            $worksheet = $viralsample->worksheet;
            /*if($worksheet->status_id == 1){
                $d = MiscViral::get_worksheet_samples($worksheet->machine_type, $worksheet->calibration, $worksheet->sampletype, 1);
                $s = $d['samples']->first();
                if($s){
                    $viralsample->worksheet_id = null;

                    $replacement = Viralsample::find($s->id);

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
            $viralsample->worksheet_id = null;
            $viralsample->result = null;
            $viralsample->interpretation = null;
            $viralsample->repeatt = 0;
        }

        if($viralsample->receivedstatus == 1 && $viralsample->getOriginal('receivedstatus') == 2){
            if($batch->batch_complete == 1) $transfer = true;
            else if($batch->batch_complete == 2){
                $batch->batch_complete = 0;
                $batch->pre_update();
            }            
        }

        if(env('APP_LAB') == 8){
            $viralsample->areaname = $request->input('areaname');
            $viralsample->label_id = $request->input('label_id');
        }

        if($viralpatient->sex == 1) $viralsample->pmtct = 3;

        $viralsample->pre_update();

        if(isset($transfer)){
            $url = $batch->transfer_samples([$viralsample->id], 'new_facility', true);
            $viralsample->refresh();
            $batch = $viralsample->batch;
            session(['toast_message' => 'The sample has been tranferred to a new batch because the batch it was in has already been dispatched.']);
        }

        if(isset($different_patient)){
            if($viralsample->run == 1 && $viralsample->has_rerun){
                $children = $viralsample->child;

                foreach ($children as $kid) {
                    $kid->patient_id = $viralpatient->id;
                    $kid->pre_update();
                }
            }
            else if($viralsample->run > 1){
                $parent = $viralsample->parent;
                $parent->pre_update();
                
                $children = $parent->child;

                foreach ($children as $kid) {
                    $kid->patient_id = $viralpatient->id;
                    $kid->pre_update();
                }
            }
        }

        MiscViral::check_batch($batch->id); 

        if($viralsample->receivedstatus == 1 && $user->is_lab_user()){            
            $work_samples_dbs = MiscViral::get_worksheet_samples(2, false, 1);
            $work_samples_edta = MiscViral::get_worksheet_samples(2, false, 2);

            $str = '';

            if($work_samples_dbs['count'] > 92) $str .= 'You now have ' . $work_samples_dbs['count'] . ' DBS samples that are eligible for testing.<br />';

            if($work_samples_edta['count'] > 20) $str .=  'You now have ' . $work_samples_edta['count'] . ' Plasma / EDTA samples that are eligible for testing.';

            if($str != '') session(['toast_message' => $str]);
        }

        if($viralsample->receivedstatus && !$viralsample->getOriginal('receivedstatus') && $batch->site_entry == 1){
            return redirect('viralbatch/site_approval_group/' . $batch->id);
        }

        $site_entry_approval = session()->pull('site_entry_approval');

        if($site_entry_approval){
            session(['toast_message' => 'The site entry sample has been approved.']);
            return redirect('viralbatch/site_approval/' . $batch->id);
        }


        return redirect('viralbatch/' . $batch->id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function save_poc(Request $request, Viralsample $sample)
    {
        if($sample->result && env('APP_LAB') != 7){
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

        $sample->fill($request->except(['_token', 'lab_id', 'result_2']));

        if(!$sample->result || $request->input('result_2')) $sample->result = $request->input('result_2');

        if(!$sample->result){
            session(['toast_message' => 'Please set a result value.', 'toast_error' => 1]);
            return back();
        }

        $sample->pre_update();

        MiscViral::check_batch($sample->batch_id);
        MiscViral::check_worklist(ViralsampleView::class, $sample->worksheet_id);

        $batch = $sample->batch;
        if($batch->site_entry == 2) $batch->lab_id = $request->input('lab_id');
        if($batch->batch_complete == 2){
            $batch->datedispatched = date('Y-m-d');
            $batch->batch_complete = 1;
        }
        $batch->pre_update();
        session(['toast_message' => 'The sample has been updated.']);

        if(env('APP_LAB') == 7){
            $dr_sample = \App\DrSample::where($sample->only(['datecollected', 'patient_id']))->first();
            $dr_sample->status_id = 1;
            $dr_sample->datedispatched = date('Y-m-d');
            $dr_sample->save();
            return redirect('dr_sample/12');
        }

        return redirect('viralsample/list_poc');        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Viralsample  $viralsample
     * @return \Illuminate\Http\Response
     */
    public function destroy(Viralsample $viralsample)
    {
        if($viralsample->result == NULL && $viralsample->run < 2 && $viralsample->worksheet_id == NULL && !$viralsample->has_rerun){
            $batch = $viralsample->batch;
            $viralsample->delete();
            record_log::save_log($viralsample->id,$viralsample->patient_id,$batch->id,'delete',null);
            $samples = $batch->sample;
            if($samples->isEmpty()) $batch->delete();
            else{
                MiscViral::check_batch($batch->id);
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

        $viralpatient = Viralpatient::where(['facility_id' => $facility_id, 'patient' => $patient])->first();        
        $data;
        if($viralpatient){
            $viralpatient->most_recent();
            $data[0] = 0;
            $data[1] = $viralpatient->toArray();

            $viralsample = Viralsample::select('id')->where(['patient_id' => $viralpatient->id])->where('result', '>', 1000)->where('repeatt', 0)->first();
            if($viralsample){
                $data[2] = ['previous_nonsuppressed' => 1];
            }
            else{
                $data[2] = ['previous_nonsuppressed' => 0];
            } 
            $data[3] = 0;
            if($viralpatient->most_recent){
                $data[3] = "The date collected for the most recent test of the patient is " . $viralpatient->most_recent->my_date_format('datecollected') . " in batch number " . $viralpatient->most_recent->batch_id;
            }
        }
        else{
            $data[0] = 1;
        }
        return $data;
    }


    public function transfer(Viralsample $sample)
    {
        $sample->sample_received_by = auth()->user()->id;
        $sample->save();
        session(['toast_message' => "The sample has been tranferred to your account."]);
        return back();
    }

    public function runs(Viralsample $sample)
    {
        // $samples = $sample->child;
        $samples = Viralsample::runs($sample)->get();
        $patient = $sample->patient;
        return view('tables.sample_runs', ['patient' => $patient, 'samples' => $samples]);
    }

    /**
     * Print the specified resource.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function individual(Viralsample $sample)
    {
        $data = Lookup::get_viral_lookups();
       // $sample->load(['patient', 'approver', 'batch.lab', 'batch.facility', 'batch.receiver', 'batch.creator']);
	$sample->load(['patient', 'worksheet', 'approver', 'batch.lab', 'batch.facility', 'batch.receiver', 'batch.creator']);
	$lab =  DB::table('viralworksheets')
                ->where('viralworksheets.id', $sample->worksheet_id )
                ->get();
        $data['samples'] = [$sample];
	$data['labs'] = [$lab];
        return view('exports.mpdf_viralsamples', $data)->with('pageTitle', 'Individual Samples');
    }

    public function send_sms(ViralsampleView $sample)
    {
        MiscViral::send_sms($sample);
        session(['toast_message' => 'The sms has been sent.']);
        return back();
    }

    public function return_for_testing(Viralsample $sample)
    {
        if($sample->result != 'Collect New Sample' || $sample->repeatt == 1 || $sample->age_in_months > 3){
            session(['toast_error' => 1, 'toast_message' => 'The sample cannot be returned for testing.']);
            return back();
        }

        $sample->repeatt = 1;
        $sample->save();

        $rerun = MiscViral::save_repeat($sample->id);

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
            return redirect('viralbatch/' . $batch->id);
        }
    }

    public function release_redraw(Viralsample $sample)
    {
        $batch = $sample->batch;
        if($sample->run == 1 || $batch->batch_complete != 0 ){
            session(['toast_message' => 'The sample cannot be released as a redraw.']);
            session(['toast_error' => 1]);
            return back();
        }
        else if($sample->run == 2){
            $prev_sample = $sample->parent;
        }
        else{
            $run = $sample->run - 1;
            $prev_sample = Viralsample::where(['parentid' => $sample->parentid, 'run' => $run])->first();
        }
        
        $sample->delete();

        $prev_sample->labcomment = "Failed Test";
        $prev_sample->repeatt = 0;
        $prev_sample->result = "Collect New Sample";
        $prev_sample->approvedby = auth()->user()->id;
        $prev_sample->approvedby2 = auth()->user()->id;
        $prev_sample->dateapproved = date('Y-m-d');
        $prev_sample->dateapproved2 = date('Y-m-d');

        $prev_sample->save();
        MiscViral::check_batch($prev_sample->batch_id);
        session(['toast_message' => 'The sample has been released as a redraw.']);
        return back();
    }

    public function release_redraws(Request $request)
    {
        $viralsamples = $request->input('samples');
        // DB::table('viralsamples')->whereIn('id', $viralsamples)->update(['repeatt' => 0, 'result' => "Collect New Sample"]);

        $viralsamples = Viralsample::whereIn('id', $viralsamples)->get();

        foreach ($viralsamples as $key => $viralsample) {
            $this->release_redraw($viralsample);
        }

        return back();
    }

    public function unreceive(Viralsample $sample)
    {
        if($sample->run > 1 || $sample->synched){
            session(['toast_error' => 1, 'toast_message' => 'The sample cannot be set to unreceived']);
        }
        else{
            $sample->fill(['sample_received_by' => null, 'receivedstatus' => null, 'rejectedreason' => null, 'worksheet_id' => null]);
            $sample->save();
            session(['toast_message' => 'The sample has been unreceived.']);
        }
        return back();
    }

    public function approve_nhrl(Request $request)
    {
        $viralsamples = $request->input('samples');
        $submit_type = $request->input('submit_type');
        $user = auth()->user();

        $batches = Viralsample::selectRaw("distinct batch_id")->whereIn('id', $viralsamples)->get();

        if($submit_type == "release"){
            Viralsample::whereIn('id', $viralsamples)->update(['synched' => 0, 'approvedby' => $user->id, 'dateapproved' => date('Y-m-d')]);
            session(['toast_message' => 'The samples have been sent to NASCOP.']);
        }
        else{
            Viralsample::whereIn('id', $viralsamples)->delete();
            session(['toast_message' => 'The samples have been sent to deleted.']);
        }

        foreach ($batches as $key => $value) {
            MiscViral::check_batch($value->batch_id);
        } 
        return back();
    }

    public function site_sample_page()
    {
        return view('forms.upload_site_samples', ['url' => 'viralsample'])->with('pageTitle', 'Upload Facility Samples');
    }

    public function upload_site_samples(Request $request)
    {
        $file = $request->upload->path();
        // $path = $request->upload->store('public/site_samples/vl');

        $problem_rows = 0;
        $created_rows = 0;

        $existing_rows = [];

        $line = fgets(fopen($file, 'r'));
        $delimiter = ',';
        if(\Str::contains($line, ';')) $delimiter = ';';

        $handle = fopen($file, "r");

        if(env('APP_LAB') == 8){
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE){
                $facility = Facility::locate($row[4])->first();
                if(!$facility || !is_numeric($row[4])) continue;

                $datecollected = Lookup::other_date($row[9]);
                $datereceived = Lookup::other_date($row[13]);
                if(!$datereceived) $datereceived = date('Y-m-d');
                $patient_string = $row[2];
                $existing = ViralsampleView::where(['facility_id' => $facility->id, 'patient' => $patient_string, 'datecollected' => $datecollected])->first();

                if($existing){
                    $existing_rows[] = $existing->toArray();
                    continue;
                }

                $batch = Viralbatch::withCount(['sample'])
                                        ->where('received_by', auth()->user()->id)
                                        ->where('datereceived', $datereceived)
                                        ->where('input_complete', 0)
                                        ->where('site_entry', 1)
                                        ->where('facility_id', $facility->id)
                                        ->first();

                if($batch){
                    if($batch->sample_count > 9){
                        unset($batch->sample_count);
                        $batch->full_batch();
                        $batch = null;
                    }
                }

                if(!$batch){
                    $batch = new Viralbatch;
                    $batch->user_id = auth()->user()->id;
                    $batch->facility_id = $facility->id;
                    $batch->received_by = auth()->user()->id;
                    $batch->lab_id = auth()->user()->lab_id;
                    $batch->datereceived = $datereceived;
                    $batch->site_entry = 1;
                    $batch->save();
                }

                $patient = Viralpatient::existing($facility->id, $patient_string)->first();
                if(!$patient) $patient = new Viralpatient;

                $patient->patient = $patient_string;
                $patient->facility_id = $facility->id;
                $patient->dob = Lookup::calculate_dob($datecollected, $row[7]);
                $patient->sex = Lookup::get_gender($row[6]);
                $patient->initiation_date = Lookup::other_date($row[11]);
                $patient->save();


                $sample = new Viralsample;
                $sample->batch_id = $batch->id;
                $sample->patient_id = $patient->id;
                $sample->datecollected = $datecollected;
                $sample->age = $row[7];
                if(\Str::contains(strtolower($row[8]), ['edta'])) $sample->sampletype = 2; 

                $sample->areaname = $row[5];
                $sample->label_id = $row[1];
                // $sample->prophylaxis = Lookup::viral_regimen($row[10]);
                $sample->prophylaxis = $row[10];
                $sample->justification = Lookup::justification($row[12]);
                $sample->pmtct = 3;
                $sample->receivedstatus = 1;
                $sample->save();

                $created_rows++;
            }
        }
        else{
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE){

                $sample = $patient = $batch = null;

                $facility = Facility::locate($row[3])->first();
                if(!$facility) continue;
                $datecollected = Lookup::other_date($row[8]);
                $datereceived = Lookup::other_date($row[15]);
                if(!$datereceived) $datereceived = date('Y-m-d');
                $existing = ViralsampleView::existing(['facility_id' => $facility->id, 'patient' => $row[1], 'datecollected' => $datecollected])->first();

                if($existing){
                    /*$sampletype = (int) $row[7];
                    if(in_array($existing->sampletype, [3, 4]) && in_array($sampletype, [1,2,5])){
                        $s = Viralsample::find($existing->id);
                        $s->delete();
                    }
                    else{
                        $existing_rows[] = $existing->toArray();
                        continue;                        
                    }*/

                    $sample = Viralsample::find($existing->id);
                    $patient = $sample->patient;
                    $batch = $sample->batch;
                    $batch->datereceived = date('Y-m-d');
                    $batch->received_by = auth()->user()->id;
                    $batch->save();
                }

                $site_entry = Lookup::get_site_entry($row[14]);

                if(!$batch){
                    $batch = Viralbatch::withCount(['sample'])
                                            ->where('received_by', auth()->user()->id)
                                            ->where('datereceived', $datereceived)
                                            ->where('input_complete', 0)
                                            ->where('site_entry', $site_entry)
                                            ->where('facility_id', $facility->id)
                                            ->first();

                    if($batch){
                        if($batch->sample_count > 9){
                            unset($batch->sample_count);
                            $batch->full_batch();
                            $batch = null;
                        }
                    }
                }

                if(!$batch){
                    $batch = new Viralbatch;
                    $batch->user_id = $facility->facility_user->id;
                    $batch->facility_id = $facility->id;
                    $batch->received_by = auth()->user()->id;
                    $batch->time_received = date('Y-m-d H:i:s');
                    $batch->lab_id = auth()->user()->lab_id;
                    $batch->datereceived = $datereceived;
                    $batch->site_entry = $site_entry;
                    $batch->save();
                }

                if(!$patient) $patient = Viralpatient::existing($facility->id, $row[1])->first();
                if(!$patient){
                    $patient = new Viralpatient;
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

                if(!$sample) $sample = new Viralsample;
                $sample->batch_id = $batch->id;
                $sample->patient_id = $patient->id;
                $sample->datecollected = $datecollected;
                $sample->age = $row[6];
                if(!$sample->age) $sample->age = Lookup::calculate_viralage($datecollected, $patient->dob);
                $sample->prophylaxis = Lookup::viral_prophylaxis($row[10]);
                // $sample->prophylaxis = $row[10];
                $sample->dateinitiatedonregimen = Lookup::other_date($row[11]);
                $sample->justification = Lookup::justification($row[12]);
                $sample->sampletype = (int) $row[7];
                if($sample->sampletype == 5) $sample->sampletype = 1;
                $sample->pmtct = $row[13];
                $sample->receivedstatus = $row[16];
                if(is_numeric($row[17])) $sample->rejectedreason = $row[17];
                $sample->save();
                $created_rows++;
            }
        }
        session(['toast_message' => "{$created_rows} samples have been created."]);

        if($existing_rows){
            return MiscViral::csv_download($existing_rows, "samples_that_were_already_existing");
        }

        return redirect('/viralbatch');        
    }


    public function transfer_samples_form($facility_id=null)
    {
        $samples = ViralsampleView::whereNull('receivedstatus')
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
            'pre' => 'viral',
        ];

        return view('forms.transfer_samples', $data);
    }

    public function transfer_samples(Request $request)
    {
        $samples = $request->input('samples');
        $lab = $request->input('lab');
        // dd($samples);
        \App\Synch::transfer_sample('vl', $lab, $samples);
        return back();
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";

        $samples = ViralsampleView::select('id')
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

        $samples = ViralsampleView::select(['id', 'order_no', 'patient'])
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
        if(!$facility_id || !$patient) return '';
        $datecollected = $request->input('datecollected');

        $dt = date('Y-m-d', strtotime('-1months'));

        $samples = ViralsampleView::where('created_at', '>', "{$dt}")
            ->where(['repeatt' => 0, 'site_entry' => 1, 'facility_id' => $facility_id, ])
            ->when($request->input('sex'), $this->query_callback($request, 'sex'))
            ->when($request->input('prophylaxis'), $this->query_callback($request, 'prophylaxis'))
            ->when($request->input('justification'), $this->query_callback($request, 'justification'))
            ->when($patient, function($query) use ($patient){
                $patient_string = substr($patient, 0, 5);
                return $query->whereRaw("(patient like '{$patient_string}%' ) ");
            })
            ->when($datecollected, function($query) use ($datecollected){
                return $query->whereBetween('datecollected', [date('Y-m-d', strtotime("{$datecollected} -1week")), date('Y-m-d', strtotime("{$datecollected} +1week"))]);
            })
            ->limit(10)
            ->get();


        $data = Lookup::get_viral_lookups();
        $data['samples'] = $samples;

        return view('tables.similar_viralsamples', $data);
    }



    public function query_callback($request, $attribute)
    {
        $val = $request->input($attribute);

        return function($query) use($attribute, $val){
            return $query->where($attribute, $val);
        };
    }


    private function clear_session(){
        session()->forget('viral_batch');
        session()->forget('viral_facility_name');
        session()->forget('viral_batch_total');
        session()->forget('viral_last_patient');

        // session()->forget('viral_batch_no');
        // session()->forget('viral_batch_dispatch');
        // session()->forget('viral_batch_dispatched');
        // session()->forget('viral_batch_received');
        // session()->forget('viral_facility_id');
        // session()->forget('viral_facility_name');
    }

    public function extract_excel_results(Request $request) {
        if ($request->method() == 'POST') {
            $file = $request->excelupload->path();
            $excelData = Excel::load($file, function($reader){
                $reader->toArray();                
            })->get();
            $data = $excelData;
            $newData = [];
            $newData[] = ['Test Type','TestingLab','SpecimenLabelID','SpecimenClientCode','FacilityName','MFLCode','Sex','PMTCT','Age','DOB','SampleType','DateCollected','CurrentRegimen','regimenLine','ART Init Date','Justification','DateReceived','loginDate','ReceivedStatus','RejectedReason','ReasonforRepeat','LabComment','Datetested','DateDispatched','Results','Edited'];
            // dd($data);
            foreach ($data as $key => $sample) {
                // dd($sample);
                // $sample = collect($sample)->flatten(1)->toArray();
                // dd($sample[3]);
                // $sample = (array)$sample;
                $dbsample = ViralsampleView::where('patient', '=', $sample[3])->where('datecollected', '=', $sample[11])->where('repeatt', '<>', 1)->get()->last();
                $sample[19] = $dbsample->rejectedreason ?? null;
                $sample[20] = $dbsample->reason_for_repeat ?? null;
                $sample[21] = $dbsample->labcomment ?? null;
                $sample[22] = (isset($dbsample->datetested)) ? date('m/d/Y', strtotime($dbsample->datetested)) : null;
                $sample[23] = (isset($dbsample->datedispatched)) ? date('m/d/Y', strtotime($dbsample->datedispatched)) : null;
                // $sample[22] = $dbsample->datetested;
                // $sample[23] = $dbsample->datedispatched;
                $sample[24] = $dbsample->result ?? null;

                $newData[] = $sample->toArray();
            }
            // dd($newData);
            ini_set("memory_limit", "-1");
            ini_set("max_execution_time", "3000");
            $title = 'EDARP reffered sample';
            Excel::create($title, function($excel) use ($newData, $title) {
                $excel->setTitle($title);
                $excel->setCreator(Auth()->user()->surname.' '.Auth()->user()->oname)->setCompany('EID/VL System');
                $excel->setDescription($title);

                $excel->sheet('Sheet1', function($sheet) use ($newData) {
                    $sheet->fromArray($newData, null, 'A1', false, false);
                });

            })->download('xlsx');
            
        } else if ($request->method() == 'GET') {
            return view('forms.viralsamplesexcelextract')->with('pageTitle', 'Get Sample');
        }
    }
}
