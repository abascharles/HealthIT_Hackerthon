<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Api\V1\Requests\ApiRequest;


use App\CovidModels\CovidWorksheet;
use App\CovidModels\CovidPatient;
use App\CovidModels\CovidSample;
use App\CovidModels\CovidTravel;
use App\Facility;
use DB;

class NatCovidSampleController extends Controller
{
  
    public function index(ApiRequest $request)
    {

    }

    
    public function store(ApiRequest $request)
    {
        $s =  json_decode($request->input('sample'));
        $p = $s->patient;
        $travels = $p->travel;
        unset($p->travel);
        if($p->national_patient_id){
            $patient = CovidPatient::find($p->national_patient_id);
        }else{
            $patient = new CovidPatient;
        }
        $patient_array = get_object_vars($p);
        $patient->original_patient_id = $p->id;
        $patient->fill($patient_array);
        $patient->save();


        $travel_data = [];

        foreach ($travels as $key => $travel) {
            $t = new CovidTravel;
            $t->fill(get_object_vars($travel));
            $t->patient_id = $patient->id;
            $t->original_travel_id = $travel->id;
            $t->save();
            $travel_data['travel_' . $travel->id] = $t->id;
        }


        $children = $s->child ?? [];
        unset($s->patient);
        unset($s->child);
        $sample_array = get_object_vars($s);

        if($s->national_sample_id){
            $sample = CovidSample::find($s->national_sample_id);
        }else if($s->id && $s->lab_id){
            $sample = CovidSample::where(['original_sample_id' => $s->id, 'lab_id' => $s->lab_id])->first();
        } else{
            $sample = new CovidSample;
        }
        if(!$sample) $sample = new CovidSample;
        $sample->fill($sample_array);
        $sample->patient_id = $patient->id;
        $sample->original_sample_id = $s->id;
        if($sample->cif_sample_id) $sample->synched = 2;
        if($s->national_sample_id && !$sample->id) $sample->id = $s->national_sample_id;
        $sample->datesynched = date('Y-m-d');
        $sample->save();
        // $sample_data[0] = ['original_id' => $s->id, 'national_id' => $sample->id];
        $sample_data['sample_' . $s->id] = $sample->id;

        foreach ($children as $key => $child) {

            if($child->national_sample_id){
                $child_sample = CovidSample::find($child->national_sample_id);
            }else if($child->id && $child->lab_id){
                $child_sample = CovidSample::where(['original_sample_id' => $child->id, 'lab_id' => $child->lab_id])->first();
            } else{
                $child_sample = new CovidSample;
            }
            if(!$child_sample) $child_sample = new CovidSample;

            // $child_sample = new CovidSample;
            $child_sample->fill(get_object_vars($child));
            $child_sample->patient_id = $patient->id;
            $child_sample->cif_sample_id = $sample->cif_sample_id;
            $child_sample->original_sample_id = $child->id;
            if($sample->cif_sample_id) $child_sample->synched = 2;
            if($child->national_sample_id && !$child_sample->id) $child_sample->id = $child->national_sample_id;
            $child_sample->datesynched = date('Y-m-d');
            $child_sample->save();
            // $sample_data[] = ['original_id' => $child->id, 'national_id' => $child_sample->id];
            $sample_data['sample_' . $child->id] = $child_sample->id;
        }

        return response()->json([
            'status' => 'ok',
            'patient' => $patient->id,
            'sample' => $sample_data,
            'travel' => $travel_data,
        ], 201);        
    }


    /**
     * Display the specified resource.
     *
     * @Get("/{id}")
     * @Response(200, body={
     *      "sample": {
     *          "id": "int",    
     *          "patient": {
     *              "id": "int",
     *          }    
     *      }
     * })
     */
    public function show($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function update(ApiRequest $request, $id=null)
    {
        $s = $request->input('sample');
        $sample = CovidSample::findOrFail($s->id);
        $sample_array = get_object_vars($s);
        unset($sample_array['patient_id']);
        $sample->fill($sample_array);
        $sample->synched = 2;
        $sample->datesynched = date('Y-m-d');
        $sample->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Facility  $facility
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function cif_samples(){        
        $samples = CovidSample::where(['synched' => 0])->whereIn('lab_id', [11, 101])->where('created_at', '>', date('Y-m-d', strtotime('-7 days')))->whereNull('original_sample_id')->whereNull('receivedstatus')->with(['patient'])->get();
        // $samples = CovidSample::whereNotNull('cif_sample_id')->with(['patient'])->get();
        return $samples;
    }

    public function cif(ApiRequest $request){
        CovidSample::where(['synched' => 0])->whereIn('lab_id', [11, 101])->whereNull('original_sample_id')->whereNull('receivedstatus')->whereIn('lab_id', $request->input('samples'))->update(['lab_id' => $request->input('lab_id')]);

        return response()->json([
            'status' => 'ok',
        ], 200);        
    }


    public function update_samples(ApiRequest $request){
        return $this->update_dash($request, CovidSample::class, 'samples', 'national_sample_id', 'original_sample_id');
    }

    public function update_patients(ApiRequest $request){
        return $this->update_dash($request, CovidPatient::class, 'patients', 'national_patient_id', 'original_patient_id');
    }

    public function update_worksheets(ApiRequest $request){
        return $this->update_dash($request, CovidWorksheet::class, 'worksheets', 'national_worksheet_id', 'original_worksheet_id');
    }

    public function update_dash(ApiRequest $request, $update_class, $input, $nat_column, $original_column)
    {
        $models_array = [];
        $errors_array = [];
        $models = json_decode($request->input($input));
        $lab_id = json_decode($request->input('lab_id'));

        foreach ($models as $key => $value) {
            if($value->$nat_column){
                $updating_model = $update_class::find($value->$nat_column);
            }else{
                if($input == 'samples'){
                    $s = CovidSample::where([$original_column => $value->id, 'lab_id' => $lab_id])->first();
                    if(!$s){
                        $errors_array[] = $value;
                        continue;
                    }
                    $updating_model = $update_class::find($s->id);
                }else{
                    $updating_model = $update_class::locate($value)->get()->first();
                }
            }

            if(!$updating_model){
                $errors_array[] = $value;
                continue;
            }

            $update_data = get_object_vars($value);

            if($input == 'samples'){
                $original_patient = $value->patient;
                $update_data['patient_id'] = $original_patient->national_patient_id;
                unset($update_data['patient']);
                $patient = $updating_model->patient;
                if(!$patient){
                    $patient = new CovidPatient;
                    $patient->fill(get_object_vars($value->patient));
                    $patient->id = $value->patient->national_patient_id;
                    $patient->original_patient_id = $value->patient->id;
                    $patient->save();
                    unset($updating_model->patient);
                }
            }

            $updating_model->fill($update_data);
            $updating_model->$original_column = $value->id;
            $updating_model->synched = 2;
            unset($updating_model->$nat_column);
            $updating_model->save();
            $models_array[] = ['original_id' => $updating_model->$original_column, $nat_column => $updating_model->id ];
        }

        if(count($errors_array) == 0) $errors_array = null;

        return response()->json([
            'status' => 'ok',
            $input => $models_array,
            'errors_array' => $errors_array,
        ], 201);        
    }

    public function delete_patients(ApiRequest $request){
        return $this->delete_dash($request, CovidPatient::class, 'patients', 'national_patient_id', 'original_patient_id');
    }


    public function delete_samples(ApiRequest $request){
        return $this->delete_dash($request, CovidSample::class, 'samples', 'national_sample_id', 'original_sample_id');
    }

    public function delete_worksheets(ApiRequest $request){
        return $this->delete_dash($request, CovidWorksheet::class, 'worksheets', 'national_worksheet_id', 'original_worksheet_id');
    }

    public function delete_dash(ApiRequest $request, $update_class, $input, $nat_column, $original_column)
    {
        $models_array = [];
        $models = json_decode($request->input($input));
        $lab_id = json_decode($request->input('lab_id'));

        foreach ($models as $key => $value) {
            if($value->$nat_column){
                $new_model = $update_class::find($value->$nat_column);
            }else{
                if($input == 'samples'){
                    $new_model = \App\CovidSample::where(['original_sample_id' => $value->id, 'lab_id' => $value->lab_id])->first();
                }else{
                    continue;
                }
            }

            if(!$new_model) continue;
            
            $models_array[] = ['original_id' => $new_model->$original_column, $nat_column => $new_model->id];
            $new_model->delete();
        }

        return response()->json([
            'status' => 'ok',
            $input => $models_array,
        ], 201);        
    }

}

