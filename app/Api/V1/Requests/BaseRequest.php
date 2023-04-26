<?php

namespace App\Api\V1\Requests;

use Dingo\Api\Http\FormRequest;
use App\ApiToken;

class BaseRequest extends FormRequest
{

    public function authorize()
    {
        $apikey = $this->headers->get('apikey');
    	$apikey2 = $this->input('apikey');
        /*$actual_key = env('API_KEY');
    	if(($apikey != $actual_key && $apikey2 != $actual_key) || !$actual_key) return false;
    	else{
    		return true;
    	}*/    

        $token = NULL;
        if($apikey) $token = ApiToken::where('token', $apikey)->first();
        if($apikey2 && !$token) $token = ApiToken::where('token', $apikey2)->first();
        if($token) return true;
        return false;
    }

    public function messages()
    {
        return [
            'before_or_equal' => 'The :attribute field must be before or equal to today.'
        ];
    }
}
