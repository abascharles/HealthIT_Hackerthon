<?php

namespace App\CorruptModels;

use Illuminate\Database\Eloquent\Model;

class Batch extends BaseModel
{
    public function sample()
    {
        return $this->hasMany('App\CorruptModels\Sample');
    }
}
