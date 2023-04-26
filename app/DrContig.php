<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrContig extends BaseModel
{
	
    public function sample()
    {
        return $this->belongsTo(DrSample::class, 'sample_id');
    }

    public function warning()
    {
        return $this->hasMany(DrContigWarning::class, 'contig_id');
    }
}
