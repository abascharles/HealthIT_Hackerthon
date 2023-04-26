<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Traveller extends BaseModel
{


    public function setResultAttribute($value)
    {
        $this->attributes['result'] = $this->getResultValue($value);
    }

    public function getResultNameAttribute()
    {
        return $this->getResultName($this->result);
    }

    public function setIgmResultAttribute($value)
    {
        $this->attributes['igm_result'] = $this->getResultValue($value);
    }

    public function getIgmResultNameAttribute()
    {
        return $this->getResultName($this->igm_result);
    }

    public function setIggIgmResultAttribute($value)
    {
        $this->attributes['igg_igm_result'] = $this->getResultValue($value);
    }

    public function getIggIgmResultNameAttribute()
    {
        return $this->getResultName($this->igg_igm_result);
    }


    public function setAntigenResultAttribute($value)
    {
        $this->attributes['antigen_result'] = $this->getResultValue($value);
    }

    public function getAntigenResultNameAttribute()
    {
        return $this->getResultName($this->antigen_result);
    }

    public function getResultName($result)
    {
        if($result == 1){ return "Negative"; }
        else if($result == 2){ return "Positive"; }
        else if($result == 3){ return "Failed"; }
        else if($result == 5){ return "Collect New Sample"; }
        else{ return ""; }        
    }

    public function getResultValue($value)
    {
        if(is_numeric($value) || !$value) return $value;
        else{
            $value = strtolower($value);
            if(\Str::contains($value, ['neg'])) return 1;
            else if(\Str::contains($value, ['pos'])) return 2;
            else if(\Str::contains($value, ['coll'])) return 5;
            return 5;
        }
    }

    /*public function setIgmIndexResultAttribute($value)
    {
        if(is_numeric($value) || !$value) $this->attributes['igm_index_result'] = $value;
        else{
            $value = strtolower($value);
            if(\Str::contains($value, ['neg'])) $this->attributes['igm_index_result'] = 1;
            else if(\Str::contains($value, ['pos'])) $this->attributes['igm_index_result'] = 2;
            else if(\Str::contains($value, ['coll'])) $this->attributes['igm_index_result'] = 5;
        }
    }

    public function getIgmIndexResultNameAttribute()
    {
        if($this->igm_index_result == 1){ return "Negative"; }
        else if($this->igm_index_result == 2){ return "Positive"; }
        else if($this->igm_index_result == 3){ return "Failed"; }
        else if($this->igm_index_result == 5){ return "Collect New Sample"; }
        else{ return ""; }
    }*/


    public function setSexAttribute($value)
    {
        if(is_numeric($value)) $this->attributes['sex'] = $value;
        else{
            if(\Str::contains($value, ['F', 'f'])) $this->attributes['sex'] = 2;
            else if(\Str::contains($value, ['M', 'm'])){
                $this->attributes['sex'] = 1;
            }
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

}
