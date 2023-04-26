<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Void_;

class SyncFacilityByUpdateTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $password;
    private $username;
    private $client_id;
    private $secret_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->password=env('KMHFL_PASSWORD');
        $this->username=env('KMHFL_USER');
        $this->client_id=env('KMHFL_CLIENT_ID');
        $this->secret_id=env('KMHFL_CLIENT_SECRET');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $token_response = json_decode($this->generateAccessToken(), true);
        $update_at=$this->getMaxDate();
        $this->getFacilityUpdateAt($token_response['access_token'],$update_at);
    }

    /**
     * Generate access token.
     *
     * @return String token
     */

    private function generateAccessToken()
    {
        /* $this->password=$_ENV['KMHFL_PASSWORD'];
         $this->username=$_ENV['KMHFL_USER'];
         $this->client_id=$_ENV['KMHFL_CLIENT_ID'];
         $this->secret_id=$_ENV['KMHFL_CLIENT_SECRET'];*/

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.kmhfl.health.go.ke/o/token/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=password&username=' . $this->username . '&password=' . $this->password . '&scope=read&client_id=' . $this->client_id . '&client_secret=' . $this->secret_id,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;


    }
    /**
     * Get facility based on updated date and time
     *
     * @return Void
     */

    private function getFacilityUpdateAt($access_token,$updated_at)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.kmhfl.health.go.ke/api/facilities/facilities/?updated_after='.$updated_at.'&format=json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$access_token
            ),
        ));

        $response = curl_exec($curl);


        curl_close($curl);

        $response=json_decode($response, true);
        $total_page=$response['total_pages'];
        $current_page=1;

        while($total_page>=$current_page)
        {
            Log::info($current_page);
            ProcessSyncFacilityByUpdateTimeApi::dispatch($access_token,$current_page,$updated_at);
            $current_page++;
        }




    }

    /**
     * get recent updated date.
     *
     * @return String time
     */

    private function getMaxDate()
    {
        $updated_at=DB::table('facilitys')->max('updated_at');
        $date_time = date('Y-m-d', strtotime($updated_at)).'T'.date('h:i:s', strtotime($updated_at));
        return $date_time;
    }


}