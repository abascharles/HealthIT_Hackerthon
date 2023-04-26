<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UlizaTwgFeedback extends BaseModel
{


    public function clinical_form()
    {
        return $this->belongsTo('App\UlizaClinicalForm', 'uliza_clinical_form_id');
    }
}
