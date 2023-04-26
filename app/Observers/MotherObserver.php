<?php

namespace App\Observers;

use App\Mother;
use App\Lookup;

class MotherObserver
{
    /**
     * Listen to the Mother updating event.
     *
     * @param  \App\Mother  $mother
     * @return void
     */
    public function updating(Mother $mother)
    {
        // Check if the dob of the facility has been changed
        if($mother->mother_dob != $mother->getOriginal('mother_dob'))
        {
            $mother->load('patient.sample');

            foreach ($mother->patient as $patient) {
                foreach ($patient->sample as $sample) {
                    $mother_age = Lookup::calculate_viralage($sample->datecollected, $mother->mother_dob);
                    $sample->mother_age = $mother_age;
                    $sample->pre_update();
                }
            }
        }
    }
}