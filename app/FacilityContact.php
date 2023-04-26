<?php

namespace App;

use App\BaseModel;

class FacilityContact extends BaseModel
{

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Get the facility's branch location
     *
     * @return string
     */
    public function getBranchLocationAttribute()
    {
        if($this->G4Sbranchname && $this->G4Slocation){
        	return $this->G4Sbranchname . ' , ' . $this->G4Slocation;
		}
		else if($this->G4Sbranchname && !$this->G4Slocation){
			return $this->G4Sbranchname;
		}
		else if(!$this->G4Sbranchname && $this->G4Slocation){
        	return $this->G4Slocation;
		}
		else{
			return null;
		}
    }

    public function getBranchPhonesAttribute()
    {
        if($this->G4Sphone1 != '' && $this->G4Sphone2 != '' && $this->G4Sphone3 != ''){
        	return $this->G4Sphone1 . ' / ' . $this->G4Sphone2 . ' / ' . $this->G4Sphone3;
		}
		else if($this->G4Sphone1 != '' && $this->G4Sphone2 != '' && $this->G4Sphone3 == ''){
        	return $this->G4Sphone1 . ' / ' . $this->G4Sphone2;
		}
		else if($this->G4Sphone1 != '' && $this->G4Sphone2 == '' && $this->G4Sphone3 != ''){
        	return $this->G4Sphone1 . ' / ' . $this->G4Sphone3;
		}
		else if($this->G4Sphone1 != '' && $this->G4Sphone2 == '' && $this->G4Sphone3 == ''){
        	return $this->G4Sphone1;
		}
		else{
			return null;
		}
    }

    public function getContactsAttribute()
    {
        if($this->contacttelephone != '' && $this->contacttelephone2 != ''){
        	return $this->contacttelephone . ' / ' . $this->contacttelephone2;
		}
		else if($this->contacttelephone != '' && $this->contacttelephone2 == ''){
        	return $this->contacttelephone;
		}
		else if($this->contacttelephone == '' && $this->contacttelephone2 != ''){
        	return $this->contacttelephone2;
		}
		else{
			return null;
		}
    }

    public function getFacilityContactsAttribute()
    {
        if($this->telephone != '' && $this->telephone2 != ''){
        	return $this->telephone . ' / ' . $this->telephone2;
		}
		else if($this->telephone != '' && $this->telephone2 == ''){
        	return $this->telephone;
		}
		else if($this->telephone == '' && $this->telephone2 != ''){
        	return $this->telephone2;
		}
		else{
			return null;
		}
    }
}
