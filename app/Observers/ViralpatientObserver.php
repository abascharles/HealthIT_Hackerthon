<?php

namespace App\Observers;

use App\Viralpatient;
use App\Viralsample;
use App\Lookup;

class ViralpatientObserver
{
    /**
     * Listen to the Viralpatient updating event.
     *
     * @param  \App\Viralpatient  $viralpatient
     * @return void
     */
    public function updating(Viralpatient $viralpatient)
    {
        // Check if the dob of the facility has been changed
        if($viralpatient->dob != $viralpatient->getOriginal('dob'))
        {
            $samples = Viralsample::where('patient_id', $viralpatient->id)->get();

            foreach ($samples as $key => $sample) {
                if(!$viralpatient->dob){
                    $sample->age = 0;
                    $sample->pre_update();
                }
                $age = Lookup::calculate_viralage($sample->datecollected, $viralpatient->dob);
                $sample->age = $age;
                $sample->pre_update();
            }
        }
    }
}