<?php

namespace App\Api\V1\Controllers;

use App\Http\Controllers\Controller;
use App\Api\V1\Requests\CovidRequest;

use App\CovidModels\CovidPatient;
use App\CovidModels\CovidSample;
use App\CovidModels\CovidTravel;
use App\CovidConsumption;
use App\CovidKit;
use App\CovidConsumptionDetail;
use App\Facility;
use App\ViewFacility;
use App\CovidModels\Lab;
use GuzzleHttp\Client;
use DB;
use Exception;

use App\CovidTestModels\CovidPatient as TestPatient;
use App\CovidTestModels\CovidSample as TestSample;


/**
 * Covid Controller resource representation.
 * @Parameters({
 *      @Parameter("id", description="The id of the sample.", type="integer", required=true),
 * })
 *
 * @Resource("Covid", uri="/covid")
 */
class CovidController extends Controller
{
  
    /**
     * Display a listing of the resource.
     * The response has links to navigate to the rest of the data.
     *
     *
     * @Get("{?page}")
     * @Response(200, body={
     *      "data": {
     *          "sample": {
     *              "id": "int",    
     *              "patient": {
     *                  "id": "int",
     *              }    
     *          }
     *      }
     * })
     */
    public function index(CovidRequest $request)
    {
        $apikey = $request->headers->get('apikey');
        if(!$apikey) $apikey = $request->input('apikey');
        if(!$apikey) abort(401, 'apikey is required');
        $lab = Lab::where(['apikey' => $apikey])->whereNotNull('apikey')->first();
        if(!$lab) abort(401);

        $sample_class = CovidSample::class;
        if(\Str::contains(url()->current(), 'test')) $sample_class = TestSample::class;

        return $sample_class::with(['patient'])
            ->where('repeatt', 0)
            ->when($lab->id != 11, function($query) use ($lab){
                return $query->where('lab_id', $lab->id);
            })
            ->paginate();
    }

    
    /**
     * Register a resource.
     *
     * @Post("/")
     * @Request({
     *      "case_id": "int, case number", 
     *      "patient_id": "patient identifier on your end", 
     *      "identifier_type": "int, identifier type", 
     *      "identifier": "string, actual identifier, National ID... ", 
     *      "patient_name": "string", 
     *      "justification": "int, reason for the test", 
     *      "nationality": "int, refer to ref tables", 
     *      "facility": "string, MFL Code or DHIS Code of the facility if any", 
     *      "county": "string", 
     *      "subcounty": "string", 
     *      "ward": "string", 
     *      "residence": "string", 
     *      "sex": "string, M for male, F for female", 
     *      "health_status": "int, health status", 
     *      "residence": "string", 
     *      "date_symptoms": "date", 
     *      "date_admission": "date", 
     *      "date_isolation": "date", 
     *      "date_death": "date", 
     *      
     *      "specimen_id": "int, id of the specimen on your end", 
     *      "lab_id": "int, refer to ref tables", 
     *      "test_type": "int", 
     *      "occupation": "string", 
     *      "temperature": "int, temp in Celcius", 
     *      "sample_type": "int, refer to ref tables", 
     *      "symptoms": "array of integers, refer to ref tables", 
     *      "observed_signs": "array of integers, refer to ref tables", 
     *      "underlying_conditions": "array of integers, refer to ref tables", 
     * })
     * @Response(201)
     */
    public function store(CovidRequest $request)
    {
        $apikey = $request->headers->get('apikey');
        if(!$apikey) $apikey = $request->input('apikey');
        if(!$apikey) abort(401, 'apikey is required');
        $lab = Lab::where(['apikey' => $apikey])->whereNotNull('apikey')->first();
        // $lab = Lab::where(['apikey' => $apikey])->first();
        if(!$lab) abort(401);

        $patient_class = CovidPatient::class;
        $sample_class = CovidSample::class;
        $patient_column = 'nhrl_patient_id';
        $sample_column = 'nhrl_sample_id';

        if(\Str::contains(url()->current(), 'test')){
            $patient_class = TestPatient::class;
            $sample_class = TestSample::class;
        }
        if($lab->id == 11){
            $patient_column = 'cif_patient_id';
            $sample_column = 'cif_sample_id';            
        }
        

        // $p = new CovidPatient;
        // if(\Str::contains(url()->current(), 'test')) $p = new TestPatient;

        // $p = $patient_class::where($request->only('national_id'))->whereNotNull('national_id')->first();
        // if(!$p) $p = $patient_class::where($request->only(['identifier']))->where($patient_column, $request->input('patient_id'))->first();
        $p = new $patient_class;
        $p->fill($request->only(['nationality', 'national_id', 'identifier_type_id', 'identifier', 'patient_name', 'justification', 'county', 'subcounty', 'phone_no', 'ward', 'residence', 'dob', 'sex', 'occupation', 'health_status', 'date_symptoms', 'date_admission', 'date_isolation', 'date_death']));
        $p->$patient_column = $request->input('patient_id');
        // if($lab->id == 11) $p->cif_patient_id = $request->input('patient_id');
        // else{
        //     $p->nhrl_patient_id = $request->input('patient_id');
        // }
        $p->facility_id = Facility::locate($request->input('facility'))->first()->id ?? null;
        if($p->county){            
            $county = DB::table('countys')->where('name', $p->county)->first();
            $p->county_id = $county->id ?? null;
        }
        try {
            $p->save();
        } catch (Exception $e) {
            return response()->json([
              'status' => 'Bad Request',
              'error' => $e->getMessage(),
              'patient' => null,
              'sample' => null,
            ], 400);
            
        }
        $s = null;

        // $s = new CovidSample;
        // if(\Str::contains(url()->current(), 'test')) $s = new TestSample;
        //$s = $sample_class::where(['lab_id' => $lab->id, $sample_column => $request->input('specimen_id'), 'datecollected' => $request->input('datecollected')])->whereNotNull($sample_column)->first();
        if(!$s && $request->input('identifier')) $s = $sample_class::where(['lab_id' => $lab->id, $sample_column => $request->input('identifier'), 'datecollected' => $request->input('datecollected')])->first();
        if(!$s) $s = $sample_class::where(['lab_id' => $lab->id, 'patient_id' => $p->id, 'datecollected' => $request->input('datecollected')])->first();
        if(!$s) $s = new $sample_class;
        // $s = new $sample_class;
        $s->fill($request->only(['lab_id', 'border_point', 'test_type', 'health_status', 'symptoms', 'temperature', 'observed_signs', 'underlying_conditions', 'result', 'age', 'age_unit', 'datecollected', 'datereceived', 'datetested']));
        $s->patient_id = $p->id;
        $s->$sample_column = $request->input('identifier');
        // if($lab->id == 11) $s->cif_sample_id = $request->input('specimen_id');
        // else{
        //     $s->nhrl_sample_id = $request->input('specimen_id');
        // }

        if($request->input('case_id')) $s->$sample_column = $request->input('case_id');

        $s->datedispatched = $s->datetested;
        
        $s->lab_id = $lab->id;
        $s->save();

        return response()->json([
          'status' => 'received',
          'patient' => $p,
          'sample' => $s,
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
    public function show(CovidRequest $request, $id)
    {
        $apikey = $request->headers->get('apikey');
        if(!$apikey) $apikey = $request->input('apikey');
        if(!$apikey) abort(401, 'apikey is required');
        $lab = Lab::where(['apikey' => $apikey])->whereNotNull('apikey')->first();
        if(!$lab) abort(401);

        $column = 'nhrl_sample_id';
        if($lab->id == 11) $column = 'cif_sample_id';

        $sample_class = CovidSample::class;
        if(\Str::contains(url()->current(), 'test')) $sample_class = TestSample::class;

        // $s = CovidSample::findOrFail($id);
        $s = $sample_class::where([$column => $id])->first();
        if(!$s) abort(404);
        $s->load(['patient']);

        return response()->json([
          'sample' => $s,
        ], 200);
    }


    public function update(CovidRequest $request, $id)
    {
        
    }


    public function destroy(Facility $facility)
    {
        //
    }


    
    /**
     * Register multiple resources.
     *
     * @Post("/save_multiple")
     * @Request({
     *      "samples": {{
     *      "case_id": "int, case number", 
     *      "identifier_type": "int, identifier type", 
     *      "identifier": "string, actual identifier, National ID... ", 
     *      "nationality": "int, refer to ref tables", 
     *      "patient_name": "string", 
     *      "justification": "int, reason for the test", 
     *      "facility": "string, MFL Code or DHIS Code of the facility if any", 
     *      "county": "string", 
     *      "subcounty": "string", 
     *      "ward": "string", 
     *      "residence": "string", 
     *      "sex": "string, M for male, F for female", 
     *      "health_status": "int, health status", 
     *      "residence": "string", 
     *      "date_symptoms": "date", 
     *      "date_admission": "date", 
     *      "date_isolation": "date", 
     *      "date_death": "date", 
     *      
     *      "lab_id": "int, refer to ref tables", 
     *      "test_type": "int", 
     *      "occupation": "string", 
     *      "temperature": "int, temp in Celcius", 
     *      "sample_type": "int, refer to ref tables", 
     *      "symptoms": "array of integers, refer to ref tables", 
     *      "observed_signs": "array of integers, refer to ref tables", 
     *      "underlying_conditions": "array of integers, refer to ref tables", 
     *      }}
     * })
     * @Response(201)
     */
    public function save_multiple(CovidRequest $request)
    {
        $lab = Lab::where(['apikey' => $request->headers->get('apikey')])->first();
        if(!$lab) abort(401);
        if(\Str::contains(url()->current(), 'test')){
            config(['database.default' => 'test']);
        }

        $input_samples = $request->input('samples', []);
        $patients = $samples = [];

        $blank = null;

        foreach ($input_samples as $key => $row_array) {

            foreach ($row_array as $key => $value) {
                if(is_array($value)) continue;
                if(trim($value) == '') $row_array[$key] = null;
            }

            $p = CovidPatient::where('cif_patient_id', $row_array['patient_id'])->first();
            if(!$p) $p = new CovidPatient;            
            $p->fill(array_only($row_array, ['case_id', 'nationality', 'identifier_type_id', 'identifier', 'patient_name', 'justification', 'county', 'subcounty', 'ward', 'residence', 'dob', 'sex', 'occupation', 'health_status', 'date_symptoms', 'date_admission', 'date_isolation', 'date_death']));
            $p->cif_patient_id = $row_array['patient_id'] ?? null;
            if(!$p->identifier){
                file_put_contents(public_path('bad_request.txt'), print_r($request->all(), true));
                $blank = $p;
                continue;
            }
            if(isset($row_array['facility'])) $p->facility_id = Facility::locate($row_array['facility'])->first()->id ?? null;
            $p->save();

            $patients[] = $p;

            $s = CovidSample::where(['patient_id' => $p->id, 'cif_sample_id' => $row_array['specimen_id']])->first();
            if(!$s) $s = new CovidSample;
            $s->fill(array_only($row_array, ['lab_id', 'test_type', 'health_status', 'symptoms', 'temperature', 'observed_signs', 'underlying_conditions', 'datecollected', ]));
            $s->patient_id = $p->id;
            $s->cif_sample_id = $row_array['specimen_id'] ?? null;
            $s->lab_id = $lab->id;
            $s->save();

            $samples[] = $s;
        }

        // if($blank) abort(400, "Patient ID {$blank->cif_patient_id} does not have an identifier.");

        return response()->json([
          'status' => 'ok',
          'patients' => $patients,
          'samples' => $samples,
        ], 201);
    }




    public function results(CovidRequest $request, $id)
    {
        $apikey = $request->headers->get('apikey');
        $actual_key = env('COVID_NHRL_KEY');
        if($actual_key != $apikey) abort(401);

        $covidSample = CovidSample::findOrFail($id);
        if($covidSample->lab_id != 7) abort(403);

        $covidSample->result = $request->input('result');
        $covidSample->receivedstatus = $request->input('received_status');
        $covidSample->datetested = $request->input('date_tested');
        $covidSample->save();

        return response()->json([
          'status' => 'ok',
        ], 200);

    }

    
    /**
     * Post complete results.
     *
     * @Post("/nhrl")
     * @Request({
     *      "case_id": "int, case number", 
     *      "identifier_type": "int, identifier type", 
     *      "identifier": "string, actual identifier, National ID... ", 
     *      "patient_name": "string", 
     *      "justification": "int, reason for the test, refer to ref tables", 
     *      "facility": "string, MFL Code or DHIS Code of the facility if any", 
     *      "county": "string", 
     *      "subcounty": "string", 
     *      "ward": "string", 
     *      "residence": "string", 
     *      "sex": "string, M for male, F for female", 
     *      "health_status": "int, health status", 
     *      "residence": "string", 
     *      "date_symptoms": "date", 
     *      "date_admission": "date", 
     *      "date_isolation": "date", 
     *      "date_death": "date", 
     *      
     *      "lab_id": "int, refer to ref tables, 7 NHRL, 11 NIC", 
     *      "test_type": "int, refer to ref tables", 
     *      "occupation": "string", 
     *      "temperature": "int, temp in Celcius", 
     *      "sample_type": "int, refer to ref tables", 
     *      "symptoms": "array of integers, refer to ref tables", 
     *      "observed_signs": "array of integers, refer to ref tables", 
     *      "underlying_conditions": "array of integers, refer to ref tables", 
     *      "datecollected": "date",  
     *      "datereceived": "date",  
     *      "datetested": "date",  
     *      "datedispatched": "date",  
     *      "receivedstatus": "int, refer to ref tables", 
     *      "result": "int, refer to ref tables",
     * }, headers={ "apikey": "secret key" })
     * @Response(201)
     */
    public function nhrl(CovidRequest $request)
    {
        $apikey = $request->headers->get('apikey');
        $actual_key = env('COVID_NHRL_KEY');
        if($actual_key != $apikey) abort(401);

        $lab_id = $request->input('lab_id');
        if(!in_array($lab_id, [7,11])) abort(400);

        $p = new CovidPatient;
        $p->fill($request->only(['case_id', 'nationality', 'identifier_type_id', 'identifier', 'patient_name', 'justification', 'county', 'subcounty', 'ward', 'residence', 'dob', 'sex', 'occupation', 'health_status', 'date_symptoms', 'date_admission', 'date_isolation', 'date_death']));
        $p->nhrl_patient_id = $request->input('patient_id');
        $p->facility_id = Facility::locate($request->input('facility'))->first()->id ?? null;
        $p->save();

        $s = new CovidSample;
        $s->fill($request->only(['lab_id', 'test_type', 'health_status', 'symptoms', 'temperature', 'observed_signs', 'underlying_conditions', 'datecollected', 'datereceived', 'datetested', 'datedispatched', 'receivedstatus', 'result']));
        $s->patient_id = $p->id;
        $s->nhrl_sample_id = $request->input('specimen_id');
        $s->save();

        return response()->json([
          'status' => 'ok',
          'patient' => $p,
          'sample' => $s,
        ], 201);
    }



    public function cif_samples(CovidRequest $request)
    {
        $apikey = $request->headers->get('apikey');
        if(!$apikey) $apikey = $request->input('apikey');
        if(!$apikey) abort(401, 'apikey is required');
        $lab = Lab::where(['apikey' => $apikey])->whereNotNull('apikey')->first();
        // $lab = Lab::where(['apikey' => $apikey])->first();
        if(!$lab || $lab->id != 12) abort(401);

        $samples = CovidSample::where(['synched' => 0, 'lab_id' => 11])->where('created_at', '>', date('Y-m-d', strtotime('-7 days')))->whereNull('original_sample_id')->whereNull('receivedstatus')->with(['patient'])->get();
        // $samples = CovidSample::whereNotNull('cif_sample_id')->with(['patient'])->get();

        foreach ($samples as $key => $sample) {
            $data[] = [
                'cif_patient_id' => $sample->patient->cif_patient_id,
                'cif_id' => $sample->cif_sample_id,
                'identifier' => $sample->patient->identifier,
                'patient_name' => $sample->patient->patient_name,
                'phone_no' => $sample->patient->phone_no,
                'justification' => $sample->patient->justification,
                'county' => $sample->patient->county,
                'subcounty' => $sample->patient->subcounty,
                'ward' => $sample->patient->ward,
                'residence' => $sample->patient->residence,
                'dob' => $sample->patient->dob,
                'sex' => $sample->patient->gender,
                'occupation' => $sample->patient->occupation,
                'health_status' => $sample->patient->health_status,
                'date_symptoms' => $sample->patient->date_symptoms,
                'date_admission' => $sample->patient->date_admission,
                'date_isolation' => $sample->patient->date_isolation,
                'date_death' => $sample->patient->date_death,

                'test_type' => $sample->test_type,
                'age' => $sample->age,
                'datecollected' => $sample->datecollected,
            ];
        }
        return response()->json([
          'status' => 'ok',
          'samples' => $data,
        ], 200);
    }

    public function ku_consumption(CovidRequest $request)
    {
        $consumptions = json_decode($request->input('consumptions'));
        $consumptions_array = [];
        foreach ($consumptions as $key => $consumption) {
            $consumption = (object) $consumption;
            $existing = CovidConsumption::existing($consumption->start_of_week, $consumption->lab_id)->first();
            if ($existing){
                $consumptions_array[] = [
                                    'original_id' => $consumption->id,
                                    'national_id' => $existing->national_id ?? NULL
                                ];
                continue;
            }
                        
            DB::beginTransaction();
            try
            {
                // Inserting the covid consumptions
                $db_consumption = new CovidConsumption;
                $consumptions_data = get_object_vars($consumption);
                $db_consumption->fill($consumptions_data);
                unset($db_consumption->id);
                unset($db_consumption->details);
                $db_consumption->save();

                // Inserting the covid details
                foreach ($consumption->details as $key => $detail) {
                    $detail = (object)$detail;
                    if (null !== $detail->kit) {
                        $detailKit = (object)$detail->kit;
                        $kit = CovidKit::withTrashed()->where('material_no', $detailKit->material_no)->first();
                        $db_detail = new CovidConsumptionDetail;
                        $detail_data = get_object_vars($detail);
                        $db_detail->fill($detail_data);
                        $db_detail->consumption_id = $db_consumption->id;
                        $db_detail->kit_id = $kit->material_no;
                        unset($db_detail->id);
                        unset($db_detail->kit);
                        $save = $db_detail->save();
                    }
                }
                DB::commit();               
                $consumptions_array[] = [
                                    'original_id' => $consumption->id,
                                    'national_id' => $db_consumption->national_id ?? NULL
                                ];
            } catch (Exception $e) {
                DB::rollback();
                return response()->json([
                        'error' => true,
                        'message' => 'Insert failed: Unexpected error occured while inserting lab' . json_decode($request->input('lab')) . ' data.',
                        'code' => 500,
                        'detailed' => $e
                    ], 500);
            }
        }
        return response()->json($consumptions_array);
    }
}

