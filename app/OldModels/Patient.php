<?php

namespace App\OldModels;

use App\OldModels\BaseModel;

class Patient extends BaseModel
{
	protected $key = 'AutoID';


    public function mother()
    {
    	return $this->belongsTo('App\OldModels\Mother', 'mother', 'ID');
    }

    public function sample()
    {
    	return $this->hasMany('App\OldModels\Sample', 'patientAUTOid', 'autoID');
    }

	public function setGenderAttribute($value)
	{
		switch ($value) {
			case 1:
				$gender = 'M';
				break;
			case 2:
				$gender = 'F';
				break;			
			default:
				$gender = 'No Data';
				break;
		}
		$this->attributes['gender'] = $gender;
	}
}