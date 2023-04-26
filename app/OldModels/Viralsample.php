<?php

namespace App\OldModels;

use App\OldModels\BaseModel;

class Viralsample extends BaseModel
{
	protected $table = 'viralsamples';


    public function patient()
    {
    	return $this->belongsTo('App\OldModels\Viralpatient', 'patientid', 'AutoID');
    }

}