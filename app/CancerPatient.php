<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancerPatient extends BaseModel
{

    public function scopeExisting($query, $facility_id, $ccc_no)
    {
        if($facility_id) 
        	return $query->where(['facility_id' => $facility_id, 'patient' => $ccc_no]);
        return $query->where(['patient' => $ccc_no])->whereNull('facility_id');
    }
}
