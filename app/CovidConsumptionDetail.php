<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CovidConsumptionDetail extends BaseModel
{
    public function kit()
    {
    	return $this->belongsTo(CovidKit::class, 'kit_id', 'id');
    }

    public function consumption()
    {
    	return $this->belongsTo(CovidConsumption::class, 'consumption_id', 'id');
    }

    public function getStartOfWeekAttribute()
    {
    	return $this->consumption->start_of_week;
    }

    public function getEndOfWeekAttribute()
    {
    	return $this->consumption->end_of_week;
    }

    public function getWeekAttribute()
    {
    	return $this->consumption->week;
    }

    public function predessesor()
    {
    	$predessesor_end_date = date('Y-m-d', strtotime("-1 day", strtotime($this->start_of_week)));
    	$predessesor = $this->where('kit_id', $this->kit_id)->get()
    					->where('end_of_week', $predessesor_end_date)->first();
    	return $predessesor;
    }

    // public function getNextWeekAttribute()
    // {
    // 	$current_week = $this->week;
    // 	return $week + 1;
    // }
}
