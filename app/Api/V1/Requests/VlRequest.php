<?php

namespace App\Api\V1\Requests;

use Config;
use App\Api\V1\Requests\BaseRequest;
use App\Rules\BeforeOrEqual;

class VlRequest extends BaseRequest
{
    public function rules()
    {
        $base = Config::get('boilerplate.sample_base'); 
        $vl = Config::get('boilerplate.vl'); 

        $val = array_merge($base, $vl);
        $val['dob'] = array_merge($val['dob'], [new BeforeOrEqual($this->input('datecollected'), 'datecollected')]);
        $val['prophylaxis'] = ['required', 'exists:viralregimen,code'];

        if($this->input('editted')) return [];
        return $val;
    }
}
