<?php


namespace App\utils;


class HttpsRequest
{
    public static function search_client_upi($client_upi)
    {
        $token = json_decode(self::generateAccessToken(), true);
        //dd (self::getRegistryClient($token['access_token'], 'MOH202205001'));
        return self::getRegistryClient($token['access_token'], $client_upi);
    }

    public static function generateAccessToken()
    {
        $grant_type = env('REGISTRY_GRANT_TYPE');
        $scope = env('REGISTRY_SCOPE');
        $client_id = env('REGISTRY_CLIENT_ID');
        $client_secret = env('REGISTRY_CLIENT_SECRET');
        $token_url = env('REGISTRY_CLIENT_TOKEN_URL');

        $curl = curl_init();
        // TODO Check if initialization had gone wrong*
        if ($curl === false) {
            throw new Exception('failed to initialize');
        }
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_URL => $token_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'grant_type=' . $grant_type . '&scope=' . $scope . '&client_id=' . $client_id . '&client_secret=' . $client_secret,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
            ),
        ));


        $response = curl_exec($curl);
        curl_close($curl);

        return $response;

    }

//identifierType and upn value to validate
    public static function
    getRegistryClient($access_token, $client_upi)
    {
        $api_search_upi_url = env('REGISTRY_SEARCH_CLIENT_API');
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_URL => $api_search_upi_url . $client_upi . '?format=json',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $access_token),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);
        if ($response['client']) {
            $u_pat = new \stdClass();
            $u_pat->clientNumber = $response['client']['clientNumber'];
            $u_pat->firstName = $response['client']['firstName'];
            $u_pat->middleName = $response['client']['middleName'];
            $u_pat->lastName = $response['client']['lastName'];
            $u_pat->gender = $response['client']['gender'];
            $u_pat->dateOfBirth = $response['client']['dateOfBirth'];
            $u_pat->maritalStatus = $response['client']['maritalStatus'];
            return ($u_pat) ? $u_pat : "client does not exist on client registry";

        } else {
            //todo  handle erro codes...
            return "client does not exist on client registry";
        }

    }
}