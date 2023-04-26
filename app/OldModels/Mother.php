<?php

namespace App\OldModels;

use App\OldModels\BaseModel;

class Mother extends BaseModel
{

    public function patient()
    {
    	return $this->hasMany('App\OldModels\Patient', 'mother', 'ID');
    }

}