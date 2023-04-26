<?php

namespace App\Http\Controllers;

use App\Abbotdeliveries;
use App\Abbotprocurement;
use App\Batch;
use App\CancerSampleView;
use App\CancerWorksheet;
use App\Cd4SampleView;
use App\Common;
use App\Consumption;
use App\Deliveries;
use App\Kits;
use App\Lab;
use App\Machine;
use App\Sample;
use App\SampleView;
use App\TestType;
use App\Taqmandeliveries;
use App\Taqmanprocurement;
use App\ViewFacility;
use App\Viralbatch;
use App\Viralsample;
use App\ViralsampleView;
use DB;
use Excel;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ReportController extends Controller
{
    public static $parent = ['ending','wasted','issued','request','pos'];
    public static $suffix = ['received','damaged'];
    public static $quarters = ['Q1' => '1,2,3', 'Q2' => '4,5,6', 'Q3' => '7,8,9', 'Q4' => '10,11,12'];

    public function index($testtype = null) {
        if($testtype == null && auth()->user()->user_type_id == 5)
            $testtype = 'EID';
        $testtype = strtoupper($testtype);
        return view('reports.reports', compact('testtype'))->with('pageTitle', 'Lab Reports');
    }

    public function cd4reports(){
        return view('reports.cd4reports')->with('pageTitle', 'CD4 Reports');
    }

    public function dateselect(Request $request)
    {
        ini_set("memory_limit", "-1");
        $dateString = '';
        if (session('testingSystem') == 'CD4') {
            $data = self::__getCD4Data($request, $dateString)->get();
            $this->__getExcel($data, $dateString, $request);
        }
        else if($request->input('types') == 'worksheet_report'){
            if($request->input('period') == 'range') $year = date('Y', strtotime($request->input('toDate')));
            else{
                $year = $request->input('year');
            }
            if(session('testingSystem') == 'EID') return \App\Misc::eid_worksheets($year);
            else if(session('testingSystem') == 'Viralload') return \App\MiscViral::vl_worksheets($year);
        }
        else {
            $data = self::__getDateData($request, $dateString)->get();
            return $this->__getExcel($data, $dateString, $request);
        }
        
        session(['toast_error' => 1, 'toast_message' => 'No Data Found']);
    	return back();
    }
    
    public static function __getCD4Data($request, &$title){
        $tbl = "cd4_samples_view";
        $columns = "$tbl.serial_no, view_facilitys.name as facilty, amrslocations.name as amrs, view_facilitys.county, view_facilitys.subcounty, $tbl.medicalrecordno, $tbl.patient_name, $tbl.provider_identifier, gender.gender_description, $tbl.dob, $tbl.datecollected, receivedstatus.name as receivedstatus, cd4rejectedreasons.name as rejectedreason, $tbl.datereceived, date($tbl.created_at) as datecreated, users.surname, $tbl.datetested, $tbl.dateresultprinted, $tbl.AVGCD3percentLymph, $tbl.AVGCD3AbsCnt, $tbl.AVGCD3CD4percentLymph, $tbl.AVGCD3CD4AbsCnt, $tbl.CD45AbsCnt";
        $model = Cd4SampleView::selectRaw($columns)->where('repeatt', '=', 0)
                    ->leftJoin('view_facilitys', 'view_facilitys.id', '=', "$tbl.facility_id")
                    ->leftJoin('amrslocations', 'amrslocations.id', '=', "$tbl.amrs_location")
                    ->leftJoin('gender', 'gender.id', '=', "$tbl.sex")
                    ->leftJoin('receivedstatus', 'receivedstatus.id', '=', "$tbl.receivedstatus")
                    ->leftJoin('cd4rejectedreasons', 'cd4rejectedreasons.id', '=', "$tbl.rejectedreason")
                    ->leftJoin('users', 'users.id', '=', "$tbl.user_id");
        if(null !== $request->input('category')) {
            $title = "cd4 test outcome report ";
            $model = self::setCD4CategoryFilters($request, $model, $title);
            $model = self::setCD4DateFilters($request, $model, $title);
            $model = self::setCD4ReportType($request, $model, $title);
        } else if (null !== $request->input('specificDate') || null !== $request->input('fromDate')) {
            $title = "cd4 samples log book ";
            $model = self::setCD4DateFilters($request, $model, $title);
        } else {
            // dd($request->all());
        }
        
        return $model;
    }

    public static function setCD4DateFilters($request, $model, &$title){
        if($request->input('specificDate')){
            $datereceived = $request->input('specificDate');
            $title .= "for $datereceived ";
            $model = $model->where('datereceived', '=', $datereceived);
        } else if (null !== $request->input('period')) {
            if ($request->input('period') == 'range') {
                $fromDate = date('Y-m-d', strtotime($request->input('fromDate')));
                $toDate = date('Y-m-d', strtotime($request->input('toDate')));
                $title .= "between $fromDate & $toDate ";
                $model = $model->whereBetween('datetested', [$fromDate,$toDate]);
            } else if ($request->input('period') == 'monthly') {
                $year = $request->input('year');
                $month = $request->input('month');
                $title .= "for $year - $month ";
                $model = $model->whereYear('datetested', $year)->whereMonth('datetested', $month);
            } else if ($request->input('period') == 'quarterly') {
                $year = $request->input('year');
                $quarter = $request->input('quarter');
                $title .= "for $year - $quarter ";
                $months = self::$quarters[$quarter];
                $model = $model->whereRaw("MONTH(datetested) in ($months)");
            } else if ($request->input('period') == 'annually') {
                $year = $request->input('year');
                $title .= "for $year";
                $model = $model->whereYear('datetested', $year);
            }
        } else if ($request->input('fromDate')){
            $fromDate = date('Y-m-d', strtotime($request->input('fromDate')));
            $toDate = date('Y-m-d', strtotime($request->input('toDate')));
            $title .= "between $fromDate & $toDate";
            $model = $model->whereBetween('datereceived', [$fromDate,$toDate]);
        }
        return $model;
    }

    public static function setCD4ReportType($request, $model, &$title){
        if ($request->input('types') == 'all') {

        } else if ($request->input('types') == 'less500') {
            $title .= " for less 500 ";
            $model = $model->where('AVGCD3CD4AbsCnt', '<', 500);
        } else if ($request->input('types') == 'above500') {
            $title .= " for above 500 ";
            $model = $model->where('AVGCD3CD4AbsCnt', '>', 500);
        }
        return $model;
    }

    public static function setCD4CategoryFilters($request, $model, &$title){
        if($request->input('category') == 'overall') {

        } else if ($request->input('category') == 'county') {
            $category = $request->input('county');
            $model = $model->where('view_facilitys.county_id', '=', $category);
            $set = ViewFacility::where('county_id', '=', $category)->first()->county;
            $title .= " for $set county ";
        } else if ($request->input('category') == 'subcounty') {
            $category = $request->input('district');
            $model = $model->where('view_facilitys.subcounty_id', '=', $category);
            $set = ViewFacility::where('subcounty_id', '=', $category)->first()->subcounty;
            $title .= " for $set sub-county ";
        } else if ($request->input('category') == 'facility') {
            $category = $request->input('facility');
            $model = $model->where('view_facilitys.id', '=', $category);
            $set = ViewFacility::where('id', '=', $category)->first()->name;
            $title .= " for $set ";
        }
        return $model;
    }// This is in the working tree

    protected function generate_samples_manifest($request, $data, $dateString) {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "3000");
        $export['samples'] = $data;
        $export['testtype'] = $request->input('testtype');
        $export['lab'] = Lab::find(env('APP_LAB'));
        $export['period'] = strtoupper($dateString);
        $filename = strtoupper("HIV MANIFEST " . $dateString) . ".pdf";
        $mpdf = new Mpdf();
        $view_data = view('exports.mpdf_samples_manifest', $export)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }


    public function generate(Request $request)
    {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "3000");
        $dateString = '';
        if (session('testingSystem') == 'CD4') {
            $data = self::__getCD4Data($request, $dateString)->get();
            $this->__getExcel($data, $dateString, $request);
        } else if (auth()->user()->user_type_id == 5) {
            $data = self::__getDateData($request,$dateString)->get();
            // dd($data);
            if ($request->input('types') == 'manifest'){
                $batches = $data->unique('batch_id')->pluck('batch_id');
                // dd($batches);
                if ($request->input('testtype') == 'EID')
                    $model = Batch::class;
                else
                    $model = Viralbatch::class;
                $dbbatches = $model::whereIn('id', $batches)->whereNull('datedispatchedfromfacility')->get();
                foreach($dbbatches as $batch) {
                    $datedispatched = date('Y-m-d');
                    if (null !== $batch->datereceived && $batch->datereceived < $datedispatched)
                        $datedispatched = $batch->created_at;
                    if ($batch->hasAttribute('datedispatchedfromfacility'))
                        $batch->datedispatchedfromfacility = $datedispatched;
                    $batch->pre_update();
                }
                $this->generate_samples_manifest($request, $data, $dateString);
            } else {
                // $data = $this->__getDateData($request, $dateString)->get();
                $this->__getExcel($data, $dateString, $request);
            }
        }else if($request->input('types') == 'worksheet_report'){
            if($request->input('period') == 'range') {
                $year = date('Y', strtotime($request->input('toDate')));
            } else {
                $year = $request->input('year');
            }
            if(session('testingSystem') == 'EID') return \App\Misc::eid_worksheets($year);
            else if(session('testingSystem') == 'Viralload') return \App\MiscViral::vl_worksheets($year);
        }else {
            if($request->input('types') == 'sitessupported' || $request->input('types') == 'remoteentrydoing') {
                $data = self::__getSiteEntryData($request,$dateString)->get();
                $this->__getSiteEntryExcel($data, $dateString, $request);
            } else if ($request->input('types') == 'tat') {
                $data = $this->__getTATData($request, $dateString)->get();
                $this->__getTATExcel($data, $dateString);
            } /*else if ($request->input('types') == 'recency_report') {
                $data = $this->__getRECENCYData($request, $dateString)->get();
                $this->__getRECENCYExcel($data, $dateString);
            }*/
            else if ($request->input('types') == 'recency_report') {
                // if($request->input('types') == 'awaitingtesting'){
                //     DB::enableQueryLog();
                //     $data = self::__getDateData($request,$dateString)->get();
                //     return DB::getQueryLog();
                // }
                $data = self::__getRECENCYData($request,$dateString)->get();
                return $this->__getRECENCYExcel($data, $dateString, $request);
            }else {
                // if($request->input('types') == 'awaitingtesting'){
                //     DB::enableQueryLog();
                //     $data = self::__getDateData($request,$dateString)->get();
                //     return DB::getQueryLog();
                // }

                $data = self::__getDateData($request,$dateString)->get();
                // dd($dateString);
                return $this->__getExcel($data, $dateString, $request);
            }
        }
        session(['toast_error' => 1, 'toast_message' => 'No Data Found']);
        // return back();
    }

    public function worksheet(Request $request)
    {
        $worksheet = CancerWorksheet::find($request->input('worksheet'));
        $dataArray = [];
        $dataArray[] = ['Sample ID', 'Lab ID', 'Run', 'Target 1', 'Target 2', 'Target 3', 'Interpretation', 'Date Approved', 'Approved By'];
        foreach ($worksheet->sample as $sample) {
            $dataArray[] = [
                $sample->patient->id, $sample->id, $sample->run, $sample->target_1, $sample->target_2, $sample->target_3, $sample->result_name, $sample->dateapproved, $sample->approver->full_name ?? ''
            ];
        }
        $title = 'Worksheet No. ' . $worksheet->id . ' samples reports';
        $title = strtoupper($title);
        return Common::csv_download($dataArray, $title, false);
    }

    public function __getTATData($request, &$dateString) {
        if (session('testingSystem') == 'Viralload'){
            $dateString = 'VL TAT ';
            $table = "viralsamples_view";
            $model = ViralsampleView::groupBy('facility');
        } else if (session('testingSystem') == 'EID'){
            $dateString = 'EID TAT ';
            $table = "samples_view";
            $model = SampleView::groupBy('facility');
        } else if (session('testingSystem') == 'CD4') {
            return back();
        }
        $max_tat = $request->input('max_tat');
        $model = $model->selectRaw("view_facilitys.facilitycode, view_facilitys.name as facility, COUNT({$table}.id) as samples_no, ROUND(AVG(tat1), 2) as tat1, ROUND(AVG(tat2), 2) as tat2, ROUND(AVG(tat3), 2) as tat3, ROUND(AVG(tat4), 2) as tat4, ROUND(AVG(tat5), 2) as tat5")->join("view_facilitys", "view_facilitys.id", "=", "$table.facility_id")->where('repeatt', '=', 0)
                    ->when($max_tat, function($query) use($max_tat){
                        $max_tat++;
                        return $query->where('tat5', '<', $max_tat);
                    })
                    ->where("$table.lab_id", '=', env('APP_LAB'))->whereNotNull('tat1')->whereNotNull('tat2')
                    ->whereNotNull('tat3')->whereNotNull('tat4')->orderBy('tat2', 'asc')
                    ->orderBy('tat1', 'asc')->orderBy('tat3', 'asc')->orderBy('tat4', 'asc');

        $model = self::__getBelongingTo($request, $model, $dateString);
        $model = self::__getDateRequested($request, $model, $table, $dateString, false);

        return $model;
    }

    public static function __getDateData($request, &$dateString)
    {
        
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "3000");
        
        $testtype = session('testingSystem');
        if (auth()->user()->user_type_id == 5) {
            if ($request->input('testtype') == 'VL')
                $testtype = 'Viralload';
            else if ($request->input('testtype') == 'EID')
                $testtype = 'EID';
            else if ($request->input('testtype') == 'HPV')
                $testtype = 'HPV';
        }
        
        $title = '';
        if ($testtype == 'Viralload') {
            $table = 'viralsamples_view';
            $columns = "$table.id,$table.batch_id,$table.worksheet_id,machines.machine as platform,$table.patient,$table.patient_name,$table.provider_identifier, IF($table.site_entry = 2, poc_lab.name, labs.labdesc) as `labdesc`, view_facilitys.partner, view_facilitys.county, view_facilitys.subcounty, view_facilitys.name as facility, view_facilitys.facilitycode, order_no as order_number, amrslocations.name as amrs_location, recency_number, gender.gender_description, $table.dob, $table.age, viralpmtcttype.name as pmtct, viralsampletype.name as sampletype, $table.datecollected,";
            
            if ($request->input('types') == 'manifest')
                $columns .= "$table.datedispatchedfromfacility,";
            $columns .= "receivedstatus.name as receivedstatus, viralrejectedreasons.name as rejectedreason, viralregimen.name as regimen, $table.initiation_date, viraljustifications.name as justification, $table.datereceived, $table.created_at, $table.datetested, $table.dateapproved, $table.datedispatched, $table.result,  $table.entered_by, users.surname, case when $table.order_no IS NOT NULL then 'EMR' when $table.site_entry  = '1, 1, NULL' then 'Health Facility' when $table.site_entry = '2, 1, 0' then 'POC' else 'Testing Lab' end ";

            if ($request->input('types') == 'failed') $columns .= ",$table.interpretation";
            // $columns .= "receivedstatus.name as receivedstatus, viralrejectedreasons.name as rejectedreason, viralregimen.name as regimen, $table.initiation_date, viraljustifications.name as justification, $table.datereceived, $table.created_at, $table.datetested, $table.dateapproved, $table.datedispatched, $table.result,  $table.entered_by, rec.surname as receiver";
            $model = ViralsampleView::selectRaw($columns)
                    ->leftJoin('users', 'users.id', '=', "$table.user_id")
                    // ->leftJoin('users as rec', 'rec.id', '=', "$table.received_by")
                    ->leftJoin('labs', 'labs.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys as poc_lab', 'poc_lab.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys', 'view_facilitys.id', '=', "$table.facility_id")
                    ->leftJoin('amrslocations', 'amrslocations.id', '=', 'viralsamples_view.amrs_location')
                    ->leftJoin('gender', 'gender.id', '=', 'viralsamples_view.sex')
                    ->leftJoin('viralsampletype', 'viralsampletype.id', '=', 'viralsamples_view.sampletype')
                    ->leftJoin('receivedstatus', 'receivedstatus.id', '=', 'viralsamples_view.receivedstatus')
                    ->leftJoin('viralrejectedreasons', 'viralrejectedreasons.id', '=', 'viralsamples_view.rejectedreason')
                    ->leftJoin('viralregimen', 'viralregimen.id', '=', 'viralsamples_view.prophylaxis')
                    ->leftJoin('viraljustifications', 'viraljustifications.id', '=', 'viralsamples_view.justification')
                    ->leftJoin('viralpmtcttype', 'viralpmtcttype.id', '=', 'viralsamples_view.pmtct')
                    ->leftJoin('viralworksheets', 'viralworksheets.id', '=', 'viralsamples_view.worksheet_id')
                    ->leftJoin('machines', 'machines.id', '=', 'viralworksheets.machine_type');
        } else if ($testtype == 'EID') {
            $table = 'samples_view';
            $columns = "samples_view.id,samples_view.batch_id,$table.worksheet_id,machines.machine as platform,samples_view.patient, samples_view.patient_name, IF($table.site_entry = 2, poc_lab.name, labs.labdesc) as `labdesc`, view_facilitys.partner, view_facilitys.county, view_facilitys.subcounty, view_facilitys.name as facility, view_facilitys.facilitycode, order_no as order_number,";
            if($request->input('types') == 'manifest')
                $columns .= " $table.datedispatchedfromfacility,";
            $columns .= " gender.gender_description, samples_view.dob, samples_view.age, ip.name as infantprophylaxis, samples_view.datecollected, pcrtype.alias as pcrtype, samples_view.spots, receivedstatus.name as receivedstatus, rejectedreasons.name as rejectedreason, mr.name as motherresult, samples_view.mother_age, mp.name as motherprophylaxis, feedings.feeding, entry_points.name as entrypoint, samples_view.datereceived,samples_view.created_at, samples_view.datetested, samples_view.dateapproved, samples_view.datedispatched, ir.name as infantresult,  $table.entered_by, users.surname, case when $table.order_no IS NOT NULL then 'EMR' when $table.site_entry  = '1, 1, NULL' then 'Health Facility' when $table.site_entry = '2, 1, 0' then 'POC' else 'Testing Lab' end ";
            if ($request->input('types') == 'failed') $columns .= ",$table.interpretation";
            // $columns .= " gender.gender_description, samples_view.dob, samples_view.age, ip.name as infantprophylaxis, samples_view.datecollected, pcrtype.alias as pcrtype, samples_view.spots, receivedstatus.name as receivedstatus, rejectedreasons.name as rejectedreason, mr.name as motherresult, mp.name as motherprophylaxis, feedings.feeding, entry_points.name as entrypoint, samples_view.datereceived,samples_view.created_at, samples_view.datetested, samples_view.dateapproved, samples_view.datedispatched, ir.name as infantresult,  $table.entered_by, rec.surname as receiver";
            $model = SampleView::selectRaw($columns)
                    ->leftJoin('users', 'users.id', '=', "$table.user_id")
                    // ->leftJoin('users as rec', 'rec.id', '=', "$table.received_by")
                    ->leftJoin('labs', 'labs.id', '=', 'samples_view.lab_id')                   
                    ->leftJoin('view_facilitys as poc_lab', 'poc_lab.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys', 'view_facilitys.id', '=', "$table.facility_id")
                    ->leftJoin('gender', 'gender.id', '=', 'samples_view.sex')
                    ->leftJoin('prophylaxis as ip', 'ip.id', '=', 'samples_view.regimen')
                    ->leftJoin('prophylaxis as mp', 'mp.id', '=', 'samples_view.mother_prophylaxis')
                    ->leftJoin('pcrtype', 'pcrtype.id', '=', 'samples_view.pcrtype')
                    ->leftJoin('receivedstatus', 'receivedstatus.id', '=', 'samples_view.receivedstatus')
                    ->leftJoin('rejectedreasons', 'rejectedreasons.id', '=', 'samples_view.rejectedreason')
                    ->leftJoin('feedings', 'feedings.id', '=', 'samples_view.feeding')
                    ->leftJoin('entry_points', 'entry_points.id', '=', 'samples_view.entry_point')
                    ->leftJoin('results as ir', 'ir.id', '=', 'samples_view.result')
                    ->leftJoin('mothers', 'mothers.id', '=', 'samples_view.mother_id')
                    ->leftJoin('results as mr', 'mr.id', '=', 'mothers.hiv_status')
                    ->leftJoin('worksheets', 'worksheets.id', '=', 'samples_view.worksheet_id')
                    ->leftJoin('machines', 'machines.id', '=', 'worksheets.machine_type');
        } else if ($testtype == 'HPV') {
            $table = 'cancer_samples_view';
            $columns = "$table.id,$table.worksheet_id,machines.machine as platform,$table.patient, $table.patient_name, IF($table.site_entry = 2, poc_lab.name, labs.labdesc) as `labdesc`, view_facilitys.partner, view_facilitys.county, view_facilitys.subcounty, view_facilitys.name as facility, view_facilitys.facilitycode,";
            
            $columns .= " gender.gender_description, $table.dob, $table.age, cancerhivstatuses.name as hiv_status, cancerjustifications.name as justification, cancersampletypes.name as sampletype, $table.datecollected, receivedstatus.name as receivedstatus, rejectedreasons.name as rejectedreason, entry_point, $table.datereceived,$table.created_at, $table.datetested, $table.dateapproved, $table.datedispatched, $table.target_1, $table.target_2, $table.target_3,ir.name as result, CONCAT(users.surname, CONCAT(' ', users.oname)) AS `entered_by`";
            // if ($request->input('types') == 'failed') $columns .= ",$table.interpretation";
            // $columns .= " gender.gender_description, samples_view.dob, samples_view.age, ip.name as infantprophylaxis, samples_view.datecollected, pcrtype.alias as pcrtype, samples_view.spots, receivedstatus.name as receivedstatus, rejectedreasons.name as rejectedreason, mr.name as motherresult, mp.name as motherprophylaxis, feedings.feeding, entry_points.name as entrypoint, samples_view.datereceived,samples_view.created_at, samples_view.datetested, samples_view.dateapproved, samples_view.datedispatched, ir.name as infantresult,  $table.entered_by, rec.surname as receiver";
            $model = CancerSampleView::selectRaw($columns)
                    ->leftJoin('users', 'users.id', '=', "$table.user_id")
                    ->leftJoin('labs', 'labs.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys as poc_lab', 'poc_lab.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys', 'view_facilitys.id', '=', "$table.facility_id")
                    ->leftJoin('gender', 'gender.id', '=', "$table.sex")
                    // ->leftJoin('pcrtype', 'pcrtype.id', '=', 'samples_view.pcrtype')
                    ->leftJoin('receivedstatus', 'receivedstatus.id', '=', "$table.receivedstatus")
                    ->leftJoin('rejectedreasons', 'rejectedreasons.id', '=', "$table.rejectedreason")
                    // ->leftJoin('entry_points', 'entry_points.id', '=', 'samples_view.entry_point')
                    ->leftJoin('results as ir', 'ir.id', '=', "$table.result")
                    ->leftJoin('worksheets', 'worksheets.id', '=', "$table.worksheet_id")
                    ->leftJoin('cancerhivstatuses', 'cancerhivstatuses.id', '=', "$table.hiv_status")
                    ->leftJoin('cancerjustifications', 'cancerjustifications.id', '=', "$table.justification")
                    ->leftJoin('cancersampletypes', 'cancersampletypes.id', '=', "$table.sample_type")
                    ->leftJoin('machines', 'machines.id', '=', 'worksheets.machine_type');

                    if ($request->input('category') == 'norl_facility') {
                        $model = $model->where('view_facilitys.norl', 1);
                    }
        }
        
        $model = self::__getBelongingTo($request, $model, $dateString);

        if ($request->input('specificDate')) {
            $dateString = date('d-M-Y', strtotime($request->input('specificDate')));
            $model = $model->where("$table.datereceived", '=', $request->input('specificDate'));
        }else {
            $receivedOnly=$useDateCollected=false;
            if (in_array($request->input('types'), ['rejected', 'manifest']) || $request->input('samples_log') == 1) $receivedOnly=true;

            if (in_array($request->input('types'), ['awaitingtesting', 'remoteentry'])) $useDateCollected=true;
            
            $model = self::__getDateRequested($request, $model, $table, $dateString, $receivedOnly, $useDateCollected);
        }
        
        $report = 'EID ';
        if ($testtype == 'Viralload')
            $report = 'VL ';
        if ($testtype == 'HPV')
            $report = 'Cancer ';

        if ($request->input('category') == 'norl_facility') {
            $report .= 'norl facilites ';
        }
        
        if ($request->input('types') == 'tested') {
            $model = $model->where("$table.receivedstatus", "<>", '2');
            $report .= 'tested outcomes ';
        } else if ($request->input('types') == 'rejected') {
            $model = $model->where("$table.receivedstatus", "=", '2');
            $report .= 'rejected outcomes ';
        } else if ($request->input('types') == 'awaitingtesting') {
            $model = $model->whereRaw("({$table}.receivedstatus is null OR {$table}.receivedstatus != 2)")->whereNull("$table.datetested")->whereNull("$table.datedispatched");
            $report .= 'awaiting testing ';
        } else if ($request->input('types') == 'positives') {
            $model = $model->where("$table.result", "=", 2);
            $report .= 'positive outcomes';
        } else if ($request->input('types') == 'cns') {
            if($testtype == 'Viralload') $model = $model->where("$table.result", "=", "Collect New Sample");
            else{
                $model = $model->where("$table.result", "=", 5);
            }
            $report .= 'collect new sample';
        } else if ($request->input('types') == 'poc') {
            $model = $model->where("$table.site_entry", '=', 2);
            $report .= 'poc tests';
        } else if ($request->input('types') == 'manifest') {
            $report .= 'sample manifest ';
        } else {
            $report .= 'samples log ';    
        }

        if ($request->input('types') == 'remoteentry')
            $model = $model->where('site_entry', '=', 1)->whereNull('datereceived');
        if ($request->input('types') == 'failed'){
            $model = $model->when($testtype, function($query) use ($testtype){
                                if ($testtype == 'EID')
                                    // return $query->whereIn('result', [3])->where('repeatt', '=', 1);
                                    return $query->whereRaw("(result IS NULL OR result IN (3)) ")->where('repeatt', '=', 1);
                                if ($testtype == 'Viralload')
                                    return $query->where('repeatt', '=', 1)
                                    ->whereRaw("(result IS NULL OR result IN ('Failed')) ");
                                    // ->whereIn('result', ['Failed', '']);
                            });
        }

        if(auth()->user()->user_type_id == 5) {
            $facility_id = auth()->user()->facility_id;
            $user_id = auth()->user()->id;
            if ($request->input('types') == 'manifest')
                $model = $model->where('site_entry', '=', 1)->whereRaw("(($table.user_id = " . auth()->user()->id . ") or ($table.facility_id = " . auth()->user()->facility_id . "))")->orderBy('created_at', 'asc');
            else
                $model = $model->whereRaw("(($table.facility_id = {$facility_id}) OR ($table.lab_id = {$facility_id}) OR ($table.user_id = {$user_id}))");
        } else {
            $model = $model->where("$table.lab_id", '=', env('APP_LAB'));
        }

        if($request->input('types') != 'failed') $model = $model->where('repeatt', '=', 0);
        if(auth()->user()->user_type_id == 5) {
            $report = auth()->user()->facility->name . ' ' . $report;
        }
        $dateString = strtoupper($report . $title . ' ' . $dateString);
        // dd($model->orderBy('datereceived', 'asc')->where('repeatt', '=', 0)->toSql());
        return $model->orderBy('datereceived', 'asc');
    }

    public static function __getSiteEntryData($request, &$dateString) {
        if(session('testingSystem') == 'Viralload') {
            $dateString = 'VL';
            $table = "viralsamples_view";
            $model = ViralsampleView::orderBy('totalsamples', 'desc')->where('viralsamples_view.lab_id', '=', env('APP_LAB'));
        } else if(session('testingSystem') == 'EID') {
            $dateString = 'EID';
            $table = "samples_view";
            $model = SampleView::orderBy('totalsamples', 'desc')->where('samples_view.lab_id', '=', env('APP_LAB'));
        }

        $majoritySelect = "$table.facilitycode, view_facilitys.name as facility, facilitys.name as enteredby,view_facilitys.county, view_facilitys.subcounty, view_facilitys.partner,count(*) as totalsamples";
        $minoritySelect = "view_facilitys.facilitycode AS `facilitycode`, Subcounty AS `subcounty`, view_facilitys.name AS `facility`, COUNT(DISTINCT {$table}.facility_id) AS `Facilities Supported`,  COUNT({$table}.id) AS `totalsamples` ";

        if($request->input('types') == 'remoteentry') {
            $sql = $majoritySelect;
            $dateString .= ' site entry ';
        } else if ($request->input('types') == 'sitessupported') {
            $sql = $majoritySelect;
            $dateString .= ' suported sites ';
        } else if ($request->input('types') == 'remoteentrydoing') {
            $sql = $minoritySelect;
            $dateString .= ' sites doing remote entry ';
        }
        
        $model = $model->selectRaw($sql)
                    ->when($request, function($query) use ($request, $table) {
                        if ($request->input('types') == 'remoteentrydoing')
                            return $query->join('users', 'users.id', '=', "{$table}.user_id")
                                        ->join('view_facilitys', 'view_facilitys.id', '=', "users.facility_id")
                                        ->groupBy(['subcounty', 'facilitycode', 'facility']);
                        else 
                            return $query->join("view_facilitys", "view_facilitys.id", "=", "$table.facility_id")
                                        ->leftJoin('users', 'users.id', '=', "$table.user_id")
                                        ->leftJoin('facilitys', 'facilitys.id', '=', 'users.facility_id')
                                        ->groupBy(['facilitycode', 'facility', 'county', 'subcounty', 'partner']);
                    })->when(true, function($query) use ($request, $table){
                        if($request->input('types') == 'remoteentry')
                            return $query->where("$table.site_entry", "=", 1);
                    })->where('repeatt', '=', 0);
        
        $model = self::__getBelongingTo($request, $model, $dateString);
        $model = self::__getDateRequested($request, $model, $table, $dateString);
        
        return $model;
    }

    public static function __getBelongingTo($request, $model, &$dateString) {
        $title = 'for ';
        if ($request->input('category') == 'county') {
            $param = $request->input('county');
            if(is_array($param)){
                $model = $model->whereIn('view_facilitys.county_id', $param);
                $names = DB::table('countys')->whereIn('id', $param)->get()->pluck('name')->toArray();
                $title .= implode(',', $names).' counties ';
            }
            else{
                $model = $model->where('view_facilitys.county_id', '=', $param);
                $county = ViewFacility::where('county_id', '=', $param)->get()->first();
                $title .= $county->county.' county ';
            }

        } else if ($request->input('category') == 'subcounty') {
            $param = $request->input('district');
            if(is_array($param)){
                $model = $model->whereIn('view_facilitys.subcounty_id', $param);
                $names = DB::table('districts')->whereIn('id', $param)->get()->pluck('name')->toArray();
                $title .= implode(',', $names).' ';
            }
            else{
                $model = $model->where('view_facilitys.subcounty_id', '=', $param);
                $subc = ViewFacility::where('subcounty_id', '=', $param)->get()->first();
                $title .= $subc->subcounty.' ';
            }

        } else if ($request->input('category') == 'facility') {
            $param = $request->input('facility');
            if(is_array($param)){
                $model = $model->whereIn('view_facilitys.id', $param);
                $names = ViewFacility::whereIn('id', $param)->get()->pluck('name')->toArray();
                $title .= implode(',', $names).' ';
            }
            else{
                $model = $model->where('view_facilitys.id', '=', $param);
                $facility = ViewFacility::find($param);
                $title .= $facility->name.' ';
            }

        } else if ($request->input('category') == 'partner') {
            $param = $request->input('partner');
            if(is_array($param)){
                $model = $model->whereIn('view_facilitys.partner_id', $param);
                $names = DB::table('partners')->whereIn('id', $param)->get()->pluck('name')->toArray();
                $title .= implode(',', $names).' ';
            }
            else{
                $model = $model->where('view_facilitys.partner_id', '=', $param);
                $partner = DB::table('partners')->where('id', $param)->first();
                $title .= $partner->name.' ';
            }

        }
        if ($request->input('types') == 'manifest') {
            $facility = ViewFacility::find(auth()->user()->facility_id);
            $title .= $facility->name;
        }
        $dateString .= $title . ' ';
        return $model;
    }

    public static function __getDateRequested($request, $model, $table, &$dateString, $receivedOnly=true,$useDateCollected=false) {
        if($request->input('types') == 'manifest') { $column = 'created_at'; } 
        else if ($useDateCollected) { $column = 'datecollected'; } 
        else if ($receivedOnly) { $column = 'datereceived'; } 
        else { $column = 'datetested'; }

        if (!$request->input('period') || $request->input('period') == 'range') {
            $dateString .= date('d-M-Y', strtotime($request->input('fromDate')))." - ".date('d-M-Y', strtotime($request->input('toDate')));
            $model = $model->when(true, function($query) use ($request, $table, $column) {
                                if ($request->input('fromDate') == $request->input('toDate'))
                                    return $query->whereRaw("date($table.$column) = '" . $request->input('fromDate') . "'");
                                else
                                    return $query->whereRaw("date($table.$column) between '" . $request->input('fromDate') . "' and '" . $request->input('toDate') . "'");
                            });
        } else if ($request->input('period') == 'monthly') {
            $dateString .= date("F", mktime(null, null, null, $request->input('month'))).' - '.$request->input('year');
            $model = $model->whereRaw("YEAR($table.$column) = '".$request->input('year')."' AND MONTH($table.$column) = '".$request->input('month')."'");
        } else if ($request->input('period') == 'quarterly') {
            if ($request->input('quarter') == 'Q1') {
                $startQuarter = 1;
                $endQuarter = 3;
            } else if ($request->input('quarter') == 'Q2') {
                $startQuarter = 4;
                $endQuarter = 6;
            } else if ($request->input('quarter') == 'Q3') {
                $startQuarter = 7;
                $endQuarter = 9;
            } else if ($request->input('quarter') == 'Q4') {
                $startQuarter = 10;
                $endQuarter = 12;
            } else {
                $startQuarter = 0;
                $endQuarter = 0;
            }
            $dateString .= $request->input('quarter').' - '.$request->input('year');
            $model = $model->whereRaw("YEAR($table.$column) = '".$request->input('year')."' AND MONTH($table.$column) BETWEEN '".$startQuarter."' AND '".$endQuarter."'");
        } else if ($request->input('period') == 'annually') {
            $dateString .= $request->input('year');
            $model = $model->whereRaw("YEAR($table.$column) = '".$request->input('year')."'");
        }
        
        return $model;
    }

    public function consumption(Request $request)
    {
        $year = $request->input('year');
        $month = $request->input('month');
        // $previousYear = date('Y', strtotime("-1 Month", strtotime($year . '-' . $month)));
        // $previousMonth = date('m', strtotime("-1 Month", strtotime($year . '-' . $month)));
        $consumption = Consumption::with(['testtype', 'platform'])
                        ->where('year', $year)
                        ->where('machine', $request->input('platform'))
                        ->where('type', $request->input('types'))
                        ->where('month', $month)->get();
        $delivery = Deliveries::where('year', $year)
                        ->where('machine', $request->input('platform'))
                        ->where('type', $request->input('types'))
                        ->where('month', $month)->first();
        $deliveries = $delivery->details ?? $delivery;
        if ($consumption->isEmpty()){
            return back();
        }

        $consumption = $consumption->first();
        $data = [
            'consumption' => $consumption,
            'deliveries' => $deliveries,
            'request' => $request->except(['_token']),
        ];
        
        return view('reports.consumptionreport', $data)->with('pageTitle', 'Consumption Report');  
        
    }

    public function update_consumption(Request $request)
    {
        $consumption = Consumption::with(['testtype', 'platform', 'details'])
                        ->where('year', $request->input('year'))
                        ->where('machine', $request->input('platform'))
                        ->where('type', $request->input('types'))
                        ->where('month', $request->input('month'))->first();
        $data = [
                'consumption' => $consumption,
                'period' => (object)$request->only(['year', 'month']),
                'machine' => Machine::find($request->input('platform')),
                'type' => TestType::find($request->input('types')),
            ];
        // dd($consumption);
        return view('reports.updateconsumptionreport', $data);
    }

    public static function __getRECENCYData($request, &$dateString)
    {
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "3000");
        
        $testtype = session('testingSystem');
        if (auth()->user()->user_type_id == 5) {
            if ($request->input('testtype') == 'VL')
                $testtype = 'Viralload';
            else if ($request->input('testtype') == 'EID')
                $testtype = 'EID';
            else if ($request->input('testtype') == 'HPV')
                $testtype = 'HPV';
        }

        $title = '';
    	if ($testtype == 'Viralload') {
            $table = 'viralsamples_view';
            $columns = "$table.id,$table.batch_id as batch_id,$table.worksheet_id,$table.patient,$table.patient_name, IF($table.site_entry = 2, poc_lab.name, labs.labdesc) as `labdesc`, view_facilitys.partner, view_facilitys.county, view_facilitys.subcounty, view_facilitys.name as facility, view_facilitys.facilitycode, recency_number, gender.gender_description, $table.dob, $table.age, viralpmtcttype.name as pmtct, viralsampletype.name as sampletype, $table.datecollected,";
            
            if ($request->input('types') == 'manifest')
                $columns .= "$table.datedispatchedfromfacility,";
                $columns .= "receivedstatus.name as receivedstatus, viralrejectedreasons.name as rejectedreason, viralregimen.name as regimen, $table.initiation_date, viraljustifications.name as justification, $table.datereceived, $table.datetested, $table.dateapproved, $table.datedispatched, $table.result";

            if ($request->input('types') == 'failed') $columns .= ",$table.interpretation";
            // $columns .= "receivedstatus.name as receivedstatus, viralrejectedreasons.name as rejectedreason, viralregimen.name as regimen, $table.initiation_date, viraljustifications.name as justification, $table.datereceived, $table.created_at, $table.datetested, $table.dateapproved, $table.datedispatched, $table.result,  $table.entered_by, rec.surname as receiver";
            $model = ViralsampleView::selectRaw($columns)
                    ->leftJoin('users', 'users.id', '=', "$table.user_id")
                    // ->leftJoin('users as rec', 'rec.id', '=', "$table.received_by")
    				->leftJoin('labs', 'labs.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys as poc_lab', 'poc_lab.id', '=', "$table.lab_id")
    				->leftJoin('view_facilitys', 'view_facilitys.id', '=', "$table.facility_id")
                    /*->leftJoin('amrslocations', 'amrslocations.id', '=', 'viralsamples_view.amrs_location')*/
    				->leftJoin('gender', 'gender.id', '=', 'viralsamples_view.sex')
    				->leftJoin('viralsampletype', 'viralsampletype.id', '=', 'viralsamples_view.sampletype')
    				->leftJoin('receivedstatus', 'receivedstatus.id', '=', 'viralsamples_view.receivedstatus')
    				->leftJoin('viralrejectedreasons', 'viralrejectedreasons.id', '=', 'viralsamples_view.rejectedreason')
    				->leftJoin('viralregimen', 'viralregimen.id', '=', 'viralsamples_view.prophylaxis')
    				->leftJoin('viraljustifications', 'viraljustifications.id', '=', 'viralsamples_view.justification')
                    ->leftJoin('viralpmtcttype', 'viralpmtcttype.id', '=', 'viralsamples_view.pmtct')
                    ->leftJoin('viralworksheets', 'viralworksheets.id', '=', 'viralsamples_view.worksheet_id')
                    /*->leftJoin('machines', 'machines.id', '=', 'viralworksheets.machine_type')*/
                    ->where('justification', '=', 12);
    	} else if ($testtype == 'EID') {
            $table = 'samples_view';
            $columns = "samples_view.id,samples_view.batch_id,$table.worksheet_id,machines.machine as platform,samples_view.patient, samples_view.patient_name, IF($table.site_entry = 2, poc_lab.name, labs.labdesc) as `labdesc`, view_facilitys.partner, view_facilitys.county, view_facilitys.subcounty, view_facilitys.name as facility, view_facilitys.facilitycode, order_no as order_number,";
            if($request->input('types') == 'manifest')
                $columns .= " $table.datedispatchedfromfacility,";
            $columns .= " gender.gender_description, samples_view.dob, samples_view.age, ip.name as infantprophylaxis, samples_view.datecollected, pcrtype.alias as pcrtype, samples_view.spots, receivedstatus.name as receivedstatus, rejectedreasons.name as rejectedreason, mr.name as motherresult, samples_view.mother_age, mp.name as motherprophylaxis, feedings.feeding, entry_points.name as entrypoint, samples_view.datereceived,samples_view.created_at, samples_view.datetested, samples_view.dateapproved, samples_view.datedispatched, ir.name as infantresult,  $table.entered_by, users.surname, users.oname";
            if ($request->input('types') == 'failed') $columns .= ",$table.interpretation";
            // $columns .= " gender.gender_description, samples_view.dob, samples_view.age, ip.name as infantprophylaxis, samples_view.datecollected, pcrtype.alias as pcrtype, samples_view.spots, receivedstatus.name as receivedstatus, rejectedreasons.name as rejectedreason, mr.name as motherresult, mp.name as motherprophylaxis, feedings.feeding, entry_points.name as entrypoint, samples_view.datereceived,samples_view.created_at, samples_view.datetested, samples_view.dateapproved, samples_view.datedispatched, ir.name as infantresult,  $table.entered_by, rec.surname as receiver";
    		$model = SampleView::selectRaw($columns)
                    ->leftJoin('users', 'users.id', '=', "$table.user_id")
                    // ->leftJoin('users as rec', 'rec.id', '=', "$table.received_by")
    				->leftJoin('labs', 'labs.id', '=', 'samples_view.lab_id')
                    ->leftJoin('view_facilitys as poc_lab', 'poc_lab.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys', 'view_facilitys.id', '=', "$table.facility_id")
    				->leftJoin('gender', 'gender.id', '=', 'samples_view.sex')
    				->leftJoin('prophylaxis as ip', 'ip.id', '=', 'samples_view.regimen')
    				->leftJoin('prophylaxis as mp', 'mp.id', '=', 'samples_view.mother_prophylaxis')
    				->leftJoin('pcrtype', 'pcrtype.id', '=', 'samples_view.pcrtype')
    				->leftJoin('receivedstatus', 'receivedstatus.id', '=', 'samples_view.receivedstatus')
    				->leftJoin('rejectedreasons', 'rejectedreasons.id', '=', 'samples_view.rejectedreason')
    				->leftJoin('feedings', 'feedings.id', '=', 'samples_view.feeding')
    				->leftJoin('entry_points', 'entry_points.id', '=', 'samples_view.entry_point')
    				->leftJoin('results as ir', 'ir.id', '=', 'samples_view.result')
    				->leftJoin('mothers', 'mothers.id', '=', 'samples_view.mother_id')
    				->leftJoin('results as mr', 'mr.id', '=', 'mothers.hiv_status')
                    ->leftJoin('worksheets', 'worksheets.id', '=', 'samples_view.worksheet_id')
                    ->leftJoin('machines', 'machines.id', '=', 'worksheets.machine_type');
    	} else if ($testtype == 'HPV') {
            $table = 'cancer_samples_view';
            $columns = "$table.id,$table.worksheet_id,machines.machine as platform,$table.patient, $table.patient_name, IF($table.site_entry = 2, poc_lab.name, labs.labdesc) as `labdesc`, view_facilitys.partner, view_facilitys.county, view_facilitys.subcounty, view_facilitys.name as facility, view_facilitys.facilitycode,";
            
            $columns .= " gender.gender_description, $table.dob, $table.age, cancerhivstatuses.name as hiv_status, cancerjustifications.name as justification, cancersampletypes.name as sampletype, $table.datecollected, receivedstatus.name as receivedstatus, rejectedreasons.name as rejectedreason, entry_point, $table.datereceived,$table.created_at, $table.datetested, $table.dateapproved, $table.datedispatched, $table.target_1, $table.target_2, $table.target_3,ir.name as result, CONCAT(users.surname, CONCAT(' ', users.oname)) AS `entered_by`";
            // if ($request->input('types') == 'failed') $columns .= ",$table.interpretation";
            // $columns .= " gender.gender_description, samples_view.dob, samples_view.age, ip.name as infantprophylaxis, samples_view.datecollected, pcrtype.alias as pcrtype, samples_view.spots, receivedstatus.name as receivedstatus, rejectedreasons.name as rejectedreason, mr.name as motherresult, mp.name as motherprophylaxis, feedings.feeding, entry_points.name as entrypoint, samples_view.datereceived,samples_view.created_at, samples_view.datetested, samples_view.dateapproved, samples_view.datedispatched, ir.name as infantresult,  $table.entered_by, rec.surname as receiver";
    		$model = CancerSampleView::selectRaw($columns)
                    ->leftJoin('users', 'users.id', '=', "$table.user_id")
    				->leftJoin('labs', 'labs.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys as poc_lab', 'poc_lab.id', '=', "$table.lab_id")
                    ->leftJoin('view_facilitys', 'view_facilitys.id', '=', "$table.facility_id")
    				->leftJoin('gender', 'gender.id', '=', "$table.sex")
    				// ->leftJoin('pcrtype', 'pcrtype.id', '=', 'samples_view.pcrtype')
    				->leftJoin('receivedstatus', 'receivedstatus.id', '=', "$table.receivedstatus")
    				->leftJoin('rejectedreasons', 'rejectedreasons.id', '=', "$table.rejectedreason")
    				// ->leftJoin('entry_points', 'entry_points.id', '=', 'samples_view.entry_point')
    				->leftJoin('results as ir', 'ir.id', '=', "$table.result")
                    ->leftJoin('worksheets', 'worksheets.id', '=', "$table.worksheet_id")
                    ->leftJoin('cancerhivstatuses', 'cancerhivstatuses.id', '=', "$table.hiv_status")
                    ->leftJoin('cancerjustifications', 'cancerjustifications.id', '=', "$table.justification")
                    ->leftJoin('cancersampletypes', 'cancersampletypes.id', '=', "$table.sample_type")
                    ->leftJoin('machines', 'machines.id', '=', 'worksheets.machine_type');

                    if ($request->input('category') == 'norl_facility') {
                        $model = $model->where('view_facilitys.norl', 1);
                    }
    	}

        $model = self::__getBelongingTo($request, $model, $dateString);

    	if ($request->input('specificDate')) {
    		$dateString = date('d-M-Y', strtotime($request->input('specificDate')));
    		$model = $model->where("$table.datereceived", '=', $request->input('specificDate'));
    	}else {
            $receivedOnly=$useDateCollected=false;
            if (in_array($request->input('types'), ['rejected', 'manifest']) || $request->input('samples_log') == 1) $receivedOnly=true;

            if (in_array($request->input('types'), ['awaitingtesting','recency_report', 'remoteentry'])) $useDateCollected=true;

            $model = self::__getDateRequested($request, $model, $table, $dateString, $receivedOnly, $useDateCollected);
    	}
        
        $report = 'EID ';
        if ($testtype == 'Viralload')
            $report = 'VL ';
        if ($testtype == 'HPV')
            $report = 'Cancer ';

        if ($request->input('category') == 'norl_facility') {
            $report .= 'norl facilites ';
        }
        
        if ($request->input('types') == 'tested') {
            $model = $model->where("$table.receivedstatus", "<>", '2');
            $report .= 'tested outcomes ';

         } elseif($request->input('types') == 'recency_report') {
            $model = $model->where("$table.justification", "=", '12');
            $report .= 'Recency report ';
        } else if ($request->input('types') == 'rejected') {
            $model = $model->where("$table.receivedstatus", "=", '2');
            $report .= 'rejected outcomes ';
        } else if ($request->input('types') == 'awaitingtesting') {
            $model = $model->whereRaw("({$table}.receivedstatus is null OR {$table}.receivedstatus != 2)")->whereNull("$table.datetested")->whereNull("$table.datedispatched");
            $report .= 'awaiting testing ';
        } else if ($request->input('types') == 'positives') {
            $model = $model->where("$table.result", "=", 2);
            $report .= 'positive outcomes';
        } else if ($request->input('types') == 'cns') {
            if($testtype == 'Viralload') $model = $model->where("$table.result", "=", "Collect New Sample");
            else{
                $model = $model->where("$table.result", "=", 5);
            }
            $report .= 'collect new sample';
        } else if ($request->input('types') == 'poc') {
            $model = $model->where("$table.site_entry", '=', 2);
            $report .= 'poc tests';
        } else if ($request->input('types') == 'manifest') {
            $report .= 'sample manifest ';
        } else {
            $report .= 'samples log ';
        }

        if ($request->input('types') == 'remoteentry')
            $model = $model->where('site_entry', '=', 1)->whereNull('datereceived');
        if ($request->input('types') == 'failed'){
            $model = $model->when($testtype, function($query) use ($testtype){
                                if ($testtype == 'EID')
                                    // return $query->whereIn('result', [3])->where('repeatt', '=', 1);
                                    return $query->whereRaw("(result IS NULL OR result IN (3)) ")->where('repeatt', '=', 1);
                                if ($testtype == 'Viralload')
                                    return $query->where('repeatt', '=', 1)
                                    ->whereRaw("(result IS NULL OR result IN ('Failed')) ");
                                    // ->whereIn('result', ['Failed', '']);
                            });
        }

        if(auth()->user()->user_type_id == 5) {
            $facility_id = auth()->user()->facility_id;
            $user_id = auth()->user()->id;
            if ($request->input('types') == 'manifest')
                $model = $model->where('site_entry', '=', 1)->whereRaw("(($table.user_id = " . auth()->user()->id . ") or ($table.facility_id = " . auth()->user()->facility_id . "))")->orderBy('created_at', 'asc');
            else
                $model = $model->whereRaw("(($table.facility_id = {$facility_id}) OR ($table.lab_id = {$facility_id}) OR ($table.user_id = {$user_id}))");
        } else {
            $model = $model->where("$table.lab_id", '=', env('APP_LAB'));
        }

        if($request->input('types') != 'failed') $model = $model->where('repeatt', '=', 0);
        if(auth()->user()->user_type_id == 5) {
            $report = auth()->user()->facility->name . ' ' . $report;
        }
        $dateString = strtoupper($report . $title . ' ' . $dateString);
        // dd($model->orderBy('datereceived', 'asc')->where('repeatt', '=', 0)->toSql());
        return $model->orderBy('datereceived', 'asc');
    }

    public static function __getExcel($data, $title, $request = null)
    {
        $title = strtoupper($title);
        $dataArray = []; 
        $vlDataArray = ['Lab ID', 'Batch #', 'Worksheet #', 'Plaform', 'Patient CCC No', 'Patient Names', 'Provider Identifier', 'Testing Lab', 'Partner', 'County', 'Sub County', 'Facility Name', 'MFL Code', 'Order Number', 'AMRS location', 'Recency Number', 'Sex', 'DOB', 'Age', 'PMTCT', 'Sample Type', 'Collection Date', 'Received Status', 'Rejected Reason / Reason for Repeat', 'Current Regimen', 'ART Initiation Date', 'Justification',  'Date Received', 'Date Entered', 'Date of Testing', 'Date of Approval', 'Date of Dispatch', 'Viral Load', 'Entered By', 'Received By','Entry'];
        $eidDataArray = ['Lab ID', 'Batch #', 'Worksheet #', 'Plaform', 'Sample Code', 'Infant Name','Testing Lab', 'Partner', 'County', 'Sub County', 'Facility Name', 'MFL Code', 'Order Number', 'Sex',    'DOB', 'Age(m)', 'Infant Prophylaxis', 'Date of Collection', 'PCR Type', 'Spots', 'Received Status', 'Rejected Reason / Reason for Repeat', 'HIV Status of Mother', 'Mother Age', 'PMTCT Intervention', 'Breast Feeding', 'Entry Point',  'Date Received', 'Date Entered', 'Date of Testing', 'Date of Approval', 'Date of Dispatch', 'Test Result', 'Entered By', 'Received By','Entry'];
        $cd4DataArray = ['Lab Serial #', 'Facility', 'AMR Location', 'County', 'Sub-County', 'Ampath #', 'Patient Names', 'Provider ID', 'Sex', 'DOB', 'Date Collected/Drawn', 'Received Status', 'Rejected Reason( if Rejected)', 'Date Received', 'Date Registered', 'Registered By', 'Date Tested', 'Date Result Printed', 'CD3 %', 'CD3 abs', 'CD4 %', 'CD4 abs', 'Total Lymphocytes'];
        $cancerArray = ['Lab ID', 'Worksheet #', 'Plaform', 'Sample Code', 'Patient Name','Testing Lab', 'Partner', 'County', 'Sub County', 'Facility Name', 'MFL Code', 'Sex', 'DOB', 'Age(m)', 'HIV Status', 'Justification', 'Sample Type', 'Date of Collection', 'Received Status', 'Rejected Reason / Reason for Repeat', 'Entry Point',  'Date Received', 'Date Entered', 'Date of Testing', 'Date of Approval', 'Date of Dispatch', 'Target 1', 'Target 2', 'Target 3', 'Test Result', 'Entered By'];
        // $VLfacilityManifestArray = ['Lab ID', 'Patient CCC #', 'Batch #', 'County', 'Sub-County', 'Facility Name', 'Facility Code', 'Gender', 'DOB', 'Sample Type', 'Justification', 'Date Collected', 'Date Tested'];
        // $EIDfacilityManifestArray = ['Lab ID', 'HEI # / Patient CCC #', 'Batch #', 'County', 'Sub-County', 'Facility Name', 'Facility Code', 'Gender', 'DOB',  'PCR Type','Spots', 'Date Collected', 'Date Tested'];
        if (auth()->user()->user_type_id == 5) {
            $newArray = [];
            if ($request->input('types') == 'manifest') {
                // $dataArray[] = ($request->input('testtype') == 'VL') ? $VLfacilityManifestArray : $EIDfacilityManifestArray;

                // foreach ($data as $key => $new) {
                //     $newArray[] = [
                //         'lab_id' => $new->id, 'patient' => $new->patient, 'batch' => $new->batch_id,
                //         'county' => $new->county, 'subcounty' => $new->subcounty, 'facility' => $new->facility,
                //         'mfl' => $new->facilitycode, 'gender' => $new->gender_description,  'dob' => $new->dob,
                //         'types' => ($request->input('testtype') == 'VL') ? $new->sampletype : $new->pcrtype,
                //         'jus-spots' => ($request->input('testtype') == 'VL') ? $new->justification : $new->spots,
                //         'datecollected' => $new->datecollected, 'datetested' => $new->datetested
                //     ];
                // }
                // $data = collect($newArray);
            } else {
                if ($request->input('testtype') == 'VL')
                    $dataArray[] = $vlDataArray;
                else if ($request->input('testtype') == 'EID')
                    $dataArray[] = $eidDataArray;
                else if ($request->input('testtype') == 'HPV')
                    $dataArray[] = $cancerArray;
            }
        } else {
            if (session('testingSystem') == 'Viralload')
                $dataArray[] = $vlDataArray;
            else if (session('testingSystem') == 'EID')
                $dataArray[] = $eidDataArray;
            else if (session('testingSystem') == 'CD4')
                $dataArray[] = $cd4DataArray;
            else if (session('testingSystem') == 'HPV')
                $dataArray[] = $cancerArray;
        }
                
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "3000");
        // dd($data->isEmpty());
        if(!$data->isEmpty()) {
            foreach ($data as $report) {
                // if ($request->input('types') == 'manifest')
                //     $dataArray[] = $report;
                // else
                    $dataArray[] = $report->toArray();
            }
            
            return Common::csv_download($dataArray, $title, false);
        } else {
            session(['toast_message' => 'No data available for the criteria provided']);
            return back();
        }
    }

    public function __getSiteEntryExcel($data, $title, $request)
    {
        $title = strtoupper($title);
        if ($request->input('types') == 'remoteentrydoing')
            $dataArray[] = ['MFL Code', 'Sub-county', 'Facility', 'Facilities Supported', 'Samples Entered'];
        else
            $dataArray[] = ['MFL Code', 'Facility Name', 'Site Entered', 'County', 'Sub-County', 'Partner', 'Total Samples'];
        $this->generate_excel($data, $dataArray, $title);
    }

    public function __getTATExcel($data, $title) {
        $title = strtoupper($title);
        $dataArray[] = ['MFL Code', 'Facility Name', 'Number of Samples', 'TAT1', 'TAT2', 'TAT3', 'TAT4', 'TAT5 (Lab TAT)'];
        return $this->generate_excel($data, $dataArray, $title);
    }

    public function __getRECENCYExcel($data, $title) {
        $title = strtoupper($title);
        $dataArray[] = ['Lab ID', 'Batch #', 'Worksheet #', 'Patient CCC No', 'Patient Names', 'Testing Lab', 'Partner', 'County', 'Sub County', 'Facility Name', 'MFL Code', 'Recency Number', 'Sex', 'DOB', 'Age', 'PMTCT', 'Sample Type', 'Collection Date', 'Received Status', 'Rejected Reason / Reason for Repeat', 'Current Regimen', 'ART Initiation Date', 'Justification',  'Date Received', 'Date of Testing', 'Date of Approval', 'Date of Dispatch', 'Viral Load'];
        return $this->generate_excel($data, $dataArray, $title);
    }

    public function generate_excel($data, $dataArray, $title){
        ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "3000");
        
        if($data->isNotEmpty()) {
            foreach ($data as $report) {
                $dataArray[] = $report->toArray();
            }
            
            return Common::csv_download($dataArray, $title, false);
        } else {
            session(['toast_error' => 1, 'toast_message' => 'No data available for the criteria provided']);
            return back();
        }
    }

}
