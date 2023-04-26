<?php

namespace App;

class DrGenotype extends BaseModel
{

    public function sample()
    {
        return $this->belongsTo(DrSample::class, 'sample_id');
    }
	
    public function residue()
    {
        return $this->belongsTo(DrResidue::class, 'genotype_id');
    }
}
