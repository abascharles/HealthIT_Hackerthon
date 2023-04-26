<?php

namespace App\Http\Controllers;

use App\CovidPatient;
use App\CovidSample;
use App\CovidSampleView;
use App\Lookup;
use Illuminate\Http\Request;

use DB;

class CovidPatientController extends Controller
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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CovidPatient  $covidPatient
     * @return \Illuminate\Http\Response
     */
    public function show(CovidPatient $covidPatient)
    {
        $user = auth()->user();
        $type = 1;

        $samples = CovidSampleView::select(['covid_sample_view.*', 'u.surname', 'u.oname', 'r.surname as rsurname', 'r.oname as roname'])
            ->leftJoin('users as u', 'u.id', '=', 'covid_sample_view.user_id')
            ->leftJoin('users as r', 'r.id', '=', 'covid_sample_view.received_by')
            ->where('patient_id', $covidPatient->id)
            ->orderBy('id', 'desc')
            ->when(($user->user_type_id == 5), function($query) use ($user){
                return $query->whereRaw("(user_id='{$user->id}' OR covid_sample_view.facility_id='{$user->facility_id}')");
            })
            ->when($user->quarantine_site, function($query) use ($user){
                return $query->where('quarantine_site_id', $user->facility_id);
            })
            ->get();
        $myurl = url('/covid_sample/index/' . $type);
        $myurl2 = url('/covid_sample/index/');        
        $results = DB::table('results')->get();
        return view('tables.covidsamples', compact('samples', 'myurl', 'myurl2', 'type', 'covidPatient', 'results'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CovidPatient  $covidPatient
     * @return \Illuminate\Http\Response
     */
    public function edit(CovidPatient $covidPatient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CovidPatient  $covidPatient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CovidPatient $covidPatient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CovidPatient  $covidPatient
     * @return \Illuminate\Http\Response
     */
    public function destroy(CovidPatient $covidPatient)
    {
        //
    }


    public function search(Request $request, $facility_id=null)
    {
        $user = auth()->user();
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        $string = "(facility_id='{$user->facility_id}')";

        $search = $request->input('search');
        $search = addslashes($search);
        
        $patients = CovidPatient::select('covid_patients.id', 'covid_patients.identifier AS patient', 'quarantine_sites.name')
            ->leftJoin('quarantine_sites', 'quarantine_sites.id', '=', 'covid_patients.quarantine_site_id')
            ->whereRaw("(identifier like '" . $search . "%' OR patient_name like '%" . $search . "%' )")
            // ->where('patients.synched', '!=', 2)
            ->when($user->quarantine_site, function($query) use ($user){
                return $query->where('quarantine_site_id', $user->facility_id);
            })
            ->when($facility_id, function($query) use ($facility_id){
                return $query->where('facility_id', $facility_id);
            })
            ->paginate(10);

        $patients->setPath(url()->current());
        return $patients;

    }


    public function national_id(Request $request, $facility_id=null)
    {
        $user = auth()->user();
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        $string = "(facility_id='{$user->facility_id}')";

        $search = $request->input('search');
        $search = addslashes($search);
        
        $patients = CovidPatient::select('covid_patients.id', 'covid_patients.national_id AS patient', 'quarantine_sites.name')
            ->leftJoin('quarantine_sites', 'quarantine_sites.id', '=', 'covid_patients.quarantine_site_id')
            ->whereRaw("(national_id like '" . $search . "%' )")
            // ->where('patients.synched', '!=', 2)
            ->when($user->quarantine_site, function($query) use ($user){
                return $query->where('quarantine_site_id', $user->facility_id);
            })
            ->when($facility_id, function($query) use ($facility_id){
                return $query->where('facility_id', $facility_id);
            })
            ->paginate(10);

        $patients->setPath(url()->current());
        return $patients;

    }
}
