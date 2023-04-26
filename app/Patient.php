<?php

namespace App;

use App\BaseModel;

class Patient extends BaseModel
{
    // protected $dates = ['datesynched', 'dob'];

    public function sample()
    {
    	return $this->hasMany('App\Sample');
    }

    public function mother()
    {
    	return $this->belongsTo('App\Mother');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function scopeExisting($query, $facility_id, $hei_number)
    {
        return $query->where(['facility_id' => $facility_id, 'patient' => $hei_number]);
    }

    public function setPatientPhoneNoAttribute($value)
    {
        if(preg_match('/[2][5][4][7][0-9]{8}/', $value)) $this->attributes['patient_phone_no'] = $value;
        else{
            $this->attributes['patient_phone_no'] = null;
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

    /**
     * Get the patient's age in months
     *
     * @return integer
     */
    public function getAgeAttribute()
    {
        return \App\Lookup::calculate_age(date('Y-m-d'), $this->dob);
    }

    public function last_test()
    {
        $sample = \App\Sample::where('patient_id', $this->id)
                ->whereRaw("datetested=
                    (SELECT max(datetested) FROM samples WHERE patient_id={$this->id} AND repeatt=0 AND result in (1, 2))")
                ->get()->first();
        $this->recent = $sample;
    }

    public function most_recent()
    {
        $sample = \App\Sample::where('patient_id', $this->id)
                ->whereRaw("created_at=
                    (SELECT max(created_at) FROM samples WHERE patient_id={$this->id})")
                ->get()->first();
        $this->most_recent = $sample;
    }
}
