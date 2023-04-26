<?php

namespace App\Observers;

use App\DrSample;
use DB;

class DrSampleObserver
{
    /**
     * Handle the dr sample "created" event.
     *
     * @param  \App\DrSample  $drSample
     * @return void
     */
    public function creating(DrSample $drSample)
    {
        if(!$drSample->lab_id) $drSample->lab_id = auth()->user()->lab_id;
        if(!$drSample->user_id) $drSample->user_id = auth()->user()->id;

        if($drSample->age)
            $drSample->age_category = \DB::table('age_categories')
                ->where('lower', '<=', $drSample->age)
                ->where('upper', '>=', $drSample->age)
                ->first()->id;

        if(!$drSample->age)
            $drSample->age_category = \DB::table('age_categories')->whereNull('lower')->first()->id;
    }

    /**
     * Handle the dr sample "updated" event.
     *
     * @param  \App\DrSample  $drSample
     * @return void
     */
    public function updated(DrSample $drSample)
    {
        //
    }

    /**
     * Handle the dr sample "deleted" event.
     *
     * @param  \App\DrSample  $drSample
     * @return void
     */
    public function deleted(DrSample $drSample)
    {
        //
    }

    /**
     * Handle the dr sample "restored" event.
     *
     * @param  \App\DrSample  $drSample
     * @return void
     */
    public function restored(DrSample $drSample)
    {
        //
    }

    /**
     * Handle the dr sample "force deleted" event.
     *
     * @param  \App\DrSample  $drSample
     * @return void
     */
    public function forceDeleted(DrSample $drSample)
    {
        //
    }
}
