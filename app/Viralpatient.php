<?php

namespace App;

class Viralpatient extends BaseModel
{
    // protected $dates = ['datesynched', 'dob'];

    public function sample()
    {
    	return $this->hasMany(Viralsample::class, 'patient_id');
    }

    public function dr_sample()
    {
        return $this->hasMany(DrSample::class, 'patient_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function mother()
    {
        return $this->hasMany('App\Mother', 'patient_id');
    }

    public function scopeExisting($query, $facility_id, $ccc_no)
    {
        if($facility_id) return $query->where(['facility_id' => $facility_id, 'patient' => $ccc_no])->where('patient', '!=', '');
        return $query->where(['patient' => $ccc_no])->where('patient', '!=', '')->whereNull('facility_id');
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
     * Get the patient's age in years
     *
     * @return integer
     */
    public function getAgeAttribute()
    {
        return \App\Lookup::calculate_viralage(date('Y-m-d'), $this->dob);
    }

    public function last_test()
    {
        /*$sql = "SELECT * FROM viralsamples WHERE patient_id={$this->id} AND datetested=
                    (SELECT max(datetested) FROM viralsamples WHERE patient_id={$this->id} AND repeatt=0  and rcategory between 1 and 4)
        ";*/ 
        // $this->recent = \DB::select($sql);

        $sample = \App\Viralsample::where('patient_id', $this->id)
                ->whereRaw("datetested=
                    (SELECT max(datetested) FROM viralsamples WHERE patient_id={$this->id} AND repeatt=0  and rcategory between 1 and 4)")
                ->get()->first();
        $this->recent = $sample;
    }

    public function most_recent()
    {
        $sample = \App\Viralsample::where('patient_id', $this->id)
                ->whereRaw("created_at=
                    (SELECT max(created_at) FROM viralsamples WHERE patient_id={$this->id})")
                ->get()->first();
        $this->most_recent = $sample;
    }
}
