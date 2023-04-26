<?php

namespace App;

use DB;

class CovidSampleView extends BaseModel
{
	protected $table = "covid_sample_view";
    
    protected $dates = ['datecollected', 'datereceived', 'datetested', 'datedispatched', 'dateapproved', 'dateapproved2'];

    protected $casts = [
        'symptoms' => 'array',
        'observed_signs' => 'array',
        'underlying_conditions' => 'array',     
    ];
    
	public function patient()
    {
        return $this->belongsTo('App\CovidPatient', 'patient_id');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function view_facility()
    {
        return $this->belongsTo(ViewFacility::class, 'facility_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lab()
    {
        return $this->belongsTo('App\Lab', 'lab_id');
    }
    
    /**
     * Get the sample's result name
     *
     * @return string
     */
    public function getResultNameAttribute()
    {
        if($this->result == 1){ return "Negative"; }
        else if($this->result == 2){ return "Positive"; }
        else if($this->result == 3){ return "Failed"; }
        else if($this->result == 5){ return "Collect New Sample"; }
        else{ return ""; }
    }


    public function getNphlJustificationAttribute()
    {
        if(in_array($this->justification, [3, 7])) return 1;
        if(in_array($this->justification, [16])) return 2;
        if(in_array($this->justification, [10])) return 3;
        return 4;
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
    
    public function getSampletypeAttribute()
    {
        return DB::table('covid_test_types')->where('id', '=', "{$this->test_type}")->first()->name ?? null;
    }
}
