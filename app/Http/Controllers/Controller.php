<?php

namespace App\Http\Controllers;

use App\Allocation;
use App\Consumption;
use App\CovidConsumption;
use App\Deliveries;
use App\LabEquipmentTracker;
use App\LabPerformanceTracker;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

     public function __construct() {
    //     // parent::__construct();
    //     // dd(session('pendingTasks')); 
         if (session('pendingTasks'))
             return redirect('pending');
     }

    public function _columnBuilder($columns = null)
    {
        $column = '<tr>';
        if ($columns == null) {
            $column .= '<th><center>No Data available</center></th>';
        } else {
            foreach ($columns as $key => $value) {
                $column .= '<th>'.$value.'</th>';
            }
        }
        $column .= '</tr>';
        return $column;
    }

    public function eligibleForEidVlConsumptions()
    {
        if (!in_array(env('APP_LAB'), [23, 25])) {
//            if (auth()->user()->eidvl_consumption_allowed)
            //    return true;
            if (auth()->user()->user_type_id == 0 && in_array(date('d'), [1,2,3]))
                return true;
        }
        return false;
    }

    public function eligibleForCovidConsumptions()
    {
        if (!in_array(env('APP_LAB'), [8])) {
           // if (auth()->user()->covid_consumption_allowed)
             //   return true;
           // if (auth()->user()->user_type_id == 1 && in_array(date('l', strtotime(date('Y-m-d'))), [/*'Thursday',*/'Friday', 'Saturday', 'Sunday']))
             //   return true;
        }
        return false;
    }

    public function eligibleForEquipmentUtilization()
    {
        if (!in_array(env('APP_LAB'), [23, 25])) {
       //     if (auth()->user()->equipment_allowed)
         //       return true;
            if (auth()->user()->user_type_id == 0 && in_array(date('d'), [1,2,3]));
                return true;
           
        }
        return false;
    }

    public function pendingTasks()
    {
        $prevyear = date('Y', strtotime("-1 Month", strtotime(date('Y-m'))));
        $prevmonth = date('m', strtotime("-1 Month", strtotime(date('Y-m'))));

        if ($this->eligibleForEidVlConsumptions()) {
            $model = new Deliveries;
            if (!collect($model->getMissingDeliveries())->isEmpty())  
                return true;

            if (Consumption::where('year', $prevyear)->where('month', $prevmonth)->get()->isEmpty())
                return true;
        }

        if ($this->eligibleForEquipmentUtilization()) {
            if (LabEquipmentTracker::where('year', $prevyear)->where('month', $prevmonth)->count() == 0)
                return false;

            if (LabPerformanceTracker::where('year', $prevyear)->where('month', $prevmonth)->count() == 0)
                return false;
        }


        if ($this->eligibleForCovidConsumptions()) {
            $time = $this->getPreviousWeek();
            // dd(CovidConsumption::whereDate('start_of_week', $time->week_start)->where('lab_id', env('APP_LAB'))->get());
            if (CovidConsumption::whereDate('start_of_week', $time->week_start)->where('lab_id', env('APP_LAB'))->get()->isEmpty()) {
                return true;
            }
        }
        
        return false;
    }

    protected function getPreviousWeek()
    {
        $date = strtotime('-7 days', strtotime(date('Y-m-d')));
        return $this->getStartAndEndDate(date('W', $date),
                                date('Y', $date));
    }

    protected function getStartAndEndDate($week, $year) {
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $ret['week_start'] = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $ret['week_end'] = $dto->format('Y-m-d');
        $ret['week'] = date('W', strtotime($ret['week_start']));
        return (object)$ret;
    }

    public function pilotAllocation()
    {
        $return = false;
        $currentAllocation = Allocation::where('year', '=', date('Y'))->where('month', date('m'))->get();
        if ($currentAllocation->isEmpty())
            $return = true;
        return $return;
    }

    public static function _getMonthQuarter($month=1, &$range=null){
        if ($month >0 && $month <4 ) {
            $quota=1;
            $range='JAN-MAR';
        }
        if ($month >3 && $month <7 ) {
            $quota=2;
            $range='APR-JUN';
        }
        if ($month >6 && $month <10 ) {
            $quota=3;
            $range='JUL-SEP';
        }
        if ($month >9 && $month <13) {
            $quota=4;
            $range='OCT-DEC';
        }
        return $quota;
    }

    public function auth_user($usertypes)
    {
        $user_type_id = auth()->user()->user_type_id;
        if($user_type_id == 0) return null;
        $a = 1;
        if(is_array($usertypes)){
            if(!in_array($user_type_id, $usertypes)) abort(403);
        }
        else{
            if($user_type_id != $usertypes) abort(403);
        }
    }

     public static function _getQuarterMonths($quarter=1) {
        $quarter = (int) $quarter;
        if ($quarter == 1) 
            $months = [1, 2, 3];
        if ($quarter == 2) 
            $months = [4, 5, 6];
        if ($quarter == 3) 
            $months = [7, 8, 9];
        if ($quarter == 4) 
            $months = [10, 11, 12];
        
        return $months;
    }

    protected function reportRelease()
    {
        return session(['pendingTasks'=> false]);
    }

}


