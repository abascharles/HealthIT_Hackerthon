<?php

namespace App;

use App\ViewModel;

class SampleView extends ViewModel
{
	protected $table = 'samples_view';

	function lab_entered_by($user=null)
	{
	$user1 = \App\User::where('id', '=', $user)->first();
          if(empty($user1)){
            return '';
        }else{
		return $user1->surname . ' '. $user1->oname;
        }
	}

	function batch_received_by($user=null)
	{
	$user1 = \App\User::where('id', '=', $user)->first();
          if(empty($user1)){
            return '';
        }else{
		return $user1->surname . ' '. $user1->oname;
        }
	}

    /**
     * Get the sample's result name
     *
     * @return string
     */
    public function getResultNameAttribute()
    {
        if($this->result == 1){ return "Negative"; }
        else if($this->result == 2){ return "Positive"; }
        else if($this->result == 3){ return "Failed"; }
        else if($this->result == 5){ return "Collect New Sample"; }
        else{ return ""; }
    }
}
