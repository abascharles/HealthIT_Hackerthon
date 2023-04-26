<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessApiRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $access_token;
    private $currentPage;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($access_token,$currentPage)
    {
        $this->access_token=$access_token;
        $this->currentPage=$currentPage;
        //
        }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->processApiRequest();
    }

   private function processApiRequest()
   {
       $curl = curl_init();
       curl_setopt_array($curl, array(
           CURLOPT_URL => 'http://api.kmhfl.health.go.ke/api/facilities/facilities/?format=json&page='.$this->currentPage,
           CURLOPT_RETURNTRANSFER => true,
           CURLOPT_ENCODING => '',
           CURLOPT_MAXREDIRS => 10,
           CURLOPT_TIMEOUT => 0,
           CURLOPT_FOLLOWLOCATION => true,
           CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
           CURLOPT_CUSTOMREQUEST => 'GET',
           CURLOPT_HTTPHEADER => array(
               'Authorization: Bearer ' . $this->access_token
           ),
       ));

       $response = json_decode(curl_exec($curl), true);
       curl_close($curl);
       Log::error('current page'.$this->currentPage);
       $results = $response['results'];
       PopulateDatabase::dispatch($results);
       //$this->populateDatabase($results);

   }
}
