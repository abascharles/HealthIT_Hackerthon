<?php

namespace App\Jobs;

use App\Facility;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SynchNewFacility implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $facility;
    protected $lab;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Facility $facility, $lab)
    {
        $this->facility = $facility;
        $this->lab = $lab;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
