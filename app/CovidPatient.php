<?php

namespace App;

use DB;
use Str;

class CovidPatient extends BaseModel
{
	protected $dates = ['dob', 'date_symptoms', 'date_admission', 'date_isolation', 'date_death'];

    public function travel()
    {
        return $this->hasMany('App\CovidTravel', 'patient_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function view_facility()
    {
        return $this->belongsTo(ViewFacility::class, 'facility_id');
    }

    public function quarantine_site()
    {
        return $this->belongsTo('App\QuarantineSite');
    }

    public function sample()
    {
        return $this->hasMany('App\CovidSample', 'patient_id');
    }

    public function contact()
    {
        return $this->hasMany('App\CovidContact', 'patient_id');
    }

    public function setHealthStatusAttribute($value)
    {
        $this->attributes['current_health_status'] = $value;
    }


    public function setSexAttribute($value)
    {
        if(is_numeric($value)) $this->attributes['sex'] = $value;
        else{
            if(\Str::contains($value, ['F', 'f'])) $this->attributes['sex'] = 2;
            else if(\Str::contains($value, ['M', 'm'])){
                $this->attributes['sex'] = 1;
            }
        }
    }


    /**
     * Get the patient's gender
     *
     * @return string
     */
    public function getGenderAttribute()
    {
        if($this->sex == 1){ return "Male"; }
        else if($this->sex == 2){ return "Female"; }
        else{ return "No Gender"; }
    }

    /*public function getCountyAttribute()
    {
        return County::find($this->county_id)->name ?? '';
    }*/

    public function most_recent()
    {
        $sample = CovidSample::where('patient_id', $this->id)
                ->whereRaw("created_at=
                    (SELECT max(created_at) FROM covid_samples WHERE patient_id={$this->id})")
                ->first();
        $this->most_recent = $sample;
    }


    public function scopeExisting($query, $data)
    {
        extract($data);
        if(isset($national_id)){
            if($national_id && strlen($national_id) > 5 && !Str::contains($national_id, ['No', 'no', 'NO', 'NA', 'N/A'])){
                return $query->where(['national_id' => $national_id]);
            }
        }
        else if(isset($identifier)){
            if(isset($facility_id)){
                $query->where(['facility_id' => $facility_id])->whereNotNull('facility_id');
            }
            else if(isset($quarantine_site_id)){
                $query->where(['quarantine_site_id' => $quarantine_site_id])->whereNotNull('quarantine_site_id');
            }
            else{
                return $query->where('id', '<', 0);
            }


            if($identifier && strlen($identifier) > 5){
                return $query->where(['identifier' => $identifier]);
            }            
        }
        return $query->where('id', '<', 0);
    }

}
