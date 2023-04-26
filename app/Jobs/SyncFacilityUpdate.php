<?php

namespace App\Jobs;
use Illuminate\Support\Facades\DB;
use App\Facility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncFacilityUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $password;
    private $username;
    private $client_id;
    private $secret_id;

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
       $token_response= json_decode($this->generateAccessToken(),true);

       $this->getTotalPage($token_response['access_token']);


    }

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
            CURLOPT_POSTFIELDS => 'grant_type=password&username='.$this->username.'&password='.$this->password.'&scope=read&client_id='.$this->client_id.'&client_secret='.$this->secret_id,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;


    }

    private function getTotalPage($access_token)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://api.kmhfl.health.go.ke/api/facilities/facilities/?format=json',
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

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);
        $totalPages = $response['total_pages'];
        $this->updateCreateFacility($totalPages,$access_token);
    }

    private function updateCreateFacility($totalPages,$access_token)
    {
        $currentPage = 1;
        while ($totalPages >= $currentPage)
        {
            ProcessApiRequest::dispatch($access_token,$currentPage);
            $currentPage++;
        }

    }


}
