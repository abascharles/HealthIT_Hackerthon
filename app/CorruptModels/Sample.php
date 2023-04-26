<?php

namespace App\CorruptModels;

use Illuminate\Database\Eloquent\Model;

class Sample extends BaseModel
{
    public function patient()
    {
    	return $this->belongsTo('App\CorruptModels\Patient');
    }

    public function batch()
    {
        return $this->belongsTo('App\CorruptModels\Batch');
    }

    public function worksheet()
    {
        return $this->belongsTo('App\CorruptModels\Worksheet');
    }

    // Parent sample
    public function parent()
    {
        return $this->belongsTo('App\CorruptModels\Sample', 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany('App\CorruptModels\Sample', 'parentid');
    }

    public function corrupt_version()
    {
        $batch = Batch::where('old_id', '=', $this->batch_id)->first();
        $worksheet = Worksheet::where('old_id', '=', $this->worksheet_id)->first();
        $patient = Patient::where('old_id', '=', $this->patient_id)->first();

        if (isset($batch))
            $this->batch_id = $batch->id;
        if (isset($worksheet))
            $this->worksheet_id = $worksheet->id;
        if (isset($patient))
            $this->patient_id = $patient->id;
            
        $this->save();
    }
}
