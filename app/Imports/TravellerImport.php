<?php

namespace App\Imports;

use Str;
use \App\Traveller;

use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TravellerImport implements OnEachRow, WithHeadingRow
{

    public function onRow(Row $row)
    {
        $row_array = $row->toArray();
        $row = json_decode(json_encode($row->toArray()));


        if(!property_exists($row, 'idpassport')){
            session(['toast_error' => 1, 'toast_message' => 'ID/PASSPORT column is not present.']);
            return;
        }
        if(!property_exists($row, 'name_3_names')){
            session(['toast_error' => 1, 'toast_message' => 'NAME (3 NAMES) column is not present.']);
            return;
        }
        if(!property_exists($row, 'gen')){
            session(['toast_error' => 1, 'toast_message' => 'GEN column is not present.']);
            return;
        }
        if(!property_exists($row, 'agein_years')){
            session(['toast_error' => 1, 'toast_message' => 'AGE(in Years) column is not present.']);
            return;
        }
        if(!property_exists($row, 'mobile_no')){
            session(['toast_error' => 1, 'toast_message' => 'MOBILE NO column is not present.']);
            return;
        }
        if(!property_exists($row, 'citizenship')){
            session(['toast_error' => 1, 'toast_message' => 'CITIZENSHIP column is not present.']);
            return;
        }
        if(!property_exists($row, 'pcr_result')){
            session(['toast_error' => 1, 'toast_message' => 'PCR Result column is not present.']);
            return;
        }
        if(!property_exists($row, 'igm_test_result')){
            session(['toast_error' => 1, 'toast_message' => 'IgM Test result column is not present.']);
            return;
        }
        if(!property_exists($row, 'igm_index_test_result')){
            session(['toast_error' => 1, 'toast_message' => 'IgM Index Test Result column is not present.']);
            return;
        }
        if(!property_exists($row, 'iggigm_result')){
            session(['toast_error' => 1, 'toast_message' => 'IgG/IgM Result column is not present.']);
            return;
        }
        if(!property_exists($row, 'antigen_result')){
            session(['toast_error' => 1, 'toast_message' => 'Antigen Result column is not present.']);
            return;
        }

        if(!$row->name_3_names) return;
        

        $datecollected = ($row->date_collected ?? null) ? date('Y-m-d H:i:s', strtotime($row->date_collected)) : date('Y-m-d H:i:s');
        $datereceived = ($row->date_received ?? null) ? date('Y-m-d H:i:s', strtotime($row->date_received)) : date('Y-m-d H:i:s');
        $datetested = ($row->date_tested ?? null) ? date('Y-m-d H:i:s', strtotime($row->date_tested)) : date('Y-m-d H:i:s');
        $datedispatched = ($row->date_dispatched ?? null) ? date('Y-m-d H:i:s', strtotime($row->date_dispatched)) : date('Y-m-d H:i:s');
        $dob = ($row->dob ?? null) ? date('Y-m-d H:i:s', strtotime($row->dob)) : null;

        if($datecollected == '1970-01-01') $datecollected = date('Y-m-d H:i:s');
        if($datereceived == '1970-01-01') $datereceived = date('Y-m-d H:i:s');
        if($datetested == '1970-01-01') $datetested = date('Y-m-d H:i:s');
        if($datedispatched == '1970-01-01') $datedispatched = date('Y-m-d H:i:s');
        if($dob == '1970-01-01') $dob = null;
        
        $t = new Traveller;
        $t->fill([
        	'id_passport' => $row->idpassport,
        	'patient_name' => $row->name_3_names,
        	'marriage_status' => $row->status ?? null,
        	'age' => $row->agein_years,
            'dob' => $row->dob,
        	'phone_no' => $row->mobile_no,
        	'citizenship' => $row->citizenship ?? null,
        	'county' => $row->county ?? null,
        	'residence' => $row->estate ?? null,
        	'sex' => $row->gen,

        	'result' => $row->pcr_result,
        	'igm_result' => $row->igm_test_result,
            'igm_index_result' => $row->igm_index_test_result,
        	'igg_igm_result' => $row->iggigm_result,
            'antigen_result' => $row->antigen_result,

        	'datecollected' => $datecollected,
        	'datereceived' => $datereceived,
        	'datetested' => $datetested,
        	'datedispatched' => $datedispatched,
        ]);
        /*$t->result = $row->pcr_result;
        $t->igm_result = $row->igm_test_result;
        $t->igg_igm_result = $row->iggigm_result;
        $t->antigen_result = $row->antigen_result;*/
        $t->save();

    }
}
