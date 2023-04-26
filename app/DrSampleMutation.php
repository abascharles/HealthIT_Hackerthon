<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrSampleMutation extends BaseModel
{

    public function sample()
    {
        return $this->belongsTo(DrSample::class, 'sample_id');
    }
	
    public function mutation()
    {
        return $this->belongsTo(DrMutation::class, 'mutation_id');
    }
}
