<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsumptionDetail extends BaseModel
{
	public function kit() {
    	return $this->belongsTo('App\Kits');
    }

    public function header()
    {
    	return $this->belongsTo(Consumption::class, 'consumption_id', 'id');
    }

    public function getYearAttribute()
    {
    	return $this->header->year;
    }

    public function getMonthAttribute()
    {
    	return $this->header->month;
    }

    public function getReceivedAmountAttribute()
    {
    	$kit = $this->kit;
    	$header = $this->header;
    	$delivery = Deliveries::with('details')
    					->where('year', '=', $header->year)->where('month', '=', $header->month)->get();
    	$received = 0;
    	if (!$delivery->isEmpty()){
    		$delivery_lines = $delivery->first()->details
							->where(['kit_id' => $kit->id, 'kit_type' => Kits::class]);
			if (!$delivery_lines->isEmpty())
    			$received = ((int)$delivery_lines->first()->received - (int)$delivery_lines->first()->damaged);
    	}
    	
		
    	return $received;
    }

    public static function updateReceived()
    {
    	foreach (ConsumptionDetail::get() as $key => $line) {
    		$line->received = $line->received_amount;
    		$line->save();
    	}
    	return true;
    }

    public function predessesor()
    {
    	$date = $this->year . '-' . $this->month . '-01';
    	$predessesor_year = date('Y', strtotime("-1 Month", strtotime($date)));
    	$predessesor_month = date('m', strtotime("-1 Month", strtotime($date)));
    	$predessesor = $this->where('kit_id', $this->kit_id)->get()
    					->where('year', $predessesor_year)->where('month', $predessesor_month)->first();
    	return $predessesor;
    }
}
