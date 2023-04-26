<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use DB;
use App\DrDashboard;

use App\DataSetElement;

class DrDashboardBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function check_null($object, $attr = 'total')
    {
    	if(!$object) return 0;
    	return (int) $object->$attr;
    }

    public function get_joins_callback($table_name)
    {
        return function($query) use($table_name){
            return $query->join('view_facilitys', 'view_facilitys.id', '=', "{$table_name}.facility")
                ->join('periods', 'periods.id', '=', "{$table_name}.period_id");
        };        
    }

    public function get_joins_callback_weeks($table_name)
    {
        return function($query) use($table_name){
            return $query->join('view_facilitys', 'view_facilitys.id', '=', "{$table_name}.facility")
                ->join('weeks', 'weeks.id', '=', "{$table_name}.week_id");
        };        
    }

    /*
        1. Partner
        2. County
        3. Subcounty
        4. Ward
        5. Facility
        6. Project  
        7. Drug Class  
        8. Drug  

        11. Year
        12. Year Month
    */


    // Add Divisions Query Here
    // Also Add Date Query Here

    public function get_callback($order_by=null, $date_column = 'datetested')
    {
    	$groupby = session('filter_groupby', 2);
    	$divisions_query = DrDashboard::divisions_query();
        $date_query = DrDashboard::date_query($date_column);
        
    	if($groupby > 10){
    		if($groupby == 11) return $this->year_callback($divisions_query, $date_query, $date_column);
    		if($groupby == 12) return $this->year_month_callback($divisions_query, $date_query, $date_column);
    	}
    	else{
    		$groupby_query = DrDashboard::groupby_query();
    		return $this->divisions_callback($divisions_query, $date_query, $groupby_query, $groupby, $order_by);
    	}
    }

    public function get_callback_no_dates($order_by=null)
    {
        $groupby = session('filter_groupby', 2);
        $divisions_query = DrDashboard::divisions_query();
        $date_query = "1";

        $groupby_query = DrDashboard::groupby_query();
        return $this->divisions_callback($divisions_query, $date_query, $groupby_query, $groupby, $order_by);
    }

    public function divisions_callback($divisions_query, $date_query, $groupby_query, $groupby, $order_by=null)
    {
    	$raw = DB::raw($groupby_query['select_query']);

    	if($order_by){
	    	return function($query) use($divisions_query, $date_query, $groupby_query, $groupby, $raw, $order_by){
	    		return $query->addSelect($raw)
					->whereRaw($divisions_query)
                    ->whereRaw($date_query)
	    			->groupBy($groupby_query['group_query'])
	    			->orderBy($order_by, 'desc');
	    	};
    	}
    	else{
	    	return function($query) use($divisions_query, $date_query, $groupby_query, $groupby, $raw){               
	    		return $query->addSelect($raw)
					->whereRaw($divisions_query)
                    ->whereRaw($date_query)
	    			->groupBy($groupby_query['group_query']);
	    	};
    	}
    }

    public function year_callback($divisions_query, $date_query, $date_column)
    {
        $select_query = DB::raw("YEAR({$date_column}) AS `year` ");
        return function($query) use($divisions_query, $date_query, $select_query){
            return $query->addSelect($select_query)
                ->whereRaw($divisions_query)
                ->whereRaw($date_query)
                ->groupBy("year")
                ->orderBy("year", 'asc');
        };
    }

    public function year_month_callback($divisions_query, $date_query, $date_column)
    {
        $select_query = DB::raw("YEAR({$date_column}) AS `year`, MONTHNAME({$date_column}) AS `month`  ");
        return function($query) use($divisions_query, $date_query, $select_query){
            return $query->addSelect($select_query)
                ->whereRaw($divisions_query)
                ->whereRaw($date_query)
                ->groupBy("year", "month")
                ->orderBy("year", 'asc')
                ->orderBy("month", 'asc');
        };
    }

	
}
