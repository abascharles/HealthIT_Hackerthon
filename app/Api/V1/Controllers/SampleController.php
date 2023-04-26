<?php

namespace App\Api\V1\Controllers;

use App\SampleView;
use App\Sample;
use App\Batch;
use App\Patient;
use App\Mother;
use App\Api\V1\Requests\ApiRequest;

class SampleController extends BaseController
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    // public function show(Sample $sample)
    public function show($id)
    {
        $sample = Sample::findOrFail($id);
        $sample->load(['patient']);
        $sample->batch;

        return $sample;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function update(ApiRequest $request, $id)
    {
        $sample = Sample::findOrFail($id);
        $fields = json_decode($request->input('sample'));
        $site_entry = $request->input('site_entry');

        if($site_entry == 2 && $sample->batch->site_entry != 2) return $this->response->errorBadRequest("This sample does not exist here.");

        $sample->national_sample_id = $fields->id;

        $unset_array = ['id', 'batch_id', 'patient_id', 'original_sample_id', 'old_id', 'amrs_location', 'previous_positive'];

        foreach ($unset_array as $value) {
            unset($fields->$value);
        }

        $sample->fill(get_object_vars($fields));

        $sample->synched = 1;
        $sample->datesynched = date('Y-m-d');
        $sample->save();

        return response()->json([
                'message' => 'The update was successful.',
                'status_code' => 200,
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sample $sample)
    {
        //
    }

    public function transfer(ApiRequest $request)
    {        
        $new_samples = json_decode($request->input('samples'));

        $ok = $samples = $batches = $patients = $mothers = [];

        foreach ($new_samples as $key => $new_sample) {
            $existing = SampleView::sample($new_sample->batch->facility_id, $new_sample->patient->patient, $new_sample->datecollected)->first();
            if($existing){
                $ok[] = $new_sample->id;
                continue;
            }

            $user = $new_sample->batch->user ?? null;
            $user_id = 20000;
            if($new_sample->batch->site_entry && $user){
                $user_id = \App\User::where('facility_id', $user->facility_id)->first()->id ?? 20000;
            }
            unset($new_sample->batch->user); 

            $b = Batch::where('created_at', $new_sample->batch->created_at)
                ->where(['facility_id' => $new_sample->batch->facility_id, 'user_id' => $user_id, 'batch_complete' => 0])
                ->first();

            if($b){
                $s = $b->sample->count();
                if($s > 9){
                    $b->full_batch();
                    $b = new Batch;
                }
            }
            else{
                $b = new Batch;
            }

            $batch_details = get_object_vars($new_sample->batch);
            unset($batch_details['id']);            
            $b = new Batch;
            $b->fill($batch_details);
            $b->user_id = $user_id;
            $b->lab_id = env('APP_LAB');
            unset($b->creator);
            $b->pre_update();
            unset($new_sample->batch);

            $m = new Mother;
            $m->fill(get_object_vars($new_sample->patient->mother));
            unset($m->id);
            $m->pre_update();
            unset($new_sample->patient->mother);


            $new_patient = false;
            $p = Patient::existing($new_sample->patient->facility_id, $new_sample->patient->patient)->first();
            if(!$p){
                $p = new Patient;
                $new_patient = true;
            }
            $p->fill(get_object_vars($new_sample->patient));
            $p->mother_id = $m->id;
            if($new_patient) unset($p->id);
            $p->pre_update();
            unset($new_sample->patient);

            $s = new Sample;
            $s->fill(get_object_vars($new_sample));
            $s->batch_id = $b->id;
            $s->patient_id = $p->id;
            unset($s->id);
            $s->pre_update();

            $mothers[] = $m;
            $patients[] = $p;
            $batches[] = $b;
            $samples[] = $s;

            $ok[] = $new_sample->id;
        }

        return response()->json([
                'ok' => $ok,
                'samples' => $samples,
                'batches' => $batches,
                'patients' => $patients,
                'mothers' => $mothers,
                'message' => 'The transfer was successful.',
                'status_code' => 201,
            ], 201);

    }
}
