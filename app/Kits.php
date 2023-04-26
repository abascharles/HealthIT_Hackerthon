<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kits extends BaseModel
{

    // protected $hidden = ['testFactor', 'factor'];
	protected $year;
    protected $previousYear;
    protected $month;
    public $previousMonth;

    public function __construct(){
        $this->year = date('Y');
        $this->month = date('m');

        $this->previousYear = $this->year;
        $this->previousMonth = $this->month - 1;
        if ($this->month == 1) {
            $this->previousMonth = 12;
            $this->previousYear = $this->year-1;
        }
    }

    // public function getTestFactorAttribute()
    // {
    //     dd($this);
    //     if (null !== $this->testFactor)
    //         return json_decode($this->testFactor);
    //     return '';
    // }

    public function getMultiplierFactorAttribute() {
        return json_decode($this->factor);
    }

    public function machine() {
    	return $this->belongsTo('App\Machine');
    }

    public function consumption_lines(){
    	return $this->hasMany(ConsumptionDetail::class, 'kit_id', 'id');
    }

    public function lastMonth(){
    	return $this->consumption->where('year', '=', $this->previousYear)->where('month', '=', $this->previousMonth);
    }
   
    public function commodities()
    {
        return $this->morphMany('App\AllocationDetailsBreakdown', 'breakdown');
    }
   
    public function deliveredkits()
    {
        return $this->morphMany('App\DeliveryDetail', 'kit');
    }

    public function delivery_lines()
    {
        return $this->hasMany(DeliveryDetail::class, 'kit_id', 'id');
    }

    public function consumption_headers()
    {
        return $this->consumption_lines->load('header')->pluck('header')->flatten();
    }

    public function lastMonthConsumption($type = null)
    {
        $lastmonthYear = date('Y', strtotime("-1 Month", strtotime(date('Y-m-d'))));// Get the year in which last month belonged to (comes in handy especialy in January)
        $lastmonth = date('m', strtotime("-1 Month", strtotime(date('Y-m-d'))));
        $id = $this->id;
        $lastmonth_header = $this->consumption_headers()
                                ->where('year', $lastmonthYear)
                                ->where('month', $lastmonth)
                                ->where('type', $type)
                                ->transform(function($header, $key) use ($id){
                                    $line = $header->details->where('kit_id', $id)->first();
                                    $line->year = $header->year;
                                    $line->month = $header->month;
                                    $line->type = $header->type;
                                    return $line;
                                });
        return $lastmonth_header;
    }

    public function begining_balance($type, $year, $month)
    {
        $lastmonthyear = date('Y', strtotime("-1 Month", strtotime($year . '-' . $month)));
        $lastmonth = date('m', strtotime("-1 Month", strtotime($year . '-' . $month)));
        $balance = 0;
        
        if (!$this->consumption_lines->isEmpty()){
            $balance = $this->consumption_headers()
                            ->where('year', $lastmonthyear)->where('month', $lastmonth)
                            ->where('type', $type)
                            ->where('machine', $this->machine_id);
            if (!$balance->isEmpty()) {
                $balance = $balance->first()->details->where('kit_id', $this->id)->first()->ending_balance;
            } else {
                return $balance->count();
            }
        }
        return $balance;
    }

    public function getDeliveries($type, $year, $month)
    {
        $delivery_lines = $this->delivery_lines->load('delivery');
        $delivery = $delivery_lines->pluck('delivery')
                        ->where('year', $year)->where('month', $month)->where('type', $type);
        if (!$delivery->isEmpty()){
            $line = $delivery_lines->where('delivery_id', $delivery->first()->id)->first();
        } else {
            return (object)[
                    'quantity' => 0,
                    'lotno' => ''
                ];
        }
        // dd($line);
        return (object)[
            'quantity' => (float)((float)$line->received - (float)$line->damaged),
            'lotno' => $line->lotno ?? ''
        ];
    }

    public function getQuantityUsed($type, $tests)
    {
        $factor = json_decode($this->factor);
        $multiplierfactor = $factor->$type ?? $factor;
        $testfactor = json_decode($this->testFactor);
        return (@($tests/$testfactor->$type) * $multiplierfactor);
    }
}