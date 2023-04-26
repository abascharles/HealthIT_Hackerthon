<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use Str;
use \App\Facility;
use \App\QuarantineSite;
use \App\CovidPatient;
use \App\CovidSample;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class KemriWRPImport implements OnEachRow, WithHeadingRow, WithChunkReading
{

    
    public function onRow(Row $row)
    {
        $row_array = $row->toArray();
        $row = json_decode(json_encode($row->toArray()));
        $column = 'quarantine_site_id';
        if($row->facility_name > 1000) $column = 'facility_id';

        $p = null;

        if(isset($row->id_passport)) $p = CovidPatient::where(['national_id' => $row->id_passport])->whereNotNull('national_id')->where('national_id', '!=', 'No Data')->first();
        if(!$p) $p = CovidPatient::where(['identifier' => $row->identifier])->first();

        $mfl = $row->mfl ?? null;

        $fac = Facility::locate($mfl)->first();
        $quarantine_site = QuarantineSite::where('name', $row->facility_name)->first();

        if(!$p) $p = new CovidPatient;

        $justification = strtolower(($row->justification ?? ''));

        if(Str::contains($justification, 'truck')) $j = 10;
        else if(Str::contains($justification, 'food')) $j = 11;
        else if(Str::contains($justification, 'health')) $j = 9;
        else if(Str::contains($justification, 'surveillance')) $j = 3;
        // else if(Str::contains($justification, 'surveillance')) $j = 9;
        else{
            $j = 3;
        }



        if(!$row->identifier ){
            $rows = session('skipped_rows', []);
            $rows[] = $row_array;  
            session(['skipped_rows' => $rows]);          
            return;
        }

        $p->fill([
            'identifier' => $row->identifier,
            'facility_id' => $fac->id ?? null,
            'quarantine_site_id' => $quarantine_site->id ?? $row->quarantine_site_id ?? null,
            'patient_name' => $row->full_name,
            'sex' => $row->gender,
            'national_id' => $row->id_passport,
            'phone_no' => $row->mobile_phone_no,
            'county' => $row->county_rep,
            'subcounty' => $row->sub_county_rep ?? null,   
            'justification' => $j,             
        ]);
        $p->save();

        $sample_type = $row->specimen_source ?? null;
        if(Str::contains($sample_type, 'Oro') && Str::contains($sample_type, 'Naso')) $s = 1;
        else if(Str::contains($sample_type, 'Oro')) $s = 3;
        else if(Str::contains($sample_type, 'Naso')) $s = 2;
        else{
            $s = 1;
        }

        $datecollected = ($row->date_collected ?? null) ? date('Y-m-d', strtotime($row->date_collected)) : date('Y-m-d');
        $datereceived = ($row->date_received ?? null) ? date('Y-m-d', strtotime($row->date_received)) : date('Y-m-d');
        $datetested = ($row->date_tested ?? null) ? date('Y-m-d', strtotime($row->date_tested)) : date('Y-m-d');

        if($datecollected == '1970-01-01') $datecollected = date('Y-m-d');
        if($datereceived == '1970-01-01') $datereceived = date('Y-m-d');
        if($datetested == '1970-01-01') $datetested = date('Y-m-d');

        $sample = CovidSample::where(['patient_id' => $p->id, 'datecollected' => $datecollected])->first();
        if(!$sample) $sample = new CovidSample;

        $sample->fill([
            'patient_id' => $p->id,
            'lab_id' => auth()->user()->lab_id,
            'kemri_id' => $row->kemri_id ?? null,
            'site_entry' => 0,
            'age' => $row->age,
            'test_type' => 1,
            'datecollected' => $datecollected,
            'datereceived' => $datereceived,
            'datetested' => $datetested,
            'datedispatched' => $datetested,
            'dateapproved' => $datetested,
            'receivedstatus' => 1,
            'sample_type' => $s,
            'result' => $row->preliminary_lab_results,
        ]);
        if(isset($row->rejected_reason) && $row->rejected_reason){
            $sample->datetested = null;
            $sample->receivedstatus = 2;
            $sample->rejectedreason = $row->rejected_reason;
        }
        $sample->pre_update();

    }

    public function chunkSize(): int
    {
        return 50;
    }
}
