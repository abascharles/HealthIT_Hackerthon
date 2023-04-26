<?php

namespace App\Api\V1\Requests;

use Dingo\Api\Http\FormRequest;

class ApiRequest extends FormRequest
{

    public function authorize()
    {
        if(auth('api')->user()->user_type_id == 0) return true;
		return false;
    }

    public function messages()
    {
        return [];
    }

    public function rules()
    {
        return [];
    }
}
