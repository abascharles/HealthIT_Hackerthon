<?php

namespace App\Http\Controllers;

use App\CovidModels\CovidPatient;
use App\CovidModels\CovidSample;
use App\CovidModels\CovidSampleView;
use App\CovidModels\CovidTravel;
use App\City;
use App\Facility;
use App\Lookup;
use App\MiscCovid;
// use Excel;
use Mpdf\Mpdf;
use DB;
use App\Mail\CovidDispatch;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CovidRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\KemriWRPImport;
use App\Imports\WRPCovidImport;
use App\Imports\AmpathCovidImport;
use App\Imports\AlupeCovidImport;
use App\Imports\KNHCovidImport;
use App\Imports\NairobiCovidImport;
use Illuminate\Http\Request;
use Carbon\Carbon;


class NatCovidSampleController extends Controller
{
    public function __construct()
    {
        $this->middleware('covid_allowed');   
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type=1, $date_start=NULL, $date_end=NULL, $facility_id=NULL, $quarantine_site_id=NULL, $lab_id=NULL)
    {
        // 0 - not received
        // 1 - all
        // 2 - dispatched
        // 3 - from cif
        // 4 - pending testing
        $user = auth()->user();
        $date_column = "covid_sample_view.created_at";
        if($type == 2) $date_column = "covid_sample_view.datedispatched";

        $samples = CovidSampleView::select(['covid_sample_view.*'])
            ->when($facility_id, function($query) use ($facility_id){
                if($facility_id == 'null') return $query->whereNull('covid_sample_view.facility_id');
                return $query->where('covid_sample_view.facility_id', $facility_id);
            })
            ->when($quarantine_site_id, function($query) use ($quarantine_site_id){
                return $query->where('quarantine_site_id', $quarantine_site_id);
            })
            ->when($date_start, function($query) use ($date_column, $date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate($date_column, '>=', $date_start)
                    ->whereDate($date_column, '<=', $date_end);
                }
                return $query->whereDate($date_column, $date_start);
            })
            ->when(true, function($query) use ($type){
                if($type == 0) return $query->whereNull('datereceived');
                else if($type == 2) return $query->whereNotNull('datedispatched');
                else if($type == 3) return $query->whereNull('datereceived')->where('u.email', 'joelkith@gmail.com');
                else if($type == 4) return $query->whereNull('datetested')->where(['receivedstatus' => 1, 'repeatt' => 0]);
            })
            ->when(($type == 2), function($query) use ($date_column){
                return $query->orderBy($date_column, 'desc');
            })
            // ->when(!$user->facility_user, function($query) use ($user){
            //     return $query->where('covid_sample_view.lab_id', $user->lab_id);
            // })
            ->when(($user->lab_id != env('APP_LAB')), function($query) use ($user){
                return $query->where('covid_sample_view.lab_id', $user->lab_id);
            })
            ->when($user->quarantine_site, function($query) use ($user){
                return $query->where('quarantine_site_id', $user->facility_id);
            })
            ->when($user->facility_user, function($query) use ($user){
                return $query->whereRaw("(user_id='{$user->id}' OR covid_sample_view.facility_id='{$user->facility_id}')");
            })
            ->when($lab_id, function($query) use ($lab_id){
                return $query->where('covid_sample_view.lab_id', $lab_id);
            })
            ->where('repeatt', 0)
            ->orderBy('covid_sample_view.id', 'desc')
            ->paginate();

        $samples->setPath(url()->current());

        $facility = Facility::find($facility_id);
        
        $myurl = url('/nat_sample/index/' . $type);
        $myurl2 = url('/nat_sample/index/');        
        $quarantine_sites = DB::table('quarantine_sites')->get();
        $justifications = DB::table('covid_justifications')->get();
        $counties = DB::table('countys')->get();
        $subcounties = DB::table('districts')->get();
        $labs = DB::table('labs')->get();
        $results = DB::table('results')->get();
        $data = compact('samples', 'myurl', 'myurl2', 'type', 'quarantine_sites', 'justifications', 'facility', 'facility_id', 'quarantine_site_id', 'lab_id', 'counties', 'subcounties', 'results', 'labs');
        // if($type == 3) $data['labs'] = DB::table('labs')->get();
        return view('tables.covidsamples', $data);
    }

    public function sample_search(Request $request)
    {
        $user = auth()->user();

        $type = $request->input('type', 1);
        $submit_type = $request->input('submit_type');
        if($submit_type == 'excel') return $this->download_excel($request);
        if($submit_type == 'email') return $this->email_multiple($request);
        if($submit_type == 'multiple_results') return $this->multiple_results($request);
        $to_print = $request->input('to_print');
        $date_start = $request->input('date_start', 0);
        if($submit_type == 'submit_date') $date_start = $request->input('filter_date', 0);
        $date_end = $request->input('date_end', 0);


        if($date_start == '') $date_start = 0;
        if($date_end == '') $date_end = 0;

        $quarantine_site_id = $request->input('quarantine_site_id', 0);
        $facility_id = $request->input('facility_id', 0);
        $lab_id = $request->input('lab_id');

        if(!$quarantine_site_id) $quarantine_site_id = 0;
        if(!$facility_id) $facility_id = 0;

        return redirect("nat_sample/index/{$type}/{$date_start}/{$date_end}/{$facility_id}/{$quarantine_site_id}/{$lab_id}");
    }

    public function download_excel($request)
    {
        $user = auth()->user();
        // dd($request->all());
        extract($request->all());

        $type = $request->input('type', 1);

        $date_column = "covid_sample_view.created_at";
        if($type == 2) $date_column = "covid_sample_view.datedispatched";

        $samples = CovidSampleView::where('repeatt', 0)
            ->when($justification_id, function($query) use ($justification_id){
                return $query->where('justification', $justification_id);
            })
            ->when($result, function($query) use ($result){
                return $query->where('result', $result);
            })
            ->when($worksheet_id, function($query) use ($worksheet_id){
                return $query->where('worksheet_id', $worksheet_id);
            })
            ->when($county_id, function($query) use ($county_id){
                return $query->where('county_id', $county_id);
            })
            ->when($subcounty_id, function($query) use ($subcounty_id){
                return $query->where('subcounty_id', $subcounty_id);
            })
            ->when($facility_id, function($query) use ($facility_id){
                if($facility_id == 'null') return $query->whereNull('facility_id');
                return $query->where('covid_sample_view.facility_id', $facility_id);
            })
            ->when($quarantine_site_id, function($query) use ($quarantine_site_id){
                if($quarantine_site_id == 'null') return $query->whereNull('quarantine_site_id');
                return $query->where('quarantine_site_id', $quarantine_site_id);
            })
            ->when($identifier, function($query) use ($identifier){
                return $query->where('identifier', 'like', $identifier . '%');
            })
            ->when($date_start, function($query) use ($date_column, $date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate($date_column, '>=', $date_start)
                    ->whereDate($date_column, '<=', $date_end);
                }
                return $query->whereDate($date_column, $date_start);
            })
            ->when(true, function($query) use ($type){
                if($type == 0) return $query->whereNull('datereceived');
                else if($type == 2) return $query->whereNotNull('datedispatched');
            })
            ->when(($type == 2), function($query) use ($date_column){
                return $query->orderBy($date_column, 'desc');
            })
            ->when($lab_id, function($query) use ($lab_id){
                return $query->where('covid_sample_view.lab_id', $lab_id);
            })
            // where(['receivedstatus' => 1])
            // ->whereNull('result')
            // ->whereNull('datedispatched')
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
                'Subcounty' => $sample->subcountyname ?? $sample->sub_county ?? $sample->subcounty ?? '',
                'Age' => $sample->age,
                'Gender' => $sample->get_prop_name($gender, 'sex', 'gender_description'),
                'Quarantine Site / Facility' => $sample->quarantine_site ?? $sample->facilityname,
                'Justification' => $sample->get_prop_name($covid_justifications, 'justification'),
                'Test Type' => $sample->get_prop_name($covid_test_types, 'test_type'),
                'Worksheet Number' => $sample->worksheet_id,
                'Date Collected' => $sample->my_date_format('datecollected'),
                'Date Received' => $sample->my_date_format('datereceived'),
                'Date Tested' => $sample->my_date_format('datetested'),
                'TAT (Receipt to Testing)' => ($sample->datetested && $sample->datereceived) ? $sample->datetested->diffInDays($sample->datereceived) : '',
                'TAT (Receipt to Testing, Weekdays Only)' => ($sample->datetested && $sample->datereceived) ? $sample->datetested->diffInWeekdays($sample->datereceived) : '',
                'Received Status' => $sample->get_prop_name($receivedstatus, 'receivedstatus'),
                'Result' => $sample->get_prop_name($results, 'result'),
                'Date Entered' => $sample->my_date_format('created_at'),
            ];
            if(env('APP_LAB') == 1) $row['Kemri ID'] = $sample->kemri_id;
            if(env('APP_LAB') == 25) $row['AMREF ID'] = $sample->kemri_id;
            $data[] = $row;
        }
        if(!$data) return back();
        return MiscCovid::csv_download($data, 'covid_samples');
    }

    public function email_multiple($request)
    {
        $user = auth()->user();
        extract($request->all());
        /*if(!$quarantine_site_id && !in_array(env('APP_LAB'), [1,3,5,6,23,25])){
            session(['toast_error' => 1, 'toast_message' => 'Kindly select a quarantine site.']);
            return back();
        }*/
        $quarantine_site = DB::table('quarantine_sites')->where('id', $quarantine_site_id)->first();
        if($quarantine_site && !$quarantine_site->email && !in_array(env('APP_LAB'), [1, 3, 5, 6])){
            session(['toast_error' => 1, 'toast_message' => 'The quarantine site does not have an email address set.']);
            return back();            
        }

        $justification = DB::table('covid_justifications')->where('id', $justification_id)->first();


        $facility = Facility::find($facility_id);
        if($facility && !$facility->covid_email){
            session(['toast_error' => 1, 'toast_message' => 'The facility does not have a Covid-19 email address set.']);
            return back();                        
        }
        $type = 2;

        $date_column = "covid_samples.datedispatched";

        $samples = CovidSample::select('covid_samples.*')
            ->join('covid_patients', 'covid_samples.patient_id', '=', 'covid_patients.id')
            ->where('repeatt', 0)
            ->when($justification_id, function($query) use ($justification_id){
                return $query->where('justification', $justification_id);
            })
            ->when($result, function($query) use ($result){
                return $query->where('result', $result);
            })
            ->when($worksheet_id, function($query) use ($worksheet_id){
                return $query->where('worksheet_id', $worksheet_id);
            })
            ->when($county_id, function($query) use ($county_id){
                return $query->where('county_id', $county_id);
            })
            ->when($subcounty_id, function($query) use ($subcounty_id){
                return $query->where('subcounty_id', $subcounty_id);
            })
            ->when($facility_id, function($query) use ($facility_id){
                return $query->where('facility_id', $facility_id);
            })
            ->when($identifier, function($query) use ($identifier){
                return $query->where('identifier', 'like', $identifier . '%');
            })
            ->when($quarantine_site_id, function($query) use ($quarantine_site_id){
                return $query->where('quarantine_site_id', $quarantine_site_id);
            })
            ->when($date_start, function($query) use ($date_column, $date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate($date_column, '>=', $date_start)
                    ->whereDate($date_column, '<=', $date_end);
                }
                return $query->whereDate($date_column, $date_start);
            })
            ->when(($user->lab_id != env('APP_LAB')), function($query) use ($user){
                return $query->where('covid_samples.lab_id', $user->lab_id);
            })
            ->when($user->quarantine_site, function($query) use ($user){
                return $query->where('quarantine_site_id', $user->facility_id);
            })
            ->when($user->facility_user, function($query) use ($user){
                return $query->whereRaw("(user_id='{$user->id}' OR covid_samples.facility_id='{$user->facility_id}')");
            })
            ->when($lab_id, function($query) use ($lab_id){
                return $query->where('covid_samples.lab_id', $lab_id);
            })
            ->whereNotNull('datedispatched')
            ->orderBy('identifier', 'asc')
            ->orderBy($date_column, 'desc')
            ->get();

        if(!$samples->count()){
            session(['toast_error' => 1, 'toast_message' => 'No samples found']);
            return back(); 
        }
        $lab = \App\Lab::find(env('APP_LAB'));

        if($lab->cc_emails) $cc_array = explode(',', $lab->cc_emails);
        else{
            $cc_array = [];
        }

        $mail_array = [];
        if($quarantine_site && $quarantine_site->email) $mail_array = explode(',', $quarantine_site->email);
        else if($facility && $facility->covid_email) $mail_array = explode(',', $facility->covid_email);
        else if($justification && $justification->email) $mail_array = explode(',', $justification->email);

        if(in_array(env('APP_LAB'), [6,25])){
            if($subcounty_id){
                $subcounty = DB::table('districts')->where('id', $subcounty_id)->first();
                if($subcounty->subcounty_emails) $mail_array = array_merge($mail_array, explode(',', $subcounty->subcounty_emails));
                $county = DB::table('countys')->where('id', $subcounty->county)->first();
                if($county->county_emails) $mail_array = array_merge($mail_array, explode(',', $county->county_emails));
            }
            else if($county_id){
                $county = DB::table('countys')->where('id', $county_id)->first();
                if($county->county_emails) $mail_array = array_merge($mail_array, explode(',', $county->county_emails));                
            }
        }

        if(!$mail_array && $cc_array){
            Mail::to($cc_array)->send(new CovidDispatch($samples));
        }else{             
            if($quarantine_site){                
                Mail::to($mail_array)->cc($cc_array)->send(new CovidDispatch($samples, $quarantine_site));
            }else if($facility){                
                Mail::to($mail_array)->cc($cc_array)->send(new CovidDispatch($samples, $facility));
            }else if($justification){                
                Mail::to($mail_array)->cc($cc_array)->send(new CovidDispatch($samples, $justification));
            }
            // else{
            //     Mail::to($mail_array)->send(new CovidDispatch($samples, $quarantine_site));
            // }
        }

        foreach ($samples as $key => $sample) {
            if(!$sample->date_email_sent){
                $sample->date_email_sent = date('Y-m-d');
                $sample->save();
            }
        }
        session(['toast_message' => 'The results have been sent to the quarantine site / facility.']);
        return back();            
    }

    public function multiple_results($request)
    {
        ini_set("memory_limit", "-1");
        $user = auth()->user();

        extract($request->all());

        $date_column = "covid_samples.datedispatched";

        $samples = CovidSample::select('covid_samples.*')
            ->join('covid_19.covid_patients', 'covid_samples.patient_id', '=', 'covid_patients.id')
            ->where('repeatt', 0)
            ->when($justification_id, function($query) use ($justification_id){
                return $query->where('justification', $justification_id);
            })
            ->when($result, function($query) use ($result){
                return $query->where('result', $result);
            })
            ->when($worksheet_id, function($query) use ($worksheet_id){
                return $query->where('worksheet_id', $worksheet_id);
            })
            ->when($county_id, function($query) use ($county_id){
                return $query->where('county_id', $county_id);
            })
            ->when($subcounty_id, function($query) use ($subcounty_id){
                return $query->where('subcounty_id', $subcounty_id);
            })
            ->when($facility_id, function($query) use ($facility_id){
                if($facility_id == 'null') return $query->whereNull('facility_id');
                return $query->where('facility_id', $facility_id);
            })
            ->when($identifier, function($query) use ($identifier){
                return $query->where('identifier', 'like', $identifier . '%');
            })
            // ->where('identifier', 'like', 'tnz%')
            ->when($quarantine_site_id, function($query) use ($quarantine_site_id){
                if($quarantine_site_id == 'null') return $query->whereNull('quarantine_site_id');
                return $query->where('quarantine_site_id', $quarantine_site_id);
            })            
            // ->whereNull('quarantine_site_id')
            // ->whereNull('facility_id')
            ->when($date_start, function($query) use ($date_column, $date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate($date_column, '>=', $date_start)
                    ->whereDate($date_column, '<=', $date_end);
                }
                return $query->whereDate($date_column, $date_start);
            })
            ->when($lab_id, function($query) use ($lab_id){
                return $query->where('covid_samples.lab_id', $lab_id);
            })
            // ->whereRaw("(covid_samples.id IN (22478, 22555, 22450, 22470) OR covid_samples.id BETWEEN 22408 AND 22420 OR covid_samples.id BETWEEN 22422 AND 22430 OR covid_samples.id BETWEEN 22432 AND 22444 OR covid_samples.id BETWEEN 22452 AND 22464 OR covid_samples.id BETWEEN 22472 AND 22475 )")
            ->whereNotNull('datedispatched')
            ->orderBy('identifier', 'asc')
            ->orderBy($date_column, 'desc')
            ->get();

        if(!$samples->count()){
            session(['toast_error' => 1, 'toast_message' => 'No samples found']);
            return back();
        }


        // $data = Lookup::covid_form();
        // $data['samples'] = $samples;
        // return view('exports.mpdf_covid_samples', $data);

        $mpdf = new Mpdf();
        $data = Lookup::covid_form();
        $data['samples'] = $samples;
        $view_data = view('exports.mpdf_covid_samples', $data)->render();
        ini_set("pcre.backtrack_limit", "500000000");
        $mpdf->WriteHTML($view_data);
        $mpdf->Output('results.pdf', \Mpdf\Output\Destination::DOWNLOAD);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = Lookup::covid_form();
        return view('forms.covidsamples', $data)->with('pageTitle', 'Add Sample');        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        abort(400);
        $data = Lookup::covid_arrays();

        $patient = null;

        if(auth()->user()->lab_id != 1){
            if(!$patient && $request->only('national_id')) $patient = CovidPatient::where($request->only('national_id'))->whereNotNull('national_id')->first();
            if(!$patient) $patient = CovidPatient::where($request->only('identifier', 'facility_id'))->whereNotNull('facility_id')->first();
            if(!$patient) $patient = CovidPatient::where($request->only('identifier', 'quarantine_site_id'))->whereNotNull('quarantine_site_id')->first();
        }
        if(!$patient) $patient = new CovidPatient;
        $patient->fill($request->only($data['patient']));
        $patient->current_health_status = $request->input('health_status');
        $patient->save();

        $sample = CovidSample::where(['patient_id' => $patient->id])->where($request->only(['datecollected']))->first();
        if($sample){
            session(['toast_error' => 1, 'toast_message' => 'The sample already exists.']);
            return back();
        }

        $sample = new CovidSample;
        $sample->fill($request->only($data['sample']));
        $sample->patient_id = $patient->id;
        if(in_array(auth()->user()->lab_id, [1,25])) $sample->kemri_id = $request->input('kemri_id');
        $sample->save();

        $travels = $request->input('travel');
        if($travels){
            $count = count($travels['travel_date']);

            for ($i=0; $i < $count; $i++) {
                $travel = new CovidTravel;
                $travel->travel_date = $travels['travel_date'][$i] ?? null;
                $travel->city_id = $travels['city_id'][$i] ?? null;
                // $travel->city_visited = $travels['city_visited'][$i];
                $travel->duration_visited = $travels['duration_visited'][$i] ?? null;
                $travel->patient_id = $patient->id;
                $travel->save();
            }
        }
        session(['toast_message' => "The sample has been created.", 'last_covid_sample' => $sample->id]);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CovidSample  $covidSample
     * @return \Illuminate\Http\Response
     */
    public function show(CovidSample $covidSample)
    {
        $user = auth()->user();
        $type=1;

        $samples = CovidSampleView::select(['covid_sample_view.*', 'u.surname', 'u.oname', 'r.surname as rsurname', 'r.oname as roname'])
            ->leftJoin('users as u', 'u.id', '=', 'covid_sample_view.user_id')
            ->leftJoin('users as r', 'r.id', '=', 'covid_sample_view.received_by')
            ->when(true, function($query) use ($covidSample){
                if($covidSample->parentid){
                    return $query->whereRaw(" (covid_sample_view.id = {$covidSample->parentid} OR parentid = {$covidSample->parentid})");
                }else{
                    return $query->whereRaw(" (covid_sample_view.id = {$covidSample->id} OR parentid = {$covidSample->id})");
                }
            }) 
            ->when($user->facility_user, function($query) use ($user){
                return $query->whereRaw("(user_id='{$user->id}' OR covid_sample_view.facility_id='{$user->facility_id}')");
            }) 
            ->when($user->quarantine_site, function($query) use ($user){
                return $query->where('quarantine_site_id', $user->facility_id);
            })          
            ->orderBy('run', 'desc')
            ->paginate();
        $myurl = url('/covid_sample/index/' . $type);
        $myurl2 = url('/covid_sample/index/');        
        $p = Lookup::get_partners();
        $data = array_merge($p, compact('samples', 'myurl', 'myurl2', 'type'));
        $data['results'] = DB::table('results')->get();
        return view('tables.covidsamples', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CovidSample  $covidSample
     * @return \Illuminate\Http\Response
     */
    public function edit(CovidSample $covidSample)
    {
        $data = Lookup::covid_form();
        $covidSample->load(['patient.facility']);

        $user = auth()->user();
        if(($user->facility_user && $covidSample->patient->facility_id != $user->facility_id) || ($user->quarantine_site && $covidSample->patient->quarantine_site_id != $user->facility_id)) abort(403);

        if($covidSample->receivedstatus && ($user->facility_user || $user->quarantine_site)){
            session(['toast_error' => 1, 'toast_message' => 'You cannot edit the sample after it has been received at the lab.']);
            return back();
        }

        if(in_array(env('APP_LAB'), [5, 3, 4]) && $covidSample->datedispatched && auth()->user()->user_type_id && !auth()->user()->covid_approver){
            session(['toast_error' => 1, 'toast_message' => "You don't have permission to edit the sample after it has been dispatched."]);
            return back();
        }

        $data['sample'] = $covidSample;
        return view('forms.covidsamples', $data)->with('pageTitle', 'Edit Sample');      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CovidSample  $covidSample
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CovidSample $covidSample)
    {
        $data = Lookup::covid_arrays();

        $patient = $covidSample->patient;
        $patient->fill($request->only($data['patient']));
        if($patient->isDirty('identifier') && $patient->sample()->where('repeatt',0)->count() > 1){
            $patient = new CovidPatient;
            $patient->fill($request->only($data['patient']));
        }
        $patient->current_health_status = $request->input('health_status');
        $patient->pre_update();

        $covidSample->fill($request->only($data['sample']));
        if(in_array(auth()->user()->lab_id, [1,25])) $covidSample->kemri_id = $request->input('kemri_id');
        $covidSample->patient_id = $patient->id;
        $covidSample->pre_update();

        // if($covidSample)

        $travels = $request->input('travel');
        if($travels){
            $count = count($travels['travel_date']);

            for ($i=0; $i < $count; $i++) {
                if(isset($travels['travel_id'][$i])) $travel = CovidTravel::find($travels['travel_id'][$i]);
                else{
                    $travel = new CovidTravel;
                }
                $travel->travel_date = $travels['travel_date'][$i] ?? null;
                $travel->city_id = $travels['city_id'][$i] ?? null;
                $travel->duration_visited = $travels['duration_visited'][$i] ?? null;
                $travel->patient_id = $patient->id;
                $travel->pre_update();
            }
        }
        session(['toast_message' => "The sample has been updated."]);
        return redirect('/covid_sample');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CovidSample  $covidSample
     * @return \Illuminate\Http\Response
     */
    public function destroy(CovidSample $covidSample)
    {
        if($covidSample->worksheet_id){
            session(['toast_error' => 1, 'toast_message' => 'Samples in a worksheet cannot be deleted.']);
            return back();
        }
        if($covidSample->receivedstatus == 2){
            session(['toast_error' => 1, 'toast_message' => 'Rejected samples cannot be deleted.']);
            return back();
        }
        // $covidSample->travel()->delete();
        $covidSample->delete();
        session(['toast_message' => 'The sample has been deleted.']);
        return back();
    }

    public function cif_samples()
    {
        $samples = \App\Synch::get_covid_samples();
        return view('tables.cif_covid_samples', compact('samples'));
    }

    public function set_cif_samples(Request $request)
    {
        $samples = $request->input('sample_ids');
        if(!$samples){
            session(['toast_error' => 1, 'toast_message' => 'No samples selected.']);
            return back();            
        }
        \App\Synch::set_covid_samples($samples);
        session(['toast_message' => 'The sample have been set to come to the lab.']);
        return redirect('covid_sample');        
    }

    public function lab_sample_page()
    {
        return view('forms.upload_site_samples', ['url' => 'covid_sample/lab'])->with('pageTitle', 'Upload Covid Samples');
    }

    public function upload_lab_samples(Request $request)
    {
        // if(env('APP_LAB') != 1) abort(403);
        $file = $request->upload->path();
        $path = $request->upload->store('public/site_samples/covid');
        $lab_id = auth()->user()->lab_id;
        $c = null;
        if($lab_id == 1) $c = new NairobiCovidImport;
        else if($lab_id == 3) $c = new AlupeCovidImport;
        else if($lab_id == 4) $c = new WRPCovidImport;
        else if($lab_id == 5) $c = new AmpathCovidImport;
        else if($lab_id == 9) $c = new KNHCovidImport;
        else if($lab_id == 18 || $lab_id == 16) $c = new KemriWRPImport;
        Excel::import($c, $path);

        if(session('toast_error')) return back();

        $skipped_rows = session()->pull('skipped_rows');
        if($skipped_rows) return MiscCovid::csv_download($skipped_rows, 'rows-skipped-due-to-incompleteness');

        session(['toast_message' => "The samples have been created."]);
        return redirect('/covid_sample'); 
    }


    // Transfer Between Remote Labs
    public function transfer_samples_form($facility_id=null)
    {
        $samples = CovidSampleView::where('site_entry', '!=', 2)
                    ->when($facility_id, function($query) use($facility_id){
                        return $query->where('facility_id', $facility_id);
                    })
                    ->whereNull('datetested')
                    ->where(['repeatt' => 0])
                    ->where('created_at', '>', date('Y-m-d', strtotime("-3 months")))
                    ->paginate(500);

        $samples->setPath(url()->current());

        if($facility_id) $facility = \App\Facility::find($facility_id);

        $data = [
            'samples' => $samples,
            'labs' => \App\Lab::all(),
            'facility' => $facility ?? null,
            'pre' => 'covid_',
        ];

        return view('forms.transfer_samples', $data);
    }

    public function transfer_samples(Request $request)
    {
        $samples = $request->input('samples');
        $lab = $request->input('lab');
        // dd($samples);
        \App\Synch::transfer_sample('covid', $lab, $samples);
        return back();
    }


    public function transfer(Request $request)
    {
        $lab_id = $request->input('lab_id');
        $sample_ids = $request->input('sample_ids');
        if(!$lab_id){            
            session(['toast_message' => "Select a lab.", 'toast_error' => 1]);
            return back();
        }
        CovidSample::whereIn('id', $sample_ids)->update(['lab_id' => $lab_id]);         
        session(['toast_message' => "The samples have been transferred."]);
        return back();
    }

    public function result(CovidSample $covidSample)
    {
        $user = auth()->user();
        if(($user->facility_user && $covidSample->patient->facility_id != $user->facility_id) || ($user->quarantine_site && $covidSample->patient->quarantine_site_id != $user->facility_id)) abort(403);

        if(!$covidSample->datedispatched){
            session(['toast_error' => 1, 'toast_message' => 'The results have not yet been dispatched.']);
            return back();
        }
        if($covidSample->repeatt == 1){
            session(['toast_error' => 1, 'toast_message' => 'You cannot print a failed run.']);
            return back();            
        }

        $data = Lookup::covid_form();
        $data['samples'] = [$covidSample];
        $data['print'] = true;
        return view('exports.mpdf_covid_samples', $data);
    }

    public function print_multiple(Request $request)
    {
        $ids = $request->input('sample_ids');
        $data = Lookup::covid_form();
        if(!$ids){       
            session(['toast_message' => "Select the samples you intend to print.", 'toast_error' => 1]);
            return back();            
        }
        $data['samples'] = CovidSample::whereIn('id', $ids)->get();
        $data['print'] = true;
        return view('exports.mpdf_covid_samples', $data);
    }

    public function receive_multiple(Request $request)
    {
        $ids = $request->input('sample_ids');
        if(!$ids){       
            session(['toast_message' => "Select the samples you intend to receive.", 'toast_error' => 1]);
            return back();            
        }
        $samples = CovidSample::whereIn('id', $ids)->get();
        foreach ($samples as $key => $sample) {
            $sample->receivedstatus = 1;
            $sample->datereceived = date('Y-m-d');
            $sample->save();
        }
        session(['toast_message' => 'The samples have been marked as received.']);
        return back();
    }


    public function cities(Request $request)
    {
        $search = $request->input('search');
        $cities = City::whereRaw("(name like '%" . $search . "%')")
            ->paginate(10);
        return $cities;
    }



    public function search(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        $string = "(covid_patients.facility_id='{$user->facility_id}' OR covid_samples.user_id='{$user->id}')";

        $samples = CovidSample::select('covid_samples.id')
            ->whereRaw("covid_samples.id like '" . $search . "%'")
            ->when($user->facility_user, function($query) use ($string){
                return $query->join('covid_patients', 'covid_samples.patient_id', '=', 'covid_patients.id')->whereRaw($string);
            })
            ->when($user->quarantine_site, function($query) use ($user){
                return $query->join('covid_patients', 'covid_samples.patient_id', '=', 'covid_patients.id')
                    ->where('quarantine_site_id', $user->facility_id);
            })
            ->paginate(10);

        $samples->setPath(url()->current());
        return $samples;
    }
    

    public function kemri_id(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        $string = "(covid_patients.facility_id='{$user->facility_id}' OR covid_samples.user_id='{$user->id}')";

        $samples = CovidSample::selectRaw('covid_samples.id, kemri_id as patient')
            ->whereRaw("covid_samples.kemri_id like '" . $search . "%'")
            ->when($user->facility_user, function($query) use ($string){
                return $query->join('covid_patients', 'covid_samples.patient_id', '=', 'covid_patients.id')->whereRaw($string);
            })
            ->when($user->quarantine_site, function($query) use ($user){
                return $query->join('covid_patients', 'covid_samples.patient_id', '=', 'covid_patients.id')
                    ->where('quarantine_site_id', $user->facility_id);
            })
            ->where('repeatt', 0)
            ->paginate(10);

        $samples->setPath(url()->current());
        return $samples;
    }


    public function new_patient(Request $request)
    {
        $patient_name = $request->input('patient_name');
        $national_id = $request->input('national_id');
        $identifier = $request->input('identifier');
        $facility_id = $request->input('facility_id');
        $quarantine_site_id = $request->input('quarantine_site_id');


        $patient = null;
        if($national_id) $patient = CovidPatient::where('national_id', $national_id)->first();
        if(!$patient && !$identifier) return ['message' => null];
        if(!$patient && $facility_id){
            $patient = CovidPatient::where(['identifier' => $identifier, 'facility_id' => $facility_id])->first();
        }
        if(!$patient && $quarantine_site_id){
            $patient = CovidPatient::where(['identifier' => $identifier, 'quarantine_site_id' => $quarantine_site_id])->first();
        }

        if(!$patient && $patient_name){
            $sql = '';
            $matched_by_patient = true;
            $names = explode(' ', $patient_name);
            foreach ($names as $key => $name) {
                $n = addslashes($name);
                $sql .= "patient_name LIKE '%{$n}%' AND ";
            }
            $sql = substr($sql, 0, -4);
            $patient = CovidPatient::whereRaw($sql)->first();
        }


        if($patient){
            $patient->most_recent();
            if($patient->most_recent){
                $message = "This patient's most recent sample was collected on " . $patient->most_recent->datecollected->toFormattedDateString() . " <br />";

                if(isset($matched_by_patient) || env('APP_LAB') == 1){
                    $p = null;
                    $message .= "If this is not the same person as the current sample then proceed. <br />";
                }else{
                    $p = $patient;
                    $message .= "Any patient details entered will overwrite existing patient details i.e. patient name, facility and county of residence but not sample details e.g. date collected and result <br />";
                    $message .= "If it is a different sample of the same patient then proceed. <br />";
                }

                $message .= "                
                Name {$patient->patient_name} <br />
                Identifier {$patient->identifier} <br />
                National ID {$patient->national_id} ";
                return ['message' => $message, 'patient' => $p];
            }
        }
        return ['message' => null];
    }


    public function cif_patient(Request $request)
    {
        $patient_name = $request->input('patient_name');
        $samples = \App\Synch::get_covid_samples($patient_name);
        $div = \Str::random(20);
        if($samples){
            return view('tables.cif_samples_partial', compact('samples', 'div'));
        }
        // abort(404);
        return null;
    }
}
