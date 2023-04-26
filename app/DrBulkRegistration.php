<?php

namespace App;

use App\BaseModel;

class DrBulkRegistration extends BaseModel
{

    public function sample_view()
    {
        return $this->hasMany(DrSampleView::class, 'bulk_registration_id');
    }

    public function sample()
    {
        return $this->hasMany(DrSample::class, 'bulk_registration_id');
    }
}
