<?php

namespace App\Console\Commands;

use App\Jobs\SyncFacilityByUpdateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AddNewFacilityCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'addFacility:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        SyncFacilityByUpdateTime::dispatch();

    }
}
