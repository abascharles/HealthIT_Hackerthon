<?php

namespace App\Api\V1\Controllers;

use App\Api\V1\Controllers\BaseController;
use App\Api\V1\Requests\NodeRequest;

use App\MiscViral;
use App\Viralworksheet;
use App\Viralsample;

use App\MiscCovid;
use App\CovidWorksheet;
use App\CovidSample;

use \App\Misc;
use \App\Worksheet;
use \App\Sample;

use Str;


class NodeResultController extends BaseController
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

    public function result(NodeRequest $request)
    {
        if(env('APP_LAB') != 1){
            return response()->json([
              'status' => 'not ok',
              'message' => 'Wrong lab.',
            ], 400);
        }
        $sample_id = $request->input('sample_id');
        $datetested_unix_timestamp = $request->input('test_datetime');
        $datetested = date('Y-m-d', $datetested_unix_timestamp);
        if($datetested == '1970-01-01') $datetested = date('Y-m-d');
        $result = $request->input('result');
        $units = $request->input('units');
        $error = $request->input('test_error');
        $assay = strtolower($request->input('assay_name'));
        $worksheet_id = $request->input('carrier_id');

        if(Str::contains($assay, ['cov'])) $test_type = 'covid';
        else if(Str::contains($assay, ['hiv'])) $test_type = 'vl';
        else if(Str::contains($assay, ['qual'])) $test_type = 'eid';

        if($test_type == 'vl'){
            $result_array = MiscViral::sample_result($result, $error, $units);
            $worksheet = Viralworksheet::find($worksheet_id);
            if(!$worksheet) return $this->plate_not_found();
            if(!is_numeric($sample_id)){
                if($sample_id == "HIV_NEG"){
                    $worksheet->neg_control_result = $result_array['result'];
                    $worksheet->neg_control_interpretation = $result_array['interpretation']; 
                    $worksheet->neg_units = $result_array['units']; 
                }else if($sample_id == "HIV_HIPOS"){
                    $worksheet->highpos_control_result = $result_array['result'];
                    $worksheet->highpos_control_interpretation = $result_array['interpretation'];
                    $worksheet->hpc_units = $result_array['units'];
                }else if($sample_id == "HIV_LOPOS"){
                    $worksheet->lowpos_control_result = $result_array['result'];
                    $worksheet->lowpos_control_interpretation = $result_array['interpretation'];
                    $worksheet->lpc_units = $result_array['units'];
                }
                return $this->plate_control_updated($worksheet, $datetested);
            }
            return $this->sample_updated(Viralsample::class, $sample_id, $result_array, $worksheet_id, $datetested);

        }
        else if($test_type == 'covid'){
            $result_array = MiscCovid::sample_result($result, $error);
            $worksheet = CovidWorksheet::find($worksheet_id);
            if(!$worksheet) return $this->plate_not_found();
            if(!is_numeric($sample_id)){
                $s = strtolower($sample_id);

                if(Str::contains($s, 'neg')){
                    $negative_control = $result_array;
                    $worksheet->neg_control_interpretation = $negative_control['interpretation'] ?? null;
                    $worksheet->neg_control_result = $negative_control['result'] ?? null;
                }
                else if(Str::contains($s, 'pos')){
                    $positive_control = $result_array;
                    $worksheet->pos_control_interpretation = $positive_control['interpretation'] ?? null;
                    $worksheet->pos_control_result = $positive_control['result'] ?? null;
                }
                return $this->plate_control_updated($worksheet, $datetested);
            }
            return $this->sample_updated(CovidSample::class, $sample_id, $result_array, $worksheet_id, $datetested);
        }
        else if($test_type == 'eid'){
            $result_array = Misc::sample_result($result, $error);
            $worksheet = Worksheet::find($worksheet_id);

            if(!$worksheet) return $this->plate_not_found();
            if(!is_numeric($sample_id)){
                $s = strtolower($sample_id);

                if(Str::contains($s, 'neg')){
                    $negative_control = $result_array;
                    $worksheet->neg_control_interpretation = $negative_control['interpretation'] ?? null;
                    $worksheet->neg_control_result = $negative_control['result'] ?? null;
                }
                else if(Str::contains($s, 'pos')){
                    $positive_control = $result_array;
                    $worksheet->pos_control_interpretation = $positive_control['interpretation'] ?? null;
                    $worksheet->pos_control_result = $positive_control['result'] ?? null;
                }
                return $this->plate_control_updated($worksheet, $datetested);
            }
            return $this->sample_updated(Sample::class, $sample_id, $result_array, $worksheet_id, $datetested);
        }
    }


    private function plate_not_found()
    {
        return response()->json([
          'status' => 'not ok',
          'message' => 'Plate could not be found.',
        ], 404);        
    }

    private function plate_already_approved()
    {
        return response()->json([
          'status' => 'not ok',
          'message' => 'Plate has already been approved',
        ], 400);
    }

    private function sample_not_found()
    {
        return response()->json([
          'status' => 'not ok',
          'message' => 'Sample could not be found.',
        ], 404);        
    }

    private function sample_already_approved()
    {
        return response()->json([
          'status' => 'not ok',
          'message' => 'Sample has already been approved',
        ], 400);
    }

    private function sample_plate_mismatch()
    {
        return response()->json([
          'status' => 'not ok',
          'message' => 'Sample plate number does not match the plate number of the request.',
        ], 400);
    }

    private function plate_control_updated($worksheet, $datetested)
    {
        if($worksheet->datereviewed) return $this->plate_already_approved();
        $worksheet->dateuploaded = date('Y-m-d');
        $worksheet->daterun = $datetested;
        $worksheet->status_id = 2;
        $worksheet->pre_update();

        return response()->json([
          'status' => 'ok',
          'message' => 'The plate control has been updated.',
        ], 200);
    }

    private function sample_updated($class_name, $sample_id, $result_array, $worksheet_id, $datetested)
    {
        $sample = $class_name::find($sample_id);
        if(!$sample) return $this->sample_not_found();
        if($sample->dateapproved) return $this->sample_already_approved();
        if($sample->worksheet_id != $worksheet_id) return $this->sample_plate_mismatch();
        $sample->fill($result_array);
        $sample->datemodified = date('Y-m-d');
        $sample->datetested = $datetested;
        $sample->pre_update();

        return response()->json([
          'status' => 'ok',
          'message' => 'The sample result has been updated.',
        ], 200);
    }

}
