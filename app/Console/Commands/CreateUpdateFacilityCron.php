<?php

namespace App\Console\Commands;

use App\Facility;
use App\Jobs\SyncFacilityByUpdateTime;
use App\Jobs\SyncFacilityUpdate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateUpdateFacilityCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateCreateFacility:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run daily facility update';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info("Running");
        SyncFacilityUpdate::dispatch();
        SyncFacilityByUpdateTime::dispatch();
    }



}
