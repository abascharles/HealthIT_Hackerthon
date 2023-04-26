<?php

namespace App;

use App\BaseModel;

class DrPatient extends BaseModel
{
    //

    public function patient()
    {
        return $this->belongsTo(Viralpatient::class, 'patient_id');
    }
}
