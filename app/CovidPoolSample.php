<?php

namespace App;

class CovidPoolSample extends BaseModel
{


    public function pool()
    {
    	return $this->belongs('App\CovidPool', 'pool_id');
    }

    public function sample()
    {
        return $this->hasMany('App\CovidSample', 'pool_sample_id');
    }



    public function getSamplesArrayAttribute($value)
    {
    	if(!$this->samples) return [];
    	return CovidSample::whereIn('id', $this->samples)->get();
    }

    public function getSampleViewArrayAttribute($value)
    {
    	if(!$this->samples) return [];
    	return CovidSampleView::whereIn('id', $this->samples)->get();
    }
}
