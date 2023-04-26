<?php

namespace App;

class DrWarning extends BaseModel
{

    public function sample()
    {
        return $this->belongsTo(DrSample::class, 'sample_id');
    }

    public function warning_code()
    {
        return $this->belongsTo(DrWarningCode::class, 'warning_id');
    }
}
