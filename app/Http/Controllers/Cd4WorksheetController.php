<?php

namespace App\Http\Controllers;

use App\Cd4Worksheet;
use App\Cd4Sample;
use App\Lookup;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Cd4WorksheetImport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Cd4WorksheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($state=null)
    {
        $data = Lookup::worksheet_lookups();
        $data['worksheets'] = Cd4Worksheet::when($state, function($query) use ($state){
                                            return $query->where('status_id', '=', $state);
                                        })->orderBy('id', 'desc')->paginate(20);
        $data = (object) $data;
        
        return view('tables.cd4-worksheets', compact('data'))->with('pageTitle', 'Worksheets');
    }

    public function state($state=null){
        $data = Lookup::worksheet_lookups();
        $data['worksheets'] = Cd4Worksheet::when($state, function($query) use ($state){
                                        if($state == 1) {
                                            return $query->whereNotNull('reviewedby')->whereNull('reviewedby2');
                                        }
                                    })->orderBy('id', 'desc')->paginate(20);
        $data = (object) $data;
        
        return view('tables.cd4-worksheets', compact('data'))->with('pageTitle', 'Worksheets');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($limit)
    {
        // $rerunsamples = $this->get_samples_for_rerun();

        // if ($rerunsamples == 0) { // No rerun samples are available
            $samples = $this->get_samples_for_run($limit);
            $sampleCount = $samples->count();
            $worksheetCount = Cd4Worksheet::max('id')+1;
            $data['samples'] = $samples;
            $data['worksheet'] = $worksheetCount;
            $data['limit'] = $limit;
            $data = (object) $data;
            // dd($data->samples->first()->patient->patient_name);
            return view('forms.cd4worksheet', compact('data'))->with('pageTitle', "Create Worksheet ($limit)");
        // } else {
            
        // }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except(['_token', 'limit']);
        $data['lab_id'] = env('APP_LAB');
        $data['createdby'] = auth()->user()->id;
        $data['status_id'] = 1;
        $worksheet = new Cd4Worksheet();
        $worksheet->fill($data);
        $worksheet->save();

        $samples = $this->get_samples_for_run($request->input('limit'));
        $sampleData = ['worksheet_id' => $worksheet->id, 'status_id' => 3];
        foreach ($samples as $key => $sample) {
            $sample->fill($sampleData);
            $sample->save();
        }

        return redirect()->route('cd4.worksheet.print', ['worksheet' => $worksheet->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cd4Worksheet  $cd4Worksheet
     * @return \Illuminate\Http\Response
     */
    public function show(Cd4Worksheet $Worksheet)
    {
        $data['worksheet'] = $Worksheet;
        $data['samples'] = $Worksheet->samples;
        $data['view'] = true;
        $data = (object)$data;
        
        return view('forms.cd4worksheet', compact('data'))->with('pageTitle', "Worksheet No. $Worksheet->id Details");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cd4Worksheet  $cd4Worksheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Cd4Worksheet $Worksheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cd4Worksheet  $cd4Worksheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cd4Worksheet $Worksheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cd4Worksheet  $cd4Worksheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cd4Worksheet $Worksheet)
    {
        //
    }

    public function upload(Request $request, Cd4Worksheet $worksheet){
        if ($request->method() == "PUT") {
            $file = $request->upload->path();
            $path = $request->upload->store('public/results/cd4'); 
            /*$data = Excel::load($file, function($reader){
                $reader->toArray();
            })->get();
            // dd($data);
            foreach ($data as $key => $value) {
                try {
                    $daterun = Carbon::parse($value[23]);
                    $daterun = $daterun->toDateString() ?? date('Y-m-d');                
                } catch (Exception $e) {
                    $daterun = null;
                }
                
                $sample = Cd4Sample::find($value[4]);
                if($sample) {
                    if ($value[9] != "") { 
                        $repeatt=2;
                    } else { 
                        $repeatt=1;
                    }

                    if ($value[10] != "") { 
                        $repeatt=2;
                    } else { 
                        $repeatt=1;
                    }

                    if ($value[11] != "") { 
                        $repeatt=2;
                    } else { 
                        $repeatt=1;
                    }
                
                    if ($value[12] != "") { 
                        $repeatt=2;
                    } else { 
                        $repeatt=1;
                    }
               
                    if ($value[21] != "") { 
                        $repeatt=2;
                    } else { 
                        $repeatt=1;
                    }

                    $sample->THelperSuppressorRatio = $value[8];
                    $sample->AVGCD3percentLymph = $value[9];
                    $sample->AVGCD3AbsCnt = $value[10];
                    $sample->AVGCD3CD4percentLymph = $value[11];
                    $sample->AVGCD3CD4AbsCnt = $value[12];
                    $sample->AVGCD3CD8percentLymph = $value[13];
                    $sample->AVGCD3CD8AbsCnt = $value[14];
                    $sample->AVGCD3CD4CD8percentLymph = $value[15];
                    $sample->AVGCD3CD4CD8AbsCnt = $value[16];
                    $sample->CD45AbsCnt = $value[21];
                    $sample->datemodified = date('Y-m-d');
                    $sample->datetested = $daterun;
                    $sample->status_id = 4;
                    $sample->repeatt = $repeatt;
                    // dd($sample);
                    $sample->save();
                }
            }*/

            $c = new Cd4WorksheetImport($worksheet);
            Excel::import($c, $path);
            $worksheet->uploadedby = auth()->user()->id;
            $worksheet->daterun = date('Y-m-d');
            $worksheet->dateuploaded = date('Y-m-d');
            $worksheet->status_id = 2;
            $worksheet->save();
            
            
            if ($worksheet) {
                session(['toast_message' => 'Import done, Results Updated successfully, Please Confirm and Approve the updated results below']);
                return redirect('cd4/worksheet/confirm/'.$worksheet->id);
            } else {
                session(['toast_message' => 'An error occured while trying to update results, please try again later', 'toast_error' => 1]);
                return back();
            }
        } else {
            return view('forms.cd4upload_results', compact('worksheet'))->with('pageTitle', "UPDATE TEST RESULTS FOR WORKSHEET NO $worksheet->id");    
        }
    }

    public function confirm_upload(Cd4Worksheet $worksheet){
        $data = Lookup::worksheet_lookups();
        $data['worksheet'] = $worksheet;
        $data['samples'] = $worksheet->samples;
        $data = (object)$data;
        // dd($data);
        return view('forms.confirm-cd4worksheet', compact('data'))->with('pageTitle', "RESULTS REVIEW (1st) FOR WORKSHEET NO $worksheet->id");
    }

    public function save_upload(Request $request, Cd4Worksheet $worksheet){
        $formData = $request->except(['_method','_token']);
        $id = $formData["id"];
        $AVGCD3percentLymph = $formData["AVGCD3percentLymph"];
        $AVGCD3AbsCnt = $formData["AVGCD3AbsCnt"];
        $AVGCD3CD4percentLymph = $formData["AVGCD3CD4percentLymph"];
        $AVGCD3CD4AbsCnt = $formData["AVGCD3CD4AbsCnt"];
        $CD45AbsCnt = $formData["CD45AbsCnt"];
        $repeatt = $formData["repeatt"];
        $checkbox = $formData["checkbox"] ?? [];

        foreach ($checkbox as $key => $value) {
            $sample = Cd4Sample::find($id[$value]);
            if(isset($sample->approvedby)){
                $sample->approvedby2 = auth()->user()->id;
                $sample->dateapproved2 = date('Y-m-d');
                $sample->status_id = 5;
            } else {
                $sample->approvedby = auth()->user()->id;
                $sample->dateapproved = date('Y-m-d');
            }
            $sample->AVGCD3percentLymph = $AVGCD3percentLymph[$value];
            $sample->AVGCD3AbsCnt = $AVGCD3AbsCnt[$value];
            $sample->AVGCD3CD4percentLymph = $AVGCD3CD4percentLymph[$value];
            $sample->AVGCD3CD4AbsCnt = $AVGCD3CD4AbsCnt[$value];
            $sample->CD45AbsCnt = $CD45AbsCnt[$value];
            $sample->repeatt = $repeatt[$value];
            if($repeatt[$value] == 2){
                $sample->AVGCD3CD4percentLymph = null;
                $sample->AVGCD3CD4AbsCnt = null;
            }

            $sample->save();
            if($sample->repeatt == 1){
                $repeatSample = new Cd4Sample();
                $repeatSample->parentid = $sample->id;
                $repeatSample->patient_id = $sample->patient_id;
                $repeatSample->facility_id = $sample->facility_id;
                $repeatSample->lab_id = $sample->lab_id;
                $repeatSample->serial_no = $sample->serial_no;
                $repeatSample->amrs_location = $sample->amrs_location;
                $repeatSample->provider_identifier = $sample->provider_identifier;
                $repeatSample->order_no = $sample->order_no;
                $repeatSample->save();
            }
        }

        if(isset($worksheet->reviewedby)){
            $worksheet->reviewedby2 = auth()->user()->id;    
            $worksheet->datereviewed2 = date('Y-m-d');
            $worksheet->status_id = 3;
        } else {
            $worksheet->reviewedby = auth()->user()->id;    
            $worksheet->datereviewed = date('Y-m-d');
        }
        $worksheet->save();

        if($worksheet->status_id == 3)
            return redirect('cd4/worksheet');

        return redirect('cd4/worksheet/confirm/'.$worksheet->id);
    }

    public function reverse_upload(Cd4Worksheet $worksheet)
    {        
        if(!in_array($worksheet->status_id, [3,7]) ){
            session(['toast_error' => 1, 'toast_message' => 'You cannot reverse results for this worksheet.']);
            return back();
        }

        $nullable_fields = ['THelperSuppressorRatio', 'AVGCD3percentLymph', 'AVGCD3AbsCnt', 'AVGCD3CD4percentLymph', 'AVGCD3CD4AbsCnt', 'AVGCD3CD8percentLymph', 'AVGCD3CD8AbsCnt', 'AVGCD3CD4CD8percentLymph', 'AVGCD3CD4CD8AbsCnt', 'CD45AbsCnt', 'result', 'datemodified', 'datetested', 'datedispatched', 'printedby', 'sent_email'];

        foreach ($worksheet->sample as $sample) {
            foreach ($nullable_fields as $nullable_field) {
                $sample->$nullable_field = null;
            }
            if($sample->repeatt == 1){
                $sample->remove_rerun();
                $sample->repeatt = 0;
            }
            $sample->status_id = 1;
            $sample->save();
        }

        $worksheet->status_id = 1;
        $worksheet->daterun = $worksheet->dateuploaded = $worksheet->uploadedby = $worksheet->datereviewed = $worksheet->reviewedby = $worksheet->datereviewed2 = $worksheet->reviewedby2 = null;
        $worksheet->save();
        session(['toast_message' => 'Reversal was successful']);
        return back();
    }

    public function cancel_upload(Cd4Worksheet $worksheet)
    {        
        if(!in_array($worksheet->status_id, [2]) ){
            session(['toast_error' => 1, 'toast_message' => 'You cannot cancel the upload for this worksheet.']);
            return back();
        }

        $nullable_fields = ['THelperSuppressorRatio', 'AVGCD3percentLymph', 'AVGCD3AbsCnt', 'AVGCD3CD4percentLymph', 'AVGCD3CD4AbsCnt', 'AVGCD3CD8percentLymph', 'AVGCD3CD8AbsCnt', 'AVGCD3CD4CD8percentLymph', 'AVGCD3CD4CD8AbsCnt', 'CD45AbsCnt', 'result', 'datemodified', 'datetested', 'datedispatched', 'printedby', 'sent_email'];

        foreach ($worksheet->sample as $sample) {
            foreach ($nullable_fields as $nullable_field) {
                $sample->$nullable_field = null;
            }
            if($sample->repeatt == 1){
                $sample->remove_rerun();
                $sample->repeatt = 0;
            }
            $sample->status_id = 1;
            $sample->save();
        }

        $worksheet->status_id = 1;
        $worksheet->daterun = $worksheet->dateuploaded = $worksheet->uploadedby = $worksheet->datereviewed = $worksheet->reviewedby = $worksheet->datereviewed2 = $worksheet->reviewedby2 = null;
        $worksheet->save();
        session(['toast_message' => 'Reversal was successful']);
        return back();
    }

    public function print(Cd4Worksheet $worksheet) {
        return view('worksheets.cd4', compact('worksheet'))->with('pageTitle', 'Worksheets');
    }

    public function cancel(Cd4Worksheet $worksheet){
        if($worksheet->status_id != 1){
            session(['toast_message' => 'The worksheet is not eligible to be cancelled.']);
            session(['toast_error' => 1]);
            return back();
        }
        $sample_array = Cd4Sample::select('id')->where('worksheet_id', $worksheet->id)->get()->pluck('id')->toArray();
        Cd4Sample::whereIn('id', $sample_array)->update(['worksheet_id' => null, 'result' => null, 'status_id' => 1]);
        $worksheet->status_id = 4;
        $worksheet->datecancelled = date("Y-m-d");
        $worksheet->cancelledby = auth()->user()->id;
        $worksheet->save();

        session(['toast_message' => 'The worksheet has been cancelled.']);
        return redirect("/cd4/worksheet");
    }

    public function get_samples_for_rerun(){
        return Cd4Sample::selectRaw("COUNT(*) as reruns")
                            ->whereNull('worksheet_id')
                            ->where('receivedstatus', '<>', 2)
                            ->where('status_id', '=', 1)
                            ->where('run', '>', 1)
                            ->where('parentid', '>', 0)->first()->reruns;

    }

    public function get_samples_for_run($limit){
        return Cd4Sample::whereNull('worksheet_id')->where('receivedstatus', '<>', 2)->where('status_id', '=', 1)
                                    ->orderBy('datereceived', 'asc')->orderBy('parentid', 'desc')->orderBy('id', 'asc')
                                    ->limit($limit)->get();
    }

    public function search(Request $request){
        $search = $request->input('search');
        $worksheets = Cd4Worksheet::whereRaw("id like '" . $search . "%'")->paginate(10);
        $worksheets->setPath(url()->current());
        return $worksheets;
    }
}
