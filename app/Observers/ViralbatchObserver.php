<?php

namespace App\Observers;

use App\Viralbatch;
use App\Viralpatient;
use App\ViralsampleView;

use DB;

class ViralbatchObserver
{
    /**
     * Listen to the Viralbatch created event.
     *
     * @param  \App\Viralbatch  $viralbatch
     * @return void
     */
    public function updating(Viralbatch $viralbatch)
    {
        // Check if the facility of the batch has been changed
        if($viralbatch->facility_id != $viralbatch->getOriginal('facility_id'))
        {
            $samples = ViralsampleView::where('batch_id', $viralbatch->id)->get();
            $patient_ids = $samples->pluck(['patient_id'])->toArray();

            $patients = Viralpatient::whereIn('id', $patient_ids)->get();

            foreach ($patients as $key => $patient) {
                $patient->facility_id = $viralbatch->facility_id;
                $patient->pre_update();
            }
        }
    }

    public function updated(Viralbatch $viralbatch)
    {
        if(env('DOUBLE_ENTRY')){
            $update_array = [
                'highpriority' => $viralbatch->highpriority,
                'inputcomplete' => $viralbatch->input_complete,
                'batchcomplete' => $viralbatch->batch_complete,
                'siteentry' => $viralbatch->site_entry,
                'sentemail' => $viralbatch->sent_email,
                'printedby' => $viralbatch->printedby,
                'userid' => $viralbatch->user_id,
                'labtestedin' => $viralbatch->lab_id,
                'facility' => $viralbatch->facility_id,
                'datedispatchedfromfacility' => $viralbatch->datedispatchedfromfacility,
                'datereceived' => $viralbatch->datereceived,
                'datebatchprinted' => $viralbatch->datebatchprinted,
                'datedispatched' => $viralbatch->datedispatched,
                'dateindividualresultprinted' => $viralbatch->dateindividualresultprinted,
            ];

            \App\OldModels\Viralsample::where('batchno', $viralbatch->id)->update($update_array);
        }
    }
}