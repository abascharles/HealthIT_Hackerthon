<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $table = "machines";

    public function kits() {
    	return $this->hasMany('App\Kits');
    }

    public function deliveries()
    {
        return $this->hasMany(Deliveries::class, 'machine', 'id');
    }

    public function consumptions()
    {
        return $this->hasMany(Consumption::class, 'machine', 'id');
    }

    public function eid_worksheets()
    {
        return $this->hasMany(Worksheet::class, 'machine_type', 'id');
    }

    public function viral_worksheets()
    {
        return $this->hasMany(Viralworksheet::class, 'machine_type', 'id');
    }

    public function covid_worksheets()
    {
        return $this->hasMany(CovidWorksheet::class, 'machine_type', 'id');
    }

    public function mapping()
    {
        return $this->hasMany(LabEquipment::class, 'type', 'id');
    }

    public function missingDeliveries($year, $month)
    {
        $data = [];
        foreach ($this->whereNotIn('id', [0,5])->get() as $key => $machine) {
            if ($machine->deliveries->where('year', $year, 'month', $month)->isEmpty())
                $data[] = $machine;
        }
        return $data;
    }

    public function missingConsumptions($year, $month)
    {
        $data = [];
        
        foreach ($this->whereNotIn('id',[0,5])->get() as $key => $machine) {
            if ($machine->consumptions->where('year', $year, 'month', $month)->isEmpty())
                $data[] = $machine;
        }
        return $data;
    }

    public function testsforLast3Months() {
    	$id = $this->id;
        $eid = Sample::selectRaw("count(*) as tests")
                    ->join('worksheets', 'worksheets.id', '=', 'samples.worksheet_id')
                    ->where('worksheets.machine_type', $id)
                    ->whereRaw("datetested >= last_day(now()) + interval 1 day - interval 3 month")
                    ->first()->tests;
        $vl = Viralsample::selectRaw("count(*) as tests")
                    ->join('viralworksheets', 'viralworksheets.id', '=', 'viralsamples.worksheet_id')
                    ->where('viralworksheets.machine_type', $id)
                    ->whereRaw("datetested >= last_day(now()) + interval 1 day - interval 3 month")
                    ->first()->tests;
        // dd();
    	// $vl = Viralsample::selectRaw("count(*) as tests")->whereHas('worksheet', function($query) use ($id) {
		   //  		return $query->where('machine_type', '=', $id);
		   //  	})->whereRaw("datetested >= last_day(now()) + interval 1 day - interval 3 month")
    	// 		->first()->tests;

    	return (object)['EID' => $eid, 'VL' => $vl];
    }

    public function tests_done($type, $year, $month)
    {
        $returnValue = 0;
        $data = [];
        $data = $this->getTestsFromStorage($year, $month);
        $returnValue = $data[$type][$this->id][$year][$month] ?? 0;
        
        return $returnValue;
    }

    private function getTestsFromStorage($year, $month)
    {
        $reference = 'tests' . $year . '-' . $month;
        if (!session($reference)) {
            $data = [];
            $vltests = Viralsample::selectRaw("count(*) as tests, viralworksheets.machine_type")
                    ->join('viralworksheets', 'viralworksheets.id', '=', 'viralsamples.worksheet_id')
                    ->whereYear('datetested', $year)
                    ->whereMonth('datetested', $month)
                    ->groupBy('machine_type')
                    ->get();
            foreach ($vltests as $key => $test) {
                $data['VL'][$test->machine_type][$year][$month] = $test->tests;
            }

            $eidtests = Sample::selectRaw("count(*) as tests, worksheets.machine_type")
                        ->join('worksheets', 'worksheets.id', '=', 'samples.worksheet_id')
                        ->whereYear('datetested', $year)
                        ->whereMonth('datetested', $month)
                        ->groupBy('machine_type')
                        ->get();
            foreach ($eidtests as $key => $test) {
                $data['EID'][$test->machine_type][$year][$month] = $test->tests;
            }
            $set = session([$reference => $data]);
        }
        
        return session($reference);
    }

    public function saveNullAllocation()
    {
        $allocation = Allocation::where('year', '=', date('Y'))
                                    ->where('month', '=', date('m'))->get();
        if ($allocation->isEmpty()){
            $model = new Allocation;
            $allocation = $model->saveAlloction();
        } else {
            $allocation = $allocation->first();
        }
        
        if($allocation->details->where('machine_id', NULL)->where('testtype', NULL)->isEmpty()){
            $detail = AllocationDetail::create([
                            'allocation_id' => $allocation->id,
                        ]);
            foreach (GeneralConsumables::get() as $key => $kit) {
                $breakdown = AllocationDetailsBreakdown::create([
                                'allocation_detail_id' => $detail->id,
                                'breakdown_id' => $kit->id,
                                'breakdown_type' => Kits::class,
                            ]);
            }
        }

        $types = TestType::get();
        foreach ($types as $key => $type) {
            $details = $allocation->details->where('machine_id', $this->id)->where('testtype', $type->id);
            if ($details->isEmpty()){
                $detail = AllocationDetail::create([
                                'allocation_id' => $allocation->id,
                                'machine_id' => $this->id,
                                'testtype' => $type->id,
                            ]);
                foreach ($this->kits as $key => $kit) {
                    $breakdown = AllocationDetailsBreakdown::create([
                                    'allocation_detail_id' => $detail->id,
                                    'breakdown_id' => $kit->id,
                                    'breakdown_type' => Kits::class,
                                ]);
                }
            }
        }
        return true;
    }

    public function getCovidTestsDone($start_date, $end_date)
    {
        $user = auth()->user();
        return CovidSample::selectRaw("count(*) as `samples`")
                        ->join('covid_worksheets', 'covid_worksheets.id', '=', 'covid_samples.worksheet_id')
                        ->whereBetween('datetested', [$start_date, $end_date])
                        ->where('machine_type', $this->id)
                        ->when($user, function($query) use ($user){
                            if ($user->user_type_id == 12)
                                return $query->where('covid_samples.lab_id', $user->lab_id);
                            else
                                return $query->where('covid_samples.lab_id', env('APP_LAB'));
                        })->first()->samples;
    }

}