<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CancerSampleView extends ViewModel
{
    protected $table = 'cancer_samples_view';

    public function worksheet()
    {
        return $this->belongsTo('App\CancerWorksheet', 'worksheet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
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

    public function getNphlResultNameAttribute()
    {
        if($this->result == 1) return "N";
        else if($this->result == 2) return "P";
        else{ return "I"; }
    }

    public function getTestedAtAttribute()
    {
        if ($this->site_entry == 2)
            return "POC Site";
        return "Lab";
    }
}
