<?php

namespace App;

use App\ViewModel;

class ViralsampleView extends ViewModel
{
	protected $table = 'viralsamples_view';

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

    public function getSampleTypeOutputAttribute()
    {
        if($this->sampletype == 1) return "FROZEN PLASMA";
        else if($this->sampletype == 2) return "WHOLE BLOOD";
        else if($this->sampletype == 3) return "DBS";
        // else if($this->sampletype == 4) return "DBS Venous";
        return "";
    }

    public function get_previous_test()
    {    
        $sample = \App\ViralsampleView::where('patient_id', $this->patient_id)
                    ->whereRaw("datetested=
                    (SELECT max(datetested) FROM viralsamples WHERE patient_id={$this->patient_id} AND repeatt=0  AND rcategory between 1 AND 4 AND datetested < '{$this->datetested}')")
                    ->first();
        return $sample;
    }
}
