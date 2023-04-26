<?php

namespace App;

class Worklist extends BaseModel
{

    public function sample()
    {
    	return $this->hasMany('App\Sample');
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function view_facility()
    {
        return $this->belongsTo(ViewFacility::class, 'facility_id');
    }


    /**
     * Get the worklist assay type
     *
     * @return string
     */
    public function getTypeAttribute()
    {
        if($this->testtype == 1){ return "Eid"; }
        else if($this->testtype == 2){ return "Vl"; }
        else{ return ""; }
    }


    /**
     * Get the worklist assay type
     *
     * @return string
     */
    public function getStatusAttribute()
    {
    	switch ($this->status_id) {
    		case 1:
    			return "In-Process";
    			break;
    		case 2:
    			return "Complete with results";
    			break;
    		case 3:
    			return "Complete with results";
    			break;
    		case 4:
    			return "Cancelled";
    			break;    		
    		default:
    			return "";
    			break;
    	}
    }

}
