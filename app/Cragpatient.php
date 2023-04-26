<?php

namespace App;

use App\BaseModel;

class Cragpatient extends BaseModel
{

	
    /**
     * Get the patient's gender
     *
     * @return string
     */
    public function getGenderAttribute()
    {
        if($this->sex == 1){ return "Male"; }
        else if($this->sex == 2){ return "Female"; }
        else{ return "No Gender"; }
    }

    /**
     * Get the patient's age in months
     *
     * @return integer
     */
    public function getAgeAttribute()
    {
        return \App\Lookup::calculate_viralage(date('Y-m-d'), $this->dob);
    }


    public function sample(){
    	return $this->belongsTo('App\CragSample', 'patient_id');
    }
}
