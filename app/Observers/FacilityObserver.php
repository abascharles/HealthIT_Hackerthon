<?php

namespace App\Observers;

use GuzzleHttp\Client;
use \App\Facility;
use \App\FacilityContact;
use \App\Synch;

class FacilityObserver
{

    public function created(Facility $facility)
    {
        $user = \App\User::create([
                'user_type_id' => 5,
                'surname' => '',
                'oname' => '',
                'lab_id' => env('APP_LAB'),
                'facility_id' => $facility->id,
                'email' => 'facility' . $facility->id . '@nascop-lab.com',
                'password' => encrypt($facility->name)
            ]);

        $contact_array = ['telephone', 'telephone2', 'fax', 'email', 'PostalAddress', 'contactperson', 'contacttelephone', 'contacttelephone2', 'physicaladdress', 'G4Sbranchname', 'G4Slocation', 'G4Sphone1', 'G4Sphone2', 'G4Sphone3', 'G4Sfax', 'ContactEmail'];

        $contact = new FacilityContact();
        $contact->fill($facility->only($contact_array));
        $contact->facility_id = $facility->id;
        $contact->save();
    }

    public function updated(Facility $facility)
    {
        $contact_array = ['telephone', 'telephone2', 'fax', 'email', 'PostalAddress', 'contactperson', 'contacttelephone', 'contacttelephone2', 'physicaladdress', 'G4Sbranchname', 'G4Slocation', 'G4Sphone1', 'G4Sphone2', 'G4Sphone3', 'G4Sfax', 'ContactEmail'];

        $fac = $facility::find($facility->id);

        $contact = $facility->facility_contact;
        if(!$contact){
            $contact = new FacilityContact;
            $contact->facility_id = $facility->id;            
        }

        foreach ($contact_array as $key => $value) {
            // if($facility->$value != $facility->getOriginal($value)){
                // $contact->fill($facility->only($contact_array));
                // $contact->save();                
            // }

            $contact->$value = $fac->getOriginal($value);
        }
        $contact->save();
    }

    /*public function updated(Facility $facility)
    {
        $base = Synch::$base;
        $client = new Client(['base_uri' => $base]);
        $today = date('Y-m-d');
        $url = 'facility/' . $facility->id;

        $response = $client->request('put', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . Synch::get_token(),
            ],
            'form_params' => [
                'facility' => $facility->toJson(),
                'lab_id' => env('APP_LAB', null),
            ],
        ]);

        $body = json_decode($response->getBody());

        $update_data = ['synched' => 1, 'datesynched' => $today,];
        $facility->fill($update_data);
        $facility->save();
    }*/

}