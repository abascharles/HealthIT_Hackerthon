<?php

namespace App\Api\V1\Controllers;

use App\Patient;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\ApiRequest;

class PatientController extends Controller
{
    use \Dingo\Api\Routing\Helpers;
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
     * @param  \App\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function show(Patient $patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function update(ApiRequest $request, $id)
    {
        $patient = Patient::findOrFail($id);
        $fields = json_decode($request->input('patient'));

        if($fields->facility_id != $patient->facility_id) return $this->response->errorBadRequest("This patient does not exist here.");

        $unset_array = ['id', 'original_patient_id', 'mother_id', ];

        foreach ($unset_array as $value) {
            unset($fields->$value);
        }

        $patient->fill(get_object_vars($fields));

        $patient->synched = 1;
        $patient->datesynched = date('Y-m-d');
        $patient->save();

        return response()->json([
                'message' => 'The update was successful.',
                'status_code' => 200,
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Patient  $patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Patient $patient)
    {
        //
    }
}
