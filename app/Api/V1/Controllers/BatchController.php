<?php

namespace App\Api\V1\Controllers;

use App\Batch;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\ApiRequest;

class BatchController extends Controller
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
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function show(Batch $batch)
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
        $batch = Batch::findOrFail($id);
        $fields = json_decode($request->input('batch'));
        $site_entry = $request->input('site_entry');

        if($site_entry == 2 && $batch->site_entry != 2) return $this->response->errorBadRequest("This batch does not exist here.");

        $unset_array = ['id', 'original_batch_id', 'sent_email', 'dateindividualresultprinted', 'datebatchprinted', 'dateemailsent', 'printedby'];

        foreach ($unset_array as $value) {
            unset($fields->$value);
        }

        $batch->fill(get_object_vars($fields));

        $batch->synched = 1;
        $batch->datesynched = date('Y-m-d');
        $batch->save();

        return response()->json([
                'message' => 'The update was successful.',
                'status_code' => 200,
            ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Batch  $batch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Batch $batch)
    {
        //
    }
}
