<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use DB;
use Exception;

use Carbon\Carbon;

class Lookup
{

    public static $double_approval = [2, 4, 5, 25];
    public static $amrs = [3, 5];
    // public static $worksheet_received = [1, 3];
    public static $worksheet_received = [1];
    public static $sms = [1, 2, 3, 4, 5, 6, 7, 8, 9 ];

    public static $api_data = ['s.id', 's.order_no', 'p.patient', 's.provider_identifier', 'f.facilitycode', 's.amrs_location', 'p.patient_name', 's.datecollected', 'b.datereceived', 's.datetested', 's.interpretation', 's.result', 'b.datedispatched', 'b.batch_complete', 's.receivedstatus', 's.approvedby', 's.repeatt'];



    public static function my_date_format($value)
    {
        if($value) return date('d-M-Y', strtotime($value));
        return '';
    }

    public static function retrieve_val($key, $id, $attr='name')
    {
        self::cacher();
        $collection = Cache::get($key);
        if(!$collection) return null;
        return $collection->where('id', $id)->first()->$attr ?? null;
    }

    public static function date_range_month($year, $month=null)
    {
        if(!$month) return ["{$year}-01-01", "{$year}-12-31"];
        if($month < 10) $month = '0' . $month;


        $date_range[0] = ($year) . '-' . $month . '-01';

        $d = Carbon::createFromFormat('Y-m-d', $date_range[0]);
        $d->addMonth();
        $d->subDay();

        $date_range[1] = $d->toDateString();
        return $date_range;     
    }

    public static function get_date_range($year, $month, $year2, $month2)
    {
        $d1 = self::date_range_month($year, $month);
        $d2 = self::date_range_month($year2, $month2);
        return [$d1[0], $d2[1]];
    }

    public static function other_date($value)
    {
        if(!$value) return null;

        try {
            $d = date('Y-m-d', strtotime($value));
            if($d != '1970-01-01') return $d;
        } catch (Exception $e) {
            
        }

        if(env('APP_LAB') == 1){

            try {
                $d = Carbon::createFromFormat('m/d/Y', $value);
                return $d->toDateString();
            } catch (Exception $e) {
                try {
                    $d = Carbon::createFromFormat('m/d/y', $value);
                    return $d->toDateString();                
                } catch (Exception $ee) {
                    return null;
                }
                return null;
            }

        }

        try {
            $d = Carbon::createFromFormat('d/m/Y', $value);
            return $d->toDateString();            
        } catch (Exception $e) {
            
        }  

        return null;      
    }

    public static function normal_date($value)
    {
        if(!$value) return null;

        try {
            $d = Carbon::parse($value);
            return $d->toDateString();
        } catch (Exception $e) {
            try {
                $d = Carbon::createFromFormat('m/d/y', $value);
                return $d->toDateString();                
            } catch (Exception $ee) {
                return null;
            }
            return null;
        }        
    }

    public static function get_gender($value)
    {
        $value = trim($value);
        $value = strtolower($value);
        if(\Str::contains($value, ['f', '2'])){
            return 2;
        }
        else if(\Str::contains($value, ['m', '1'])){
            return 1;
        }
        else{
            return 3;
        }
    }

    public static function get_site_entry($value)
    {
        $value = trim($value);
        $value = strtolower($value);
        if(\Str::contains($value, ['n', '1'])){
            return 0;
        }
        else{
            return 1;
        }
    }


    public static function calculate_dob($datecollected, $years, $months=0)
    {
        if(!is_numeric($years) && !$months) return null;
        if(!$years && !$months) return null;
        try {           
            $dc = Carbon::createFromFormat('Y-m-d', $datecollected);
            $dc->subYears($years);
            $dc->subMonths($months);
            return $dc->toDateString();
            
        } catch (Exception $e) {
            return null;
        }
        return null;
    }

    public static function get_api()
    {
        self::cacher();
        return [
            'genders' => Cache::get('genders'),
            'amrs_locations' => Cache::get('amrs_locations'),
            'results' => Cache::get('results'),
            'received_statuses' => Cache::get('received_statuses'),
        ];
    }

    public static function get_rejected_reason($test, $rejectedreason)
    {
        self::cacher();

        if($test == 1) $reasons = Cache::get('rejected_reasons');
        else if($test == 2) $reasons = Cache::get('viral_rejected_reasons');
        else if($test == 3) $reasons = Cache::get('cd4_rejected_reasons');
        else {
            return '';
        }

        return $reasons->where('id', $rejectedreason)->first()->name ?? 'Unknown';
    }

    public static function facility_mfl($mfl)
    {
        // self::cacher(); 
        // $fac = Cache::get('facilities');       
        // return $fac->where('facilitycode', $mfl)->first()->id;

        return \App\Facility::locate($mfl)->first()->id ?? null;
    }

    public static function get_partners()
    {
        self::cacher();
        return [
            'partners' => Cache::get('partners'),
            'subcounties' => Cache::get('subcounties'),
        ];
    }

    public static function get_machines()
    {
        self::cacher();
        return Cache::get('machines');
    }

    public static function get_facilities()
    {
        self::cacher();
        return Cache::get('facilities');
    }

    public static function get_result($res)
    {
        self::cacher();
        $results = Cache::get('results');

        return $results->where('id', $res)->first()->name ?? '';
    }

    public static function get_cd4_status($id)
    {
        self::cacher();
        $statuses = Cache::get('cd4sample_statuses');

        return $statuses->where('id', $id)->first()->name ?? '';
    }


    public static function get_mrslocation($val)
    {
        self::cacher();       
        $my_array = Cache::get('amrs_locations');       
        return $my_array->where('identifier', $val)->first()->id ?? $val;
    }

    public static function get_mrslocation_reverse($val)
    {
        self::cacher();       
        $my_array = Cache::get('amrs_locations');       
        return $my_array->where('id', $val)->first()->identifier ?? $val;
    }

    public static function worksheet_lookups()
    {
        self::cacher();
        return [
            'machines' => Cache::get('machines'),
            'worksheet_statuses' => Cache::get('worksheet_statuses'),
            'worksheet_sampletypes' => Cache::get('worksheet_sampletypes'),
            'double_approval' => self::$double_approval,
        ];
    }

    public static function worksheet_approve_lookups()
    {
        self::cacher();
        return [
            'machines' => Cache::get('machines'),
            'worksheet_statuses' => Cache::get('worksheet_statuses'),
            'actions' => Cache::get('actions'),
            'worksheet_sampletypes' => Cache::get('worksheet_sampletypes'),
            'dilutions' => Cache::get('dilutions'),
            'results' => Cache::get('results'),
            'double_approval' => self::$double_approval,
        ];
    }

    public static function get_lookups()
    {
        self::cacher();
        return [
            'rejected_reasons' => Cache::get('rejected_reasons'),
            'genders' => Cache::get('genders'),
            'feedings' => Cache::get('feedings'),
            'iprophylaxis' => Cache::get('iprophylaxis'),
            'interventions' => Cache::get('interventions'),
            'entry_points' => Cache::get('entry_points'),
            'results' => Cache::get('results'),
            'received_statuses' => Cache::get('received_statuses'),
            'pcrtypes' => Cache::get('pcr_types'),
            'amrs_locations' => Cache::get('amrs_locations'),
            'sample_types' => Cache::get('sample_types'),
        ];
    }

    public static function cancer_lookups()
    {
        self::cacher();
        return [
            'genders' => Cache::get('genders'),
            'facility_name' => session('viral_facility_name', 0),
            'sampletypes' => Cache::get('cancer_sample_types'),
            'justifications' => Cache::get('cancerjustifications'),
            'hivstatuses' => Cache::get('cancerhivstatuses'),
            'rejectedreasons' => Cache::get('cancerrejectedreasons'),
            'results' => Cache::get('results'),
            'actions' => Cache::get('canceractions'),
            'receivedstatuses' => Cache::get('received_statuses'),
        ];
    }

    public static function cd4_lookups()
    {
        self::cacher();
        return [
                'rejected_reasons' => Cache::get('cd4rejected_reasons'),
                'sample_statuses' => Cache::get('cd4sample_statuses'),
                'received_statuses' => Cache::get('received_statuses'),
            ];
    }

	public static function samples_form()
	{
        self::cacher();
        return [
            // 'facilities' => DB::table('facilitys')->select('id', 'name')->get(),
            // 'facilities' => Cache::get('facilities'),
            'amrs_locations' => Cache::get('amrs_locations'),
            'rejectedreasons' => Cache::get('rejected_reasons'),
            'genders' => Cache::get('genders'),
            'feedings' => Cache::get('feedings'),
            'iprophylaxis' => Cache::get('iprophylaxis'),
            'interventions' => Cache::get('interventions'),
            'entry_points' => Cache::get('entry_points'),
            'hiv_statuses' => Cache::get('hiv_statuses'),
            'pcrtypes' => Cache::get('pcr_types'),
            'receivedstatuses' => Cache::get('received_statuses'),

            'languages' => Cache::get('languages'),

            'batch' => session('batch'),
            'facility_name' => session('facility_name', 0),
            'amrs' => self::$amrs,
            'sms' => self::$sms,
        ];
	}

    public static function cd4sample_form()
    {
        self::cacher();

        return [
                'rejectedreasons' => Cache::get('cd4rejected_reasons'),
                'receivedstatuses' => Cache::get('received_statuses'),
                'amrs_locations' => Cache::get('amrs_locations'),
                'genders' => Cache::get('genders'),
                'samplestatus' => Cache::get('cd4sample_statuses')
            ];
    }

    public static function calculate_age($date_collected, $dob)
    {
        // $patient_age = $request->input('sample_months') + ( $request->input('sample_weeks') / 4 );
        // $dt = Carbon::today();
        // $dt->subMonths($request->input('sample_months'));
        // $dt->subWeeks($request->input('sample_weeks'));
        // $patient->dob = $dt->toDateString();

        // $dc = Carbon::createFromFormat('Y-m-d', $request->input('datecollected'));
        $dob = Carbon::parse( $dob );
        $dc = Carbon::parse( $date_collected );
        if($dob->greaterThan($dc)) return 0;
        $months = $dc->diffInMonths($dob);
        $weeks = $dc->diffInWeeks($dob->copy()->addMonths($months));
        $total = $months + ($weeks / 4);
        if($total == 0) $total = 0.1;
        return $total;
    }

    public static function calculate_mother_dob($date_collected, $age = null)
    {
        if(!$age) return null;
        $dc = Carbon::parse( $date_collected );
        $dc->subYears($age);
        return $dc->toDateString();
    } 


    public static function eid_regimen($val)
    {
        self::cacher();       
        $my_array = Cache::get('iprophylaxis');       
        return $my_array->where('rank_id', $val)->first()->id ?? 14;
    } 


    public static function eid_intervention($val)
    {
        self::cacher();       
        $my_array = Cache::get('interventions');   
        if(is_numeric($val)) return $my_array->where('rank_id', $val)->first()->id ?? 7;   
        return $my_array->where('alias', $val)->first()->id ?? 7;
    } 

    public static function eid_feeding($val)
    {
        self::cacher();       
        $my_array = Cache::get('feedings');   
        if(is_numeric($val)) return $val;   
        return $my_array->where('feeding', $val)->first()->id ?? 5;
    } 

    public static function samples_arrays()
    {
        return [
            'batch' => ['datereceived', 'datedispatchedfromfacility', 'highpriority', 'facility_id', 'lab_id', 'site_entry', 'entered_by'],

            'mother' => ['hiv_status', 'facility_id', 'ccc_no', 'mother_dob'],

            'patient' => ['sex', 'patient_name', 'facility_id', 'patient_phone_no', 'preferred_language', 'patient', 'dob', 'entry_point', 'enrollment_ccc_no', 'patient_status'],

            'sample' => ['comments', 'labcomment', 'datecollected', 'spots', 'patient_id', 'rejectedreason', 'receivedstatus', 'mother_prophylaxis', 'mother_age', 'mother_last_result', 'feeding', 'regimen', 'redraw', 'pcrtype', 'provider_identifier', 'amrs_location', 'sample_type', 'order_no'],

            'sample_rerun' => ['comments', 'labcomment', 'datecollected', 'spots', 'patient_id', 'rejectedreason', 'receivedstatus', 'mother_prophylaxis', 'mother_age', 'mother_last_result', 'feeding', 'regimen', 'redraw', 'pcrtype', 'provider_identifier', 'amrs_location', 'sample_type', 'order_no', 'batch_id', 'patient_id', 'run', 'parentid', 'age'],

            'sample_except' => ['_token', 'patient_name', 'submit_type', 'facility_id', 'sex', 'sample_months', 'sample_weeks', 'entry_point', 'caregiver_phone', 'hiv_status', 'patient', 'new_patient', 'datereceived', 'datedispatchedfromfacility', 'dob', 'ccc_no', 'highpriority'],

            'sample_api' => ['comments', 'labcomment', 'datecollected', 'spots', 'patient_id', 'rejectedreason', 'receivedstatus', 'mother_prophylaxis', 'mother_age', 'mother_last_result', 'feeding', 'regimen', 'redraw', 'pcrtype', 'provider_identifier', 'amrs_location', 'sample_type', 'order_no', 'datetested', 'result'],

            'hpv_sample_rerun' => ['comments', 'labcomment', 'datecollected', 'user_id', 'patient_id', 'rejectedreason', 'receivedstatus', 'justification', 'facility_id', 'mother_last_result', 'feeding', 'regimen', 'redraw', 'datereceived', 'site_entry', 'sample_type', 'lab_id', 'patient_id', 'run', 'parentid', 'age'],
        ]; 
    }

    public static function get_viral_lookups()
    {
        self::cacher();
        return [
            'viral_rejected_reasons' => Cache::get('viral_rejected_reasons'),
            'vl_result_guidelines' => Cache::get('vl_result_guidelines'),
            'genders' => Cache::get('genders'),
            'sample_types' => Cache::get('sample_types'),
            'received_statuses' => Cache::get('received_statuses'),
            'prophylaxis' => Cache::get('prophylaxis'),
            'justifications' => Cache::get('justifications'),
            'pmtct_types' => Cache::get('pmtct_types'),
            'amrs_locations' => Cache::get('amrs_locations'),
        ];        
    }

    public static function viralsample_form()
    {
        self::cacher();
        return [
            // 'facilities' => Cache::get('facilities'),
            'amrs_locations' => Cache::get('amrs_locations'),
            'genders' => Cache::get('genders'),
            'rejectedreasons' => Cache::get('viral_rejected_reasons'),
            'receivedstatuses' => Cache::get('received_statuses'),
            'pmtct_types' => Cache::get('pmtct_types'),
            'prophylaxis' => Cache::get('prophylaxis'),
            'justifications' => Cache::get('justifications'),
            'sampletypes' => Cache::get('sample_types'),
            // 'regimenlines' => Cache::get('regimen_lines'),

            'languages' => Cache::get('languages'),

            'batch' => session('viral_batch'),
            'facility_name' => session('viral_facility_name', 0),
            'amrs' => self::$amrs,
            'sms' => self::$sms,

            'regimen_age' => ['', 'Adult', 'Paediatric'],
            'regimen_line' => ['', 'First Line', 'Second Line', 'Third Line'],
        ];
    }

    public static function cancersample_form()
    {
        self::cacher();
        return [
            'genders' => Cache::get('genders'),
            'facility_name' => session('viral_facility_name', 0),
            'sampletypes' => Cache::get('cancer_sample_types'),
            'justifications' => Cache::get('cancerjustifications'),
            'hivstatuses' => Cache::get('cancerhivstatuses'),
            'rejectedreasons' => Cache::get('cancerrejectedreasons'),
            'receivedstatuses' => Cache::get('received_statuses'),
            'actions' => Cache::get('canceractions'),
        ];
    }

    public static function calculate_viralage($date_collected, $dob)
    {
        if(!$dob) return 0;
        $dob = Carbon::parse( $dob );
        $dc = Carbon::parse( $date_collected );
        $years = $dc->diffInYears($dob, true);

        if($years == 0) $years = ($dc->diffInMonths($dob)/12);
        return $years;
    }


    public static function viral_regimen($val)
    {
        self::cacher();       
        $my_array = Cache::get('prophylaxis');       
        return $my_array->where('category', $val)->first()->id ?? 16;
    }  

    public static function viral_prophylaxis($val)
    {
        self::cacher();       
        $my_array = Cache::get('prophylaxis'); 
        $val = substr($val, 0, 4);      
        return $my_array->where('code', $val)->first()->id ?? NULL;
    }    

    public static function justification($val)
    {
        self::cacher();       
        $my_array = Cache::get('justifications');       
        return $my_array->where('rank_id', $val)->first()->id ?? 8;
    }    

    public static function sample_type($val)
    {
        self::cacher();       
        $my_array = Cache::get('sample_types');       
        $id =  $my_array->where('sampletype', $val)->first()->id ?? 4;
        if($id == 3) return 4;
        return $id;
    }

    public static function get_dr()
    {
        self::cacher();
        $data =  [
            'drug_resistance_reasons' => DB::table('drug_resistance_reasons')->get(),
            'dr_primers' => DB::table('dr_primers')->get(),
            'dr_patient_statuses' => DB::table('dr_patient_statuses')->get(),
            // 'dr_sample_types' => Cache::get('dr_sample_types'),
            'dr_projects' => DB::table('dr_projects')->get(),
            'sampletypes' => Cache::get('sample_types'),
            'tb_treatment_phases' => DB::table('tb_treatment_phases')->get(),
            'clinical_indications' => DB::table('clinical_indications')->get(),
            'arv_toxicities' => DB::table('arv_toxicities')->get(),
            'other_medications' => DB::table('other_medications')->get(),

            'worksheet_statuses' => Cache::get('worksheet_statuses'),
            'received_statuses' => Cache::get('received_statuses'),
            'prophylaxis' => Cache::get('prophylaxis'),
            'dr_rejected_reasons' => DB::table('dr_rejected_reasons')->get(),

            'primers' => ['F1', 'F2', 'F3', 'R1', 'R2', 'R3'],
            'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'],
            'resistance_colours' => \App\MiscDr::$call_array,
            'double_approval' => self::$double_approval,

            'container_types' => DB::table('container_types')->get(),
            'amount_units' => DB::table('amount_units')->get(),
            'dr_plate_statuses' => DB::table('dr_plate_statuses')->get(),
            'dr_sample_statuses' => DB::table('dr_sample_statuses')->get(),
            // 'warning_codes' => DB::table('dr_warning_codes')->get(),
            // 'regimens' => DB::table('dr_viralprophylaxis')->get(),
            'regimen_classes' => DB::table('regimen_classes')->get(),

            'genders' => Cache::get('genders'),

            'regimen_age' => ['', 'Adult', 'Paediatric'],
            'regimen_line' => ['', 'First Line', 'Second Line', 'Third Line'],
        ];

        return $data;
    }

    public static function viralsamples_arrays()
    {
        return [
            'batch' => ['datereceived', 'datedispatchedfromfacility', 'highpriority', 'facility_id', 'lab_id', 'site_entry', 'entered_by'],

            'patient' => ['sex', 'patient_name', 'facility_id', 'patient_phone_no', 'preferred_language', 'patient', 'dob', 'initiation_date', 'patient_status'],

            'sample' => ['comments', 'labcomment', 'datecollected', 'patient_id', 'rejectedreason', 'receivedstatus', 'pmtct', 'sampletype', 'prophylaxis', 'justification', 'provider_identifier', 'amrs_location', 'vl_test_request_no', 'order_no', 'dateinitiatedonregimen', 'dateseparated', 'recency_number'],

            'sample_rerun' => ['comments', 'labcomment', 'datecollected', 'patient_id', 'rejectedreason', 'receivedstatus', 'pmtct', 'sampletype', 'prophylaxis', 'justification', 'provider_identifier', 'amrs_location', 'vl_test_request_no', 'order_no', 'batch_id', 'patient_id', 'run', 'parentid', 'age', 'recency_number'],

            'sample_except' => ['_token', 'patient_name', 'submit_type', 'facility_id', 'sex', 'caregiver_phone', 'patient', 'new_patient', 'datereceived', 'datedispatchedfromfacility', 'dob', 'initiation_date', 'highpriority'],

            'sample_api' => ['comments', 'labcomment', 'datecollected', 'patient_id', 'rejectedreason', 'receivedstatus', 'pmtct', 'sampletype', 'prophylaxis', 'justification', 'provider_identifier', 'amrs_location', 'vl_test_request_no', 'order_no', 'dateinitiatedonregimen', 'dateseparated', 'datetested', 'result'],

            // 'dr_sample' => ['patient_id', 'facility_id', 'datecollected', 'datereceived', 'rejectedreason', 'receivedstatus', 'prophylaxis', 'prev_prophylaxis', 'date_current_regimen', 'date_prev_regimen', 'sample_type', 'sampletype', 'clinical_indications', 'has_opportunistic_infections', 'opportunistic_infections', 'has_tb', 'tb_treatment_phase_id', 'has_arv_toxicity', 'arv_toxicities', 'cd4_result', 'has_missed_pills', 'missed_pills', 'has_missed_visits', 'missed_visits', 'has_missed_pills_because_missed_visits', 'other_medications', 'clinician_name'],

            'dr_sample_rerun' => ['patient_id', 'facility_id', 'datecollected', 'datereceived', 'rejectedreason', 'receivedstatus', 'prophylaxis', 'prev_prophylaxis', 'date_current_regimen', 'date_prev_regimen', 'project', 'sampletype', 'sample_amount', 'container_type', 'amount_unit', 'clinical_indications', 'has_opportunistic_infections', 'opportunistic_infections', 'has_tb', 'tb_treatment_phase_id', 'has_arv_toxicity', 'arv_toxicities', 'cd4_result', 'has_missed_pills', 'missed_pills', 'has_missed_visits', 'missed_visits', 'has_missed_pills_because_missed_visits', 'other_medications', 'clinician_name', 'run', 'parentid', 'age', 'lab_id', 'user_id' ],
        ];
    }


    public static function covid_form()
    {
        $tables = ['identifier_types', 'health_statuses', 'covid_justifications', 'covid_test_types', 'covid_symptoms', 'covid_vaccination_statuses', 'observed_signs', 'underlying_conditions', 'covid_isolations', 'covid_sample_types', 'viralrejectedreasons', 'amrslocations', 'receivedstatus', 'gender', 'results', 'countys', 'nationalities', 'quarantine_sites', 'districts'];

        $data = [];

        foreach ($tables as $key => $value) {
            $data[$value] = DB::table($value)->get();
        }
        $data['amrs'] = self::$amrs;
        $data['sms'] = self::$sms;
        return $data;
    }

    public static function covid_arrays()
    {
        return [
            'sample' => ['test_type', 'amrs_location', 'provider_identifier', 'order_no', 'health_status', 'symptoms', 'temperature', 'observed_signs', 'underlying_conditions', 'comments', 'labcomment', 'sample_type', 'vaccination_status', 'receivedstatus', 'rejectedreason', 'datecollected', 'datereceived', 'datetested', 'result', 'age', 'highpriority'],
            'patient' => ['identifier_type', 'identifier', 'national_id', 'nationality', 'patient_name', 'occupation', 'justification', 'county', 'subcounty', 'ward', 'residence', 'hospital_admitted', 'dob', 'sex', 'date_symptoms', 'date_admission', 'date_isolation', 'date_death', 'facility_id', 'county_id', 'subcounty_id', 'patient_name', 'email_address', 'phone_no', 'contact_email_address', 'contact_phone_no', 'quarantine_site_id'],
        ];
    }

	public static function cacher()
	{
        if(Cache::has('amrslocations')){}

        else{
            // Common Lookup Data
            // $facilities = DB::table('facilitys')->select('id', 'name', 'facilitycode')->get();
            $amrs_locations = DB::table('amrslocations')->get();
            $genders = DB::table('gender')->where('id', '<', 3)->get();
            $received_statuses = DB::table('receivedstatus')->where('id', '<', 3)->get();

            $languages = [
                '1' => 'English',
                '2' => 'Kiswahili',
            ];

            // Eid Lookup Data
            $rejected_reasons = DB::table('rejectedreasons')->get();
            $feedings = DB::table('feedings')->get();
            $iprophylaxis = DB::table('prophylaxis')->where(['ptype' => 2, 'flag' => 1])->orderBy('rank_id', 'asc')->get();
            $interventions = DB::table('prophylaxis')->where(['ptype' => 1, 'flag' => 1])->orderBy('rank_id', 'asc')->get();
            $entry_points = DB::table('entry_points')->get();
            $hiv_statuses = DB::table('results')->whereNotIn('id', [3, 5])->get();
            $pcr_types = DB::table('pcrtype')->get();
            $results = DB::table('results')->get();

            // Viralload Lookup Data
            $viral_rejected_reasons = DB::table('viralrejectedreasons')->get();
            $pmtct_types = DB::table('viralpmtcttype')->get();
            $prophylaxis = DB::table('viralregimen')->get();
            $justifications = DB::table('viraljustifications')->orderBy('rank_id', 'asc')->get();
            $sample_types = DB::table('viralsampletype')->get();
            // $regimen_lines = DB::table('viralregimenline')->where('flag', 1)->get();
            $vl_result_guidelines = DB::table('vlresultsguidelines')->get();

            // Worksheet Lookup Data
            $machines = DB::table('machines')->get();
            $actions = DB::table('actions')->get();
            $worksheet_sampletypes = DB::table('viralworksheetsampletypes')->get();
            $dilutions = DB::table('viraldilutionfactors')->get();
            $worksheet_statuses = DB::table('worksheetstatus')->get();


            if(in_array(env('APP_LAB'), [1, 7])){
                // Drug Resistance Lookup Data
                $dr_rejected_reasons = DB::table('dr_rejected_reasons')->get();
                $drug_resistance_reasons = DB::table('drug_resistance_reasons')->get();
                $dr_primers = DB::table('dr_primers')->get();
                $dr_patient_statuses = DB::table('dr_patient_statuses')->get();
                $dr_projects = DB::table('dr_projects')->get();
                $tb_treatment_phases = DB::table('tb_treatment_phases')->get();
                $clinical_indications = DB::table('clinical_indications')->get();
                $arv_toxicities = DB::table('arv_toxicities')->get();
                $other_medications = DB::table('other_medications')->get();
                $container_types = DB::table('container_types')->get();
                $amount_units = DB::table('amount_units')->get();
            }

            if(env('APP_LAB') == 5) {
                // CD4 Lookup Data
                $cd4rejected_reasons = DB::table('cd4rejectedreasons')->get();
                $cd4sample_statuses = DB::table('samplestatus')->get();
            }
            

            $partners = DB::table('partners')->get();
            $subcounties = DB::table('districts')->get();

            if(env('APP_LAB') < 10 && env('APP_LAB') != 7){
            $cancer_sample_types = DB::table('cancersampletypes')->get();
            $cancerjustifications = DB::table('cancerjustifications')->get();
            $cancerhivstatuses = DB::table('cancerhivstatuses')->get();
            $cancerrejectedreasons = DB::table('cancerrejectedreasons')->get();
            $canceractions = DB::table('canceractions')->get();
            }

            $minutes = 60*60;

            // Cache::put('facilities', $facilities, $minutes);
            Cache::put('amrs_locations', $amrs_locations, $minutes);
            Cache::put('genders', $genders, $minutes);
            Cache::put('received_statuses', $received_statuses, $minutes);
            Cache::put('languages', $languages, $minutes);

            Cache::put('rejected_reasons', $rejected_reasons, $minutes);
            Cache::put('feedings', $feedings, $minutes);
            Cache::put('iprophylaxis', $iprophylaxis, $minutes);
            Cache::put('interventions', $interventions, $minutes);
            Cache::put('entry_points', $entry_points, $minutes);
            Cache::put('hiv_statuses', $hiv_statuses, $minutes);
            Cache::put('pcr_types', $pcr_types, $minutes);
            Cache::put('results', $results, $minutes);

            Cache::put('viral_rejected_reasons', $viral_rejected_reasons, $minutes);
            Cache::put('pmtct_types', $pmtct_types, $minutes);
            Cache::put('prophylaxis', $prophylaxis, $minutes);
            Cache::put('interventions', $interventions, $minutes);
            Cache::put('justifications', $justifications, $minutes);
            Cache::put('sample_types', $sample_types, $minutes);
            // Cache::put('regimen_lines', $regimen_lines, $minutes);
            Cache::put('vl_result_guidelines', $vl_result_guidelines, $minutes);

            Cache::put('machines', $machines, $minutes);
            Cache::put('actions', $actions, $minutes);
            Cache::put('worksheet_sampletypes', $worksheet_sampletypes, $minutes);
            Cache::put('dilutions', $dilutions, $minutes);
            Cache::put('worksheet_statuses', $worksheet_statuses, $minutes);

            if(in_array(env('APP_LAB'), [1, 7])){
                Cache::put('dr_rejected_reasons', $dr_rejected_reasons, $minutes);
                Cache::put('drug_resistance_reasons', $drug_resistance_reasons, $minutes);
                Cache::put('dr_primers', $dr_primers, $minutes);
                Cache::put('dr_patient_statuses', $dr_patient_statuses, $minutes);
                Cache::put('dr_projects', $dr_projects, $minutes);
                Cache::put('tb_treatment_phases', $tb_treatment_phases, $minutes);
                Cache::put('clinical_indications', $clinical_indications, $minutes);
                Cache::put('arv_toxicities', $arv_toxicities, $minutes);
                Cache::put('other_medications', $other_medications, $minutes);
                Cache::put('container_types', $container_types, $minutes);
                Cache::put('amount_units', $amount_units, $minutes);
            }

            if(env('APP_LAB') == 5) {
                // CD4 Lookup Data
                Cache::put('cd4rejected_reasons', $cd4rejected_reasons, $minutes);
                Cache::put('cd4sample_statuses', $cd4sample_statuses, $minutes);
            }
            
            Cache::put('partners', $partners, $minutes);
            Cache::put('subcounties', $subcounties, $minutes);
            if(env('APP_LAB') < 10 && env('APP_LAB') != 7){
            Cache::put('cancer_sample_types', $cancer_sample_types, $minutes);
            Cache::put('cancerjustifications', $cancerjustifications, $minutes);
            Cache::put('cancerhivstatuses', $cancerhivstatuses, $minutes);
            Cache::put('cancerrejectedreasons', $cancerrejectedreasons, $minutes);
            Cache::put('canceractions', $canceractions, $minutes);
            }
        }		
	}

    public static function clear_cache()
    {
        // Cache::forget('facilities');
        Cache::forget('amrs_locations');
        Cache::forget('genders');
        Cache::forget('received_statuses');
        Cache::forget('rejected_reasons');
        Cache::forget('feedings');
        Cache::forget('iprophylaxis');
        Cache::forget('interventions');
        Cache::forget('entry_points');
        Cache::forget('pcr_types');
        Cache::forget('results');

        Cache::forget('viral_rejected_reasons');
        Cache::forget('pmtct_types');
        Cache::forget('prophylaxis');
        Cache::forget('interventions');
        Cache::forget('justifications');
        Cache::forget('sample_types');
        Cache::forget('regimen_lines');
        Cache::forget('vl_result_guidelines');

        Cache::forget('machines');
        Cache::forget('actions');
        Cache::forget('worksheet_sampletypes');
        Cache::forget('dilutions');
        Cache::forget('worksheet_statuses');

        Cache::forget('dr_rejected_reasons');
        Cache::forget('drug_resistance_reasons');
        Cache::forget('dr_primers');
        Cache::forget('dr_patient_statuses');
        Cache::forget('dr_sample_types');
        Cache::forget('tb_treatment_phases');
        Cache::forget('clinical_indications');
        Cache::forget('arv_toxicities');
        Cache::forget('other_medications');

        if(env('APP_LAB') == 5) {
            // CD4 Lookup Data
            Cache::forget('cd4rejected_reasons');
        }

        Cache::forget('partners');
        Cache::forget('subcounties');

        Cache::forget('cancer_sample_types');
        Cache::forget('cancerjustifications');
        Cache::forget('cancerhivstatuses');
        Cache::forget('cancerrejectedreasons');
        Cache::forget('canceractions');
    }

    public static function refresh_cache()
    {
        self::clear_cache();
        self::cacher();
    }
}
