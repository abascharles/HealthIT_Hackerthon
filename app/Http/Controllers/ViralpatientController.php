<?php

namespace App\Http\Controllers;

use App\utils\HttpsRequest;
use App\Viralpatient;
use App\Viralsample;
use App\DrSample;
use App\Lookup;
use Illuminate\Http\Request;

class ViralpatientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($facility_id=null)
    {
        $user = auth()->user();
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;

        // if(!$facility_id && !$facility_user) abort(404);

        $patients = Viralpatient::with(['facility'])
        ->withCount(['sample' => function ($query){
            $query->where('repeatt', 0);
        } ])
        ->when($facility_user, function($query) use ($user){
            return $query->where('facility_id', $user->facility_id);
        })
        ->when($facility_id, function($query) use ($facility_id){
            return $query->where('facility_id', $facility_id);
        })
        ->get();

        $data = Lookup::get_viral_lookups();
        $data['patients'] = $patients;
        return view('tables.viralpatients', $data);
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
     * @param  \App\Viralpatient  $viralpatient
     * @return \Illuminate\Http\Response
     */
    public function show(Viralpatient $viralpatient)
    {
        $samples = $viralpatient->sample;
        $viralpatient->load(['facility']);
        // $samples->load(['batch']);
        $data = Lookup::get_viral_lookups();
        $data['samples'] = $samples;
        $data['patient'] = $viralpatient;

        return view('tables.viralpatient_samples', $data)->with('pageTitle', 'Patient Samples');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Viralpatient  $viralpatient
     * @return \Illuminate\Http\Response
     */
    public function edit(Viralpatient $viralpatient)
    {
        $viralpatient->load(['facility']);
        $data = Lookup::get_viral_lookups();
        $data['patient'] = $viralpatient;

        return view('forms.viralpatients', $data)->with('pageTitle', 'Patient');         
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Viralpatient  $viralpatient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Viralpatient $viralpatient)
    {
        $viralsamples_arrays = Lookup::viralsamples_arrays();
        $data = $request->only($viralsamples_arrays['patient']);
        $viralpatient->fill($data);
        $viralpatient->pre_update();

        session(['toast_message' => "The patient has been updated."]);

        return redirect('viralpatient/index/' . $viralpatient->facility_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Viralpatient  $viralpatient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Viralpatient $viralpatient)
    {
        //
    }

    public function merge(Viralpatient $patient)
    {
        $patient->load(['facility']);
        $url = url('viralpatient/search/' . $patient->facility->id);
        $submit_url = url()->current();
        return view('forms.merge_patients', ['patient' => $patient, 'url' => $url, 'submit_url' => $submit_url]);
    }

    public function merge_patients(Request $request, Viralpatient $patient)
    {
        $patients = $request->input('patients');

        $samples = Viralsample::whereIn('patient_id', $patients)->get();

        foreach ($samples as $key => $sample) {
            $sample->patient_id = $patient->id;
            $sample->pre_update();
        }

        $patient_array = Viralpatient::whereIn('id', $patients)->where('id', '!=', $patient->id)->update(['synched' => 3]);

        session(['toast_message' => "The patient records have been merged. The records will be propagated to the national database and then they will be removed."]);

        return redirect('viralpatient/index/' . $patient->facility_id);
    }

    public function transfer(Viralpatient $patient)
    {
        $patient->load(['facility']);
        $submit_url = url()->current();
        return view('forms.transfer_patient', ['patient' => $patient, 'submit_url' => $submit_url]);
    }

    public function transfer_patient(Request $request, Viralpatient $patient)
    {
        $prev_facility_id = $patient->facility_id;
        $patient->facility_id = $request->input('facility_id');
        $patient->pre_update();

        session(['toast_message' => "The patient has been transferred to another facility."]);

        return redirect('viralpatient/index/' . $prev_facility_id);
    }

    public function dr(Viralpatient $patient)
    {        
        $data = Lookup::get_dr();
        $data['dr_samples'] = DrSample::select(['dr_samples.*'])
            ->with(['patient.facility'])
            ->where(['control' => 0, 'repeatt' => 0, 'dr_samples.patient_id' => $patient->id])
            ->orderBy('id', 'desc')
            ->paginate();

        $data['dr_samples']->setPath(url()->current());
        $data['sample_status'] = null;
        $data = array_merge($data, Lookup::get_partners());
        return view('tables.dr_samples', $data)->with('pageTitle', 'Drug Resistance Patient Samples'); 
    }

    public function search(Request $request, $facility_id=null, $female=false)
    {
        $user = auth()->user();

        $string = null;
        if($user->is_facility) $string = "(facility_id='{$user->facility_id}')";
        else if($user->is_partner) $string = "(facilitys.partner='{$user->facility_id}')";
        

        $search = $request->input('search');
        $search = addslashes($search);
        $query = '';
        if (str_contains($search, "REC")) {
            $query = "viralsamples.recency_number like '" . $search . "%'";
        } else $query = "patient like '" . $search . "%'";

        $patients = Viralpatient::select('viralpatients.id', 'viralpatients.patient', 'facilitys.name', 'facilitys.facilitycode')
            ->join('facilitys', 'facilitys.id', '=', 'viralpatients.facility_id')
            ->join('viralsamples', 'patient_id', '=', 'viralpatients.id')
            ->whereRaw($query)
            // ->where('viralpatients.synched', '!=', 2)
            ->when($string, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->when($facility_id, function($query) use ($facility_id){
                return $query->where('facility_id', $facility_id);
            })
            ->when($female, function($query){
                return $query->where('sex', 2);
            })
            ->paginate(10);

        $patients->setPath(url()->current());
        return $patients;
    }

    public function nat_id(Request $request)
    {
        $user = auth()->user();

        $string = null;
        if($user->is_facility) $string = "(facility_id='{$user->facility_id}')";
        else if($user->is_partner) $string = "(facilitys.partner='{$user->facility_id}')";

        $search = addslashes($request->input('search'));
        
        $patients = Viralpatient::select('viralpatients.id', 'viralpatients.patient', 'viralpatients.nat', 'facilitys.name', 'facilitys.facilitycode')
            ->join('facilitys', 'facilitys.id', '=', 'viralpatients.facility_id')
            ->whereRaw("nat like '%" . $search . "%'")
            // ->where('viralpatients.synched', '!=', 2)
            ->when($string, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->paginate(10);

        $patients->transform(function($patient){
            return [
                'id' => $patient->id,
                'patient' => "{$patient->nat} - ({$patient->patient})",
            ];
        });

        $patients->setPath(url()->current());
        return $patients;
    }

    public function cnt_id(Request $request)
    {
        $user = auth()->user();

        $string = null;
        if($user->is_facility) $string = "(facility_id='{$user->facility_id}')";
        else if($user->is_partner) $string = "(facilitys.partner='{$user->facility_id}')";

        $search = addslashes($request->input('search'));
        
        $patients = Viralpatient::select('viralpatients.id', 'viralpatients.patient', 'viralpatients.cnt', 'facilitys.name', 'facilitys.facilitycode')
            ->join('facilitys', 'facilitys.id', '=', 'viralpatients.facility_id')
            ->whereRaw("cnt like '" . $search . "%'")
            // ->where('viralpatients.synched', '!=', 2)
            ->when($string, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->paginate(10);

        $patients->transform(function($patient){
            return [
                'id' => $patient->id,
                'patient' => "{$patient->cnt} - ({$patient->patient})",
            ];
        });

        $patients->setPath(url()->current());
        return $patients;
    }
    public function patient_verify($upi_no)
    {
//        $ac = HttpsRequest::generateAccessToken();
        $patient = HttpsRequest::search_client_upi($upi_no);
        return response()->json($patient);

    }
}
