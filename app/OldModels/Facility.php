<?php

namespace App\OldModels;

use App\OldModels\BaseModel;

class Facility extends BaseModel
{
    protected $table = "facilitys";

    public function scopeLocate($query, $mfl)
    {
        return $query->where("facilitycode", $mfl);
    }
}