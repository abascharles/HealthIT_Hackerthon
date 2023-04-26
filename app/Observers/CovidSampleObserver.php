<?php

namespace App\Observers;

use App\CovidSample;
use DB;

class CovidSampleObserver
{
    /**
     * Handle the covid sample "saving" event.
     *
     * @param  \App\CovidSample  $covidSample
     * @return void
     */
    public function saving(CovidSample $covidSample)
    {
        $user = auth()->user();
        if(!$covidSample->site_entry && $user){
            if($user->user_type_id == 5) $covidSample->site_entry = 1;
            else{
                $covidSample->site_entry = 0;
            }
        }
        if(!$covidSample->lab_id && $user && $user->other_lab) $covidSample->lab_id = $user->lab_id;
        if(!$covidSample->lab_id && $user) $covidSample->lab_id = env('APP_LAB');
        if(!$covidSample->user_id && $user) $covidSample->user_id = $user->id;
        if(!$covidSample->received_by && $covidSample->datereceived && $user) $covidSample->received_by = $user->id;
        if(($covidSample->patient->dob && !$covidSample->age)) $covidSample->calc_age();

        if($covidSample->isDirty('result') && $covidSample->result && !$covidSample->worksheet_id){
            $covidSample->dateapproved = $covidSample->dateapproved2 = date('Y-m-d');
            $covidSample->approvedby = $covidSample->approvedby2 = $user->id;
            if(!$covidSample->datetested) $covidSample->datetested = date('Y-m-d');
        }

        if($covidSample->isDirty('receivedstatus') && $covidSample->national_sample_id && $covidSample->receivedstatus == 1 && $covidSample->datedispatched)
        {
            $covidSample->datedispatched = null;
            $covidSample->dateapproved = $covidSample->dateapproved2 = null;
            $covidSample->approvedby = $covidSample->approvedby2 = null;
        }

        if($covidSample->receivedstatus == 2){
            $covidSample->worksheet_id = null;
        }

        /*if($covidSample->county && !$covidSample->county_id){
            $covidSample->county_id = DB::table('countys')->where('name', $covidSample->county)->first()->id ?? null;
        }*/
    }

    /**
     * Handle the covid sample "created" event.
     *
     * @param  \App\CovidSample  $covidSample
     * @return void
     */
    public function created(CovidSample $covidSample)
    {
        //
    }

    /**
     * Handle the covid sample "updated" event.
     *
     * @param  \App\CovidSample  $covidSample
     * @return void
     */
    public function updated(CovidSample $covidSample)
    {
        //
    }

    /**
     * Handle the covid sample "deleted" event.
     *
     * @param  \App\CovidSample  $covidSample
     * @return void
     */
    public function deleted(CovidSample $covidSample)
    {
        //
    }

    /**
     * Handle the covid sample "restored" event.
     *
     * @param  \App\CovidSample  $covidSample
     * @return void
     */
    public function restored(CovidSample $covidSample)
    {
        //
    }

    /**
     * Handle the covid sample "force deleted" event.
     *
     * @param  \App\CovidSample  $covidSample
     * @return void
     */
    public function forceDeleted(CovidSample $covidSample)
    {
        //
    }
}
