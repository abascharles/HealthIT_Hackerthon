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
use DB;

class NairobiCovidImport implements OnEachRow, WithHeadingRow
{
    
    public function onRow(Row $row)
    {
        $row_array = $row->toArray();
        $row = json_decode(json_encode($row->toArray()));

        if(!property_exists($row, 'name')){
            session(['toast_error' => 1, 'toast_message' => 'Patient Name column is not present.']);
            return;
        }
        if(!property_exists($row, 'patient_id')){
            session(['toast_error' => 1, 'toast_message' => 'Patient ID (identifier) column is not present.']);
            return;
        }
        if(!property_exists($row, 'age')){
            session(['toast_error' => 1, 'toast_message' => 'Age column is not present.']);
            return;
        }
        if(!property_exists($row, 'sex')){
            session(['toast_error' => 1, 'toast_message' => 'Sex column is not present.']);
            return;
        }

        if(!$row->name || !$row->patient_id || (!$row->age && $row->age != 0) || !$row->sex) {
            $rows = session('skipped_rows', []);
            $rows[] = $row_array;  
            session(['skipped_rows' => $rows]);          
            return;
        }

        $mfl = (int) ($row->mfl_code ?? 0);
        if(!$row->patient_id) return;

        $fac = Facility::locate($mfl)->first();
        // if(!$fac) return;
        $p = null;

        if(isset($row->national_id) && strlen($row->national_id) > 6) $p = CovidPatient::where(['national_id' => ($row->national_id ?? null)])->whereNotNull('national_id')->where('national_id', '!=', 'No Data')->first();
        if(!$p && $row->patient_id && strlen($row->patient_id) > 5 && $fac) $p = CovidPatient::where(['identifier' => $row->patient_id, 'facility_id' => $fac->id])->first();
        if(!$p && $row->patient_id && strlen($row->patient_id && isset($row->quarantine_site_id)) > 5) $p = CovidPatient::where(['identifier' => $row->patient_id, 'quarantine_site_id' => $row->quarantine_site_id])->first();


        if(!$p) $p = new CovidPatient;

        $p->fill([
            'identifier' => $row->patient_id ?? $row->name,
            'facility_id' => $fac->id ?? null,
            'quarantine_site_id' => $row->quarantine_site_id ?? 40,
            'email_address' => $row->email_address ?? null,
            'patient_name' => $row->name,
            'sex' => $row->sex,
            'national_id' => $row->national_id ?? null,
            'nationality' => DB::table('nationalities')->where('name', ($row->nationality ?? ''))->first()->id ?? 1,
            'phone_no' => $row->telephone_number ?? null,
            'county' => $row->county_of_residence ?? null,
            'subcounty' => $row->sub_county ?? null,  
            'residence' => $row->residence ?? null,  
            'occupation' => $row->occupation ?? null,    
            'justification' => $row->justification ?? 3,             
        ]);
        $p->save();

        $datecollected = ($row->date_collected ?? null) ? date('Y-m-d', strtotime($row->date_collected)) : date('Y-m-d');
        $timecollected = ($row->time_collected ?? null) ? date('Y-m-d h:i:s',strtotime($row->time_collected)): date('Y-m-d h:i:s');
        $datereceived = ($row->date_received ?? null) ? date('Y-m-d', strtotime($row->date_received)) : date('Y-m-d');
        $timereceived = ($row->time_received ?? null) ? date('Y-m-d h:i:s',strtotime($row->time_received)): date('Y-m-d h:i:s');
        $datetested = ($row->date_tested ?? null) ? date('Y-m-d', strtotime($row->date_tested)) : date('Y-m-d');
        $timetested = ($row->time_tested ?? null) ? date('Y-m-d h:i:s',strtotime($row->time_tested)): date('Y-m-d h:i:s');

        if($datecollected == '1970-01-01' || date('Y', strtotime($datecollected)) > date('Y')) $datecollected = date('Y-m-d');
        if($datereceived == '1970-01-01' || date('Y', strtotime($datereceived)) > date('Y')) $datereceived = date('Y-m-d');
        if($datetested == '1970-01-01' || date('Y', strtotime($datetested)) > date('Y')) $datetested = date('Y-m-d');

        $sample = CovidSample::where(['patient_id' => $p->id, 'datecollected' => $datecollected])->first();
        if(!$sample) $sample = new CovidSample;

        $sample->fill([
            'patient_id' => $p->id,
            'lab_id' => env('APP_LAB'),
            'site_entry' => 0,
            'age' => $row->age,
            'test_type' => 1,
            'datecollected' => $datecollected,
            'timecollected' =>$timecollected,
            'datereceived' => $datereceived,
            'timereceived' =>$timereceived,
            'datetested' =>$datetested,
            'timetested' =>$timetested,
            'receivedstatus' => 1,
            'sample_type' => 1,
            'vaccination_status' => $row->vaccination_status ?? 1, 
        ]);
        $sample->pre_update();

    }
}
