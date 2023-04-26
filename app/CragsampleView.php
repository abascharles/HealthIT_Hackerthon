<?php

namespace App;

use App\ViewModel;

class CragsampleView extends ViewModel
{
	protected $table = 'crag_samples_view';

    public function first_approver(){
        return $this->belongsTo(User::class, 'approvedby');
    }

    public function second_approver(){
        return $this->belongsTo(User::class, 'approvedby2');
    }

    public function printer(){
        return $this->belongsTo(User::class, 'printedby');
    }

    /**
     * Get the patient's age in years
     *
     * @return integer
     */
    // public function getAgeAttribute()
    // {
    //     return \App\Lookup::calculate_viralage(date('Y-m-d'), $this->dob);
    // }
}
