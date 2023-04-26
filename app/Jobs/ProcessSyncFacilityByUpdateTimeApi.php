<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class ProcessSyncFacilityByUpdateTimeApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $access_token;
    private $current_page;
    private $updated_time;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($access_token,$current_page,$update_time)
    {
        $this->access_token=$access_token;
        $this->current_page=$current_page;
        $this->updated_time=$update_time;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->getFacility();
        //
    }


    /**
 * Get facility from KMHFL API.
 * @return void
 */
    private function getFacility()
     {
         $curl = curl_init();

         curl_setopt_array($curl, array(
             CURLOPT_URL => 'http://api.kmhfl.health.go.ke/api/facilities/facilities/?format=json&page='.$this->current_page.'&updated_after='.$this->updated_time,
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_ENCODING => '',
             CURLOPT_MAXREDIRS => 10,
             CURLOPT_TIMEOUT => 0,
             CURLOPT_FOLLOWLOCATION => true,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => 'GET',
             CURLOPT_HTTPHEADER => array(
                 'Authorization: Bearer '.$this->access_token
             ),
         ));

         $response = curl_exec($curl);

         curl_close($curl);
         try
         {
             $response=json_decode($response, true);
             $results=$response['results'];
             PopulateDatabase::dispatch($results);
             Log::info("Success");

         }
         catch (\Exception $e)
         {
             Log::error($e->getMessage());

         }
    //

     }

}
