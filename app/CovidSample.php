<?php

namespace App;

use DB;

class CovidSample extends BaseModel
{

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

    public function worksheet()
    {
        return $this->belongsTo('App\CovidWorksheet', 'worksheet_id');
    }

    public function travel()
    {
        return $this->hasMany('App\CovidTravel', 'sample_id');
    }

    public function lab()
    {
        return $this->belongsTo('App\Lab', 'lab_id');
    }

    public function pool_sample()
    {
        return $this->belongsTo('App\CovidPoolSample', 'pool_sample_id');
    }


    // Parent sample
    public function parent()
    {
        return $this->belongsTo('App\CovidSample', 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany('App\CovidSample', 'parentid');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approvedby');
    }

    public function final_approver()
    {
        return $this->belongsTo(User::class, 'approvedby2');
    }


    public function calc_age()
    {
        if($this->datecollected) $this->age = $this->datecollected->diffInYears($this->patient->dob);
        $this->age = now()->diffInYears($this->patient->dob);
    }


    public function calc_result()
    {
        if(!$this->result){
            if(is_numeric($this->target1) || is_numeric($this->target2)){
                $this->result = 2;
            }else{
                $this->result = 1;
            }
            $this->pre_update();
        }
    }


    public function setResultAttribute($value)
    {
        if(is_numeric($value) || !$value) $this->attributes['result'] = $value;
        else{
            $value = strtolower($value);
            if(\Str::contains($value, ['neg'])) $this->attributes['result'] = 1;
            else if(\Str::contains($value, ['pos'])) $this->attributes['result'] = 2;
            else if(\Str::contains($value, ['coll'])) $this->attributes['result'] = 5;
        }
    }

    public function setSampleTypeAttribute($value)
    {
        if(is_numeric($value)) $this->attributes['sample_type'] = $value;
        else{
            $a = explode(' ', $value);
            if(count($a) == 1) $a = explode('-', $value);
            $word = $a[0];
            $this->attributes['sample_type'] = DB::table('covid_sample_types')->where('name', 'like', "{$value}%")->first()->id ?? null;
        }
    }

    

    public function remove_rerun()
    {
        if($this->parentid == 0) $this->remove_child();
        else{
            $this->remove_sibling();
        }
    }

    public function remove_child()
    {
        $children = $this->child;

        foreach ($children as $s) {
            $s->delete();
        }

        $this->repeatt=0;
        $this->save();
    }

    public function remove_sibling()
    {
        $parent = $this->parent;
        $children = $parent->child;

        foreach ($children as $s) {
            if($s->run > $this->run) $s->delete();            
        }

        $this->repeatt=0;
        $this->save();
    }

    public function set_tat()
    {
        $this->tat1 = Common::get_days($this->datecollected, $this->datereceived, false);
        $this->tat2 = Common::get_days($this->datereceived, $this->datetested, false);
        $this->tat3 = Common::get_days($this->datetested, $this->datedispatched, false);
        $this->tat4 = Common::get_days($this->datecollected, $this->datedispatched, false);        
    }

    public function getIsReadyAttribute()
    {
        if($this->repeatt == 0){
            if(in_array(env('APP_LAB'), \App\Lookup::$double_approval)){
                if(($this->dateapproved && $this->dateapproved2) || ($this->approvedby && $this->approvedby2)){
                    return true;
                }
            }
            else{
                if($this->dateapproved || $this->approvedby) return true;
            }
        }
        return false;
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

    /**
     * Get if rerun has been created
     *
     * @return string
     */
    public function getHasRerunAttribute()
    {
        if($this->parentid == 0){
            $child_count = $this->child->count();
            if($child_count) return true;
        }
        else{
            $run = $this->run + 1;
            $child = \App\Sample::where(['parentid' => $this->parentid, 'run' => $run])->first();
            if($child) return true;
        }
        return false;
    }


    public function scopeRuns($query, $sample)
    {
        if($sample->parentid == 0){
            return $query->whereRaw("parentid = {$sample->id} or id = {$sample->id}")->orderBy('run', 'asc');
        }
        else{
            return $query->whereRaw("parentid = {$sample->parentid} or id = {$sample->parentid}")->orderBy('run', 'asc');
        }
    }

}
