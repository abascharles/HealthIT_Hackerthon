<?php

namespace App\CorruptModels;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();
    }

    protected $guarded = [];

    public $timestamps = false;

    protected $connection = 'corrupt';
}
