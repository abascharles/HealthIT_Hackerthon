<?php

namespace App\Api\V1\Requests;

use App\Api\V1\Requests\BaseRequest;
use App\Rules\BeforeOrEqual;

class NodeRequest extends BaseRequest
{
    public function authorize()
    {
        $apikey = $this->headers->get('apikey');
    	$apikey2 = $this->input('apikey');
        $actual_key = env('NODE_API_KEY');
    	if(($apikey != $actual_key && $apikey2 != $actual_key) || !$actual_key) return false;
    	else{
    		return true;
    	} 
    }

    
    public function rules()
    {
        return [];
    }
}
