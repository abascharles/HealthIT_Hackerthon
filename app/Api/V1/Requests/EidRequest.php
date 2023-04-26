<?php

namespace App\Api\V1\Requests;

use Config;
use App\Api\V1\Requests\BaseRequest;
use App\Rules\BeforeOrEqual;

class EidRequest extends BaseRequest
{
    public function rules()
    {
        $base = Config::get('boilerplate.sample_base'); 
        $eid = Config::get('boilerplate.eid'); 

        $val = array_merge($base, $eid);
        $val['dob'] = array_merge($val['dob'], [new BeforeOrEqual($this->input('datecollected'), 'datecollected')]);

        if($this->input('editted')) return [];
        return $val;
    }
}
