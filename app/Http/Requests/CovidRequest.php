<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseRequest;
use Config;
use App\Rules\BeforeOrEqual;

class CovidRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->input('facility_id') == 7148 || $this->input('receivedstatus') == 2 || $this->input('submit_type') == 'cancel') return [];

        $rules = Config::get('boilerplate.covid'); 
        $received = Config::get('boilerplate.lab_user'); 

        $user  = auth()->user();

        if($user->is_lab_user()) $rules = array_merge($rules, $received);
        unset($rules['rejectedreason']);


        return $rules;
    }
}
