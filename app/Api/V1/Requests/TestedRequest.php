<?php

namespace App\Api\V1\Requests;

use App\Api\V1\Requests\BaseRequest;


class TestedRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'dob' => 'date_format:Y-m-d',
            'datecollected' => 'date_format:Y-m-d',
            'datereceived' => 'date_format:Y-m-d',
            'datetested' => 'date_format:Y-m-d',
            'datedispatched' => 'date_format:Y-m-d',

            'editted' => 'filled',
            'lab' => 'required|integer',
            'mflCode' => 'required|integer',
            'result' => 'required',
            'sex' => 'filled',
            'gender' => 'filled',
        ];
    }

    public function authorize()
    {
    	return true;        
    }
}
