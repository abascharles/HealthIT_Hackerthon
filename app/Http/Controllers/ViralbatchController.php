<?php

namespace App\Http\Controllers;

use App\Facility;
use App\Viralbatch;
use App\Viralsample;
use App\ViralsampleView;
use App\MiscViral;
use App\Lookup;
use App\DashboardCacher as Refresh;

// use DOMPDF;
use Mpdf\Mpdf;
use Excel;
use DB;
use App\Mail\VlDispatch;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;


class ViralbatchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($batch_complete=4, $date_start=NULL, $date_end=NULL, $facility_id=NULL, $subcounty_id=NULL, $partner_id=NULL)
    {
        $user = auth()->user();
        $facility_user = false;
        $subtotals = $date_modified = $date_tested = null;
        $dt = date('Y-m-d', strtotime('-30 day'));
        $date_column = "viralbatches.datereceived";
        if(in_array($batch_complete, [1, 6])) $date_column = "viralbatches.datedispatched";
        if($user->user_type_id == 5) $facility_user=true;

        $s_facility_id = session()->pull('facility_search');
        if($s_facility_id){ 
            $myurl = url("viralbatch/facility/{$facility_id}/{$batch_complete}"); 
            $myurl2 = url("viralbatch/facility/{$facility_id}"); 
        }
        else{ 
            $myurl = url('viralbatch/index/' . $batch_complete); 
            $myurl2 = url('viralbatch/index'); 
        }

        $string = "(user_id='{$user->id}' OR viralbatches.facility_id='{$user->facility_id}')";

        $batches = Viralbatch::select(['viralbatches.*', 'facilitys.name', 'u.surname', 'u.oname', 'r.surname as rsurname', 'r.oname as roname'])
            ->leftJoin('facilitys', 'facilitys.id', '=', 'viralbatches.facility_id')
            ->leftJoin('users as u', 'u.id', '=', 'viralbatches.user_id')
            ->leftJoin('users as r', 'r.id', '=', 'viralbatches.received_by')
            ->when($date_start, function($query) use ($date_column, $date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate($date_column, '>=', $date_start)
                    ->whereDate($date_column, '<=', $date_end);
                }
                return $query->whereDate($date_column, $date_start);
            })
            ->when(true, function($query) use ($user, $string){
                if($user->user_type_id == 5) return $query->whereRaw($string);
                return $query->where('viralbatches.lab_id', $user->lab_id)->where('site_entry', '!=', 2);
            })
            ->when($facility_id, function($query) use ($facility_id){
                return $query->where('viralbatches.facility_id', $facility_id);
            })
            ->when($subcounty_id, function($query) use ($subcounty_id){
                return $query->where('facilitys.district', $subcounty_id);
            })
            ->when($partner_id, function($query) use ($partner_id){
                return $query->where('facilitys.partner', $partner_id);
            })
            ->when(true, function($query) use ($batch_complete){
                if($batch_complete < 4) return $query->where('batch_complete', $batch_complete);

                else if($batch_complete == 5){
                    return $query->whereNull('datereceived')
                        ->where(['site_entry' => 1, 'batch_complete' => 0])
                        ->where('viralbatches.created_at', '<', date('Y-m-d', strtotime('-10 days')));
                }

                else if($batch_complete == 6){
                    return $query->where('batch_complete', 1)->where('tat5', '<', 11);
                }
            })
            ->when(true, function($query) use ($batch_complete){
                if(in_array($batch_complete, [1, 6])) return $query->orderBy('viralbatches.datedispatched', 'desc');
                return $query->orderBy('viralbatches.created_at', 'desc');
            })
            ->paginate();

        $this->batches_transformer($batches, $batch_complete);

        $p = Lookup::get_partners();
        $fac = false;
        if($facility_id) $fac = Facility::find($facility_id);

        $view = 'tables.batches';
        if($batch_complete == 1) $view = 'tables.dispatched_viralbatches';

        return view($view, [
            'batches' => $batches, 'myurl' => $myurl, 'myurl2' => $myurl2, 'pre' => 'viral', 
            'batch_complete' => $batch_complete, 
            'partners' => $p['partners'], 'subcounties' => $p['subcounties'], 
            'partner_id' => $partner_id, 'subcounty_id' => $subcounty_id, 'facility' => $fac])->with('pageTitle', 'Samples by Batch');

        // if($batch_complete == 1){
        //     $p = Lookup::get_partners();
        //     $fac = false;
        //     if($facility_id) $fac = Facility::find($facility_id);

        //     return view('tables.dispatched_viralbatches', [
        //         'batches' => $batches, 'myurl' => $myurl, 'myurl2' => $myurl2, 'pre' => 'viral', 
        //         'batch_complete' => $batch_complete, 
        //         'partners' => $p['partners'], 'subcounties' => $p['subcounties'], 
        //         'partner_id' => $partner_id, 'subcounty_id' => $subcounty_id, 'facility' => $fac])->with('pageTitle', 'Samples by Batch');
        // }

        // return view('tables.batches', ['batches' => $batches, 'myurl' => $myurl, 'myurl2' => $myurl2, 'pre' => 'viral', 'batch_complete' => $batch_complete])->with('pageTitle', 'Samples by Batch');
    }

    public function to_print($date_start=NULL, $date_end=NULL, $facility_id=NULL, $subcounty_id=NULL, $partner_id=NULL)
    {
        $user = auth()->user();
        $facility_user = false;
        $subtotals = $date_modified = $date_tested = null;
        $date_column = "viralbatches.datedispatched";
        if($user->user_type_id == 5) $facility_user=true;
        $batch_complete = 1;

        $s_facility_id = session()->pull('facility_search');
        if($s_facility_id){ 
            $myurl = url("viralbatch/facility/{$facility_id}/{$batch_complete}"); 
            $myurl2 = url("viralbatch/facility/{$facility_id}"); 
        }
        else{  
            $myurl = $myurl2 = url('viralbatch/to_print'); 
        }

        $string = "(user_id='{$user->id}' OR viralbatches.facility_id='{$user->facility_id}')";

        $batches = Viralbatch::select(['viralbatches.*', 'facilitys.name', 'users.surname', 'users.oname'])
            ->leftJoin('facilitys', 'facilitys.id', '=', 'viralbatches.facility_id')
            ->leftJoin('users', 'users.id', '=', 'viralbatches.user_id')
            ->where('batch_complete', $batch_complete)
            ->whereRaw('(datebatchprinted is null or dateindividualresultprinted is null)')
            ->when($date_start, function($query) use ($date_column, $date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate($date_column, '>=', $date_start)
                    ->whereDate($date_column, '<=', $date_end);
                }
                return $query->whereDate($date_column, $date_start);
            })
            ->when($facility_user, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->when($facility_id, function($query) use ($facility_id){
                return $query->where('viralbatches.facility_id', $facility_id);
            })
            ->when($subcounty_id, function($query) use ($subcounty_id){
                return $query->where('facilitys.district', $subcounty_id);
            })
            ->when($partner_id, function($query) use ($partner_id){
                return $query->where('facilitys.partner', $partner_id);
            })
            ->when(true, function($query) use ($facility_user){
                if(!$facility_user) return $query->where('site_entry', '!=', 2);
            })
            ->orderBy('viralbatches.datedispatched', 'desc')
            ->paginate(50);

        $this->batches_transformer($batches, $batch_complete);

        $p = Lookup::get_partners();
        $fac = false;
        if($facility_id) $fac = Facility::find($facility_id);

        return view('tables.dispatched_viralbatches', [
            'batches' => $batches, 'myurl' => $myurl, 'myurl2' => $myurl2, 'pre' => 'viral', 
            'batch_complete' => 1, 'to_print' => true,  
            'partners' => $p['partners'], 'subcounties' => $p['subcounties'], 
            'partner_id' => $partner_id, 'subcounty_id' => $subcounty_id, 'facility' => $fac])->with('pageTitle', 'Samples by Batch');

    }

    public function delayed_batches()
    {
        $batches = Viralbatch::selectRaw("viralbatches.*, COUNT(viralsamples.id) AS `samples_count`, facilitys.name, users.surname, users.oname")
            ->leftJoin('facilitys', 'facilitys.id', '=', 'viralbatches.facility_id')
            ->leftJoin('users', 'users.id', '=', 'viralbatches.user_id')
            ->join('viralsamples', 'viralbatches.id', '=', 'viralsamples.batch_id')
            ->where(['batch_complete' => 0, 'viralbatches.lab_id' => env('APP_LAB')])
            ->when(true, function($query){
                if(in_array(env('APP_LAB'), \App\Lookup::$double_approval)){
                    return $query->whereRaw("( receivedstatus=2 OR  (result IS NOT NULL AND result != 'Failed' AND result != '' AND (repeatt = 0 or repeatt is null) AND approvedby IS NOT NULL AND approvedby2 IS NOT NULL) )");
                }
                return $query->whereRaw("( receivedstatus=2 OR  (result IS NOT NULL AND result != 'Failed' AND result != '' AND (repeatt = 0 or repeatt is null) AND approvedby IS NOT NULL) )");
            })
            ->groupBy('viralbatches.id')
            // ->having('samples_count', '>', 0)
            ->havingRaw('COUNT(viralsamples.id) > 0')
            ->paginate();

        $this->batches_transformer($batches);

        return view('tables.batches', ['batches' => $batches, 'display_delayed' => true, 'pre' => 'viral', ])->with('pageTitle', 'Delayed Batches');
    }

    public function facility_batches($facility_id, $batch_complete=4, $date_start=NULL, $date_end=NULL)
    {
        session(['facility_search' => $facility_id]);
        return $this->index($batch_complete, $date_start, $date_end, $facility_id);
    }

    public function batch_search(Request $request)
    {
        $batch_complete = $request->input('batch_complete', 1);
        $submit_type = $request->input('submit_type');
        $to_print = $request->input('to_print');
        $date_start = $request->input('from_date', 0);
        if($submit_type == 'submit_date') $date_start = $request->input('filter_date', 0);
        $date_end = $request->input('to_date', 0);

        if($date_start == '') $date_start = 0;
        if($date_end == '') $date_end = 0;

        $partner_id = $request->input('partner_id', 0);
        $subcounty_id = $request->input('subcounty_id', 0);
        $facility_id = $request->input('facility_id', 0);

        if($partner_id == '') $partner_id = 0;
        if($subcounty_id == '') $subcounty_id = 0;
        if($facility_id == '') $facility_id = 0;

        if($submit_type == 'excel') return $this->dispatch_report($batch_complete, $date_start, $date_end, $facility_id, $subcounty_id, $partner_id);

        if($to_print) return redirect("viralbatch/to_print/{$date_start}/{$date_end}/{$facility_id}/{$subcounty_id}/{$partner_id}");

        return redirect("viralbatch/index/{$batch_complete}/{$date_start}/{$date_end}/{$facility_id}/{$subcounty_id}/{$partner_id}");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Viralbatch  $viralbatch
     * @return \Illuminate\Http\Response
     */
    public function show(Viralbatch $viralbatch)
    {
        $viralsamples = $viralbatch->sample;
        $viralsamples->load(['patient']);
        $viralbatch->load(['view_facility', 'receiver', 'creator.facility']);
        $data = Lookup::get_viral_lookups();
        $data['batch'] = $viralbatch;
        $data['samples'] = $viralsamples;

        if (!$viralbatch->dateviewed && auth()->user()->user_type_id == 5 && $viralbatch->datedispatched != null) {
            $viralbatch->dateviewed = date('Y-m-d');
            $viralbatch->pre_update();
        }

        return view('tables.viralbatch_details', $data)->with('pageTitle', 'Batches');
    }

    public function labels(Viralbatch $batch)
    {
        $samples = ViralsampleView::where(['batch_id' => $batch->id])->where('repeatt', '!=', 1)->whereRaw('(receivedstatus IS NULL or receivedstatus=1)')->get();
        return view('worksheets.labels', ['samples' => $samples, 'i' => 0]);
    }

    public function transfer(Viralbatch $viralbatch)
    {
        $viralsamples = $viralbatch->sample;
        $viralsamples->load(['patient']);
        $viralbatch->load(['view_facility', 'receiver', 'creator.facility']);
        $data = Lookup::get_viral_lookups();
        $data['batch'] = $viralbatch;
        $data['samples'] = $viralsamples;

        return view('tables.transfer_viralbatch_samples', $data)->with('pageTitle', 'Transfer Samples');
    }

    public function transfer_to_new_batch(Request $request, Viralbatch $batch)
    {
        $sample_ids = $request->input('samples');
        $submit_type = $request->input('submit_type');

        if(!$sample_ids){
            session(['toast_message' => "No samples have been selected."]);
            session(['toast_error' => 1]);
            return back();            
        }

        $new_batch = new Viralbatch;
        $new_batch->fill($batch->replicate(['synched', 'batch_full', 'national_batch_id', 'sent_email', 'dateindividualresultprinted', 'datebatchprinted', 'dateemailsent'])->toArray());
        if($submit_type != "new_facility"){
            $new_batch->id = (int) $batch->id + 0.5;
            $new_id = $batch->id + 0.5;
            $existing_batch = Viralbatch::find($new_id);
            if($existing_batch){
                session(['toast_message' => "Batch {$new_id} already exists.", 'toast_error' => 1]);
                return back();
            }
            if($new_batch->id == floor($new_batch->id)){
                session(['toast_message' => "The batch {$batch->id} cannot have its samples transferred.", 'toast_error' => 1]);
                return back();
            }
        }
        $new_batch->created_at = $batch->created_at;
        $new_batch->save();

        if($submit_type == "new_facility") $new_id = $new_batch->id;

        $count = 0;
        $s;
        
        $has_received_status = false;

        foreach ($sample_ids as $key => $id) {
            $sample = Viralsample::find($id);
            if($submit_type == "new_batch" && ($sample->receivedstatus == 2 || ($sample->repeatt == 0 && $sample->result && $sample->result != "Failed"))){
                continue;
            }else{
                $parent = $sample->parent;
                if($parent){
                    $parent->batch_id = $new_id;
                    $parent->pre_update();

                    $children = $parent->children;
                    if($children){
                        foreach ($children as $child) {
                            $child->batch_id = $new_id;
                            $child->pre_update();
                        }
                        
                    }
                }
            }
            if($sample->result && $submit_type == "new_batch") continue;
            if($sample->receivedstatus) $has_received_status = true;
            $sample->batch_id = $new_id;
            $sample->pre_update();
            $s = $sample;
            $count++;
        }

        if($count == 0){
            // $new_batch->delete();
        }

        if(!$has_received_status){
            Viralbatch::where(['id' => $new_id])->update(['datereceived' => null, 'received_by' => null, 'time_received' => null]);
        }

        MiscViral::check_batch($batch->id);
        MiscViral::check_batch($new_id);
        Refresh::refresh_cache();
        session(['toast_message' => "The batch {$batch->id} has had {$count} samples transferred to  batch {$new_id}."]);
        if($submit_type == "new_facility"){
            session(['toast_message' => "The batch {$batch->id} has had {$count} samples transferred to  batch {$new_id}. Update the facility on this form to complete the process."]);
            return redirect('viralsample/' . $s->id . '/edit');
        }
        return redirect('viralbatch/' . $new_id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Viralbatch  $viralbatch
     * @return \Illuminate\Http\Response
     */
    public function edit(Viralbatch $viralbatch)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Viralbatch  $viralbatch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Viralbatch $viralbatch)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Viralbatch  $viralbatch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Viralbatch $viralbatch)
    {
        if(!$viralbatch->delete_button) abort(409, "This batch is not eligible for deletion.");
        Viralsample::where(['batch_id' => $viralbatch->id])->delete();
        $viralbatch->delete();
        session(['toast_message' => 'The batch has been deleted.']);
        return back();
    }

    public function destroy_multiple(Request $request)
    {
        $batches = $request->input('batches');

        foreach ($batches as $id) {
            $batch = Viralbatch::find($id);
            $batch->batch_delete();
        }
        session(['toast_message' => "The selected batches have been deleted."]);
        return back();
    }


    public function batch_dispatch()
    {
        return $this->get_rows();
    }

    public function confirm_dispatch(Request $request)
    {
        $batches = $request->input('batches');
        $final_dispatch = $request->input('final_dispatch');

        if (empty($batches)){
            session(['toast_message' => "No batch selected<br /><br />Please select a batch",
                'toast_error' => 1]);
            return redirect('/viralbatch/dispatch');
            // return redirect('/viralbatch/complete_dispatch');
        }
        if(!$final_dispatch) return $this->get_rows($batches);
        
        foreach ($batches as $key => $value) {
            $batch = Viralbatch::find($value);
            $batch->datedispatched = date('Y-m-d');
            $batch->batch_complete = 1;
            $batch->pre_update();
        }
        Refresh::refresh_cache();
        
        return redirect('/viralbatch/index/1');
    }

    public function get_rows($batch_list=NULL)
    {
        ini_set('memory_limit', "-1");
        $batches = Viralbatch::select('viralbatches.*', 'facility_contacts.email', 'facilitys.name')
            ->leftJoin('facilitys', 'facilitys.id', '=', 'viralbatches.facility_id')
            ->leftJoin('facility_contacts', 'facilitys.id', '=', 'facility_contacts.facility_id')
            ->when($batch_list, function($query) use ($batch_list){
                return $query->whereIn('viralbatches.id', $batch_list);
            })
            ->where('batch_complete', 2)
            ->where('lab_id', env('APP_LAB'))
            ->get();

        $noresult_a = MiscViral::get_totals(0, null, true);
        $redraw_a = MiscViral::get_totals(5, null, true);
        $failed_a = MiscViral::get_totals(3, null, true);
        $detected_a = MiscViral::get_totals(2, null, true);
        $undetected_a = MiscViral::get_totals(1, null, true);

        $rejected = MiscViral::get_rejected(null, true);
        $date_modified = MiscViral::get_maxdatemodified(null, true);
        $date_tested = MiscViral::get_maxdatetested(null, true);
        $currentdate=date('d-m-Y');

        $batches->transform(function($batch, $key) use ($noresult_a, $redraw_a, $failed_a, $detected_a, $undetected_a, $rejected, $date_modified, $date_tested){

            $noresult = $noresult_a->where('batch_id', $batch->id)->first()->totals ?? 0;
            $redraw = $redraw_a->where('batch_id', $batch->id)->first()->totals ?? 0;
            $failed = $failed_a->where('batch_id', $batch->id)->first()->totals ?? 0;
            $detected = $detected_a->where('batch_id', $batch->id)->first()->totals ?? 0;
            $undetected = $undetected_a->where('batch_id', $batch->id)->first()->totals ?? 0;

            $rej = $rejected->where('batch_id', $batch->id)->first()->totals ?? 0;
            $results = $undetected + $detected;
            $total = $noresult + $failed + $redraw + $results + $rej;

            $dm = $date_modified->where('batch_id', $batch->id)->first()->mydate ?? '';
            $dt = $date_tested->where('batch_id', $batch->id)->first()->mydate ?? '';

            switch ($batch->batch_complete) {
                case 0:
                    $status = "In process";
                    break;
                case 1:
                    $status = "Dispatched";
                    break;
                case 2:
                    $status = "Awaiting Dispatch";
                    break;
                default:
                    break;
            }

            $batch->total = $total;
            $batch->redraw = $redraw;
            $batch->noresult = $noresult;
            $batch->result = $results;
            $batch->failed = $failed;
            $batch->rejected = $rej;
            $batch->date_modified = $dm;
            $batch->date_tested = $dt;
            $batch->status = $status;
            return $batch;
        });

        return view('tables.dispatch_viral', ['batches' => $batches, 'pending' => $batches->count(), 'batch_list' => $batch_list])->with('pageTitle', 'Batch Dispatch');

        // $table_rows = "";

        // foreach ($batches as $key => $batch) {

        //     $noresult = $this->checknull($noresult_a->where('batch_id', $batch->id));
        //     $redraw = $this->checknull($redraw_a->where('batch_id', $batch->id));
        //     $failed = $this->checknull($failed_a->where('batch_id', $batch->id));
        //     $detected = $this->checknull($detected_a->where('batch_id', $batch->id));
        //     $undetected = $this->checknull($undetected_a->where('batch_id', $batch->id));
        //     $rej = $this->checknull($rejected->where('batch_id', $batch->id));

        //     $results = $undetected + $detected;
        //     $total = $noresult + $failed + $redraw + $results + $rej;

        //     $dm = $date_modified->where('batch_id', $batch->id)->first()->mydate;
        //     $dt = $date_tested->where('batch_id', $batch->id)->first()->mydate;

        //     $maxdate=date("d-M-Y",strtotime($dm));

        //     $delays = MiscViral::working_days($maxdate, $currentdate);

        //     switch ($batch->batch_complete) {
        //         case 0:
        //             $status = "In process";
        //             break;
        //         case 1:
        //             $status = "Dispatched";
        //             break;
        //         case 2:
        //             $status = "Awaiting Dispatch";
        //             break;
        //         default:
        //             break;
        //     }

        //     $table_rows .= "<tr> 
        //     <td><div align='center'><input name='batches[]' type='checkbox' id='batches[]' value='{$batch->id}' /> </div></td>
        //     <td>{$batch->id}</td>
        //     <td>{$batch->name}</td>
        //     <td>{$batch->email}</td>
        //     <td>{$batch->datereceived}</td>
        //     <td>{$batch->created_at}</td>
        //     <td>{$delays}</td>
        //     <td>{$total}</td>
        //     <td>{$rej}</td>
        //     <td>{$results}</td>
        //     <td>{$failed}</td>
        //     <td>{$redraw}</td>
        //     <td>{$status}</td>
        //     <td><a href='" . url("/viralbatch/" . $batch->id) . "'>View</a> </td>
        //     </tr>";
        // }


        // return view('tables.dispatch_viral', ['rows' => $table_rows, 'pending' => $batches->count()])->with('pageTitle', 'Batch Dispatch');

    }

    public function approve_site_entry()
    {
        // ini_set('memory_limit', "-1");
        $query = Viralbatch::selectRaw("viralbatches.*, COUNT(viralsamples.id) AS sample_count, facilitys.name, creator.name as creator")
            ->leftJoin('viralsamples', 'viralbatches.id', '=', 'viralsamples.batch_id')
            ->leftJoin('facilitys', 'facilitys.id', '=', 'viralbatches.facility_id')
            ->leftJoin('users', 'users.id', '=', 'viralbatches.user_id')
            ->leftJoin('facilitys as creator', 'creator.id', '=', 'users.facility_id')
            ->whereNull('receivedstatus')
            ->whereNull('datedispatched')
            ->where('site_entry', 1)
            ->groupBy('viralbatches.id');

        if(env('APP_LAB') == 9){
            $batches = $query->paginate(20);
            $batches->setPath(url()->current());
        }
        else{
            $batches = $query->get();
        } 

        $batch_ids = $batches->pluck(['id'])->toArray();

        $noresult_a = MiscViral::get_totals(0, $batch_ids, false);

        $rejected = MiscViral::get_rejected($batch_ids, false);

        $batches->transform(function($batch, $key) use ($noresult_a, $rejected){

            $noresult = $noresult_a->where('batch_id', $batch->id)->first()->totals ?? 0;
            $rej = $rejected->where('batch_id', $batch->id)->first()->totals ?? 0;
            $total = $noresult + $rej;
            $batch->delays = '';
            $batch->datecreated = $batch->my_date_format('created_at');
            $batch->datereceived = $batch->my_date_format('datereceived');
            $batch->total = $total;
            $batch->rejected = $rej;
            $batch->result = '';
            $batch->noresult = $noresult;
            $batch->status = $batch->batch_complete;
            $batch->approval = true;
            return $batch;
        });

        if(env('APP_LAB') == 9) return view('tables.batches', ['batches' => $batches, 'site_approval' => true, 'pre' => 'viral']);

        return view('tables.batches', ['batches' => $batches, 'site_approval' => true, 'pre' => 'viral', 'datatable'=>true]);
        // return view('tables.batches', ['batches' => $batches, 'site_approval' => true, 'pre' => 'viral']);
    }

    public function sample_manifest(Request $request) {
        if ($request->method() == 'POST') {
            ini_set("memory_limit", "-1");
            ini_set("max_execution_time", "3000");
            $facility_user = \App\User::where('facility_id', '=', $request->input('facility_id'))->first();
            $batches = Viralbatch::whereRaw("(facility_id = $facility_user->facility_id or user_id = $facility_user->id)")
                            ->where('site_entry', '=', 1)
                            ->when(true, function($query) use ($request) {
                                if ($request->input('from') == $request->input('to'))
                                    return $query->whereRaw("date(`created_at`) = '" . date('Y-m-d', strtotime($request->input('from'))) . "'");
                                else
                                    return $query->whereRaw("date(`created_at`) BETWEEN '" . date('Y-m-d', strtotime($request->input('from'))) . "' AND '" . date('Y-m-d', strtotime($request->input('to'))) . "'");
                            })->get();
            foreach ($batches as $batch) {
                $batch->received_by = auth()->user()->id;
                $batch->time_received = date('Y-m-d H:i:s');
                $batch->datereceived = date('Y-m-d');
                $batch->pre_update();
                if(!empty($batch->samples)){
                    foreach ($batch->samples as $sample) {
                        $sample->sample_received_by = auth()->user()->id;
                        $sample->pre_update();
                    }
                }
                
            }
            Refresh::refresh_cache();
            $this->generate_sampleManifest($request, $facility_user);
            return back();
        }else 
            return view('forms.sample_manifest_form')->with('pageTitle', 'Generate Sample Manifest');
    }

    protected function generate_sampleManifest($request, $facility_user) {
        $dateString = 'for date(s)';
        if ($request->input('from') == $request->input('to'))
            $dateString .= date('Y-m-d', strtotime($request->input('from')));
        else
            $dateString .= date('Y-m-d', strtotime($request->input('from'))) . ' to ' . date('Y-m-d', strtotime($request->input('to')));
            
        $column = "viralsamples_view.patient, viralsamples_view.batch_id, facilitys.name as facility, facilitys.facilitycode, viralsampletype.name as sampletype, viralsamples_view.datecollected, viralsamples_view.created_at, viralsamples_view.entered_by, viralsamples_view.datedispatchedfromfacility, viralsamples_view.datereceived, rec.surname as receiver";
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "3000");
        $data = ViralsampleView::selectRaw($column)
                        ->leftJoin('facilitys', 'facilitys.id', '=', 'viralsamples_view.facility_id')
                        ->leftJoin('viralsampletype', 'viralsampletype.id', '=', 'viralsamples_view.sampletype')
                        ->leftJoin('users as rec', 'rec.id', '=', "viralsamples_view.received_by")
                        ->whereRaw("(`viralsamples_view`.`facility_id` = $facility_user->facility_id or `viralsamples_view`.`user_id` = $facility_user->id )")
                        ->where('viralsamples_view.site_entry', '=', 1)
                        ->when(true, function($query) use ($request) {
                            if ($request->input('from') == $request->input('to'))
                                return $query->whereRaw("date(`viralsamples_view`.`created_at`) = '" . date('Y-m-d', strtotime($request->input('from'))). "'");
                            else
                                return $query->whereRaw("date(`viralsamples_view`.`created_at`) BETWEEN '" . date('Y-m-d', strtotime($request->input('from'))) . "' AND '" . date('Y-m-d', strtotime($request->input('to'))) . "'");
                        })->orderBy('created_at', 'asc')->get();
        // dd($data);
        $export['samples'] = $data;
        $export['testtype'] = 'VL';
        $export['lab'] = \App\Lab::find(env('APP_LAB'));
        $export['period'] = strtoupper($dateString);
        $filename = strtoupper("HIV VL MANIFEST " . $dateString) . ".pdf";
        $mpdf = new Mpdf();
        $view_data = view('exports.mpdf_samples_manifest', $export)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }

    public function site_entry_approval(Viralbatch $batch)
    {
        $viralsample = Viralsample::where('batch_id', $batch->id)->whereNull('receivedstatus')->get()->first();

        if($viralsample){
            session(['site_entry_approval' => true]);
            $viralsample->load(['patient', 'batch']);
            $data = Lookup::viralsample_form();
            $data['viralsample'] = $viralsample;
            $data['site_entry_approval'] = true;
            return view('forms.viralsamples', $data);
        }
        else{
            $batch->received_by = auth()->user()->id;
            $batch->time_received = date('Y-m-d H:i:s');
            $batch->save();
            Refresh::refresh_cache();
            session(['toast_message' => "All the samples in the batch have been received."]);
            return redirect('viralbatch/site_approval');
        }
    }


    public function site_entry_approval_group(Viralbatch $batch)
    {
        $samples = Viralsample::with(['patient'])->where('batch_id', $batch->id)->whereRaw("(receivedstatus is null or receivedstatus=0)")->get();

        if($samples->count() > 0){            
            $data = Lookup::viralsample_form();
            $batch->load(['creator.facility', 'view_facility']);
            $data['batch'] = $batch;
            $data['samples'] = $samples;
            $data['pageTitle'] = "Approve batch";
            return view('forms.approve_viralbatch', $data);
        }
        else{
            session(['toast_message' => "All the samples in the batch have been received."]);
            return redirect('batch/site_approval');
        }
    }

    public function site_entry_approval_group_save(Request $request, Viralbatch $batch)
    {
        if(env('APP_LAB') != 8){
            $request->validate([
                'datereceived' => ['required', 'before_or_equal:today', 'date_format:Y-m-d'],
            ]);
        }
        
        $sample_ids = $request->input('samples');
        $rejectedreason_array = $request->input('rejectedreason');
        $submit_type = $request->input('submit_type');

        if(!$sample_ids) return back();

        foreach ($sample_ids as $key => $value) {
            $sample = Viralsample::find($value);
            if($sample->batch_id != $batch->id) continue;

            $sample->labcomment = $request->input('labcomment');
            if ($sample->sample_received_by == NULL)
                $sample->sample_received_by = $request->input('received_by');

            if($submit_type == "accepted"){
                $sample->receivedstatus = 1;
            }else if($submit_type == "rejected"){
                $sample->receivedstatus = 2;
                $sample->rejectedreason = $rejectedreason_array[$key] ?? null;
                if(!$sample->rejectedreason){
                    session(['toast_error' => 1, 'toast_message' => 'Please set a rejected reason for all the samples that you wish to reject.']);
                    return back();
                }
            }
            $sample->save();
        }
        // // $batch->received_by = auth()->user()->id;
        if ($batch->received_by == NULL) {
            $batch->received_by = auth()->user()->id ?? $request->input('received_by');
            $batch->time_received = date('Y-m-d H:i:s');
            $batch->datereceived = $request->input('datereceived');
        }
        
        $batch->save();
        Refresh::refresh_cache();
        session(['toast_message' => 'The selected samples have been ' . $submit_type]);

        if($submit_type == "accepted"){            
            $work_samples_dbs = MiscViral::get_worksheet_samples(2, false, 1);
            $work_samples_edta = MiscViral::get_worksheet_samples(2, false, 2);

            $str = '';

            if($work_samples_dbs['count'] > 92) $str .= 'You now have ' . $work_samples_dbs['count'] . ' DBS samples that are eligible for testing.<br />';

            if($work_samples_edta['count'] > 20) $str .=  'You now have ' . $work_samples_edta['count'] . ' Plasma / EDTA samples that are eligible for testing.';

            if($str != '') session(['toast_message' => $str]);
        }

        $sample = Viralsample::where('batch_id', $batch->id)->whereNull('receivedstatus')->get()->first();
        if($sample) return back();
        return redirect('viralbatch/site_approval');        
    }

    /**
     * Print the specified resource.
     *
     * @param  \App\Viralbatch  $batch
     * @return \Illuminate\Http\Response
     */
    public function individual(Viralbatch $batch)
    {
        if(!$batch->dateindividualresultprinted){
            $batch->dateindividualresultprinted = date('Y-m-d');
            $batch->pre_update();
        }

        $data = Lookup::get_viral_lookups();
        $samples = $batch->sample;
        //$samples->load(['patient', 'approver', 'batch.lab', 'batch.facility', 'batch.receiver', 'batch.creator']);
	 $samples->load(['patient','worksheet', 'approver', 'batch.lab', 'batch.facility', 'batch.receiver', 'batch.creator']);
	$i = 0;
        foreach ($samples as $key => $sample){
            // if(!$sample[$i]->worksheet_id) continue;
            $data['labs'][$i] =  DB::table('viralworksheets')
            ->where('viralworksheets.id', $sample->worksheet_id )
            ->get();
            $i++;
        }
        $data['samples'] = $samples;

        return view('exports.mpdf_viralsamples', $data)->with('pageTitle', 'Individual Batch');
    }

    public function individual_pdf(Viralbatch $batch)
    {
        $filename = "individual_results_for_batch_" . $batch->id . ".pdf";

        $mpdf = new Mpdf();
        $data = Lookup::get_lookups();
        $samples = $batch->sample;
        $samples->load(['patient', 'approver', 'batch.lab', 'batch.facility', 'batch.receiver', 'batch.creator']);
        $data['samples'] = $samples;
        $view_data = view('exports.mpdf_viralsamples', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }

    /**
     * Print the specified resource.
     *
     * @param  \App\Viralbatch  $batch
     * @return \Illuminate\Http\Response
     */
    public function summary(Viralbatch $batch)
    {
        if(!$batch->datebatchprinted){
            $batch->datebatchprinted = date('Y-m-d');
            $batch->printedby = auth()->user()->id;
            $batch->pre_update();
        }

        $batch->load(['sample.patient', 'facility', 'lab', 'receiver', 'creator']);
        $data = Lookup::get_viral_lookups();
        $data['batches'] = [$batch];

        $filename = "summary_results_for_batch_" . $batch->id . ".pdf";

        $mpdf = new Mpdf(['format' => 'A4-L']);
        $view_data = view('exports.mpdf_viralsamples_summary', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
        // $mpdf->Output('summary.pdf', \Mpdf\Output\Destination::INLINE);
    }

    public function summaries(Request $request)
    {
        $batch_ids = $request->input('batch_ids');
        if(!$batch_ids) return back();
        if($request->input('print_type') == "individual") return $this->individuals($batch_ids);
        if($request->input('print_type') == "envelope") return $this->envelopes($batch_ids);
        $batches = Viralbatch::whereIn('id', $batch_ids)->with(['sample.patient', 'facility', 'lab', 'receiver', 'creator'])->get();

        foreach ($batches as $key => $batch) {
            if(!$batch->datebatchprinted){
                $batch->datebatchprinted = date('Y-m-d');
                $batch->printedby = auth()->user()->id;
                $batch->pre_update();
            }
        }
        
        $data = Lookup::get_viral_lookups();
        $data['batches'] = $batches;
        $mpdf = new Mpdf(['format' => 'A4-L']);
        $view_data = view('exports.mpdf_viralsamples_summary', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output('summary.pdf', \Mpdf\Output\Destination::DOWNLOAD);
        // $mpdf->Output('summary.pdf', \Mpdf\Output\Destination::INLINE);
        
        // $pdf = DOMPDF::loadView('exports.viralsamples_summary', $data)->setPaper('a4', 'landscape');
        // return $pdf->stream('summary.pdf');
    }

    public function individuals($batch_ids)
    {
        $samples = Viralsample::whereIn('batch_id', $batch_ids)->with(['patient', 'approver'])->orderBy('batch_id')->get();
        $samples->load(['batch.lab', 'batch.facility', 'batch.receiver', 'batch.creator']);
        $data = Lookup::get_viral_lookups();
        $data['samples'] = $samples;

        Viralbatch::whereIn('id', $batch_ids)->update(['dateindividualresultprinted' => date('Y-m-d')]);

        return view('exports.mpdf_viralsamples', $data)->with('pageTitle', 'Individual Batch');
    }

    public function envelope(Viralbatch $batch)
    {
        $batch->load(['facility.facility_contact', 'view_facility']);
        $data['batches'] = [$batch];
        return view('exports.envelopes', $data);
    }

    public function envelopes($batch_ids)
    {
        $batches = Viralbatch::whereIn('id', $batch_ids)->with(['facility.facility_contact', 'view_facility'])->get();
        return view('exports.envelopes', ['batches' => $batches]);
    }

    public function email(Viralbatch $batch)
    {
        // $facility = Facility::find($batch->facility_id);
        // if($facility->email != null || $facility->email != '')
        // {
        //     $mail_array = array('joelkith@gmail.com', 'tngugi@gmail.com', 'baksajoshua09@gmail.com');
        //     if(env('APP_ENV') == 'production') $mail_array = $facility->email_array;
        //     Mail::to($mail_array)->cc(['joel.kithinji@dataposit.co.ke', 'joshua.bakasa@dataposit.co.ke'])->send(new VlDispatch($batch));
        // }

        // if(!$batch->sent_email){
        //     $batch->sent_email = true;
        //     $batch->dateemailsent = date('Y-m-d');
        //     $batch->save();
        // }
        MiscViral::dispatch_batch($batch);
        session(['toast_message' => "The batch {$batch->id} has had its results sent to the facility."]);
        return back();
    }

    public function dispatch_report($batch_complete, $date_start=NULL, $date_end=NULL, $facility_id=NULL, $subcounty_id=NULL, $partner_id=NULL)
    {
        ini_set('memory_limit', '-1');
        $date_column = "datereceived";
        if(in_array($batch_complete, [1, 6])) $date_column = "datedispatched";

        if(!$date_start){
            session(['toast_error' => 1, 'toast_message' => 'Please select a date range.']);
            return back();
        }

        $samples = ViralsampleView::select(['viralsamples_view.*', 'view_facilitys.subcounty',])
            ->leftJoin('view_facilitys', 'view_facilitys.id', '=', 'viralsamples_view.facility_id')
            // ->leftJoin('users', 'users.id', '=', 'batches.user_id')
            ->when($date_start, function($query) use ($date_column, $date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate($date_column, '>=', $date_start)
                    ->whereDate($date_column, '<=', $date_end);
                }
                return $query->whereDate($date_column, $date_start);
            })
            ->when($facility_id, function($query) use ($facility_id){
                return $query->where('facility_id', $facility_id);
            })
            ->when($subcounty_id, function($query) use ($subcounty_id){
                return $query->where('subcounty_id', $subcounty_id);
            })
            ->when($partner_id, function($query) use ($partner_id){
                return $query->where('partner_id', $partner_id);
            })
            ->when(true, function($query) use ($batch_complete){
                if($batch_complete < 4) return $query->where('batch_complete', $batch_complete);

                else if($batch_complete == 5){
                    return $query->whereNull('datereceived')
                        ->where(['site_entry' => 1, 'batch_complete' => 0])
                        ->where('created_at', '<', date('Y-m-d', strtotime('-10 days')));
                }

                else if($batch_complete == 6){
                    return $query->where('batch_complete', 1)->where('tat5', '<', 11);
                }
            })
            ->when(true, function($query) use ($batch_complete){
                if(in_array($batch_complete, [1, 6])) return $query->orderBy('datedispatched', 'desc');
                return $query->orderBy('created_at', 'desc');
            })
            ->where(['viralsamples_view.lab_id' => env('APP_LAB'), 'repeatt' => 0])
            // ->where('batch_complete', 1)
            // ->orderBy($date_column, 'desc')
            // ->orderBy('batch_id', 'desc')
            ->get();

        $data = [];

        foreach ($samples as $key => $sample) {
            $data[$key]['#'] = $key+1;
            $data[$key]['Batch #'] = $sample->batch_id;
            $data[$key]['Facility'] = $sample->facilityname;
            $data[$key]['Sub County'] = $sample->subcounty;
            $data[$key]['Sample/Patient ID'] = $sample->{'patient'};
            $data[$key]['Gender'] = $sample->gender;
            $data[$key]['Date of Birth'] = $sample->my_date_format('dob');
            $data[$key]['Test Outcome'] = $sample->result;
            $data[$key]['Date Collected'] = $sample->my_date_format('datecollected');
            $data[$key]['Date Received'] = $sample->my_date_format('datereceived');
            $data[$key]['Date Tested'] = $sample->my_date_format('datetested');
            $data[$key]['Date Dispatched'] = $sample->my_date_format('datedispatched');
            $data[$key]['Date Email Sent'] = $sample->my_date_format('dateemailsent');
            $data[$key]['Date Individual Results Printed'] = $sample->my_date_format('dateindividualresultprinted');
            $data[$key]['Date Batch Printed'] = $sample->my_date_format('datebatchprinted');
            $data[$key]['Lab TAT'] = $sample->tat5;
            $data[$key]['Time Dispatched'] = '';
            $data[$key]['Dispatched By'] = '';
            $data[$key]['Initials'] = '';
            $data[$key]['Collected By'] = '';            
        }

        if(!$data) return null;

        $date_start = Lookup::my_date_format($date_start);
        $date_end = Lookup::my_date_format($date_end);

        $filename = "DISPATCH REPORT FOR Vl RESULTS DISPATCHED BETWEEN {$date_start} AND {$date_end}";
        return MiscViral::csv_download($data, $filename);
    }

    public function convert_to_site_entry(Viralbatch $batch)
    {
        if($batch->site_entry == 2 && !$batch->datedispatched){
            $batch->site_entry = 1;
            $batch->lab_id = env('APP_LAB');
            $batch->save();
            session(['toast_message' => 'The batch has been converted to a site entry']);
            return back();
        }
        session(['toast_message' => 'The batch has not been converted to a site entry', 'toast_error' => 1]);
        return back();
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        // $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}')";
        $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";

        $search = $request->input('search');
        $batches = Viralbatch::whereRaw("id like '" . $search . "%'")
            ->when(true, function($query) use ($user, $string){
                if($user->user_type_id == 5) return $query->whereRaw($string);
                return $query->whereRaw("(lab_id={$user->lab_id} or site_entry=2)");
            })
            ->paginate(10);

        $batches->setPath(url()->current());
        return $batches;
    }

    public function checknull($var)
    {
        return $var->first()->totals ?? 0;
    }

    public function batches_transformer(&$batches, $batch_complete=4)
    {
        $subtotals = $date_modified = $date_tested = null;

        $batches->setPath(url()->current());

        $batch_ids = $batches->pluck(['id'])->toArray();

        if($batch_ids){
            $noresult_a = MiscViral::get_totals(0, $batch_ids, false);
            $redraw_a = MiscViral::get_totals(5, $batch_ids, false);
            $failed_a = MiscViral::get_totals(3, $batch_ids, false);
            $detected_a = MiscViral::get_totals(2, $batch_ids, false);
            $undetected_a = MiscViral::get_totals(1, $batch_ids, false);
            
            $date_modified = MiscViral::get_maxdatemodified($batch_ids, false);
            $date_tested = MiscViral::get_maxdatetested($batch_ids, false);

            $rejected = MiscViral::get_rejected($batch_ids, false);

            if($batch_complete == 1) $subtotals = MiscViral::get_subtotals($batch_ids, false);
        }
        else{
            $noresult_a = $redraw_a = $failed_a = $detected_a = $undetected_a = $rejected = false;
        }

        $batches->transform(function($batch, $key) use ($batch_complete, $undetected_a, $detected_a, $failed_a, $redraw_a, $noresult_a, $rejected, $subtotals, $date_modified, $date_tested){

            if(!$noresult_a && !$redraw_a && !$failed_a && !$detected_a && !$undetected_a && !$rejected){
                $total = $rej = $result = $noresult = 0;
            }else{
                $undetected = $undetected_a->where('batch_id', $batch->id)->first()->totals ?? 0;
                $detected = $detected_a->where('batch_id', $batch->id)->first()->totals ?? 0;
                $failed = $failed_a->where('batch_id', $batch->id)->first()->totals ?? 0;
                $redraw = $redraw_a->where('batch_id', $batch->id)->first()->totals ?? 0;
                $noresult = $noresult_a->where('batch_id', $batch->id)->first()->totals ?? 0;

                $rej = $rejected->where('batch_id', $batch->id)->first()->totals ?? 0;
                $total = $undetected + $detected + $failed + $redraw + $noresult + $rej;

                $result = $detected + $undetected + $redraw + $failed;
            }

            if($batch_complete == 1){
                $und = $subtotals->where('batch_id', $batch->id)->where('rcategory', 1)->first()->totals ?? 0;
                $under1000 = $subtotals->where('batch_id', $batch->id)->where('rcategory', 2)->first()->totals ?? 0;
                $under5000 = $subtotals->where('batch_id', $batch->id)->where('rcategory', 3)->first()->totals ?? 0;
                $over5000 = $subtotals->where('batch_id', $batch->id)->where('rcategory', 4)->first()->totals ?? 0;
                $unknown = $subtotals->where('batch_id', $batch->id)->where('rcategory', 0)->first()->totals ?? 0;
                $f = $subtotals->where('batch_id', $batch->id)->where('rcategory', 5)->first()->totals ?? 0;

                $batch->suppressed = $und + $under1000;
                $batch->nonsuppressed = $under5000 + $over5000;
                $batch->failures = $unknown + $f;
            }

            $batch->date_modified = $date_modified->where('batch_id', $batch->id)->first()->mydate ?? '';
            $batch->date_tested = $date_tested->where('batch_id', $batch->id)->first()->mydate ?? '';


            $batch->creator = $batch->surname . ' ' . $batch->oname;
            $batch->receptor = $batch->rsurname . ' ' . $batch->roname;
            $batch->datecreated = $batch->my_date_format('created_at');
            $batch->datereceived = $batch->my_date_format('datereceived');
            $batch->datedispatched = $batch->my_date_format('datedispatched');
            $batch->total = $total;
            $batch->rejected = $rej;
            $batch->result = $result;
            $batch->noresult = $noresult;
            $batch->status = $batch->batch_complete;
            $batch->approval = false;
            return $batch;
        });

        return $batches;
    }
    public function approve_emr_entry()
    {
        // ini_set('memory_limit', "-1");
        $query = Viralbatch::selectRaw("viralbatches.*, COUNT(viralsamples.id) AS sample_count, facilitys.name, creator.name as creator")
            ->leftJoin('viralsamples', 'viralbatches.id', '=', 'viralsamples.batch_id')
            ->leftJoin('facilitys', 'facilitys.id', '=', 'viralbatches.facility_id')
            ->leftJoin('users', 'users.id', '=', 'viralbatches.user_id')
            ->leftJoin('facilitys as creator', 'creator.id', '=', 'users.facility_id')
            ->whereNull('receivedstatus')
            ->whereNull('datedispatched')
            ->whereNotNull('order_no')
            ->groupBy('viralbatches.id');

        if(env('APP_LAB') == 9){
            $batches = $query->paginate(20);
            $batches->setPath(url()->current());
        }
        else{
            $batches = $query->get();
        }

        $batch_ids = $batches->pluck(['id'])->toArray();

        $noresult_a = MiscViral::get_totals(0, $batch_ids, false);

        $rejected = MiscViral::get_rejected($batch_ids, false);

        $batches->transform(function($batch, $key) use ($noresult_a, $rejected){

            $noresult = $noresult_a->where('batch_id', $batch->id)->first()->totals ?? 0;
            $rej = $rejected->where('batch_id', $batch->id)->first()->totals ?? 0;
            $total = $noresult + $rej;
            $batch->delays = '';
            $batch->datecreated = $batch->my_date_format('created_at');
            $batch->datereceived = $batch->my_date_format('datereceived');
            $batch->total = $total;
            $batch->rejected = $rej;
            $batch->result = '';
            $batch->noresult = $noresult;
            $batch->status = $batch->batch_complete;
            $batch->approval = true;
            return $batch;
        });

        if(env('APP_LAB') == 9) return view('tables.batches', ['batches' => $batches, 'emr_approval' => true, 'pre' => 'viral']);

        return view('tables.batches', ['batches' => $batches, 'emr_approval' => true, 'pre' => 'viral', 'datatable'=>true]);
    }
}