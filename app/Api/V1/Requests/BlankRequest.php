<?php

namespace App\Api\V1\Requests;

use App\Api\V1\Requests\BaseRequest;
use App\Rules\BeforeOrEqual;

class BlankRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'test' => 'required|integer|max:3',
            'start_date' => ['date_format:Y-m-d', 'required_with:end_date', new BeforeOrEqual($this->input('end_date'), 'end_date')],
            'end_date' => 'date_format:Y-m-d',
            'date_dispatched_start' => ['date_format:Y-m-d', 'required_with:date_dispatched_end', new BeforeOrEqual($this->input('date_dispatched_end'), 'date_dispatched_end')],
            'date_dispatched_end' => 'date_format:Y-m-d',
            
        ];
    }
}
