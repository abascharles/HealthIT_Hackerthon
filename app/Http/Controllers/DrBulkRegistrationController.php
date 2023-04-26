<?php

namespace App\Http\Controllers;

use App\DrBulkRegistration;
use App\DrSample;
use App\Lookup;
use Illuminate\Http\Request;
use Excel;

class DrBulkRegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = DrBulkRegistration::withCount(['sample'])->get();
        return view('tables.dr_bulk_registration', ['templates' => $templates]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $samples = DrSample::whereNull('bulk_registration_id')
        // ->whereNull('worksheet_id')
        // ->whereNull('extraction_worksheet_id')
        // ->where('datereceived', '>', date('Y-m-d', strtotime('-1 year')))
        // ->where(['receivedstatus' => 1, 'control' => 0])
        // ->orderBy('run', 'desc')
        // ->orderBy('datereceived', 'asc')
        // ->orderBy('id', 'asc')
        // ->get();

        // if($samples->first()){
        //     $b = DrBulkRegistration::create(['createdby' => auth()->user()->id, 'lab_id' => env('APP_LAB')]);
        //     DrSample::whereNull('bulk_registration_id')
        //         ->whereNull('worksheet_id')
        //         ->whereNull('extraction_worksheet_id')
        //         ->where('datereceived', '>', date('Y-m-d', strtotime('-1 year')))
        //         ->where(['receivedstatus' => 1, 'control' => 0])
        //         ->update(['bulk_registration_id' => $b->id]);

        //     session(['toast_message' => 'The bulk registration template has been created.']);
        //     return back();
        // }

        // session(['toast_error' => 1, 'toast_message' => 'The bulk registration template could not be created.']);
        // return back();

        $data = Lookup::get_dr();
        $data = array_merge($data, MiscDr::get_bulk_registration_samples());
        return view('forms.dr_bulk_registrations', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = MiscDr::get_bulk_registration_samples($request->input('samples'), 16);

        if(!$data['create']){
            session(['toast_error' => 1, 'toast_message' => 'The bulk registration could not be created.']);
            return back();
        }

        $drBulkRegistration = new DrBulkRegistration;
        $drBulkRegistration->fill($request->except(['_token', 'samples']));
        $drBulkRegistration->save();        

        $samples = $data['samples'];

        foreach ($samples as $s) {
            $sample = DrSample::find($s->id);
            $sample->bulk_registration_id = $drBulkRegistration->id;
            $sample->save();
        }

        session(['toast_message' => 'The bulk registration has been created.']);

        return redirect('dr_bulk_registration/');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DrBulkRegistration  $drBulkRegistration
     * @return \Illuminate\Http\Response
     */
    public function show(DrBulkRegistration $drBulkRegistration)
    {
        $samples = DrSample::with(['patient'])->where(['bulk_registration_id' => $drBulkRegistration->id])->get();
        $data = [];

        foreach ($samples as $key => $sample) {
            $data[] = [
                'Patient ID' => $sample->patient->nat,
                'Project Name' => Lookup::retrieve_val('dr_projects', $sample->project),
                'Full Name' => $sample->patient->patient_names,
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

        $filename = 'bulk_template_' . $drBulkRegistration->id;

        Excel::create($filename, function($excel) use($data){
            $excel->sheet('Sheetname', function($sheet) use($data) {
                $sheet->fromArray($data);
            });
        })->download('csv');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DrBulkRegistration  $drBulkRegistration
     * @return \Illuminate\Http\Response
     */
    public function edit(DrBulkRegistration $drBulkRegistration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DrBulkRegistration  $drBulkRegistration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DrBulkRegistration $drBulkRegistration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DrBulkRegistration  $drBulkRegistration
     * @return \Illuminate\Http\Response
     */
    public function destroy(DrBulkRegistration $drBulkRegistration)
    {
        //
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

            $data = MiscDr::get_worksheet_files($worksheet);

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

        if($approved && is_array($approved)) DrSample::whereIn('id', $approved)->where(['worksheet_id' => $worksheet_id])->update($data);
        if($cns && is_array($cns)) DrSample::whereIn('id', $cns)->where(['worksheet_id' => $worksheet_id])->update($cns_data);

        $samples = DrSample::whereIn('id', $rerun)->get();
        unset($data['datedispatched']);

        foreach ($samples as $key => $sample){
            $sample->create_rerun($data);
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
            if(!$w->sequencing && !$w->pending_worksheet){
                $w->status_id = 3;
                $w->save();
            }
            session(['toast_message' => 'The worksheet has been approved fully.']);
        }
        return redirect('dr_worksheet');
    }


}
