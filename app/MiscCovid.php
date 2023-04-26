<?php

namespace App;

use DB;

use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use App\Mail\TestMail;
use App\Mail\CovidDispatch;

use Excel;


class MiscCovid extends Common
{


    public static function roche_sample_result($target1, $target2, $error=null)
    {
        $target1 = trim(strtolower($target1));
        $target2 = trim(strtolower($target2));
        $repeatt = 0;

        if($target1 == 'positive'){
            $result = 2;
            $interpretation = 'Positive';
        }
        else if($target2 == 'positive'){
            $result = 2;
            // $interpretation = 'Presumed Positive. Requires Rerun.';
            $interpretation = 'Presumed Positive. New Sample Required to Confirm Results.';
        }
        else if($target1 == 'negative' && $target1 == $target2){
            $result = 1;
            $interpretation = 'Negative';
        }
        else if(in_array($target1, ['invalid', 'negative']) && in_array($target2, ['invalid', 'negative'])){
            $result = 3;
            $interpretation = 'Failed';
            $repeatt = 1;
        }
        else if($target1 == 'valid' && $target1 == $target2){
            $result = 6;
            $interpretation = 'Valid';            
        }
        else{
            $result = 3;
            $interpretation = 'Failed';
            $repeatt = 1;
        }

        // return ['result' => $result, 'interpretation' => $interpretation, 'repeatt' => 0];
        return compact('result', 'interpretation', 'repeatt', 'target1', 'target2', 'error');
    }

    public static function sample_result($interpretation, $error=null)
    {
        $str = strtolower($interpretation);
        $repeatt = 0;

        // if(\Str::contains($str, ['cn', 'dc'])){
        if(preg_match("/[0-9]/", $interpretation)){
            $result = 2;
        }
        else if(\Str::contains($str, ['not']) && \Str::contains($str, ['detected'])){
            $result = 1;
        }
        else{
            $result = 3;
            $interpretation = $error;
            $repeatt = 1;
        }

        return compact('result', 'interpretation', 'repeatt');
    }


    public static function save_repeat($sample_id, $do_default=true)
    {
        $original = CovidSample::find($sample_id);
        if($original->run == 3 && $do_default && !in_array(env('APP_LAB'), [25])){
            $original->repeatt=0;
            $original->save();
            return false;
        }

        $sample = $original->replicate(['national_sample_id', 'worksheet_id', 'interpretation', 'result', 'repeatt', 'datetested', 'dateapproved', 'dateapproved2', 'target1', 'target2', 'error', 'approvedby', 'approvedby2', 'pool_sample_id']); 
        $sample->run++;
        if($original->parentid == 0) $sample->parentid = $original->id;

        $s = CovidSample::where(['parentid' => $sample->parentid, 'run' => $sample->run])->first();
        if($s) return $s;
        
        $sample->save();
        return $sample;
    }

    public static function get_worksheet_samples($machine_type, $limit, $entered_by=null)
    {
        $machines = Lookup::get_machines();
        $machine = $machines->where('id', $machine_type)->first();

        $user = auth()->user();

        $temp_limit = $limit;     

        if($entered_by){
            $repeats = CovidSampleView::selectRaw("covid_sample_view.*, users.surname, users.oname")
                ->leftJoin('users', 'users.id', '=', 'covid_sample_view.user_id')
                ->where('datereceived', '>', date('Y-m-d', strtotime('-4 months')))
                ->where('parentid', '>', 0)
                ->whereNull('datedispatched')
                ->whereNull('worksheet_id')
                ->whereNull('result')
                ->where(['receivedstatus' => 1, 'covid_sample_view.lab_id' => $user->lab_id, 'repeatt' => 0])
                ->where('site_entry', '!=', 2)
                ->orderBy('covid_sample_view.id', 'desc')
                ->limit($temp_limit)
                ->get();
            $temp_limit -= $repeats->count();
        }

        $samples = CovidSampleView::selectRaw("covid_sample_view.*, users.surname, users.oname")
            ->leftJoin('users', 'users.id', '=', 'covid_sample_view.user_id')
            ->where('datereceived', '>', date('Y-m-d', strtotime('-4 months')))
            ->when($entered_by, function($query) use ($entered_by){
            	$query->where('parentid', 0);
                if(is_array($entered_by)) return $query->whereIn('received_by', $entered_by);
                return $query->where('received_by', $entered_by);
            })
            ->whereNull('datedispatched')
            ->whereNull('worksheet_id')
            ->whereNull('result') 
            ->where(['receivedstatus' => 1, 'covid_sample_view.lab_id' => $user->lab_id, 'repeatt' => 0])
            ->where('site_entry', '!=', 2)  
            ->orderBy('run', 'desc')
            ->orderBy('highpriority', 'desc')
            ->orderBy('datereceived', 'asc')
            ->when(in_array(env('APP_LAB'), [2, 3, 6]), function($query){
                if(in_array(env('APP_LAB'), [3])) return $query->orderBy('justification', 'desc');
                return $query->orderBy('quarantine_site_id')->orderBy('facility_id');
            })
            ->orderBy('covid_sample_view.id', 'asc')     
            ->limit($temp_limit)
            ->get();

        // dd($samples);

        if($entered_by && $repeats->count() > 0) $samples = $repeats->merge($samples);
        $count = $samples->count();        

        $create = false;
        if($count) $create = true;
        // if($count == $limit) $create = true;
        // if(!in_array(env('APP_LAB'), [3]) && $count) $create = true;
        $covid = true;

        return compact('count', 'limit', 'create', 'machine_type', 'machine', 'samples', 'covid');
    }

    public static function dispatch_covid()
    {
        $quarantine_sites = CovidSampleView::selectRaw('distinct quarantine_site_id')
            ->where('datedispatched', '>', date('Y-m-d', strtotime('-2 days')))
            ->where(['repeatt' => 0])
            ->whereNull('date_email_sent')
            ->whereNotNull('datedispatched')
            ->whereNotNull('national_sample_id')
            ->whereNotNull('quarantine_site_id')
            ->get();

        foreach ($quarantine_sites as $key => $quarantine_site) {
            self::send_results($quarantine_site->quarantine_site_id);
        }

        $facilities = CovidSampleView::selectRaw('distinct facility_id')
            ->where('datedispatched', '>', date('Y-m-d', strtotime('-2 days')))
            ->where(['repeatt' => 0])
            ->whereNull('date_email_sent')
            ->whereNotNull('datedispatched')
            ->whereNotNull('national_sample_id')
            ->whereNotNull('facility_id')
            ->get();

        foreach ($facilities as $key => $facility) {
            self::send_results(null, $facility->facility_id);
        }

        if(env('APP_LAB') == 25){
            $lab = Lab::find(env('APP_LAB'));
            $cc_array = explode(',', $lab->cc_emails);
            $bcc_array = $lab->bcc_emails ? explode(',', $lab->bcc_emails) : [];

            $samples = CovidSampleView::where('datedispatched', '>', date('Y-m-d', strtotime('-2 days')))
                ->where(['repeatt' => 0])
                ->whereNull('date_email_sent')
                ->whereNotNull('datedispatched')
                ->whereNotNull('national_sample_id')
                ->whereNotNull('email_address')
                ->get();

            foreach ($samples as $key => $s) {
                if(!filter_var($s->email_address, FILTER_VALIDATE_EMAIL)) continue;

                $sample = CovidSample::find($s->id);
                Mail::to($s->email_address)->cc($cc_array)->bcc($bcc_array)->send(new CovidDispatch([$sample]));
                $sample->date_email_sent = date('Y-m-d');
                $sample->save();
            }
        }

        if(env('APP_LAB') == 23){
            $lab = Lab::find(env('APP_LAB'));
            $cc_array = $lab->cc_emails ? explode(',', $lab->cc_emails) : [];
            $bcc_array = $lab->bcc_emails ? explode(',', $lab->bcc_emails) : [];

            $samples = CovidSampleView::where('datedispatched', '>', date('Y-m-d', strtotime('-2 days')))
                ->where(['repeatt' => 0])
                ->whereNull('date_email_sent')
                ->whereNotNull('datedispatched')
                ->whereNotNull('dateapproved2')
                ->whereNotNull('national_sample_id')
                ->whereNotNull('email_address')
                ->get();

            foreach ($samples as $key => $s) {
                if(!filter_var($s->email_address, FILTER_VALIDATE_EMAIL)) continue;

                $sample = CovidSample::find($s->id);
                Mail::to($s->email_address)->cc($cc_array)->bcc($bcc_array)->send(new CovidDispatch([$sample]));
                $sample->date_email_sent = date('Y-m-d');
                $sample->save();
            }
        }
    }


    public static function send_results($quarantine_site_id=null, $facility_id=null)
    {
        $lab = Lab::find(env('APP_LAB'));
        $cc_array = explode(',', $lab->cc_emails);
        $bcc_array = $lab->bcc_emails ? explode(',', $lab->bcc_emails) : [];

        $samples = CovidSample::select('covid_samples.*')
            ->join('covid_patients', 'covid_samples.patient_id', '=', 'covid_patients.id')
            ->where('datedispatched', '>', date('Y-m-d', strtotime('-2 days')))
            ->where(['repeatt' => 0])
            ->when($facility_id, function($query) use ($facility_id){
                return $query->where('facility_id', $facility_id);
            })
            ->when($quarantine_site_id, function($query) use ($quarantine_site_id){
                return $query->where('quarantine_site_id', $quarantine_site_id);
            })
            ->whereNull('date_email_sent')
            ->whereNotNull('datedispatched')
            ->whereNotNull('national_sample_id')
            ->orderBy('identifier', 'asc')
            ->get();


        $mail_array = [];

        if($quarantine_site_id){
            $quarantine_site = QuarantineSite::find($quarantine_site_id);
            if($quarantine_site && $quarantine_site->email) $mail_array = explode(',', $quarantine_site->email);
        }

        else if($facility_id){
            $quarantine_site = Facility::find($facility_id);
            if($facility && $facility->covid_email) $mail_array = explode(',', $facility->covid_email);
        }
        

        if(!$mail_array && $cc_array){
            Mail::to($cc_array)->bcc($bcc_array)->send(new CovidDispatch($samples));
        }else{                 
            if($quarantine_site_id){                
                Mail::to($mail_array)->cc($cc_array)->bcc($bcc_array)->send(new CovidDispatch($samples, $quarantine_site));
            }else if($facility_id){                
                Mail::to($mail_array)->cc($cc_array)->bcc($bcc_array)->send(new CovidDispatch($samples, $facility));
            }
        }

        foreach ($samples as $key => $sample) {
            $sample->date_email_sent = date('Y-m-d');
            $sample->save();
        }
    }

    public static function covid_worksheets($year = null, $download=true)
    {
        if(!$year) $year = date('Y');
        $data = CovidSample::selectRaw("year(daterun) as year, month(daterun) as month, machine_type, result, count(*) as tests ")
            ->join('covid_worksheets', 'covid_worksheets.id', '=', 'covid_samples.worksheet_id')
            ->where('site_entry', '!=', 2)
            ->whereYear('daterun', $year)
            ->where(['covid_samples.lab_id' => env('APP_LAB')])
            ->groupBy('year', 'month', 'machine_type', 'result')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('machine_type', 'asc')
            ->orderBy('result', 'asc')
            ->get();

        $results = [1 => 'Negative', 2 => 'Positive', 3 => 'Failed', 4 => 'Unknown', 5 => 'Collect New Sample'];
        $machines = [0 => 'Manual', 2 => 'Abbott', 3 => 'C8800'];

        $rows = [];

        for ($i=1; $i < 13; $i++) { 
            foreach ($machines as $mkey => $mvalue) {
                $row = ['Year of Testing' => $year, 'Month of Testing' => date('F', strtotime("{$year}-{$i}-1")), ];
                $row['Machine'] = $mvalue;
                $total = 0;

                foreach ($results as $rkey => $rvalue) {
                    $row[$rvalue] = $data->where('result', $rkey)->where('machine_type', $mkey)->where('month', $i)->first()->tests ?? 0;
                    if($rkey == 3) $row[$rvalue] += $data->where('result', null)->where('machine_type', $mkey)->where('month', $i)->first()->tests ?? 0;
                    $total += $row[$rvalue];
                }

                $row['Total'] = $total;
                $rows[] = $row;
            }
            if($year == date('Y') && $i == date('m')) break;
        }

        $file = 'covid_worksheets_data';
        return Common::csv_download($rows, $file);
    }



    public static function download_excel()
    {
        ini_set("memory_limit", "-1");
        // ini_set("max_execution_time", "720");

        $samples = CovidSampleView::select('covid_sample_view.*', 'machines.machine')
            ->where(['repeatt' => 0])
            ->leftJoin('covid_worksheets', 'covid_worksheets.id', '=', 'covid_sample_view.worksheet_id')
            ->leftJoin('machines', 'machines.id', '=', 'covid_worksheets.machine_type')
            ->whereNotNull('datedispatched')
            ->get();

        extract(Lookup::covid_form());

        $data = [];

        foreach ($samples as $key => $sample) {
            $row = [
                'Lab ID' => $sample->id,
                'Identifier' => $sample->identifier,
                'National ID' => $sample->national_id,
                'Patient Name' => $sample->patient_name,
                'Phone Number' => $sample->phone_no,
                'County' => $sample->countyname ?? $sample->county,
                'Subcounty' => $sample->sub_county ?? $sample->subcountyname ?? $sample->subcounty ?? '',
                'Age' => $sample->age,
                'Gender' => $sample->get_prop_name($gender, 'sex', 'gender_description'),
                'Quarantine Site / Facility' => $sample->quarantine_site ?? $sample->facilityname,
                'Justification' => $sample->get_prop_name($covid_justifications, 'justification'),
                'Test Type' => $sample->get_prop_name($covid_test_types, 'test_type'),
                'Worksheet Number' => $sample->worksheet_id,
                'Machine' => $sample->machine,
                'Date Collected' => $sample->my_date_format('datecollected'),
                'Date Received' => $sample->my_date_format('datereceived'),
                'Date Tested' => $sample->my_date_format('datetested'),
                'TAT (Receipt to Testing)' => ($sample->datetested && $sample->datereceived) ? $sample->datetested->diffInDays($sample->datereceived) : '',
                'TAT (Receipt to Testing, Weekdays Only)' => ($sample->datetested && $sample->datereceived) ? $sample->datetested->diffInWeekdays($sample->datereceived) : '',
                'Received Status' => $sample->get_prop_name($receivedstatus, 'receivedstatus'),
                'Result' => $sample->get_prop_name($results, 'result'),
                'Entered By' => $sample->creator->full_name ?? null,
                'Date Entered' => $sample->my_date_format('created_at'),
            ];
            if(env('APP_LAB') == 1) $row['Kemri ID'] = $sample->kemri_id;
            if(env('APP_LAB') == 25) $row['AMREF ID'] = $sample->kemri_id;
            $data[] = $row;
        }
        return MiscCovid::csv_download($data, 'all-covid-samples', true, true);
    }

}
