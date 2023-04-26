<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deliveries extends BaseModel
{
   
   public function details()
   {
   		return $this->hasMany('App\DeliveryDetail', 'delivery_id');
   }

   public function testtype()
   {
      return $this->belongsTo(TestType::class, 'type', 'id');
   }

   public function platform()
   {
      return $this->belongsTo(Machine::class, 'machine', 'id');
   }

   public function scopeExisting($query, $year, $quarter, $type, $lab_id)
   {
        return $query->where(['year' => $year, 'quarter' => $quarter, 'type' => $type, 'lab_id' => $lab_id]);
   }

   public function scopeDuplicate($query, $year, $month, $type, $machine, $lab_id)
   {
      return $query->where(['year' => $year, 'month' => $month, 'type' => $type, 'machine' => $machine, 'lab_id' => $lab_id]);
   }

	public function getMissingDeliveries()
	{
		$data = [];

      $year = $this->selectRaw("max(`year`) as `year`")->get()->first()->year;
      
      // This will only apply for the first month this logic is run alone
      if ($this->whereNotNull('month')->get()->isEmpty()){
         $month = $this->selectRaw("month(datereceived) as month")->where('year', '=', $year)->get()->max('month');
      } else {
         $month = $this->select('year', 'month')->where('year', '=', $year)->get()->max('month');
      }
      
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

   public function getMissingDeliveriesTestFn()
   {
      $data = [];
      $year = $this->selectRaw("max(`year`) as `year`")->get()->first()->year;
      // This will only apply for the first month this logic is run alone
      if ($this->whereNotNull('month')->get()->isEmpty()){
         $month = $this->selectRaw("month(datereceived) as month")->where('year', '=', $year)->get()->max('month');
      } else {
         $month = $this->select('year', 'month')->where('year', '=', $year)->get()->max('month');
      }
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


   public function submitNullDeliveries($year, $month)
   {
      $submitted_deliveries = $this->where('year', $year)->where('month', $month)->get()->pluck('machine');
      $unsubmittedMachines = Machine::whereNotIn('id', $submitted_deliveries->toArray())->get();
      foreach ($unsubmittedMachines as $machinekey => $machine) {
         foreach (TestType::get() as $typekey => $type) {
            $this->year = $year;
            $this->month = $month;
            $this->type = $type->id;
            $this->machine = $machine->id;
            $this->lab_id = env('APP_LAB');
            $this->save();
            foreach ($machine->kits as $key => $kit) {
               DeliveryDetail::create([
                     'delivery_id' => $this->id,
                     'kit_id' => $kit->id,
                     'kit_type' =>  Kit::class,
                  ]);
            }
         }
      }
      return true;
   }

}
