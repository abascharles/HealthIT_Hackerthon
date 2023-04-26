<?php

namespace App\OldModels;

use App\OldModels\BaseModel;

class Viralpatient extends BaseModel
{
	protected $table = 'viralpatients';
	protected $key = 'AutoID';

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