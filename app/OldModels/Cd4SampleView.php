<?php

namespace App\OldModels;

use Illuminate\Database\Eloquent\Model;

class Cd4SampleView extends Model
{
    protected $connection = 'cd4';

    public $timestamps = false;
    
	protected $table = 'old_cd4_samples_view';
}
