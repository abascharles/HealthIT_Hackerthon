<?php

namespace App\Api\V1\Controllers;

use App\Viralpatient;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\ApiRequest;

class ViralpatientController extends Controller
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
     * @param  \App\Viralpatient  $viralpatient
     * @return \Illuminate\Http\Response
     */
    public function show(Viralpatient $viralpatient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Viralpatient  $viralpatient
     * @return \Illuminate\Http\Response
     */
    public function update(ApiRequest $request, $id)
    {
        $viralpatient = Viralpatient::findOrFail($id);
        $fields = json_decode($request->input('patient'));

        if($fields->facility_id != $viralpatient->facility_id) return $this->response->errorBadRequest("This patient does not exist here.");

        $unset_array = ['id', 'original_patient_id', 'mother_id', ];

        foreach ($unset_array as $value) {
            unset($fields->$value);
        }

        $viralpatient->fill(get_object_vars($fields));

        $viralpatient->synched = 1;
        $viralpatient->datesynched = date('Y-m-d');
        $viralpatient->save();

        return response()->json([
                'message' => 'The update was successful.',
                'status_code' => 200,
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Viralpatient  $viralpatient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Viralpatient $viralpatient)
    {
        //
    }
}
