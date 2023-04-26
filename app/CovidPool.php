<?php

namespace App;

class CovidPool extends BaseModel
{

    public function pool_sample()
    {
    	return $this->hasMany('App\CovidPoolSample', 'pool_id');
    }

    public function worksheet()
    {
        return $this->hasMany('App\CovidWorksheet', 'pool_id');
    }

    public function creator()
    {
    	return $this->belongsTo(User::class, 'createdby');
    }
}
