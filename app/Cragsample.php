<?php

namespace App;

use App\BaseModel;

class Cragsample extends BaseModel
{


	public function first_approver(){
		return $this->belongsTo(User::class, 'approvedby');
	}

	public function second_approver(){
		return $this->belongsTo(User::class, 'approvedby2');
	}

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function patient(){
    	return $this->belongsTo('App\CragPatient', 'patient_id');
    }
}
