<?php

namespace App\Imports;

use DB;
use Str;
use \App\Facility;
use \App\QuarantineSite;
use \App\CovidPatient;
use \App\CovidSample;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KisumuCovidImport implements OnEachRow, WithHeadingRow
{
    
    public function onRow(Row $row)
    {
        $row_array = $row->toArray();
        $row = json_decode(json_encode($row->toArray()));

        if(!property_exists($row, 'patient_name')){
            session(['toast_error' => 1, 'toast_message' => 'Patient Name column is not present.']);
            return;
        }
        if(!property_exists($row, 'identifier')){
            session(['toast_error' => 1, 'toast_message' => 'Identifier column is not present.']);
            return;
        }
        if(!property_exists($row, 'age')){
            session(['toast_error' => 1, 'toast_message' => 'Age column is not present.']);
            return;
        }
        if(!property_exists($row, 'gender')){
            session(['toast_error' => 1, 'toast_message' => 'Gender column is not present.']);
            return;
        }

        if(!$row->patient_name || !$row->identifier || !((int) $row->age) || !$row->gender){
            $rows = session('skipped_rows', []);
            $rows[] = $row_array;  
            session(['skipped_rows' => $rows]);          
            return;
        }


        $p = null;

        if(isset($row->national_id) && strlen($row->national_id) > 6) $p = CovidPatient::where(['national_id' => ($row->national_id ?? null)])->whereNotNull('national_id')->first();
        if(strlen($row->identifier) > 6) $p = CovidPatient::where(['identifier' => $row->identifier])->first();


        if(!$p) $p = new CovidPatient;
        $mfl = (int) ($row->mfl_code ?? null);
        $fac = Facility::locate($mfl)->first();

        $date_symptoms = ($row->date_symptoms ?? null) ? date('Y-m-d', strtotime($row->date_symptoms)) : date('Y-m-d');
        if($date_symptoms == '1970-01-01') $date_symptoms = null;

        $p->fill([
            'identifier' => $row->identifier ?? $row->national_id ?? $row->patient_name,
            'facility_id' => $fac->id ?? null,
            'quarantine_site_id' => $row->quarantine_site_id ?? null,
            'patient_name' => $row->patient_name,
            'sex' => $row->gender,
            'national_id' => $row->national_id ?? null,
            'current_health_status' => $row->health_status ?? null,
            'nationality' => DB::table('nationalities')->where('name', ($row->nationality ?? 'Kenyan'))->first()->id ?? 1,
            'phone_no' => $row->phone_number ?? null,
            'county' => $row->county ?? null,
            'subcounty' => $row->subcounty ?? null,  
            'residence' => $row->residence ?? null,  
            'occupation' => $row->occupation ?? null,    
            'justification' => $row->justification ?? 3,  
            'date_symptoms' => $date_symptoms,     
            'email_address' => $row->email_address ?? null,         
        ]);
        $p->save();

        $datecollected = ($row->date_collected ?? null) ? date('Y-m-d', strtotime($row->date_collected)) : date('Y-m-d');
        $datereceived = ($row->date_received ?? null) ? date('Y-m-d', strtotime($row->date_received)) : date('Y-m-d');

        if($datecollected == '1970-01-01') $datecollected = date('Y-m-d');
        if($datereceived == '1970-01-01') $datereceived = date('Y-m-d');

        $sample = CovidSample::where(['patient_id' => $p->id, 'datecollected' => $datecollected])->first();
        if(!$sample) $sample = new CovidSample;

        $sample->fill([
            'patient_id' => $p->id,
            'lab_id' => env('APP_LAB'),
            'site_entry' => 0,
            'age' => (int) $row->age,
            'test_type' => $row->test_type ?? 1,
            'health_status' => $row->health_status ?? null,
            'datecollected' => $datecollected,
            'datereceived' => $datereceived,
            'receivedstatus' => 1,
            'sample_type' => 1,
            'vaccination_status' => $row->vaccination_status ?? 1, 
        ]);
        if(isset($row->repeat) && $row->repeat) $sample->test_type = 2;
        if(isset($row->symptoms) && strlen($row->symptoms) > 1) $sample->symptoms = explode(',', $row->symptoms);
        $sample->pre_update();

    }
}
