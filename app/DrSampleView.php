<?php

namespace App;

use App\ViewModel;

class DrSampleView extends ViewModel
{
	protected $table = 'dr_samples_view';
	

    // Parent sample
    public function parent()
    {
        return $this->belongsTo(DrSampleView::class, 'parentid');
    }

    // Child samples
    public function child()
    {
        return $this->hasMany(DrSampleView::class, 'parentid');
    }

    public function getVlSampleAttribute()
    {
        $sample = Viralsample::where($this->only(['patient_id', 'datecollected']))->first();
        return $sample;
    }

    /**
     * Get the patient's gender
     *
     * @return string
     */
    public function getControlTypeAttribute()
    {
        if($this->control == 1){ return "Negative Control"; }
        else if($this->control == 2){ return "Positive Control"; }
        else{ return "Normal Sample"; }
    }

    public function getChromatogramLinkAttribute()
    {
        $ui_url = \App\MiscDr::$ui_url;
        return $ui_url . $this->chromatogram_url;
    }

    public function getViewChromatogramAttribute()
    {
        $full_link = "<a href='{$this->chromatogram_link}' target='_blank'> View Chromatogram </a>";
        return $full_link;
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
            $child = \App\DrSample::where(['parentid' => $this->parentid, 'run' => $run])->first();
            if($child) return true;
        }
        return false;
    }


    // mid being my id
    // Used when sending samples to sanger
    public function getMidAttribute()
    {
        if(env('APP_LAB') == 100) return $this->patient;
        if(env('APP_LAB') == 1 && $this->run > 1) return env('DR_PREFIX') . $this->parentid;
        return env('DR_PREFIX') . $this->id;
    }


    public function scopeExisting($query, $data_array)
    {
        // return $query->where(['facility_id' => $data_array['facility_id'], 'patient' => $data_array['patient'], 'datecollected' => $data_array['datecollected'], ]);
        $facility_id = $data_array['facility_id'] ?? null;

        $min_date = date('Y-m-d', strtotime($data_array['datecollected'] . ' -3 days'));
        $max_date = date('Y-m-d', strtotime($data_array['datecollected'] . ' +3 days'));
        return $query->where(['facility_id' => $data_array['facility_id'], 'patient' => $data_array['patient'], 'receivedstatus' => 1])
                    // ->whereBetween('datecollected', [$min_date, $max_date])
                    ->when(true, function($query) use($facility_id){
                        if($facility_id) return $query->where('facility_id', $facility_id);
                        return $query->whereNull('facility_id');                        
                    })                    
                    ->whereNull('datedispatched');
    }
}
