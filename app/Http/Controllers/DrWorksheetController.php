<?php

namespace App\Http\Controllers;

use App\DrExtractionWorksheet;
use App\DrWorksheet;
use App\DrPatient;
use App\DrSample;
use App\DrSampleView;
use App\User;

use App\Lookup;
use App\MiscDr;
use App\MiscDrOld;

use Excel;
use Illuminate\Http\Request;

class DrWorksheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($state=0, $date_start=NULL, $date_end=NULL, $worksheet_id=NULL)
    {
        $worksheets = DrWorksheet::with(['creator', 'reviewer'])->withCount(['sample'])
            ->when($state, function ($query) use ($state){
                return $query->where('status_id', $state);
            })
            ->when($date_start, function($query) use ($date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate('dr_worksheets.created_at', '>=', $date_start)
                    ->whereDate('dr_worksheets.created_at', '<=', $date_end);
                }
                return $query->whereDate('dr_worksheets.created_at', $date_start);
            })
            ->where('status_id', '!=', 4)
            ->orderBy('id', 'desc')
            ->paginate();

        $data = Lookup::get_dr();
        $data['worksheets'] = $worksheets;
        $data['myurl'] = url('dr_worksheet/index/' . $state . '/');
        return view('tables.dr_worksheets', $data)->with('pageTitle', 'Worksheets (Bulk Templates)');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    public function create($extraction_worksheet_id=null)
    {
        $samples = DrSampleView::whereNull('worksheet_id')
                        ->where(['receivedstatus' => 1, 'control' => 0, 'passed_gel_documentation' => 1])
                        // ->orderBy('control', 'desc')
                        ->when($extraction_worksheet_id, function($query) use ($extraction_worksheet_id){
                            return $query->where('extraction_worksheet_id', $extraction_worksheet_id);
                        })
                        ->orderBy('run', 'desc')
                        ->orderBy('id', 'asc')
                        ->when(true, function($query){
                            if(env('APP_LAB') == 1) return $query; 
                            return $query->limit(16);
                        })
                        ->get();

        $data = Lookup::get_dr();
        $data['samples'] = $samples;
        $data['create'] = $samples->count();
        $data['extraction_worksheet_id'] = $extraction_worksheet_id;

        // $data = array_merge($data, MiscDr::get_worksheet_samples(null, 30));
        return view('forms.dr_worksheets', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $limit = 16;
        $c = $request->input('control_samples');
        $extraction_worksheet_id = $request->input('extraction_worksheet_id');
        $selected_samples = $request->input('samples');
        if($c) $limit = 14;

        $samples = DrSampleView::whereNull('worksheet_id')
                        ->where(['receivedstatus' => 1, 'control' => 0, 'passed_gel_documentation' => 1])
                        // ->orderBy('control', 'desc')
                        ->when($selected_samples, function($query) use($selected_samples){
                            return $query->whereIn('id', $selected_samples); 
                        })
                        ->when($extraction_worksheet_id, function($query) use ($extraction_worksheet_id){
                            return $query->where('extraction_worksheet_id', $extraction_worksheet_id);
                        })
                        ->orderBy('run', 'desc')
                        ->orderBy('id', 'asc')
                        ->limit($limit)
                        ->get();

        if(!$samples->count()){
            session(['toast_error' => 1, 'toast_message' => 'The sequencing woksheet could not be created.']);
            return back();
        }

        $dr_worksheet = new DrWorksheet;
        $dr_worksheet->fill($request->except(['_token', 'samples', 'control_samples']));
        $dr_worksheet->save();
        // $samples = $data['samples'];

        foreach ($samples as $s) {
            $sample = DrSample::find($s->id);
            $sample->worksheet_id = $dr_worksheet->id;
            $sample->save();
        }

        if($c){
            $positive_control = DrSample::create(['worksheet_id' => $dr_worksheet->id, 'patient_id' => 0, 'control' => 2]);
            $negative_control = DrSample::create(['worksheet_id' => $dr_worksheet->id, 'patient_id' => 0, 'control' => 1]);
        }

        $ext = DrExtractionWorksheet::find($extraction_worksheet_id);
        if(!$ext) $ext = $samples->first()->extraction_worksheet;
        if($ext && !$ext->sequencing){
            $ext->status_id = 3;
            $ext->save();
        }

        if(env('APP_LAB') == 7) return $this->download($dr_worksheet);
        return redirect('dr_worksheet/print/' . $dr_worksheet->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DrWorksheet  $drWorksheet
     * @return \Illuminate\Http\Response
     */
    public function show(DrWorksheet $drWorksheet, $print=false)
    {
        $data = Lookup::get_dr();
        // $data['samples'] = $drWorksheet->sample;
        $data['samples'] = DrSample::where(['worksheet_id' => $drWorksheet->id])->orderBy('id', 'asc')->get();
        $data['date_created'] = $drWorksheet->my_date_format('created_at', "Y-m-d");
        if($print) $data['print'] = true;
        return view('worksheets.dr_worksheet', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DrWorksheet  $drWorksheet
     * @return \Illuminate\Http\Response
     */
    public function print(DrWorksheet $worksheet)
    {
        return $this->show($worksheet, true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DrWorksheet  $drWorksheet
     * @return \Illuminate\Http\Response
     */
    public function edit(DrWorksheet $drWorksheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DrWorksheet  $drWorksheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DrWorksheet $drWorksheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DrWorksheet  $drWorksheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(DrWorksheet $drWorksheet)
    {
        //
    }

    /**
     * Download the specified resource as csv.
     *
     * @param  \App\DrWorksheet $worksheet
     * @return \Illuminate\Http\Response
     */
    public function download(DrWorksheet $worksheet)
    {
        $samples = DrSample::with(['patient'])->where(['worksheet_id' => $worksheet->id])->get();
        $data = [];

        foreach ($samples as $key => $sample) {
            $data[] = [
                'NAT ID' => $sample->patient->nat,
                'Sample Number' => $sample->mid,
                'Patient CCC' => $sample->patient->patient,
                'Project Name' => Lookup::retrieve_val('dr_projects', $sample->project),
                'Full Name' => $sample->patient->patient_name,
                'DOB' => $sample->patient->dob,
                'Sex' => $sample->patient->gender,
                'Date of Sample Collection' => $sample->datecollected,
                'Sample Type' => Lookup::retrieve_val('sample_types', $sample->sampletype),
                'Most Current HIV VL Result (copies/mL)' => $sample->vl_result1,
                'Most Current HIV VL Result Date' => $sample->vl_date_result1,
                'Patient Regimen' => Lookup::retrieve_val('prophylaxis', $sample->prophylaxis),
                'Most Recent CD4 Count' => $sample->cd4_result,
                'Patient Current Age' => $sample->age,
                'Amount' => $sample->sample_amount,
                'Amount Unit' => Lookup::retrieve_val('amount_units', $sample->amount_unit),
                'Container Type' => Lookup::retrieve_val('container_types', $sample->container_type),
                'Location Barcode' => '',
            ];
        }

        $filename = 'bulk_template_' . $worksheet->id;

        return MiscDr::csv_download($data, $filename);
    }

    public function abfiles(DrWorksheet $worksheet)
    {
        $samples = DrSample::with(['patient'])->where(['worksheet_id' => $worksheet->id])->get();
        $primers = ['F1', 'F2', 'F3', 'R1', 'R2', 'R3'];
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $data = [];
        $row_key = 0;
        $column = 1;
        $created_at = $worksheet->my_date_format('created_at', "Y-m-d");

        foreach ($samples as $sample_key => $sample) {
            if(!isset($rows[$row_key])){
                $row_key = 0;
                $column++;
            }

            foreach ($primers as $key => $primer) {
                $data[] = [
                    'Lab ID' => $sample->id,
                    'Sample Number' => $sample->mid,
                    'Patient CCC' => $sample->patient->patient,
                    'Primer Label' => $sample->mid . '-Seq' . $primer . '_' . $rows[$row_key] . $column . '_' . $created_at,
                ];
            }
            $row_key++;
        }
        $filename = 'ab1_file_names_' . $worksheet->id;

        return MiscDr::csv_download($data, $filename);
    }


    public function upload(DrWorksheet $worksheet)
    {
        $worksheet->load(['creator']);
        $users = User::where('user_type_id', 1)->get();
        $data = ['worksheet' => $worksheet, 'users' => $users, 'type' => 'dr'];
        if(session('toast_error')) $data['upload_errors'] = session('upload_errors');
        return view('forms.upload_dr_results', $data)->with('pageTitle', 'Worksheet Upload');        
    }

    public function save_results(Request $request, DrWorksheet $worksheet)
    {
        $worksheet->fill($request->except(['_token', 'upload']));
        $file = $request->upload->path();
        
        $zip = new \ZipArchive;
        $path = storage_path('app/public/results/dr/' . $worksheet->id . '/');
        if(is_dir($path)) MiscDr::delete_folder($path);
        mkdir($path, 0777, true);

        $p = $request->upload->store('public/results/dr/' . $worksheet->id );

        if($zip->open($file) === TRUE){
            $zip->extractTo($path);
            $zip->close();

            $data = MiscDrOld::get_worksheet_files($worksheet);

            if($data['errors']){
                session(['upload_errors' => $data['errors'], 'toast_error' => 1, 'toast_message' => 'The upload has errors.']);
                return back();
            }

            $worksheet->save();
            session(['toast_message' => 'The worksheet results has been uploaded.']);
        }
        else{
            session([
                'toast_message' => 'The worksheet results could not be uploaded. Please try again.', 
                'toast_error' => 1
            ]);
            return back();
        }
        $worksheet->sample()->update(['datetested' => $worksheet->dateuploaded]);
        
        return redirect('dr_worksheet');
    }

    public function cancel(DrWorksheet $worksheet)
    {
        if($worksheet->status_id != 1){
            session(['toast_message' => 'The worksheet is not eligible to be cancelled.']);
            session(['toast_error' => 1]);
            return back();
        }
        DrSample::where('worksheet_id', $worksheet->id)->update(['worksheet_id' => null, 'datetested' => null, ]);
        $worksheet->dateuploaded = $worksheet->uploadedby = null;
        $worksheet->datecancelled = date('Y-m-d');
        $worksheet->cancelledby = auth()->user()->id;
        $worksheet->status_id = 4;
        $worksheet->save();

        session(['toast_message' => 'The worksheet has been cancelled.']);
        return redirect('dr_worksheet');
    }


    public function cancel_upload(DrWorksheet $worksheet)
    {
        if($worksheet->status_id != 2){
            session(['toast_message' => 'The worksheet upload cannot be reversed.']);
            session(['toast_error' => 1]);
            return back();
        }

        if($worksheet->uploadedby != auth()->user()->id && auth()->user()->user_type_id != 0){
            session(['toast_message' => 'Only the user who uploaded the results can reverse the upload.']);
            session(['toast_error' => 1]);
            return back();
        }

        $path = storage_path('app/public/results/dr/' . $worksheet->id . '/');
        MiscDr::delete_folder($path);
        $worksheet->status_id = 1;
        $worksheet->save();
        session(['toast_message' => 'The worksheet upload has been reversed.']);
        return redirect('dr_worksheet/upload/' . $worksheet->id);
    }

    public function approve_results(DrWorksheet $worksheet)
    {
        $data = Lookup::get_dr();
        $data['samples'] = DrSampleView::where(['worksheet_id' => $worksheet->id])->orderBy('id', 'asc')->get();
        $data['worksheet'] = $worksheet;
        return view('tables.confirm_dr_results', $data);
    }

    public function approve(Request $request, DrWorksheet $worksheet)
    {
        $double_approval = Lookup::$double_approval;
        $approved = $request->input('approved');
        $cns = $request->input('cns');
        $rerun = $request->input('rerun');

        $today = date('Y-m-d');
        $approver = auth()->user()->id;

        if(in_array(env('APP_LAB'), $double_approval) && $worksheet->reviewedby && !$worksheet->reviewedby2 && $worksheet->reviewedby != $approver){                
            $data = [
                'approvedby2' => $approver,
                'dateapproved2' => $today,
            ];              
            $w_data = [
                'reviewedby2' => $approver,
                'datereviewed2' => $today,
            ];
            $column = 'dateapproved2';
        }
        else{
            $data = [
                'approvedby' => $approver,
                'dateapproved' => $today,
            ];             
            $w_data = [
                'reviewedby' => $approver,
                'datereviewed' => $today,
            ];
            $column = 'dateapproved';
        }

        if(in_array(env('APP_LAB'), $double_approval)){
            if(isset($data['approvedby2'])) $data['datedispatched'] = $today;
        }else{
            $data['datedispatched'] = $today;
        }

        $cns_data = array_merge($data, ['collect_new_sample' => 1]);

        $worksheet_id = $worksheet->id;

        if($approved && is_array($approved)) DrSample::whereIn('id', $approved)->where(['worksheet_id' => $worksheet_id])->update($data);
        if($cns && is_array($cns)) DrSample::whereIn('id', $cns)->where(['worksheet_id' => $worksheet_id])->update($cns_data);

        unset($data['datedispatched']);

        if($rerun && is_array($rerun)){
            $samples = DrSample::whereIn('id', $rerun)->get();
            foreach ($samples as $key => $sample){
                $sample->create_rerun($data);
            }
        }

        session(['toast_message' => 'The worksheet has been approved.']);

        $total = DrSample::where(['worksheet_id' => $worksheet_id, 'parentid' => 0])->count();
        $dispatched = DrSample::whereNotNull('datedispatched')->where(['worksheet_id' => $worksheet_id])->count();
        $reruns = DrSample::where(['worksheet_id' => $worksheet_id, 'repeatt' => 1])->count();

        if($total == ($dispatched + $reruns)){
            $worksheet->fill($w_data);
            $worksheet->status_id = 3;
            $worksheet->save();

            $w = $worksheet->extraction_worksheet;
            if($w && !$w->sequencing && !$w->pending_worksheet){
                $w->status_id = 3;
                $w->save();
            }
            session(['toast_message' => 'The worksheet has been approved fully.']);
        }
        return redirect('dr_worksheet');
    }

    public function create_plate(DrWorksheet $worksheet)
    {
        MiscDr::create_plate($worksheet);
        session(['toast_message' => 'The samples have been uploaded to exatype and will be ready later.']);
        return back();
    }

    public function get_plate_result(DrWorksheet $worksheet)
    {
        $result = MiscDr::get_plate_result($worksheet);
        // dd($result);
        session(['toast_message' => 'The results have been retrieved.']);
        return back();
    }



    
}
