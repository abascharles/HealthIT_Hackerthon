<?php

namespace App\Imports;

use \DB;
use App\PartnerFacilityContact;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PartnerFacilityContactsImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $contcts = [];
        foreach ($collection as $key => $item) {
            if (null !== $item['personal_email']) {
                $county = DB::table('countys')->where('name', $item['county'])->get();

                if ($county->isEmpty()) {                
                    $contcts[] = [];
                } else {
                    $data_to_be_saved = [
                                    'county_id' => $county->first()->id,
                                    'name' => $item['name_of_casco'],
                                    'email' => $item['personal_email'],
                                    'critical_results' => 1
                                ];
                    $partner_contact_i = new PartnerFacilityContact;
                    $partner_contact_i->fill($data_to_be_saved);
                    $partner_contact_i->save();
                    $contcts[] = $partner_contact_i;
                }
            }
        }

        return collect($contcts);
    }
}
