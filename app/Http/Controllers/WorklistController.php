<?php

namespace App\Http\Controllers;

use App\Worklist;
use App\SampleView;
use App\Sample;
use App\ViralsampleView;
use App\Viralsample;
use Illuminate\Http\Request;

class WorklistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($testtype = null)
    {
        $facility_id = auth()->user()->facility_id;
        $worklists = Worklist::with(['facility'])->when($testtype, function($query) use ($testtype){
            return $query->where('testtype', $testtype);
        })->where('facility_id', $facility_id)->get();
        $eid_samples = $this->get_worklists(1);
        $vl_samples = $this->get_worklists(2);

        $worklists->transform(function($worklist, $key) use ($eid_samples, $vl_samples){
            if($worklist->testtype == 1){
                $worklist->sample_count = $eid_samples->where('worksheet_id', $worklist->id)->first()->totals ?? 0;
            }
            else if($worklist->testtype == 2){
                $worklist->sample_count = $vl_samples->where('worksheet_id', $worklist->id)->first()->totals ?? 0;
            }
            
            return $worklist;
        });

        return view('tables.worklists', ['worklists' => $worklists]);
    }

    /**
     * Show the form for creating a new resource.
     * By default create a eid worklist.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($testtype = 1)
    {
        if($testtype == 1){
            $model = SampleView::class;
            $worklist_type = "Eid";
        }else{ 
            $model = ViralsampleView::class; 
            $worklist_type = "Vl";
        }
        $facility_id = auth()->user()->facility_id;
        $user_id = auth()->user()->id;

        $samples = $model::whereNull('worksheet_id')
            ->whereIn('receivedstatus', [1, 3])
            ->whereRaw("((result IS NULL ) OR (result='0' ))")
            ->whereRaw("(facility_id = {$facility_id} or user_id = {$user_id} or lab_id = {$facility_id})")
            ->where('input_complete', true)
            ->where('site_entry', 2)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('forms.worklists', ['samples' => $samples, 'worklist_type' => $worklist_type]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $submit_type = $request->input('submit_type');
        if($submit_type == "rejected") return redirect('worklist');

        $samples = $request->input('samples');
        if(!is_array($samples)){
            session(['toast_error' => 1, 'toast_message' => 'Please select samples before attempting to create a worklist.']);
            return back();
        }
        $testtype = $request->input('testtype');
        $worklist = new Worklist;
        $worklist->facility_id = auth()->user()->facility_id;
        $worklist->testtype = $testtype;
        $worklist->save();

        if($testtype == 1){
            $model = Sample::class;
        }else{ 
            $model = Viralsample::class; 
        }
        $model::whereIn('id', $samples)->update(['worksheet_id' => $worklist->id]);
        session(['toast_message' => 'The worklist has been created.']);
        return redirect('worklist');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Worklist  $worklist
     * @return \Illuminate\Http\Response
     */
    public function show(Worklist $worklist)
    {
        if($worklist->testtype == 1){
            $model = SampleView::class;
            $worklist_type = "Eid";
        }else{ 
            $model = ViralsampleView::class; 
            $worklist_type = "Vl";
        }

        $samples = $model::with(['facility'])
            ->where('worksheet_id', $worklist->id)
            ->where('site_entry', 2)
            ->get();

        return view('worksheets.worklists', ['worklist' => $worklist, 'samples' => $samples]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Worklist  $worklist
     * @return \Illuminate\Http\Response
     */
    public function edit(Worklist $worklist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Worklist  $worklist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Worklist $worklist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Worklist  $worklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(Worklist $worklist)
    {
        //
    }

    /**
     * Print the specified resource.
     *
     * @param  \App\Worklist  $worklist
     * @return \Illuminate\Http\Response
     */
    public function print(Worklist $worklist)
    {
        if($worklist->testtype == 1){
            $model = SampleView::class;
            $worklist_type = "Eid";
        }else{ 
            $model = ViralsampleView::class; 
            $worklist_type = "Vl";
        }

        $samples = $model::with(['facility'])
            ->where('worksheet_id', $worklist->id)
            ->where('site_entry', 2)
            ->get();

        return view('worksheets.worklists', ['worklist' => $worklist, 'samples' => $samples, 'print' => true]);
    }

    public function get_worklists($type=1, $worklist_id=NULL)
    {
        $sample_view = ViralsampleView::class;
        if($type == 1) $sample_view = SampleView::class;
        $samples = $sample_view::selectRaw("count(*) as totals, worksheet_id")
            ->whereNotNull('worksheet_id')
            ->when($worklist_id, function($query) use ($worklist_id){                
                if (is_array($worklist_id)) {
                    return $query->whereIn('worksheet_id', $worklist_id);
                }
                return $query->where('worksheet_id', $worklist_id);
            })
            ->whereNotNull('worksheet_id')
            ->where('receivedstatus', '!=', 2)
            ->where('site_entry', 2)
            ->groupBy('worksheet_id')
            ->get();

        return $samples;
    }
}
