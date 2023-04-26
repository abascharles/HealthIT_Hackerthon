<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Controllers\BaseController;
use App\Api\V1\Requests\CragRequest;

use App\Lookup;
use App\Cragpatient;
use App\Cragsample;
use App\CragsampleView;


class CragController extends BaseController
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

    public function partial(CragRequest $request)
    {
        if(env('APP_LAB') != 5) return $this->response->errorBadRequest("This lab does not provide Crag.");
        $code = $request->input('mflCode');
        $datecollected = $request->input('datecollected');
        $order_no = $request->input('order_no');
        $dob = $request->input('dob');
        $lab = $request->input('lab') ?? env('APP_LAB');

        $facility = Lookup::facility_mfl($code);
        $age = Lookup::calculate_viralage($datecollected, $dob);

        $sample_exists = CragsampleView::where(['order_no' => $order_no])->first();
        // $fields = Lookup::viralsamples_arrays();

        if($sample_exists) return $this->response->errorBadRequest("This sample already exists.");

        $patient = new Cragpatient;
        $patient->patient_name = $request->input('patient_name');
        $patient->patient_number = $request->input('patient_number');
        $patient->dob = $request->input('dob');
        $patient->sex = $request->input('sex');
        $patient->save();

        $sample = new Cragsample;
        $sample->patient_id = $patient->id;
        $sample->facility_id = $facility;
        $sample->lab_id = $lab;
        $sample->order_no = $order_no;
        $sample->user_id = 66;
        $sample->age = $age;
        $sample->status_id = 1;
        $sample->datecollected = $datecollected;
        $sample->receivedstatus = 1;
        $sample->datereceived = $request->input('datereceived', date('Y-m-d'));

        // $sample->serial_no = $request->input('serial_no', 0);
        $sample->amrs_location = $request->input('amrs_location');        
        $sample->amrs_location = Lookup::get_mrslocation($sample->amrs_location);
        $sample->provider_identifier = $request->input('provider_identifier');
        $sample->save();
        $sample->load(['patient']);
        return $sample;
    }

    // public function complete_result(CragRequest $request)
    // {
    //     if(env('APP_LAB') != 5) return $this->response->errorBadRequest("This lab does not provide CD4.");
    //     $code = $request->input('mflCode');
    //     $datecollected = $request->input('datecollected');
    //     $order_no = $request->input('order_no');
    //     $dob = $request->input('dob');
    //     $lab = $request->input('lab') ?? env('APP_LAB');

    //     $editted = $request->input('editted');

    //     $facility = Lookup::facility_mfl($code);
    //     $age = Lookup::calculate_viralage($datecollected, $dob);
    //     // $sex = Lookup::get_gender($gender);

    //     $sample_exists = CragsampleView::where(['order_no' => $order_no])->first();
    //     $fields = Lookup::viralsamples_arrays();

    //     if($sample_exists && !$editted) return $this->response->errorBadRequest("This sample already exists.");

    //     $a = true;

    //     if($editted){
    //         $sample = Cragsample::where(['order_no' => $order_no])->first();

    //         $patient = $sample->patient;
    //         $patient->fill($request->only(['patient_name', 'medicalrecordno', 'dob']));
    //         $patient->save();

    //         $sample->fill($request->only(['datedispatched', 'amrs_location', 'provider_identifier', 'datecollected', 
    //              'datereceived', 'result', 
    //             'THelperSuppressorRatio', 'AVGCD3percentLymph', 'AVGCD3AbsCnt', 'AVGCD3CD4percentLymph', 'AVGCD3CD4AbsCnt',
    //                 'AVGCD3CD8percentLymph', 'AVGCD3CD8AbsCnt', 'AVGCD3CD4CD8percentLymph', 'AVGCD3CD4CD8AbsCnt', 'CD45AbsCnt', ]));
    //     }

    //     else{

    //         $patient = new Cragpatient;
    //         $patient->patient_name = $request->input('patient_name');
    //         $patient->medicalrecordno = $request->input('medicalrecordno');
    //         $patient->dob = $request->input('dob');
    //         $patient->sex = $request->input('sex');
    //         $patient->save();

    //         $sample = new Cragsample;

    //         $sample->patient_id = $patient->id;
    //         $sample->facility_id = $facility;
    //         $sample->lab_id = $lab;
    //         $sample->order_no = $order_no;
    //         $sample->age = $age;
    //         $sample->status_id = 1;
    //         $sample->datecollected = $datecollected;
    //         $sample->receivedstatus = 1;
    //         // $sample->serial_no = $request->input('serial_no', 0);
    //         $sample->amrs_location = $request->input('amrs_location');
    //         $sample->provider_identifier = $request->input('provider_identifier');
    //         $sample->datedispatched = $request->input('datedispatched');

    //     }

    //     if($sample->datedispatched){
    //         $sample->status_id = 6; 
    //         $sample->dateapproved = $sample->datedispatched;
    //     }
    //     $sample->save();
    //     $sample->load(['patient']);
    //     return $sample;
    // }


}
