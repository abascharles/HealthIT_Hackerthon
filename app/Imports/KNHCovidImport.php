<?php

namespace App\Imports;

use Str;
use \App\Facility;
use \App\QuarantineSite;
use \App\CovidPatient;
use \App\CovidSample;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KNHCovidImport implements OnEachRow, WithHeadingRow
{
    
    public function onRow(Row $row)
    {
        $row = json_decode(json_encode($row->toArray()));

        $mfl = (int) $row->mfl_code;

        $fac = Facility::locate($mfl)->first();
        if(!$fac) return;
        $p = null;

        if($row->national_id) $p = CovidPatient::where(['national_id' => ($row->national_id ?? null)])->whereNotNull('national_id')->where('national_id', '!=', 'No Data')->first();
        if(!$p) $p = CovidPatient::where(['identifier' => $row->identifier, 'facility_id' => $fac->id])->first();


        if(!$p) $p = new CovidPatient;

        $p->fill([
            'identifier' => $row->identifier,
            'facility_id' => $fac->id ?? null,
            'patient_name' => $row->patient_name,
            'sex' => $row->gender,
            'national_id' => $row->national_id ?? null,
            'nationality' => 1,
            'phone_no' => $row->phone_number ?? null,
            'county' => $row->county_of_residence ?? null,
            'subcounty' => $row->subcounty_of_residence ?? null,   
            'residence' => $row->village_estate ?? null,   
            'occupation' => $row->occupation ?? null,   
            'justification' => 3,             
        ]);
        $p->save();

        $datecollected = $row->date_collected ?? date('Y-m-d');

        $test_type = $row->type_of_case ?? null;
        $test_type = strtolower($test_type);

        $sample = CovidSample::where(['patient_id' => $p->id, 'datecollected' => $datecollected])->first();
        if(!$sample) $sample = new CovidSample;

        $sample->fill([
            'patient_id' => $p->id,
            'lab_id' => env('APP_LAB'),
            'site_entry' => 1,
            'age' => $row->age,
            'test_type' => Str::contains($test_type, 'rep') ? 2 : 1,
            'datecollected' => $row->date_collected ?? date('Y-m-d'),
            'datereceived' => $row->date_received ?? date('Y-m-d'),
            'receivedstatus' => 1,
            'sample_type' => 1,
        ]);
        $sample->pre_update();

    }
}
