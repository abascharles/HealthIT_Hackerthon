<?php

namespace App\CovidTestModels;

class CovidSampleView extends BaseModel
{
	protected $table = "covid_sample_view";

    public function lab()
    {
        return $this->belongsTo('App\Lab', 'lab_id');
    }

    
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
}
