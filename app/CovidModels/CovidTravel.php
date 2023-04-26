<?php

namespace App\CovidModels;

use Illuminate\Database\Eloquent\Model;

class CovidTravel extends BaseModel
{
	protected $dates = ['travel_date'];

    protected $table = 'covid_travels';
}
