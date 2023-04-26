<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Allocation extends BaseModel
{
    /**
     * The attributes that should be guarded from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    public function machine(){
        return $this->belongsTo('App\Machine');
    }

    public function details() {
    	return $this->hasMany('App\AllocationDetail');
    }

    public function saveAlloction()
    {
        $this->year = date('Y');
        $this->month = date('m');
        $this->order_num = date('Y-M');
        $this->datesubmitted = date('Y-m-d');
        $this->submittedby = auth()->user()->full_name;
        $this->lab_id = env('APP_LAB');
        return $this->save();
    }
}
