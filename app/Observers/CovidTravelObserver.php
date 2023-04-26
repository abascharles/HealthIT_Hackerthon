<?php

namespace App\Observers;

use App\CovidTravel;

class CovidTravelObserver
{
    /**
     * Handle the covid travel "created" event.
     *
     * @param  \App\CovidTravel  $covidTravel
     * @return void
     */
    public function created(CovidTravel $covidTravel)
    {
        //
    }

    /**
     * Handle the covid travel "updated" event.
     *
     * @param  \App\CovidTravel  $covidTravel
     * @return void
     */
    public function updated(CovidTravel $covidTravel)
    {
        //
    }

    /**
     * Handle the covid travel "deleted" event.
     *
     * @param  \App\CovidTravel  $covidTravel
     * @return void
     */
    public function deleted(CovidTravel $covidTravel)
    {
        //
    }

    /**
     * Handle the covid travel "restored" event.
     *
     * @param  \App\CovidTravel  $covidTravel
     * @return void
     */
    public function restored(CovidTravel $covidTravel)
    {
        //
    }

    /**
     * Handle the covid travel "force deleted" event.
     *
     * @param  \App\CovidTravel  $covidTravel
     * @return void
     */
    public function forceDeleted(CovidTravel $covidTravel)
    {
        //
    }
}
