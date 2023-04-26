<?php

namespace App\OldModels;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    protected static function boot()
    {
        parent::boot();
    }

    protected $guarded = [];

    public $timestamps = false;

    protected $connection = 'old';

    protected $key = 'ID';
}
