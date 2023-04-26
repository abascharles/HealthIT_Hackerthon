<?php

namespace App\Api\V1\Requests;

use Config;
use App\Api\V1\Requests\BaseRequest;
use App\Rules\BeforeOrEqual;

class EidCompleteRequest extends BaseRequest
{
    public function rules()
    {
        $base = Config::get('boilerplate.sample_base'); 
        $eid = Config::get('boilerplate.eid'); 
        $complete = Config::get('boilerplate.complete_result'); 

        $semi = array_merge($base, $eid);
        $val = array_merge($semi, $complete);
        $val['pcrtype'] = array_merge($val['pcrtype'], ['required']);
        $val['dob'] = array_merge($val['dob'], [new BeforeOrEqual($this->input('datecollected'), 'datecollected')]);
        $val['datecollected'] = array_merge($val['datecollected'], [new BeforeOrEqual($this->input('datereceived'), 'datereceived')]);
        $val['datereceived'] = array_merge($val['datereceived'], [new BeforeOrEqual($this->input('datetested'), 'datetested')]);
        $val['datetested'] = array_merge($val['datetested'], [new BeforeOrEqual($this->input('datedispatched'), 'datedispatched')]);

        if($this->input('editted')) return [];
        
        return $val;
    }
}
