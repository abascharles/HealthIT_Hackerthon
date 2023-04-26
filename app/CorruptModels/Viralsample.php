<?php

namespace App\CorruptModels;

use Illuminate\Database\Eloquent\Model;

class Viralsample extends BaseModel
{
    public function patient()
    {
    	return $this->belongsTo('App\CorruptModels\Viralpatient', 'patient_id');
    }

    public function batch()
    {
        return $this->belongsTo('App\CorruptModels\Viralbatch', 'batch_id');
    }

    public function worksheet()
    {
        return $this->belongsTo('App\CorruptModels\Viralworksheet', 'worksheet_id');
    }


    // Parent sample
    public function parent()
    {
        return $this->belongsTo('App\CorruptModels\Viralsample', 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany('App\CorruptModels\Viralsample', 'parentid');
    }

    public function corrupt_version()
    {
        $batch = Viralbatch::where('old_id', '=', $this->batch_id)->first();
        $worksheet = Viralworksheet::where('old_id', '=', $this->worksheet_id)->first();
        $patient = Viralpatient::where('old_id', '=', $this->patient_id)->first();
        if (isset($batch))
            $this->batch_id = $batch->id;
        if (isset($worksheet))
            $this->worksheet_id = $worksheet->id;
        if (isset($patient))
            $this->patient_id = $patient->id;
        
        $this->save();
    }
}
