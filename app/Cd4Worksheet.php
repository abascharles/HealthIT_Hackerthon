<?php

namespace App;

use App\BaseModel;

class Cd4Worksheet extends BaseModel
{
	protected $table = 'cd4worksheets';

	public function creator(){
		return $this->belongsTo(User::class, 'createdby');
	}

	public function uploader(){
		return $this->belongsTo(User::class, 'uploadedby');
	}

	public function first_reviewer(){
		return $this->belongsTo(User::class, 'reviewedby');
	}

	public function second_reviewer(){
		return $this->belongsTo(User::class, 'reviewedby2');
	}

	public function cancellor(){
		return $this->belongsTo(User::class, 'cancelledby');
	}

	public function sample(){
		return $this->hasMany('App\Cd4Sample', 'worksheet_id');
	}

	public function samples(){
		return $this->hasMany('App\Cd4SampleView', 'worksheet_id');
	}
}
