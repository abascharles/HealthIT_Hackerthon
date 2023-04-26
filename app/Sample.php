<?php

namespace App;

use App\BaseModel;

class Sample extends BaseModel
{
    // protected $dates = ['datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'dateinitiatedontreatment', 'datesynched'];

    public function tat($datedispatched)
    {
        return \App\Misc::get_days($this->datecollected, $datedispatched, false);
    }

    public function patient()
    {
    	return $this->belongsTo('App\Patient');
    }

    public function batch()
    {
        return $this->belongsTo('App\Batch');
    }

    public function worksheet()
    {
        return $this->belongsTo('App\Worksheet');
    }

    // Parent sample
    public function parent()
    {
        return $this->belongsTo('App\Sample', 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany('App\Sample', 'parentid');
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

    public function last_test()
    {
        $sample = \App\Sample::where('patient_id', $this->patient_id)
                ->whereRaw("datetested=
                    (SELECT max(datetested) FROM samples WHERE patient_id={$this->patient_id} AND repeatt=0 AND result in (1, 2) AND datetested < '{$this->datetested}')")
                ->get()->first();
        $this->recent = $sample;
    }

    public function prev_tests()
    {
        $s = $this;
        $samples = \App\Sample::where('patient_id', $this->patient_id)
                ->when(true, function($query) use ($s){
                    if($s->datetested) return $query->where('datetested', '<', $s->datetested);
                    return $query->where('datecollected', '<', $s->datecollected);
                })
                ->where('repeatt', 0)
                ->whereIn('result', [1, 2])
                ->orderBy('id', 'desc')
                ->get();
        $this->previous_tests = $samples;
    }



    /**
     * Get the sample's previous worksheet
     *
     * @return string
     */
    public function getPrevWorksheetAttribute()
    {
        if($this->run < 2) return null;
        else{
            if($this->run == 2) $this->prev_run = $this->parent;
            else{
                $this->prev_run = Sample::where(['parentid' => $this->parentid, 'run' => $this->run-1])->first();
            }
            return $this->prev_run->worksheet_id;
        }
    }

    public function scopeExisting($query, $patient_id, $batch_id, $created_at)
    {
        return $query->where(['patient_id' => $patient_id, 'batch_id' => $batch_id, 'created_at' => $created_at]);
    }

    public function corrupt_version()
    {
        $batch = Batch::where('old_id', '=', $this->batch_id)->first();
        $worksheet = Worksheet::where('old_id', '=', $this->worksheet_id)->first();
        $patient = Patient::where('old_id', '=', $this->patient_id)->first();

        $this->batch_id = $batch->id;
        $this->worksheet_id = $worksheet->id;
        $this->patient_id = $patient->id;
        $this->save();
    }

}
