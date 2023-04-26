<?php

namespace App;

class Viralsample extends BaseModel
{
    // protected $dates = ['datecollected', 'datetested', 'datemodified', 'dateapproved', 'dateapproved2', 'dateinitiatedontreatment', 'datesynched'];


    public function tat($datedispatched)
    {
        return Misc::get_days($this->datecollected, $datedispatched, false);
    }

    public function patient()
    {
    	return $this->belongsTo(Viralpatient::class, 'patient_id');
    }

    public function batch()
    {
        return $this->belongsTo(Viralbatch::class, 'batch_id');
    }

    public function worksheet()
    {
        return $this->belongsTo(Viralworksheet::class, 'worksheet_id');
    }


    // Parent sample
    public function parent()
    {
        return $this->belongsTo(Viralsample::class, 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany(Viralsample::class, 'parentid');
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

    public function scopeRuns($query, $sample)
    {
        if($sample->parentid == 0){
            return $query->whereRaw("parentid = {$sample->id} or id = {$sample->id}")->orderBy('run', 'asc');
        }
        else{
            return $query->whereRaw("parentid = {$sample->parentid} or id = {$sample->parentid}")->orderBy('run', 'asc');
        }
    }

    public function setProphylaxisAttribute($value)
    {
        if(!is_numeric($value)) $this->attributes['prophylaxis'] = \App\Lookup::viral_prophylaxis($value);
        else{
            $this->attributes['prophylaxis'] = $value;
        }
    }



    public function last_test()
    {
        $sample = \App\Viralsample::where('patient_id', $this->patient_id)
                ->whereRaw("datetested=
                    (SELECT max(datetested) FROM viralsamples WHERE patient_id={$this->patient_id} AND repeatt=0 AND rcategory between 1 and 4 AND datetested < '{$this->datetested}')")
                ->get()->first();
        $this->recent = $sample;
    }

    public function prev_tests()
    {
        $s = $this;
        $samples = \App\Viralsample::where('patient_id', $this->patient_id)
                ->when(true, function($query) use ($s){
                    if($s->datetested) return $query->where('datetested', '<', $s->datetested);
                    return $query->where('datecollected', '<', $s->datecollected);
                })
                ->where('repeatt', 0)
                ->whereIn('rcategory', [1, 2, 3, 4])
                ->orderBy('id', 'desc')
                ->get();
        $this->previous_tests = $samples;
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
     * Get the sample's coloured result name
     *
     * @return string
     */

    public function getColouredResultAttribute()
    {
        if(is_numeric($this->result)){
            if($this->result < 1000){
                return "<strong><div style='color: #00ff00;'>{$this->result} </div></strong>";
            }
            else{
                return "<strong><div style='color: #ff0000;'>{$this->result} </div></strong>";             
            }
        }
        else if($this->result == "< LDL copies/ml" || $this->result == "Target Not Detected"){
            return "<strong><div style='color: #00ff00;'>&lt; LDL copies/ml</div></strong>";
        }
        else if($this->result == "> 10,000,000 cp/ml"){
            return "<strong><div style='color: #ff0000;'>{$this->result} </div></strong>";
        }
        else{
            return "<strong><div style='color: #cccc00;'>{$this->result} </div></strong>";
        }
    }

    /**
     * Get the sample's Sample Type
     *
     * @return string
     */
    public function getSampleTypeOutputAttribute()
    {
        if($this->sampletype == 1) return "FROZEN PLASMA";
        else if($this->sampletype == 2) return "WHOLE BLOOD";
        else if($this->sampletype == 3) return "DBS";
        // else if($this->sampletype == 4) return "DBS Venous";
        return "";
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
            $child = \App\Viralsample::where(['parentid' => $this->parentid, 'run' => $run])->first();
            if($child) return true;
        }
        return false;
    }


    /**
     * Get the sample's result comment
     *
     * @return string
     */
    public function getResultCommentAttribute()
    {
        $str = '';
        $result = $this->result;
        $interpretation = $this->interpretation;
        $lower_interpretation = strtolower($interpretation);
        // < ldl
        if(\Str::contains($interpretation, ['<'])){
            $str = "LDL:Lower Detectable Limit i.e. Below Detectable levels by machine ";
            if(\Str::contains($interpretation, ['839'])){
                $str .= "( Abbott DBS  &lt;839 copies/ml )";
            }
            else if(\Str::contains($interpretation, ['40'])){
                $str .= "( Abbott Plasma  &lt;40 copies/ml )";
            }
            else if(\Str::contains($interpretation, ['150'])){
                $str .= "( Abbott Plasma  &lt;150 copies/ml )";
            }
            else if(\Str::contains($interpretation, ['20'])){
                $str .= "( Roche Plasma  &lt;20 copies/ml )";
            }
            else if(\Str::contains($interpretation, ['Titer', 'titer'])){
                $str .= "( C8800 Plasma  &lt;20 copies/ml )";
            }
            else if(\Str::contains($interpretation, ['30'])){
                $str .= "( Pantha Plasma  &lt;30 copies/ml )";
            }
            else if(\Str::contains($interpretation, ['log']) && \Str::contains($interpretation, ['<'])){
                $x = preg_replace("/[^<0-9.]/", "", $interpretation);
                $n = round(pow(10, $x));
                $str .= "( &lt;{$n} copies/ml )";
            }
            else{
                $n = preg_replace("/[^<0-9]/", "", $interpretation);
                $str .= "( &lt;{$n} copies/ml )";
            }
        }
        else if(\Str::contains($result, ['<']) && \Str::contains($lower_interpretation, ['not detected'])){
            $str = "No circulating virus ie. level of HIV in blood is below the threshold needed for detection by this test. Doesn’t mean client Is Negative";
        }
        else if($result == "Target Not Detected"){
            $str = "No circulating virus ie. level of HIV in blood is below the threshold needed for detection by this test. Doesn’t mean client Is Negative";
        }
        else if($result == "Collect New Sample" || $result == "Failed"){
            $str = "Sample failed during processing due to sample deterioration or equipment malfunction.  Redraw another sample and send to lab as soon as possible";
        }
        else{}
        return "<small>{$str}</small>";
    }

    public function release_redraw()
    {   
        $this->labcomment = "Failed Test";
        $this->repeatt = 0;
        $this->result = "Collect New Sample";
        $this->approvedby = auth()->user()->id;
        $this->approvedby2 = auth()->user()->id;
        $this->dateapproved = date('Y-m-d');
        $this->dateapproved2 = date('Y-m-d');
        $this->save();
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
                $this->prev_run = Viralsample::where(['parentid' => $this->parentid, 'run' => $this->run-1])->first();
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
        $batch = Viralbatch::where('old_id', '=', $this->batch_id)->first();
        $worksheet = Viralworksheet::where('old_id', '=', $this->worksheet_id)->first();
        $patient = Viralpatient::where('old_id', '=', $this->patient_id)->first();

        $this->batch_id = $batch->id;
        $this->worksheet_id = $worksheet->id;
        $this->patient_id = $patient->id;
        $this->save();
    }
    
}
