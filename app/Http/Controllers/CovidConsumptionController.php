<?php

namespace App\Http\Controllers;

use App\CovidConsumption;
use App\CovidConsumptionDetail;
use App\CovidKit;
use App\CovidSample;
use App\HCMPCovidAllocations;
use App\CovidAllocation;
use App\CovidAllocationDetail;
use App\Machine;
use App\Synch;
use DB;
use Illuminate\Http\Request;

class CovidConsumptionController extends Controller
{
    public function index()
    {
        $consumption = new CovidConsumption;
        $weeks = $consumption->getMissingConsumptions();
        
        if (sizeof($weeks) == 0) {
            session(['toast_message' => "Covid Consumption already filled.",
                'toast_error' => true]);
            return redirect('pending');
        }

        $time = collect($weeks)->first();
        $user = auth()->user();
    	
        $kits = CovidKit::with('machine')->when($user, function($query) use ($user){
                                        if ($user->user_type_id == 12)
                                            return $query->where('type', '<>', 'Kit');
                                        else
                                            return $query->where('type', '<>', 'Manual');
                                    })->orderBy('machine', 'desc')->get();/*->groupby('machine');*/

        if ($user->user_type_id == 12){
            $kits = $kits->groupby('type')->sortKeysDesc();
            $tests = $consumption->getTestsDone($time->week_start, $time->week_end);
            return view('tasks.covid.manualconsumption', [
                            'covidkits' => $kits,
                            'tests' => $tests,
                            'time' => $time
                        ]);
        } else {
            $allocations = $this->checkCovidAllocations($time->week_end);
            
            if(!$allocations->isEmpty()) {
                return view('tasks.covid.allocation', ['allocations' => $allocations]);
            }
            
            $allocations = $this->getWeekCovidAllocations($time->week_start,$time->week_end);
            // dd($allocations);
            $kits = $kits->groupby('machine');
            return view('tasks.covid.consumption',
                        [
                            'covidkits' => $kits,
                            'time' => $time,
                            'allocations' => $allocations
                        ]);
        }
    }

    public function pending()
    {
        // $data['covidconsumption'] = CovidConsumption::where('start_of_week', '=', $this->getPreviousWeek()->week_start)
        //                                 ->where('lab_id', '=', auth()->user()->lab_id)->count();
        $covidconsumption = new CovidConsumption;
        $data['time'] = $covidconsumption->getMissingConsumptions();
        // dd($data);
        return view('tasks.covid.manual', $data);
    }

    public function submitConsumption(Request $request)
    {
        if ($request->has('ending')){
            foreach ($request->input('ending') as $key => $value) {
                $value = (int)$value;
                if ($value < 0){
                    session(['toast_message' => 'No negative ending balances are allowed. Please fill in the respective kits received to proceed with this submission', 'toast_error' => true]);
                    return back();
                }
            }
        }
        $consumption = new CovidConsumption;
        $time = collect($consumption->getMissingConsumptions())->first();
        // $time = $this->getPreviousWeek();
        if ($request->input('week_start') != $time->week_start) {
            session(['toast_message' => "Bad Request in submitting the form kindly refresh your browser and try again.",
                'toast_error' => true]);
            return back();
        }
        
        $data = $this->buildConsumptionData($request);
        
        $tests = [];
        if (!$request->has('machine')) {
            $tests[] = ['Manual' => $request->input('tests')];
        } else {
            foreach ($request->input('machine') as $key => $id) {
                $machine = Machine::find($id);
                $tests[] = [$machine->machine => $machine->getCovidTestsDone($time->week_start, $time->week_end)];
            }
        }
    	
        if (CovidConsumption::where('start_of_week', '=', $time->week_start)
                    ->where('lab_id', env('APP_LAB'))->get()->isEmpty()) {
            // Start transaction!
            DB::beginTransaction();

            try {
                $lab = env('APP_LAB');
                if (auth()->user()->user_type_id == 12)
                    $lab = auth()->user()->lab_id;
                $consumption = new CovidConsumption;
                $consumption->fill([
                                'start_of_week' => $time->week_start,
                                'end_of_week' => $time->week_end,
                                'week' => $time->week,
                                'lab_id' => $lab
                            ]);
                $consumption->tests = json_encode($tests);
                $consumption->save();

                foreach ($data as $key => $detail) {
                    $consumption_detail = new CovidConsumptionDetail;
                    $consumption_detail->consumption_id = $consumption->id;
                    $consumption_detail->fill($detail);
                    $consumption_detail->save();
                }
                DB::commit();
            } catch(\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    	$consumption = new CovidConsumption;
        $weeks = $consumption->getMissingConsumptions();
        if (sizeof($weeks) == 0) {
            if (auth()->user()->user_type_id != 12)
                $this->reportRelease();
            if (env('APP_SECURE_URL'))
                Synch::synchCovidConsumption();
        }
        if (auth()->user()->user_type_id == 12)
            return redirect('covidkits/pending');   
        return redirect('pending');
    	
    }

    public function submitAllocation(Request $request)
    {
        if (!$request->has(['received', 'response'])) {
            session(['toast_error' => true, 'toast_message' => 'Bad request. The posted details are incomplete']);
        }

        foreach ($request->input('datereceived') as $key => $datereceived) {
            if ($request->input('response') == 'YES' && !isset($datereceived)) {
                session(['toast_error' => true, 'toast_message' => 'Bad request. The posted details are incomplete. Ensure date received is set']);
                return back();
            }


            $allocations = CovidAllocation::whereDate('allocation_date', date('Y-m-d', strtotime($key)))->get();
            if ($request->input('response') == 'YES') {
                $already_submitted_consumption = CovidConsumption::where('start_of_week', '<', $datereceived)
                                                                ->where('end_of_week', '>', $datereceived)
                                                                ->get();

                if (!$already_submitted_consumption->isEmpty()) {
                    if ($request->has('consumption_confirmation')) {
                        $consumption = $already_submitted_consumption->first();
                        if ($request->input('amend')) {
                            $details = $consumption->details;
                            foreach ($request->input('received') as $key => $value) {
                                $allocation_detail = CovidAllocationDetail::find($key);
                                $detail = $details->where('kit_id', $allocation_detail->kit->id);
                                if (!$detail->isEmpty()) {
                                    $detail = $detail->first();
                                    $orig_received = $detail->received;
                                    $orig_end = $detail->ending;
                                    $new_received = $value ?? 0;
                                    $new_end = (($orig_end - $orig_received) + $new_received);
                                    if ($new_end < 0)
                                        $new_end = 0;
                                    $detail->received = $new_received;
                                    $detail->ending = $new_end;
                                    $detail->save();
                                }
                            }
                            $consumption->amendSuccessors();
                        }
                    } else {
                        $user = auth()->user();
                        $kits = CovidKit::with('machine')->when($user, function($query) use ($user){
                                                if ($user->user_type_id == 12)
                                                    return $query->where('type', '<>', 'Kit');
                                                else
                                                    return $query->where('type', '<>', 'Manual');
                                            })->orderBy('machine', 'desc')->get();
                        if ($user->user_type_id == 12)
                            $kits = $kits->groupby('type')->sortKeysDesc();
                        else
                            $kits = $kits->groupby('machine');
                        session(['toast_message' => 'A Consumption report was already submitted for the week of this allocation. Check below the changes that will be effected.']);

                        $detail_kits = [];
                        foreach ($request->input('received') as $key => $value) {
                            $detail = CovidAllocationDetail::find($key);
                            $detail_kits[$detail->kit->id] = $value;
                        }
                        
                        return view('tasks.covid.allocationconfirmation',  [
                                            'covidkits' => $kits,
                                            'consumption' => $already_submitted_consumption->first(),
                                            'allocation' => $request->except('_token'),
                                            'detail_kits' => $detail_kits,
                                        ]); 
                    }                
                }
            }
            
            
            foreach ($allocations as $key => $allocation) {
                $allocation->received = $request->input('response');
                $allocation->responded = 'YES';
                if ($request->input('response') == 'NO') {
                    $allocation->responded = 'POSTPONED';
                }
                $allocation->respond_count = $allocation->respond_count+1;
                $allocation->date_responded = date('Y-m-d');
                $allocation->date_received = $datereceived;
                $allocation->save();
            }
        }
        
        foreach ($request->input('received') as $key => $value) {
            $allocation_line = CovidAllocationDetail::find($key);
            // $allocation_line->received = $request->input('response');
            if ($request->input('response') == 'YES') {
                $allocation_line->received_kits = $value;
            }
            $allocation_line->save();
        }

        return redirect('covidkits');
    }

    public function reports(Request $request, CovidConsumption $consumption)
    {
        $user = auth()->user();
        if (null !== $consumption->start_of_week){

            $kits = CovidKit::with('machine')->when($user, function($query) use ($user){
                                        if ($user->user_type_id == 12)
                                            return $query->where('type', '<>', 'Kit');
                                        else
                                            return $query->where('type', '<>', 'Manual');
                                    })->orderBy('machine', 'desc')->get();
            if ($user->user_type_id == 12)
                $kits = $kits->groupby('type')->sortKeysDesc();
            else
                $kits = $kits->groupby('machine');
            return view('reports.covidconsumptiondetails', ['consumption' => $consumption, 'covidkits' => $kits]);
        }
    	return view('reports.covidconsumption',
                    ['consumptions' => CovidConsumption::when($user, function ($query) use ($user){
                                                if ($user->user_type_id == 12){
                                                    return $query->where('lab_id', '=', $user->lab_id);
                                                } else {
                                                    return $query->where('lab_id', env('APP_LAB'));
                                                }
                                        })->get()
                ]);
    }

    public function deliveries(Request $request)
    {
        $allocations = CovidAllocation::where('lab_id', env('APP_LAB'))->orderBy('allocation_date', 'desc')->limit('4')->get();
        $newallocation = [];
        foreach ($allocations as $key => $allocation) {
            $newallocation[$allocation->allocation_date] = [
                'allocation_date' => $allocation->allocation_date,
                'received' => $allocation->received,
                'date_received' => $allocation->date_received,
            ];
            $newallocation[$allocation->allocation_date]['types'][] = $allocation->allocation_type;
            $newallocation[$allocation->allocation_date]['data'][] = $allocation;
        }
        
        $allocations = (object)$newallocation;
        
        return view('tasks.covid.deliveries', ['allocations' => $allocations]);
    }

    public function refresh_allocations()
    {
        $model = new HCMPCovidAllocations;
        $response = $model->pullAllocations();
        session(['toast_message' => $response->message, 'toast_error' => $response->status]);
        return back();
    }

    private function buildConsumptionData($request)
    {
    	$datacolumns = $request->only(["kits_used","begining_balance","received","positive",
							"negative","wastage","ending","requested"]);
    	$data = [];
    	foreach ($datacolumns as $columnkey => $datacolumn) {
    		foreach ($datacolumn as $key => $value) {
    			$kit = CovidKit::where('material_no', $key)->first();
    			$data[$key]['kit_id'] = $kit->id;
    			$data[$key][$columnkey] = $value;
    		}
    	}
    	return $data;
    }

    private function getWeekCovidAllocations($start_of_week, $end_of_week)
    {
        $allocations = CovidAllocation::with('details')->whereRaw("date_received BETWEEN '{$start_of_week}' AND '{$end_of_week}'")->where('responded', 'YES')->get();
        if ($allocations->isEmpty()) {
            return collect([]);
        } else {
            return $allocations->first()->details;
        }
    }

    private function checkCovidAllocations($end_of_week)
    {
        // Check there is an allocation made that has been received in the previous week
        $allowable_min_date = date('Y-m-d', strtotime("-1 Month", strtotime($end_of_week)));
        $allowable_date_range = [$allowable_min_date, date('Y-m-d', strtotime($end_of_week))];
        
        $allocations = CovidAllocation::/*with('kit.machine')->*/
                            where('received', 'NO')
                            ->whereBetween('allocation_date', $allowable_date_range)
                            ->whereIn('responded', ['NO', 'POSTPONED'])
                            // ->where('date_responded', '<', $end_of_week)
                            ->get();
        
        $newallocation = [];
        foreach ($allocations as $key => $allocation) {
            if ($allocation->responded == 'POSTPONED') {
                if ($end_of_week > $allocation->date_responded) {
                    $newallocation[] = $allocation;
                }
            } else {
                $newallocation[] = $allocation;
            }
        }

        
        return collect($newallocation)->groupby('allocation_date');
    }

    private function getCovidAllocations($end_of_week)
    {
        $allocation_date = HCMPCovidAllocations::where('allocation_date', '<', $end_of_week)->get()->max('allocation_date');
        return HCMPCovidAllocations::where('received', 'YES')->whereNull('consumption_detail_id')
                            ->whereDate('allocation_date', $allocation_date)
                            ->get();
    }

    public function allocation_details(Request $request)
    {
        return response()->json($request->all());
    }
}

