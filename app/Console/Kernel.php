<?php

namespace App\Console;

use App\Jobs\SyncFacilityByUpdateTime;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CreateUpdateFacilityCron::class,

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

         $schedule->command('updateCreateFacility:cron') // Update all facility with invalid info or create
                 ->monthlyOn(1,'0:0'); //to be changed to run on monthly  basis

       /*  $schedule->command('addFacility:cron') //Create new facility based on updated timestamp
          ->daily();  */  //to be run on daily, weekly basis

    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
