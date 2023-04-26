<?php

namespace App;

class DrWorksheetWarning extends BaseModel
{

    public function worksheet()
    {
        return $this->hasMany(DrWorksheet::class, 'worksheet_id');
    }
}
