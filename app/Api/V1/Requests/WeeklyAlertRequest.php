<?php

namespace App\Api\V1\Requests;

use App\Api\V1\Requests\BaseRequest;
use App\Rules\BeforeOrEqual;

class WeeklyAlertRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'lab' => 'required|integer',            
        ];
    }
}
