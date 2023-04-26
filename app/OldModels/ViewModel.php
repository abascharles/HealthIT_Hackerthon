<?php

namespace App\OldModels;

use Illuminate\Database\Eloquent\Model;

class ViewModel extends Model
{
    protected $connection = 'old';

    public $timestamps = false;
}
