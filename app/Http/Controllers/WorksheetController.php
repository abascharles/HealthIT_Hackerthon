<?php

namespace App\Http\Controllers;

use App\Worksheet;
use App\Sample;
use App\SampleView;
use App\User;
use App\Misc;
use App\Lookup;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WorksheetImport;
use Illuminate\Http\Request;

class WorksheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($state=0, $date_start=NULL, $date_end=NULL, $worksheet_id=NULL)
    {
        // $state = session()->pull('worksheet_state', null); 
        $worksheets = Worksheet::with(['creator'])->withCount(['sample'])
        ->when($worksheet_id, function ($query) use ($worksheet_id){
            return $query->where('worksheets.id', $worksheet_id);
        })
        ->when($state, function ($query) use ($state){
            if($state == 1 || $state == 12) $query->orderBy('worksheets.id', 'asc');
            if($state == 12){
                return $query->where('status_id', 1)->whereRaw("worksheets.id in (
                    SELECT DISTINCT worksheet_id
                    FROM samples_view
                    WHERE parentid > 0 AND site_entry != 2
                )");
            }
            return $query->where('status_id', $state);
        })
        ->when($date_start, function($query) use ($date_start, $date_end){
            if($date_end)
            {
                return $query->whereDate('worksheets.created_at', '>=', $date_start)
                ->whereDate('worksheets.created_at', '<=', $date_end);
            }
            return $query->whereDate('worksheets.created_at', $date_start);
        })
        ->orderBy('worksheets.id', 'desc')
        ->paginate();

        $worksheets->setPath(url()->current());

        $worksheet_ids = $worksheets->pluck(['id'])->toArray();
        $samples = $this->get_worksheets($worksheet_ids);
        $reruns = $this->get_reruns($worksheet_ids);
        $data = Lookup::worksheet_lookups();

        $worksheets->transform(function($worksheet, $key) use ($samples, $reruns, $data){
            $status = $worksheet->status_id;
            $total = $worksheet->sample_count;

            if(($status == 2 || $status == 3) && $samples){
                $neg = $samples->where('worksheet_id', $worksheet->id)->where('result', 1)->first()->totals ?? 0;
                $pos = $samples->where('worksheet_id', $worksheet->id)->where('result', 2)->first()->totals ?? 0;
                $failed = $samples->where('worksheet_id', $worksheet->id)->where('result', 3)->first()->totals ?? 0;
                $redraw = $samples->where('worksheet_id', $worksheet->id)->where('result', 5)->first()->totals ?? 0;
                $noresult = $samples->where('worksheet_id', $worksheet->id)->where('result', 0)->first()->totals ?? 0;

                $rerun = $reruns->where('worksheet_id', $worksheet->id)->first()->totals ?? 0;
            }
            else{
                $neg = $pos = $failed = $redraw = $noresult = $rerun = 0;

                if($status == 1){
                    $noresult = $worksheet->sample_count;
                    $rerun = $reruns->where('worksheet_id', $worksheet->id)->first()->totals ?? 0;
                }
            }
            $worksheet->rerun = $rerun;
            $worksheet->neg = $neg;
            $worksheet->pos = $pos;
            $worksheet->failed = $failed;
            $worksheet->redraw = $redraw;
            $worksheet->noresult = $noresult;
            $worksheet->mylinks = $this->get_links($worksheet->id, $status, $worksheet->datereviewed);
            $worksheet->machine = $data['machines']->where('id', $worksheet->machine_type)->first()->output ?? '';
            $worksheet->status = $data['worksheet_statuses']->where('id', $status)->first()->output ?? '';

            return $worksheet;
        });

        $data = Lookup::worksheet_lookups();
        $data['status_count'] = Worksheet::selectRaw("count(*) AS total, status_id, machine_type")
            ->groupBy('status_id', 'machine_type')
            ->orderBy('status_id', 'asc')
            ->orderBy('machine_type', 'asc')
            ->get();
        $data['worksheets'] = $worksheets;
        $data['myurl'] = url('worksheet/index/' . $state . '/');
        $data['link_extra'] = '';

        return view('tables.worksheets', $data)->with('pageTitle', 'Worksheets');
    }

    
    static function query($state=0, $date_start=NULL, $date_end=NULL)
    {
        $worksheets = Worksheet::with(['creator'])
        ->when($state, function ($query) use ($state){
            return $query->where('status_id', $state);
        })
        ->when($date_start, function($query) use ($date_start, $date_end){
            if($date_end)
            {
                return $query->whereDate('worksheets.created_at', '>=', $date_start)
                ->whereDate('worksheets.created_at', '<=', $date_end);
            }
            return $query->whereDate('worksheets.created_at', $date_start);
        });
        // ->orderBy('worksheets.created_at', 'desc')
        // ->get();
        return $worksheets;
    }

    public function set_sampletype_form($machine_type, $limit=false)
    {
        $data = Lookup::worksheet_lookups();
        $data['machine_type'] = $machine_type;
        $data['limit'] = $limit;
        $data['users'] = User::whereIn('user_type_id', [0, 1, 4])->where('email', '!=', 'rufus.nyaga@ken.aphl.org')
            ->whereRaw(" (
                id IN 
                (SELECT DISTINCT received_by FROM samples_view WHERE site_entry != 2 AND receivedstatus = 1 and result IS NULL AND worksheet_id IS NULL AND datedispatched IS NULL AND parentid=0 ) 
                OR id IN
                (SELECT DISTINCT sample_received_by FROM samples_view WHERE site_entry != 2 AND receivedstatus = 1 and result IS NULL AND worksheet_id IS NULL AND datedispatched IS NULL AND parentid=0 AND sample_received_by IS NOT NULL) 
                )
                ")
            ->withTrashed()
            ->get();

        return view('forms.set_worksheet_sampletype', $data)->with('pageTitle', 'Set Sample Type');
    }

    public function set_sampletype(Request $request)
    {
        $machine_type = $request->input('machine_type');
        $limit = $request->input('limit');
        $entered_by = $request->input('entered_by');
        // return redirect("/viralworksheet/create/{$sampletype}/{$machine_type}/{$calibration}/{$limit}/{$entered_by}");

        return $this->create($machine_type, $limit, $entered_by);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($machine_type=2, $limit=null, $entered_by=null)
    {
        $data = Misc::get_worksheet_samples($machine_type, $limit, $entered_by);
        if(!$data){
            session(['toast_message' => 'An error has occurred.', 'toast_error' => 1]);
            return back();
        }
        return view('forms.worksheets', $data)->with('pageTitle', 'Create Worksheet');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $worksheet = new Worksheet;
        $worksheet->fill($request->except('_token', 'limit'));
        $worksheet->createdby = auth()->user()->id;
        $worksheet->lab_id = auth()->user()->lab_id;
        $worksheet->save();

        $data = Misc::get_worksheet_samples($worksheet->machine_type, $request->input('limit'));

        if(!$data || !$data['create']){
            $worksheet->delete();
            session(['toast_message' => "The worksheet could not be created.", 'toast_error' => 1]);
            return back();            
        }
        $samples = $data['samples'];
        $sample_ids = $samples->pluck('id')->toArray();

        Sample::whereIn('id', $sample_ids)->update(['worksheet_id' => $worksheet->id]);

        return redirect()->route('worksheet.print', ['worksheet' => $worksheet->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Worksheet  $worksheet
     * @return \Illuminate\Http\Response
     */
    public function show(Worksheet $worksheet, $print=false)
    {
        $worksheet->load(['creator']);
        $sample_array = SampleView::select('id')->where('worksheet_id', $worksheet->id)->where('site_entry', '!=', 2)->get()->pluck('id')->toArray();
        // $samples = Sample::whereIn('id', $sample_array)->with(['patient', 'batch.facility'])->get();


        $samples = Sample::join('batches', 'samples.batch_id', '=', 'batches.id')
                    ->with(['patient', 'batch.facility'])
                    ->select('samples.*', 'batches.facility_id')
                    ->whereIn('samples.id', $sample_array)
                    ->orderBy('run', 'desc')
                    ->when(true, function($query){
                        if(in_array(env('APP_LAB'), [2])) return $query->orderBy('facility_id')->orderBy('batch_id', 'asc');
                        if(in_array(env('APP_LAB'), [3])) $query->orderBy('datereceived', 'asc');
                        if(!in_array(env('APP_LAB'), [8, 9, 1])) return $query->orderBy('batch_id', 'asc');
                    })
                    ->orderBy('samples.id', 'asc')
                    ->get();

        $data = ['worksheet' => $worksheet, 'samples' => $samples, 'i' => 0];

        if($print) $data['print'] = true;

        if($worksheet->machine_type == 1){
            return view('worksheets.other-table', $data)->with('pageTitle', 'Worksheets');
        }
        else{
            return view('worksheets.abbot-table', $data)->with('pageTitle', 'Worksheets');
        }
    }

    public function labels(Worksheet $worksheet)
    {
        $samples = SampleView::where('worksheet_id', $worksheet->id)
                    ->orderBy('run', 'desc')
                    ->when(true, function($query){
                        if(in_array(env('APP_LAB'), [2])) return $query->orderBy('facility_id')->orderBy('batch_id', 'asc');
                        if(in_array(env('APP_LAB'), [3])) $query->orderBy('datereceived', 'asc');
                        if(!in_array(env('APP_LAB'), [8, 9, 1])) return $query->orderBy('batch_id', 'asc');
                    })
                    ->orderBy('samples_view.id', 'asc')
                    ->where('site_entry', '!=', 2)->get();
        return view('worksheets.labels', ['samples' => $samples, 'i' => 2]);
    }

    public function find(Worksheet $worksheet)
    {
        session(['toast_message' => 'Found 1 worksheet.']);
        return $this->index(0, null, null, $worksheet->id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Worksheet  $worksheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Worksheet $worksheet)
    {
        $samples = $worksheet->sample;
        return view('forms.worksheets', ['create' => true, 'machine_type' => $worksheet->machine_type, 'samples' => $samples, 'worksheet' => $worksheet])->with('pageTitle', 'Edit Worksheet');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Worksheet  $worksheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Worksheet $worksheet)
    {
        $worksheet->fill($request->except('_token'));
        $worksheet->save();
        return redirect('worksheet/print/' . $worksheet->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Worksheet  $worksheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Worksheet $worksheet)
    {
        if($worksheet->status_id != 4){
            session(['toast_error' => 1, 'toast_message' => 'The worksheet cannot be deleted.']);
            return back();
        }
        // DB::table("samples")->where('worksheet_id', $worksheet->id)->update(['worksheet_id' => NULL, 'result' => NULL]);
        $worksheet->delete();
        return back();
    }

    public function print(Worksheet $worksheet)
    {
        return $this->show($worksheet, true);
    }

    public function convert_worksheet(Worksheet $worksheet, $machine_type)
    {
        // if($machine_type == 1 || $worksheet->machine_type == 1 || $worksheet->status_id != 1){
        if($worksheet->status_id != 1){
            session(['toast_error' => 1, 'toast_message' => 'The worksheet cannot be converted to the requested type.']);
            return back();            
        }
        $worksheet->machine_type = $machine_type;
        $worksheet->save();
        session(['toast_message' => 'The worksheet has been converted.']);
        return back();
        // return redirect('worksheet/' . $worksheet->id . '/edit');
    }

    public function cancel(Worksheet $worksheet)
    {
        if($worksheet->status_id != 1){
            session(['toast_message' => 'The worksheet is not eligible to be cancelled.']);
            session(['toast_error' => 1]);
            return back();
        }
        $sample_array = SampleView::select('id')->where('worksheet_id', $worksheet->id)->where('site_entry', '!=', 2)->get()->pluck('id')->toArray();
        Sample::whereIn('id', $sample_array)->update(['worksheet_id' => null, 'result' => null]);
        $worksheet->status_id = 4;
        $worksheet->datecancelled = date("Y-m-d");
        $worksheet->cancelledby = auth()->user()->id;
        $worksheet->save();

        session(['toast_message' => 'The worksheet has been cancelled.']);
        return redirect("/worksheet");
    }

    public function cancel_upload(Worksheet $worksheet)
    {
        if($worksheet->status_id != 2){
            session(['toast_message' => 'The upload for this worksheet cannot be reversed.']);
            session(['toast_error' => 1]);
            return back();
        }

        if($worksheet->uploadedby != auth()->user()->id && auth()->user()->user_type_id != 0){
            session(['toast_message' => 'Only the user who uploaded the results can reverse the upload.']);
            session(['toast_error' => 1]);
            return back();
        }

        $samples = Sample::where(['repeatt' => 1, 'worksheet_id' => $worksheet->id])->get();

        foreach ($samples as $sample) {
            $sample->remove_rerun();
        }

        $sample_array = SampleView::select('id')->where('worksheet_id', $worksheet->id)->where('site_entry', '!=', 2)->get()->pluck('id')->toArray();
        Sample::whereIn('id', $sample_array)->update(['result' => null, 'interpretation' => null, 'datemodified' => null, 'datetested' => null, 'repeatt' => 0, 'dateapproved' => null, 'approvedby' => null]);
        $worksheet->status_id = 1;
        $worksheet->neg_control_interpretation = $worksheet->pos_control_interpretation = $worksheet->neg_control_result = $worksheet->pos_control_result = $worksheet->daterun = $worksheet->dateuploaded = $worksheet->uploadedby = $worksheet->datereviewed = $worksheet->reviewedby = $worksheet->datereviewed2 = $worksheet->reviewedby2 = null;
        $worksheet->save();

        session(['toast_message' => 'The upload has been reversed.']);
        return redirect("/worksheet/upload/" . $worksheet->id);
    }

    public function reverse_upload(Worksheet $worksheet)
    {
        if(!in_array($worksheet->status_id, [3,7]) || $worksheet->daterun->lessThan(date('Y-m-d', strtotime('-2 days')))){
            session(['toast_error' => 1, 'toast_message' => 'The upload for this worksheet cannot be reversed.']);
            return back();
        }
        $worksheet->status_id = 1;
        $worksheet->neg_control_interpretation = $worksheet->pos_control_interpretation = $worksheet->neg_control_result = $worksheet->pos_control_result = $worksheet->daterun = $worksheet->dateuploaded = $worksheet->uploadedby = $worksheet->datereviewed = $worksheet->reviewedby = $worksheet->datereviewed2 = $worksheet->reviewedby2 = null;
        $worksheet->save();

        $batches_data = ['batch_complete' => 0, 'sent_email' => 0, 'printedby' => null,  'dateemailsent' => null, 'datebatchprinted' => null, 'dateindividualresultprinted' => null, 'datedispatched' => null, ];
        $samples_data = ['datetested' => null, 'result' => null, 'interpretation' => null, 'repeatt' => 0, 'approvedby' => null, 'approvedby2' => null, 'datemodified' => null, 'dateapproved' => null, 'dateapproved2' => null, 'tat1' => null, 'tat2' => null, 'tat3' => null, 'tat4' => null];

        // $samples = Sample::where(['worksheet_id' => $worksheet->id, 'repeatt' => 1])->get();
        // $samples = Sample::where(['worksheet_id' => $worksheet->id])->get();

        $sample_array = SampleView::select('id')->where('worksheet_id', $worksheet->id)->where('site_entry', '!=', 2)->get()->pluck('id')->toArray();
        $samples = Sample::whereIn('id', $sample_array)->get();

        foreach ($samples as $key => $sample) {
            if($sample->parentid == 0) $del_samples = Sample::where('parentid', $sample->id)->get();
            else{
                $run = $sample->run+1;
                $del_samples = Sample::where(['parentid' => $sample->parentid, 'run' => $run])->get();
            }
            foreach ($del_samples as $del) {
                if($del->worksheet_id && $del->result){  
                    if($sample->parentid == 0){
                        if($del->run == 2){
                            $del->run = 1;
                            $del->parentid = 0;
                            $del->repeatt = 1;
                            $del->pre_update();

                            $sample->run = 2;
                            $sample->parentid = $del->id;
                        }
                    } 
                    else{
                        $del->run--;
                        $del->pre_update();
                        $sample->run++;
                    }
                }
                else{
                    $del->pre_delete();                       
                }
            }

            $sample->fill($samples_data);
            $sample->pre_update();
            $batch_ids[$key] = $sample->batch_id;
        }
        $batch_ids = collect($batch_ids);
        $unique = $batch_ids->unique();

        foreach ($unique as $key => $id) {
            $batch = \App\Batch::find($id);
            $batch->fill($batches_data);
            $batch->pre_update();
        }
        return back();

    }

    public function upload(Worksheet $worksheet)
    {
        if(!in_array($worksheet->status_id, [1, 4])){
            session(['toast_error' => 1, 'toast_message' => 'You cannot update results for this worksheet.']);
            return back();
        }
        $worksheet->load(['creator']);
        $users = User::whereIn('user_type_id', [1, 4])->where('email', '!=', 'rufus.nyaga@ken.aphl.org')->get();
        return view('forms.upload_results', ['worksheet' => $worksheet, 'users' => $users])->with('pageTitle', 'Worksheet Upload');
    }




    /**
     * Update the specified resource in storage with results file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Worksheet  $worksheet
     * @return \Illuminate\Http\Response
     */
    public function save_results(Request $request, Worksheet $worksheet)
    {
        if(!in_array($worksheet->status_id, [1, 4])){
            session(['toast_error' => 1, 'toast_message' => 'You cannot update results for this worksheet.']);
            return back();
        }
        $file = $request->upload->path();
        $path = $request->upload->store('public/results/eid'); 

        $c = new WorksheetImport($worksheet, $request);
        Excel::import($c, $path);
        
        return redirect('worksheet/approve/' . $worksheet->id);
    }

    public function approve_results(Worksheet $worksheet)
    {        
        $worksheet->load(['reviewer', 'creator', 'runner', 'sorter', 'bulker']);

        // $samples = Sample::where('worksheet_id', $worksheet->id)->with(['approver'])->get();
        
        $samples = Sample::join('batches', 'samples.batch_id', '=', 'batches.id')
                    ->with(['approver', 'final_approver'])
                    ->select('samples.*', 'batches.facility_id')
                    ->where('worksheet_id', $worksheet->id) 
                    ->where('site_entry', '!=', 2) 
                    ->orderBy('run', 'desc')
                    ->when(true, function($query){
                        if(in_array(env('APP_LAB'), [2])) return $query->orderBy('facility_id')->orderBy('batch_id', 'asc');
                        if(in_array(env('APP_LAB'), [3])) $query->orderBy('datereceived', 'asc');
                        if(!in_array(env('APP_LAB'), [8, 9, 1])) return $query->orderBy('batch_id', 'asc');
                    })
                    ->orderBy('samples.id', 'asc')
                    ->get();

        $s = $this->get_worksheets($worksheet->id);

        $neg = $s->where('result', 1)->first()->totals ?? 0;
        $pos = $s->where('result', 2)->first()->totals ?? 0;
        $failed = $s->where('result', 3)->first()->totals ?? 0;
        $redraw = $s->where('result', 5)->first()->totals ?? 0;
        $noresult = $s->where('result', 0)->first()->totals ?? 0;

        $total = $neg + $pos + $failed + $redraw + $noresult;

        $subtotals = ['neg' => $neg, 'pos' => $pos, 'failed' => $failed, 'redraw' => $redraw, 'noresult' => $noresult, 'total' => $total];

        $data = Lookup::worksheet_approve_lookups();
        $data['samples'] = $samples;
        $data['subtotals'] = $subtotals;
        $data['worksheet'] = $worksheet;

        return view('tables.confirm_results', $data)->with('pageTitle', 'Approve Results');
    }

    public function approve(Request $request, Worksheet $worksheet)
    {
        $double_approval = Lookup::$double_approval;
        $samples = $request->input('samples', []);
        $batches = $request->input('batches');
        $results = $request->input('results');
        $actions = $request->input('actions');

        $today = date('Y-m-d');
        $approver = auth()->user()->id;

        if(in_array(env('APP_LAB'), $double_approval) && $worksheet->reviewedby == $approver){
            session(['toast_message' => "You are not permitted to do the second approval.", 'toast_error' => 1]);
            return redirect('/worksheet');            
        }

        $batch = array();

        foreach ($samples as $key => $value) {

            if(in_array(env('APP_LAB'), $double_approval) && $worksheet->reviewedby && !$worksheet->reviewedby2 && $worksheet->datereviewed){
                $data = [
                    'approvedby2' => $approver,
                    'dateapproved2' => $today,
                ];
            }
            else{
                $data = [
                    'approvedby' => $approver,
                    'dateapproved' => $today,
                ];
            }

            $data['result'] = $results[$key];
            $data['repeatt'] = $actions[$key];

            if($data['result'] == 5){
                $data['labcomment'] = "Failed Run";
                $data['repeatt'] = 0;
            } 

            // Sample::where('id', $samples[$key])->update($data);
            
            $sample = Sample::find($samples[$key]);
            $sample->fill($data);
            $sample->pre_update();

            // if($actions[$key] == 1){
            if($data['repeatt'] == 1){
                Misc::save_repeat($samples[$key]);
            }
        }

        if($batches){
            $batch = collect($batches);
            $b = $batch->unique();
            $unique = $b->values()->all();

            foreach ($unique as $value) {
                Misc::check_batch($value);
            }
        }

        $checked_batches = true;

        if(in_array(env('APP_LAB'), $double_approval)){
            if($worksheet->reviewedby && $worksheet->reviewedby != $approver && $worksheet->datereviewed){
                $worksheet->status_id = 3;
                $worksheet->datereviewed2 = $today;
                $worksheet->reviewedby2 = $approver;
                $worksheet->save();
                session(['toast_message' => "The worksheet has been approved."]);

                return redirect('/batch/dispatch');                 
            }
            else{
                $worksheet->datereviewed = $today;
                $worksheet->reviewedby = $approver;
                $worksheet->save();
                session(['toast_message' => "The worksheet has been approved. It is awaiting the second approval before the results can be prepared for dispatch."]);

                return redirect('/worksheet');
            }
        }
        else{
            $worksheet->status_id = 3;
            $worksheet->datereviewed = $today;
            $worksheet->reviewedby = $approver;
            $worksheet->save();
            session(['toast_message' => "The worksheet has been approved."]);

            return redirect('/batch/dispatch');            
        }
    }

    public function rerun_worksheet(Worksheet $worksheet)
    {
        if($worksheet->status_id != 2 || !$worksheet->failed){
            session(['toast_error' => 1, 'toast_message' => "The worksheet is not eligible for rerun."]);
            return back();
        }
        $worksheet->status_id = 7;
        $worksheet->save();

        $new_worksheet = $worksheet->replicate(['national_worksheet_id', 'status_id',
            'neg_control_result', 'pos_control_result', 
            'neg_control_interpretation', 'pos_control_interpretation',
            'datecut', 'datereviewed', 'datereviewed2', 'dateuploaded', 'datecancelled', 'daterun',
        ]);
        $new_worksheet->save();

        
        $samples = Sample::where(['worksheet_id' => $worksheet->id])
                    ->where('site_entry', '!=', 2) 
                    ->select('samples.*')
                    ->join('batches', 'batches.id', '=', 'samples.batch_id')
                    ->get();

        foreach ($samples as $key => $sample) {
            $sample->repeatt = 1;
            $sample->pre_update();
            $rsample = Misc::save_repeat($sample->id);
            $rsample->worksheet_id = $new_worksheet->id;
            $rsample->save();
        }
        session(['toast_message' => "The worksheet has been marked as failed as is ready for rerun."]);
        return redirect($worksheet->route_name);  
    }

    public function cns_worksheet(Worksheet $worksheet)
    {
        if($worksheet->status_id != 1){
            session(['toast_error' => 1, 'toast_message' => "The worksheet is not eligible to be dispatched as CNS."]);
            return back();
        }
        $worksheet->status_id = 3;
        $worksheet->daterun = $worksheet->datereviewed = $worksheet->datereviewed2 = date('Y-m-d');
        $worksheet->save();
        
        $samples = Sample::where(['worksheet_id' => $worksheet->id])
                    ->where('site_entry', '!=', 2) 
                    ->select('samples.*')
                    ->join('batches', 'batches.id', '=', 'samples.batch_id')
                    ->get();

        foreach ($samples as $key => $sample) {
            $sample->repeatt = 0;
            $sample->result = 5;
            $sample->dateapproved = $sample->dateapproved2 = date('Y-m-d');
            $sample->pre_update();
        }
        session(['toast_message' => "The worksheet has been dispatched as CNS."]);
        return redirect($worksheet->route_name);  
    }

    public function mtype($machine)
    {
        if($machine == 1){
            return "<strong> TaqMan </strong>";
        }
        else{
            return " <strong><font color='#0000FF'> Abbott </font></strong> ";
        }
    }

    public function wstatus($status)
    {
        switch ($status) {
            case 1:
                return "<strong><font color='#FFD324'>In-Process</font></strong>";
                break;
            case 2:
                return "<strong><font color='#0000FF'>Tested</font></strong>";
                break;
            case 3:
                return "<strong><font color='#339900'>Approved</font></strong>";
                break;
            case 4:
                return "<strong><font color='#FF0000'>Cancelled</font></strong>";
                break;            
            default:
                break;
        }
    }

    public function get_links($worksheet_id, $status, $datereviewed)
    {
        if($status == 1)
        {
            $d = "<a href='" . url('worksheet/' . $worksheet_id) . "' title='Click to view Samples in this Worksheet' target='_blank'>Details</a> | "
                . "<a href='" . url('worksheet/print/' . $worksheet_id) . "' title='Click to Print this Worksheet' target='_blank'>Print</a> | "
                . "<a href='" . url('worksheet/cancel/' . $worksheet_id) . "' title='Click to Cancel this Worksheet' onClick=\"return confirm('Are you sure you want to Cancel Worksheet {$worksheet_id}?'); \" >Cancel</a> | "
                . "<a href='" . url('worksheet/upload/' . $worksheet_id) . "' title='Click to Upload Results File for this Worksheet'>Update Results</a>";
        }
        else if($status == 2)
        {
            $d = "<a href='" . url('worksheet/approve/' . $worksheet_id) . "' title='Click to Approve Samples Results in worksheet for Rerun or Dispatch' target='_blank'> Approve Worksheet Results ";

            if($datereviewed) $d .= "(Second Review)";

            $d .= "</a>";

        }
        else if($status == 3)
        {
            $d = "<a href='" . url('worksheet/' . $worksheet_id) . "' title='Click to view Samples in this Worksheet' target='_blank'>Details</a> | "
                . "<a href='" . url('worksheet/approve/' . $worksheet_id) . "' title='Click to View Approved Results & Action for Samples in this Worksheet' target='_blank'>View Results</a> | "
                . "<a href='" . url('worksheet/print/' . $worksheet_id) . "' title='Click to Print this Worksheet' target='_blank'>Print</a> ";

        }
        else if($status == 4 || $status == 5)
        {
            $d = "<a href='" . url('worksheet/' . $worksheet_id) . "' title='Click to View Cancelled Worksheet Details' target='_blank'>Details</a> ";
        }
        else{
            $d = '';
        }
        return $d;
    }

    public function get_worksheets($worksheet_id=NULL)
    {
        if(!$worksheet_id) return false;
        $samples = SampleView::selectRaw("count(*) as totals, worksheet_id, result")
            ->whereNotNull('worksheet_id')
            ->when($worksheet_id, function($query) use ($worksheet_id){                
                if (is_array($worksheet_id)) {
                    return $query->whereIn('worksheet_id', $worksheet_id);
                }
                return $query->where('worksheet_id', $worksheet_id);
            })
            ->where('receivedstatus', '!=', 2)
            ->where('site_entry', '!=', 2)
            ->groupBy('worksheet_id', 'result')
            ->get();

        return $samples;
    }

    public function get_reruns($worksheet_id=NULL)
    {
        if(!$worksheet_id) return false;
        $samples = SampleView::selectRaw("count(*) as totals, worksheet_id")
            ->whereNotNull('worksheet_id')
            ->when($worksheet_id, function($query) use ($worksheet_id){                
                if (is_array($worksheet_id)) {
                    return $query->whereIn('worksheet_id', $worksheet_id);
                }
                return $query->where('worksheet_id', $worksheet_id);
            })
            ->where('parentid', '>', 0)
            ->where('receivedstatus', '!=', 2)
            ->where('site_entry', '!=', 2)
            ->groupBy('worksheet_id')
            ->get();

        return $samples;
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        $worksheets = Worksheet::whereRaw("id like '" . $search . "%'")->paginate(10);
        $worksheets->setPath(url()->current());
        return $worksheets;
    }

}
