<?php

namespace App;

class DrResidue extends BaseModel
{
	
    public function genotype()
    {
        return $this->belongsTo(DrGenotype::class, 'genotype_id');
    }
}
