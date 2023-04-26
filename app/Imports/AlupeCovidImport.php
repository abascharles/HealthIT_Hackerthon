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

class AlupeCovidImport implements OnEachRow, WithHeadingRow
{
    private $facility_id;
    private $quarantine_site_id;

    public function __construct($request)
    {
        $this->facility_id = $request->input('facility_id');
        $this->quarantine_site_id = $request->input('quarantine_site_id');
    }
    
    public function onRow(Row $row)
    {
        $row_array = $row->toArray();
        $row = json_decode(json_encode($row->toArray()));

        /*if(!property_exists($row, 'mfl_code')){
            session(['toast_error' => 1, 'toast_message' => 'MFL Code column is not present.']);
            return;
        }*/
        if(!property_exists($row, 'name') && !property_exists($row, 'patient_name')){
            session(['toast_error' => 1, 'toast_message' => 'Patient Name column is not present.']);
            return;
        }
        if(!property_exists($row, 'unique_identifier')){
            session(['toast_error' => 1, 'toast_message' => 'Identifier column is not present.']);
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
        if(!property_exists($row, 'idpassport') && !property_exists($row, 'national_id')){
            session(['toast_error' => 1, 'toast_message' => 'ID/Passport column is not present.']);
            return;
        }

        if((!isset($row->name) && !isset($row->patient_name)) || !$row->unique_identifier || !$row->sex){
            $rows = session('skipped_rows', []);
            $rows[] = $row_array;  
            session(['skipped_rows' => $rows]);          
            return;
        }
        $p = null;

        if(isset($row->idpassport) && strlen($row->idpassport) > 6) $p = CovidPatient::where(['national_id' => ($row->idpassport ?? null)])->whereNotNull('national_id')->where('national_id', '!=', 'No Data')->first();
        if(!$p && $row->unique_identifier && strlen($row->unique_identifier) > 5 && $this->facility_id) $p = CovidPatient::where(['identifier' => $row->unique_identifier, 'facility_id' => $this->facility_id])->first();
        if(!$p && $row->unique_identifier && strlen($row->unique_identifier) > 5 && $this->quarantine_site_id) $p = CovidPatient::where(['identifier' => $row->unique_identifier, 'quarantine_site_id' => $this->quarantine_site_id])->first();

        /*if(!auth()->user()->user_type_id){
            $rows = session('skipped_rows', []);
            $rows[] = $row_array;  
            session(['skipped_rows' => $rows]);          
            return;            
        }*/


        if(!$p) $p = new CovidPatient;


        $p->fill([
            'identifier' => $row->unique_identifier ?? $row->name ?? $row->patient_name ?? '',
            'facility_id' => $this->facility_id ?? null,
            'quarantine_site_id' => $this->quarantine_site_id ?? null,
            'patient_name' => $row->name ?? $row->patient_name,
            'email_address' => $row->email_address ?? null,
            'sex' => $row->sex,
            'national_id' => $row->idpassport ?? $row->national_id ?? null,
            'current_health_status' => $row->health_status ?? null,
            'nationality' => DB::table('nationalities')->where('name', ($row->nationality ?? 'Kenyan'))->first()->id ?? 1,
            'phone_no' => $row->phone_number ?? $row->phone_no ?? null,
            'county' => $row->county ?? null,
            'subcounty' => $row->subcounty ?? null,  
            'residence' => $row->area_of_residence ?? $row->area ?? null,  
            'occupation' => $row->occupation ?? null,    
            'justification' => DB::table('covid_justifications')->where('name', ($row->justification ?? 'none'))->first()->id ?? 3,             
        ]);
        $p->pre_update();

        $datecollected = ($row->date_collected ?? null) ? date('Y-m-d', strtotime($row->date_collected)) : date('Y-m-d');
        $datereceived = ($row->date_received ?? null) ? date('Y-m-d', strtotime($row->date_received)) : date('Y-m-d');

        if($datecollected == '1970-01-01') $datecollected = date('Y-m-d');
        if($datereceived == '1970-01-01') $datereceived = date('Y-m-d');

        $sample = CovidSample::where(['patient_id' => $p->id, 'datecollected' => $datecollected])->first();
        /*if(!$sample && !auth()->user()->user_type_id){
            $sample = CovidSample::where(['patient_id' => $p->id])
            ->whereBetween('datecollected', [
                date('Y-m-d', strtotime($datecollected . ' -6days')), 
                date('Y-m-d', strtotime($datecollected . ' +6days')), 
            ])->first();
        }*/
        if(!$sample) $sample = new CovidSample;

        $test_type = $row->test_type ?? 'initial';
        $test_type = strtolower($test_type);


        $sample->fill([
            'patient_id' => $p->id,
            'lab_id' => env('APP_LAB'),
            'site_entry' => 0,
            'age' => $row->age ?? 0,
            'test_type' => Str::contains($test_type, 'initial') ? 1 : 2,
            'health_status' => $row->health_status ?? null,
            'datecollected' => $datecollected,
            'datereceived' => $datereceived,
            'receivedstatus' => 1,
            'sample_type' => 1,
            'vaccination_status' => $row->vaccination_status ?? 1, 
        ]);
        $sample->pre_update();

        /*if(auth()->user()->user_type_id) $sample->pre_update();
        else{            
            $rows = session('skipped_rows', []);
            $row_array['CASE_ID'] = $p->identifier;
            $row_array['SAMPLE_NUMBER'] = $sample->id;
            $row_array['NATIONAL_ID'] = $p->national_id;
            $rows[] = $row_array;  
            session(['skipped_rows' => $rows]);          
            return;
        }*/

    }
}
