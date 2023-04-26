<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UlizaClinicalVisit extends BaseModel
{


    public function clinical_form()
    {
        return $this->belongsTo('App\UlizaClinicalForm');
    }

}
