<?php

namespace App\Http\Controllers;

use App\CovidPool;
use App\CovidPoolSample;
use App\CovidSample;
use App\CovidWorksheet;
use App\Lookup;
use Illuminate\Http\Request;

class CovidPoolController extends Controller
{

    public function __construct()
    {
        $this->middleware('covid_allowed');   
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($state=0, $date_start=NULL, $date_end=NULL, $pool_id=NULL)
    {
        $pools = CovidPool::with(['creator', 'worksheet'])
            ->when($pool_id, function ($query) use ($pool_id){
                return $query->where('id', $pool_id);
            })
            ->when($state, function ($query) use ($state){
                if($state == 1 || $state == 12) $query->orderBy('id', 'asc');
                return $query->where('status_id', $state);
            })
            ->when($date_start, function($query) use ($date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate('created_at', '>=', $date_start)
                    ->whereDate('created_at', '<=', $date_end);
                }
                return $query->whereDate('created_at', $date_start);
            })
            ->where('lab_id', auth()->user()->lab_id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        $pools->setPath(url()->current());

        $data = Lookup::worksheet_lookups();

        $pools->transform(function($pool, $key) use ($data){
            $pool->machine = $data['machines']->where('id', $pool->machine_type)->first()->output ?? '';
            $pool->status = $data['worksheet_statuses']->where('id', $pool->status_id)->first()->output ?? '';
            return $pool;
        });
        $data['pools'] = $pools;
        $data['myurl'] = url('covid_pool/index/' . $state . '/');
        $data['link_extra'] = 'covid_';
        return view('tables.covid_pools', $data)->with('pageTitle', 'Covid Worksheet Pools');   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $worksheets = CovidWorksheet::where(['lab_id' => auth()->user()->lab_id, 'status_id' => 1])
            /*->whereRaw("id NOT IN (
                    SELECT DISTINCT worksheet_id
                    FROM covid_samples
                    WHERE parentid > 0 AND site_entry != 2
                )")*/
            ->get();

        return view('forms.covid_pool', compact('worksheets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $worksheets = CovidWorksheet::with(['sample'])
            ->whereIn('id', $request->input('worksheet_ids'))
            ->where(['lab_id' => auth()->user()->lab_id, 'status_id' => 1])
            ->get();

        if(!$worksheets){
            session(['toast_error' => 1, 'toast_message' => 'No worksheets selected.']);
            return back();
        }

        $covidPool = new CovidPool;
        $covidPool->machine_type = $worksheets->first()->machine_type;
        $covidPool->createdby = auth()->user()->id;
        $covidPool->lab_id = auth()->user()->lab_id;
        $covidPool->save();

        foreach ($worksheets as $worksheet) {
            $worksheet->pool_id = $covidPool->id;
            $worksheet->save();
        }

        for ($key=0; $key < 94; $key++) { 
            $position = $key + 1;

            $pool_sample = $covidPool->pool_sample()->create(compact('position'));

            foreach ($worksheets as $worksheet) {
                $sample = $worksheet->sample[$key] ?? null;
                if(!$sample) continue;
                $sample->pool_sample_id = $pool_sample->id;
                $sample->save();
            }

            if(!$pool_sample->sample->count()) break;
        }

        session(['toast_message' => 'The Covid pool has been created.']);
        return redirect('/covid_pool');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CovidPool  $covidPool
     * @return \Illuminate\Http\Response
     */
    public function show(CovidPool $covidPool, $print=false)
    {
        $covidPool->load(['pool_sample.sample.patient', 'worksheet']);
        if($print) $data['print'] = true;
        $data['covidPool'] = $covidPool;
        return view('worksheets.pools', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CovidPool  $covidPool
     * @return \Illuminate\Http\Response
     */
    public function edit(CovidPool $covidPool)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CovidPool  $covidPool
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CovidPool $covidPool)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CovidPool  $covidPool
     * @return \Illuminate\Http\Response
     */
    public function destroy(CovidPool $covidPool)
    {
        //
    }
    
    public function convert_pool(CovidPool $pool, $machine_type)
    {
        if($pool->lab_id != auth()->user()->lab_id && auth()->user()->user_type_id) abort(403);
        if($pool->status_id != 1){
            session(['toast_error' => 1, 'toast_message' => 'The pool cannot be converted to the requested type.']);
            return back();            
        }
        $pool->machine_type = $machine_type;
        $pool->save();
        session(['toast_message' => 'The pool has been converted.']);
        return back();
    }

    public function print(CovidPool $pool)
    {
        return $this->show($pool, true);
    }

    public function cancel(CovidPool $pool)
    {
        if($pool->lab_id != auth()->user()->lab_id && auth()->user()->user_type_id) abort(403);
        if($pool->status_id != 1){
            session(['toast_error' => 1, 'toast_message' => 'The pool is not eligible to be cancelled.']);
            return back();
        }
        $pool->status_id = 4;
        $pool->save();
        $pool->worksheet()->update(['pool_id' => null]);

        foreach ($pool->pool_sample as $pool_sample) {
            foreach ($pool_sample->sample as $sample) {
                $sample->pool_sample_id = null;
                $sample->save();
            }
            $pool_sample->delete();
        }


        session(['toast_message' => 'The pool has been cancelled.']);
        return redirect("/covid_pool");
    }
}
