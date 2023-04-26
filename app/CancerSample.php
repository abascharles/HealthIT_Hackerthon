<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancerSample extends BaseModel
{
    public function patient()
    {
    	return $this->belongsTo(CancerPatient::class, 'patient_id', 'id');
    }

    public function lab()
    {
        return $this->belongsTo(Lab::class, 'lab_id', 'id');
    }

    public function facility()
    {
    	return $this->belongsTo(Facility::class);
    }

    public function facility_lab()
    {
        return $this->belongsTo(Facility::class, 'lab_id', 'id');
    }

    public function worksheet()
    {
        return $this->belongsTo('App\CancerWorksheet');
    }

    // Parent sample
    public function parent()
    {
        return $this->belongsTo('App\CancerSample', 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany('App\CancerSample', 'parentid');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'createdby');
    }

    public function canceller()
    {
        return $this->belongsTo(User::class, 'cancelledby');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approvedby');
    }

    public function final_approver()
    {
        return $this->belongsTo(User::class, 'approvedby2');
    }

    public function getTestingLabAttribute()
    {
        if (in_array($this->site_entry, [0,1]))
            return $this->lab;
        return $this->facility_lab;
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

    public function setTATs()
    {        
        $this->setTAT1();
        $this->setTAT2();
        $this->setTAT3();
        $this->setTAT4();
        return $this->save();
    }

    private function setTAT1()
    {
        $this->tat1 = $this->getTATDaysCount($this->datereceived, $this->datecollected);
    }

    private function setTAT2()
    {
        $this->tat2 = $this->getTATDaysCount($this->datetested, $this->datereceived);
    }

    private function setTAT3()
    {
        $this->tat3 = $this->getTATDaysCount($this->datedispatched, $this->datetested);
    }

    private function setTAT4()
    {
        $this->tat4 = $this->getTATDaysCount($this->datedispatched, $this->datecollected);
    }

    private function getTATDaysCount($greater_date, $lesser_data)
    {
        $days = NULL;
        if (null !== $greater_date && null !== $lesser_data) {
            $diff = date_diff(date_create($greater_date), date_create($lesser_data));
            $days = $diff->d;
            if ($days == 0)
                $days = 1;
        }
        
        return $days;
    }
}
