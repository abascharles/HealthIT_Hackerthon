<?php

namespace App\Api\V1\Controllers;

use App\CovidPatient;
use App\CovidSample;
use App\CovidTravel;
use App\Api\V1\Requests\ApiRequest;

class CovidSampleController extends BaseController
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ApiRequest $request)
    {
        $sample = json_decode($request->input('sample'));
        $p = CovidPatient::where('national_patient_id', $sample->patient->id)->first();
        if(!$p){
            $p = new CovidPatient;
        }
        $patient_details = get_object_vars($sample->patient);
        $p->national_patient_id = $patient_details['id'];
        unset($patient_details['original_patient_id']);
        // unset($patient_details['cif_patient_id']);
        unset($patient_details['nhrl_patient_id']);
        unset($patient_details['date_recovered']);
        $p->fill($patient_details);
        $p->save();

        unset($sample->patient);

        $s = new CovidSample;
        $s->fill(get_object_vars($sample));
        $s->patient_id = $p->id;
        $s->national_sample_id = $sample->id;
        unset($s->original_sample_id);
        // unset($s->cif_sample_id);
        unset($s->nhrl_sample_id);
        unset($s->age_category);
        $s->save();

        return response()->json([
                'status' => 'ok',
                'sample' => $s,
                'patient' => $p,
                'message' => 'The transfer was successful.',
                'status_code' => 201,
            ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function update(ApiRequest $request, $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function transfer(ApiRequest $request)
    {        
        $new_samples = json_decode($request->input('samples'));

        $ok = $samples = $patients = [];

        foreach ($new_samples as $key => $new_sample) {

            $travels = $new_sample->patient->travel ?? null;

            unset($new_sample->patient->travel);

            $p = new CovidPatient;
            $p->fill(get_object_vars($new_sample->patient));
            $p->pre_update();
            unset($new_sample->patient);

            if($travels){
                foreach ($travels as $key => $travel) {
                    $t = new CovidTravel;
                    $t->fill(get_object_vars($travel));
                    $t->patient_id = $p->id;
                    $t->save();
                }
            }

            $s = new CovidSample;
            $s->fill(get_object_vars($new_sample));
            $s->patient_id = $p->id;
            $s->lab_id = env('APP_LAB');
            $s->datereceived = $s->user_id = $s->received_by = $s->receivedstatus = null;
            $s->pre_update();

            $patients[] = $p;
            $samples[] = $s;

            $ok[] = $new_sample->id;
        }

        return response()->json([
                'ok' => $ok,
                'samples' => $samples,
                'patients' => $patients,
                'message' => 'The transfer was successful.',
                'status_code' => 201,
            ], 201);

    }
}
