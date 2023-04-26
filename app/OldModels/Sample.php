<?php

namespace App\OldModels;

use App\OldModels\BaseModel;

class Sample extends BaseModel
{

    public function patient()
    {
    	return $this->belongsTo('App\OldModels\Patient', 'patientAUTOid', 'autoID');
    }
}