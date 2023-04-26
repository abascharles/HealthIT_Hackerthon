<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CovidContact extends BaseModel
{

    public function patient()
    {
        return $this->belongsTo('App\CovidPatient', 'patient_id');
    }
}
