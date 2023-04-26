<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CovidConsumption extends BaseModel
{
    public function details()
    {
    	return $this->hasMany(CovidConsumptionDetail::class, 'consumption_id', 'id');
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id', 'id');
    }    

    public function scopeExisting($query, $start_of_week, $lab)
    {
        return $query->where(['start_of_week' => $start_of_week, 'lab_id' => $lab]);
    }

    public function getTestsDoneAttribute()
    {
        $total = 0;
        $tests = (array)json_decode($this->tests);
        foreach ($tests as $key => $machinevalue) {
            $machinevalue = (array)$machinevalue;
            foreach ($machinevalue as $key => $value) {
                $total += (int)$value;
            }
        }
        return $total;
    }

    public function getTestsDone($start_date, $end_date)
    {
        $user = auth()->user();
        return CovidSample::whereBetween('datetested', [$start_date, $end_date])
                    ->where('receivedstatus', '<>', 2)
                    ->when($user, function($query) use ($user){
                        return $query->where('lab_id', '=', env('APP_LAB'));
                    })->get()->count();
    }

    public function synchComplete()
    {
        foreach ($this->details as $key => $detail) {
            $detail->synced = 1;
            $detail->datesynced = date('Y-m-d');
            $detail->save();
        }
        $this->synced = 1;
        $this->datesynced = date('Y-m-d');
        $this->save();
    }

    public function lastweekConsumption()
    {
        $time = $this->getPreviousWeek();
        return $this->whereDate('start_of_week', $time->week_start)->get();
    }

    public function getMissingConsumptions()
    {
        $data = [];
        $user = auth()->user();

        // Getting the first date covid tests where done
        $firsttestdate = CovidSampleView::selectRaw("max(`datetested`) AS `datetested`")
            ->whereNotNull('datetested')
            ->when($user, function ($query) use ($user) {
                if ($user->user_type_id == 12)
                    return $query->where('lab_id', '=', $user->lab_id);
                else
                    return $query->where('lab_id', '=', env('APP_LAB'));
            })->first();
        
        if (null !== $firsttestdate->datetested) { // Tests exists, get the weeks that need to be reported on
            $date = $firsttestdate->datetested;
            $lastweeek = $this->getPreviousWeek();
            $loop = true;
            while ($loop) {
                $week = $this->getStartAndEndDate(date('W', strtotime($date)), date('Y', strtotime($date)));
                if ($this->getdata($week->week_start))
                    $data[] = $week;
                $date = date('Y-m-d', strtotime("+1 day",strtotime($week->week_end)));
                if ($week->week_end == $lastweeek->week_end)
                    $loop = false;
            }
        } else {// Tests not done report for last week
            $week = $this->getPreviousWeek();
            if ($this->getdata($week->week_start))
                $data[] = $this->getPreviousWeek();
        }
        
        return $data;

        // $lastweeek = $this->getPreviousWeek();
        // dd($firsttestdate);
        // $previous_weeks = [
        //                 'first' => '2020-04-06',
        //                 'second' => '2020-04-13',
        //                 'third' => '2020-04-20',
        //             ];
        // $data = [];
        // foreach ($previous_weeks as $key => $week) {
        //     if ($key == 'first' && env('APP_LAB') == 1){
        //         if ($this->getdata($week))
        //             $data[] = $this->getdata($week);
        //     } else if (in_array(env('APP_LAB'), [1, 5, 9]) && $key != 'first') {
        //         if ($this->getdata($week))
        //             $data[] = $this->getdata($week);
        //     }
            
        // }
        // $data[] = $this->getPreviousWeek();
        // return $data;
    }

    public function fillTestsDone()
    {
        foreach ($this->get() as $key => $consumption) {
            $consumption->tests = (int)$this->getTestsDone($consumption->start_of_week, $consumption->end_of_week);
            $consumption->save();
        }
        // Synch::synchCovidConsumption();
    }

    private function getdata($week)
    {
        $user = auth()->user();
        $filled = $this->whereDate('start_of_week', $week)->when($user, function($query) use ($user) {
                    if ($user->user_type_id == 12)
                            return $query->where('lab_id', '=', $user->lab_id);
                        else
                            return $query->where('lab_id', '=', env('APP_LAB'));
                })->get();
        
        if ($filled->isEmpty())
            return (object)['week_start' => $week,
                            'week_end' => date('Y-m-d', strtotime('+6 days', strtotime($week))),
                            'week' => date('W', strtotime($week))
                        ];
        return null;
    }

    public static function onetime()
    {
        $tests = [];
        foreach (CovidConsumption::get() as $key => $consumption) {
            $tests[] = ['C8800' => $consumption->tests];
            $consumption->tests = json_encode($tests);
            $consumption->save();
            $tests = [];
        }
        return true;
    }

    public static function fillerData()
    {
        $model = new CovidConsumption;
        $weeks = $model->getMissingConsumptions();
        $today = date('Y-m-d');
        $current_week  = date('W', strtotime($today));
        $backdate = $current_week - 3;
        foreach ($weeks as $key => $week) {
            if ($week->week < $backdate) {
                CovidConsumption::create([
                    'start_of_week' => $week->week_start,
                    'end_of_week' => $week->week_end,
                    'week' => $week->week,
                    'lab_id' => env('APP_LAB'),
                    'tests' => json_encode([
                                ['C8800' => 0],
                                ['Abbott' => 0],
                            ]),
                    'synced' => 1,
                    'datesynced' => date('Y-m-d'),
                ]);
            }
        }
        return true;
    }

    public function amendSuccessors()
    {
        $week = $this->start_of_week;
        $current_consummption = $this;
        $successors = CovidConsumption::where('start_of_week', '>', $week)
                            ->orderBy('start_of_week', 'asc')->get();
        foreach ($successors as $key => $successor) {
            foreach ($successor->details as $key => $detail) {
                $predessesor = $detail->predessesor();
                $current_ending = $detail->ending;
                $predessesor_ending = $predessesor->ending ?? 0;
                $replaced = $detail->begining_balance;
                $ending = (($current_ending - $replaced) + $predessesor_ending);
                
                $detail->begining_balance = $predessesor_ending;
                $detail->ending = ($ending);
                $detail->save();
            }
        }
        return true;
    }
}