<?php

namespace App\Api\V1\Requests;

use Config;
use App\Api\V1\Requests\BaseRequest;
use App\Rules\BeforeOrEqual;

class CragRequest extends BaseRequest
{
    public function rules()
    {
        $val = Config::get('boilerplate.cd4'); 
        $val['dob'] = array_merge($val['dob'], [new BeforeOrEqual($this->input('datecollected'), 'datecollected')]);

        if($this->input('editted')) return [];
        return $val;
    }
}
