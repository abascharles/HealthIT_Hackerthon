<?php

namespace App;

use App\BaseModel;

class Facility extends BaseModel
{
    //
    protected $table = "facilitys";
    public $timestamps = false;

    
    use \Venturecraft\Revisionable\RevisionableTrait;
    protected $revisionEnabled = true;
    protected $revisionCleanup = true; 
    protected $historyLimit = 500; 



    public function subcounty()
    {
        return $this->belongsTo('App\District', 'district');
    }

    public function facility_contact()
    {
        return $this->hasOne('App\FacilityContact');
    }

    public function facility_user()
    {
        return $this->hasOne(User::class);
    }

    public function scopeLocate($query, $mfl)
    {
        return $query->where("facilitycode", $mfl)->whereNotNull('facilitycode')->whereNotIn('facilitycode', ['', '0']);
    }

    /**
     * Get the facility's branch location
     *
     * @return string
     */
    public function getBranchLocationAttribute()
    {
        if($this->facility_contact->G4Sbranchname && $this->facility_contact->G4Slocation){
        	return $this->facility_contact->G4Sbranchname . ' , ' . $this->facility_contact->G4Slocation;
		}
		else if($this->facility_contact->G4Sbranchname && !$this->facility_contact->G4Slocation){
			return $this->facility_contact->G4Sbranchname;
		}
		else if(!$this->facility_contact->G4Sbranchname && $this->facility_contact->G4Slocation){
        	return $this->facility_contact->G4Slocation;
		}
		else{
			return null;
		}
    }

    public function getBranchPhonesAttribute()
    {
        if($this->facility_contact->G4Sphone1 != '' && $this->facility_contact->G4Sphone2 != '' && $this->facility_contact->G4Sphone3 != ''){
        	return $this->facility_contact->G4Sphone1 . ' / ' . $this->facility_contact->G4Sphone2 . ' / ' . $this->facility_contact->G4Sphone3;
		}
		else if($this->facility_contact->G4Sphone1 != '' && $this->facility_contact->G4Sphone2 != '' && $this->facility_contact->G4Sphone3 == ''){
        	return $this->facility_contact->G4Sphone1 . ' / ' . $this->facility_contact->G4Sphone2;
		}
		else if($this->facility_contact->G4Sphone1 != '' && $this->facility_contact->G4Sphone2 == '' && $this->facility_contact->G4Sphone3 != ''){
        	return $this->facility_contact->G4Sphone1 . ' / ' . $this->facility_contact->G4Sphone3;
		}
		else if($this->facility_contact->G4Sphone1 != '' && $this->facility_contact->G4Sphone2 == '' && $this->facility_contact->G4Sphone3 == ''){
        	return $this->facility_contact->G4Sphone1;
		}
		else{
			return null;
		}
    }

    public function getContactsAttribute()
    {
        $contacttelephone = $this->contacttelephone;
        $contacttelephone2 = $this->contacttelephone2;

        return $this->concat_contacts($contacttelephone, $contacttelephone2);
    }

    public function getFacilityContactsAttribute()
    {
        $telephone = $this->telephone;
        $telephone2 = $this->telephone2;

        return $this->concat_contacts($telephone, $telephone2);
    }

    public function getEmailStringAttribute()
    {
        $email = $this->email;
        $contact_email = $this->contact_email;

        return $this->concat_contacts($email, $contact_email);
    }

    public function getTelephoneStringAttribute()
    {
        $telephone = $this->telephone;
        $contacttelephone = $this->contacttelephone;

        return $this->concat_contacts($telephone, $contacttelephone);
    }

    public function getEmailArrayAttribute()
    {
        if($this->email != trim($this->email)){
            $this->email = trim($this->email);
            $this->save();
            unset($this->facility_contact);
        }
        if($this->contact_email != trim($this->contact_email)){
            $this->ContactEmail = trim($this->contact_email);
            $this->save();
            unset($this->facility_contact);
        }

        if(($this->email && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) || $this->email == ''){
            $this->email = null;
            $this->save();
            unset($this->facility_contact);
        }

        if(($this->contact_email && !filter_var($this->contact_email, FILTER_VALIDATE_EMAIL)) || $this->contact_email == ''){
            $this->ContactEmail = null;
            $this->save();
            unset($this->facility_contact);
        }

        $emails = [];
        if($this->email) $emails[] = $this->email;
        if($this->contact_email) $emails[] = $this->contact_email;
        if($this->subcounty->subcounty_email1 && filter_var($this->subcounty->subcounty_email1, FILTER_VALIDATE_EMAIL)) $emails[] = $this->subcounty->subcounty_email1;
        if($this->subcounty->subcounty_email2 && filter_var($this->subcounty->subcounty_email2, FILTER_VALIDATE_EMAIL)) $emails[] = $this->subcounty->subcounty_email2;
        // $f = ['dmltemail', 'dtlcemail', 'subcountyemail', 'countyemail', 'partneremail', 'partnerlabmail', 'partnerpointmail'];

        // foreach ($f as $val) {
        //     if($this->$val && $this->$val != '') $emails[] = $this->$val;
        // }
        return $emails;
    }

    public function getEmailAttribute()
    {
        return $this->facility_contact->email ?? $this->email ?? null;
    }

    public function getTelephoneAttribute()
    {
        return $this->facility_contact->telephone ?? $this->telephone ?? null;
    }

    public function getTelephone2Attribute()
    {
        return $this->facility_contact->telephone2 ?? $this->telephone2 ?? null;
    }

    public function getContactpersonAttribute()
    {
        return $this->facility_contact->contactperson ?? $this->contactperson ?? null;
    }

    public function getContacttelephoneAttribute()
    {
        return $this->facility_contact->contacttelephone ?? $this->contacttelephone ?? null;
    }

    public function getContacttelephone2Attribute()
    {
        return $this->facility_contact->contacttelephone2 ?? $this->contacttelephone2 ?? null;
    }

    public function getContactEmailAttribute()
    {
        return $this->facility_contact->ContactEmail ?? $this->ContactEmail ?? null;
    }

    public function getFaxAttribute()
    {
        return $this->facility_contact->fax ?? $this->fax ?? null;
    }

    public function getPhysicaladdressAttribute()
    {
        return $this->facility_contact->physicaladdress ?? $this->physicaladdress ?? null;
    }


    public function concat_contacts($first, $second)
    {
        if($first && !$second) return $first;
        if(!$first && $second) return $second;
        if(!$first && !$second) return null;
        return $first . ' / ' . $second;
    }
}
