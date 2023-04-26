<?php

namespace App\CovidTestModels;

use DB;

class CovidSample extends BaseModel
{

	protected $dates = ['datecollected', 'datereceived', 'datetested', 'datedispatched', 'dateapproved', 'dateapproved2'];

	protected $casts = [
		'symptoms' => 'array',
		'observed_signs' => 'array',
		'underlying_conditions' => 'array',		
	];


    public function travel()
    {
        return $this->belongsTo(CovidTravel::class, 'sample_id');
    }

    public function patient()
    {
        return $this->belongsTo(CovidPatient::class, 'patient_id');
    }

    public function worksheet()
    {
        return $this->belongsTo(CovidWorksheet::class, 'worksheet_id');
    }

    public function lab()
    {
        return $this->belongsTo('App\Lab', 'lab_id');
    }

    public function calc_age()
    {
        $this->age = now()->diffInYears($this->patient->dob);
    }


    /**
     * Get the sample's result name
     *
     * @return string
     */
    public function getResultNameAttribute()
    {
        if($this->result == 1){ return "Negative"; }
        else if($this->result == 2){ return "Positive"; }
        else if($this->result == 3){ return "Failed"; }
        else if($this->result == 5){ return "Collect New Sample"; }
        else{ return ""; }
    }

    public function setResultAttribute($value)
    {
        if(is_numeric($value)) $this->attributes['result'] = $value;
        else{
            $value = strtolower($value);
            if(\Str::contains($value, ['neg'])) $this->attributes['result'] = 1;
            else{
                $this->attributes['result'] = 2;
            }
        }
    }

    public function setSampleTypeAttribute($value)
    {
        if(is_numeric($value)) $this->attributes['sample_type'] = $value;
        else{
            $a = explode(' ', $value);
            if(count($a) == 1) $a = explode('-', $value);
            $word = $a[0];
            $this->attributes['sample_type'] = DB::table('covid_sample_types')->where('name', 'like', "{$value}%")->first()->id ?? null;
        }
    }

}
