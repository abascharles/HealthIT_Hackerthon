<?php

namespace App;

use App\ViewModel;

class SampleCompleteView extends ViewModel
{	
	protected $table = 'sample_complete_view';


   	public function worksheet()
   	{
      	return $this->belongsTo('App\Worksheet', 'worksheet_id');
   	}
}
