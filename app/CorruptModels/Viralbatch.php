<?php

namespace App\CorruptModels;

use Illuminate\Database\Eloquent\Model;

class Viralbatch extends BaseModel
{
    public function sample()
    {
        return $this->hasMany(Viralsample::class, 'batch_id');
    }
}
