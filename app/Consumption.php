<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consumption extends Model
{
    /**
     * The attributes that should be guarded from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

   public function scopeExisting($query, $year, $month, $type, $lab_id)
    {
        return $query->where(['year' => $year, 'month' => $month, 'type' => $type, 'lab_id' => $lab_id]);
    }

    public function details()
    {
    	return $this->hasMany(ConsumptionDetail::class, 'consumption_id', 'id');
    }

    public function testtype()
    {
        return $this->belongsTo(TestType::class, 'type', 'id');
    }

    public function platform()
    {
        return $this->belongsTo(Machine::class, 'machine', 'id');
    }

   public function scopeDuplicate($query, $year, $month, $type, $machine, $lab_id)
   {
      return $query->where(['year' => $year, 'month' => $month, 'type' => $type, 'machine' => $machine, 'lab_id' => $lab_id]);
   }

    public function getMissingConsumptions()
    {
        $data = [];
        $year = $this->selectRaw("max(`year`) as `year`")->get()->first()->year;

        $month = $this->select('year', 'month')->where('year', '=', $year)->get()->max('month');
        
        $latestdate = (object)[
                           'year' => date('Y', strtotime("+1 Month", strtotime($year.'-'.$month))),
                           'month' => date('m', strtotime("+1 Month", strtotime($year.'-'.$month))),
                        ];
        
        $limit = date('Y-m', strtotime("-1 Month", strtotime(date('Y-m'))));
        $currentloopdate = $latestdate->year . '-' . $latestdate->month;
        while (strtotime($limit) >= strtotime($currentloopdate)) {
            $data[] = (object)[
                    'year' => date('Y', strtotime($currentloopdate)),
                    'month' => date('m', strtotime($currentloopdate)),
                ];
            $currentloopdate = date('Y-m', strtotime("+1 Month", strtotime($currentloopdate)));
        }
        
        
        return $data;
    }

    public function submitNullConsumption($year, $month)
    {
        $submitted_consumptions = $this->where('year', $year)->where('month', $month)->get()->pluck('machine');
        $unsubmittedMachines = Machine::whereNotIn('id', $submitted_consumptions->toArray())->get();
        foreach ($unsubmittedMachines as $machinekey => $machine) {
            foreach (TestType::get() as $typekey => $type) {
                $this->year = $year;
                $this->month = $month;
                $this->type = $type->id;
                $this->machine = $machine->id;
                $this->lab_id = env('APP_LAB');
                $this->save();
                foreach ($machine->kits as $key => $kit) {
                    ConsumptionDetail::create([
                        'consumption_id' => $this->id,
                        'kit_id' => $kit->id,
                    ]);
                }
            }
        }
      return true;
    }

    public function amendSuccessors()
    {
        $current_consummption = $this;
        $successors = Consumption::where('id', '>', $this->id)
                            ->orderBy('id', 'asc')->get();
        foreach ($successors as $key => $successor) {
            foreach ($successor->details as $key => $detail) {
                $predessesor = $detail->predessesor();
                $current_ending = $detail->ending_balance;
                $predessesor_ending = $predessesor->ending_balance ?? 0;
                $replaced = $detail->begining_balance;
                $ending = (($current_ending - $replaced) + $predessesor_ending);
                
                $detail->begining_balance = $predessesor_ending;
                $detail->ending_balance = ($ending);
                $detail->save();
            }
            $successor->synched = 2;
            $successor->save();
        }
        return true;
    }
}
