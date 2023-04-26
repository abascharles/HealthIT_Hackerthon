<?php

namespace App\Observers;

use App\Patient;
use App\Sample;
use App\Lookup;

class PatientObserver
{
    /**
     * Listen to the Patient updating event.
     *
     * @param  \App\Patient  $patient
     * @return void
     */
    public function updating(Patient $patient)
    {
        // Check if the dob of the facility has been changed
        if($patient->dob != $patient->getOriginal('dob'))
        {
            $samples = Sample::where('patient_id', $patient->id)->get();

            foreach ($samples as $key => $sample) {
                if(!$patient->dob){
                    $sample->age = 0;
                    $sample->pre_update();
                }
                $age = Lookup::calculate_age($sample->datecollected, $patient->dob);
                $sample->age = $age;
                $sample->pre_update();
            }
        }
    }

    
}