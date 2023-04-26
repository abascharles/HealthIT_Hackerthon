<?php

namespace App\Http\Controllers;

use App\Cd4Sample;
use App\Cd4Patient;
use Illuminate\Http\Request;
use App\Lookup;
use App\ViewFacility;

class Cd4SampleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        ini_set("memory_limit", "-1");
        $data = Lookup::cd4_lookups();
        $data['samples'] = Cd4Sample::orderBy('id', 'desc')->orderBy('datecollected', 'desc')->paginate(20);
        $data = (object) $data;
        // dd($data);
        return view('tables.cd4-samples', compact('data'))->with('pageTitle', 'Samples Summary');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = Lookup::cd4sample_form();
        return view('forms.cd4samples', $data)->with('pageTitle', 'Add CD4 Sample');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $checknew = $request->input('new_patient');
        $sampleData = $request->except(['_token','medicalrecordno','patient_name','dob','sex','submit_type','new_patient']);
        $sample = new Cd4Sample();
        $sample->user_id = auth()->user()->id;
        if ($checknew == 0) {
            $patientData = $request->only(['medicalrecordno','patient_name','dob','sex']);
            $patient = new Cd4Patient();
            $patient->fill($patientData);
            $patient->save();
            $sampleData['patient_id'] = $patient->id;
        }

        if ($request->input('receivedstatus') == 2) { //If rejected set status to rejected also
            $sampleData['status_id'] = $request->input('receivedstatus');
        } else {
            $sampleData['status_id'] =  1;
        }
        
        $sample->fill($sampleData);
        $sample->save();
        if ($sample) {
            session(['toast_message'=>'Sample Created Successfully']);
        } else {
            session(['toast_message'=>'Sample creation failed', 'toast_error' => 1]);
        }
        
        if ($request->input('submit_type') == 'add') {
            return back();
        } else {
            return redirect('cd4/sample');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Cd4Sample  $cd4Sample
     * @return \Illuminate\Http\Response
     */
    public function show(Cd4Sample $sample)
    {
        // $data = Lookup::cd4sample_form();
        // $data['sample'] = $sample;
        // $data['view'] = true;
        
        // return view('forms.cd4samples', $data)->with('pageTitle', 'View CD4 Sample');

        $samples[] = $sample;
        
        $data = Lookup::cd4_lookups();
        $data['samples'] = $samples;
        $data['search'] = true;
        $data = (object) $data;
        // dd($data);
        return view('tables.cd4-samples', compact('data'))->with('pageTitle', 'Samples Summary');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cd4Sample  $cd4Sample
     * @return \Illuminate\Http\Response
     */
    public function edit(Cd4Sample $cd4Sample)
    {
        $data = Lookup::cd4sample_form();
        $data['sample'] = $cd4Sample->first();
        
        return view('forms.cd4samples', $data)->with('pageTitle', 'Edit CD4 Sample');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cd4Sample  $cd4Sample
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cd4Sample $cd4Sample)
    {
        $sample = $cd4Sample->first(); // Get the sample collection

        //Differentiating the data sets
        $sampleData = $request->except(['_token','medicalrecordno','patient_name','dob','sex','submit_type','new_patient']);
        $patientData = $request->only(['medicalrecordno','patient_name','dob','sex']);

        //Check for changes in the patient data then update
        if ($patientData['medicalrecordno'] == $sample->patient->medicalrecordno) {
            $sample->patient->fill($patientData);
            $sample->patient->save();
        } else {
            $patient = new Cd4Patient();
            $patient->fill($patientData);
            $patient->save();
            $sampleData['patient_id'] = $patient->id;
        }

        if ($request->input('receivedstatus') == 2) { //If rejected set status to rejected also
            $sampleData['status_id'] = $request->input('receivedstatus');
        } else { //If accepted, check if previously rejected
            if($sample->receivedstatus == 2){
                $sampleData['status_id'] = 1;
                $sampleData['rejectedreason'] = null;
            }
        }
        
        //Update sample data
        $sample->fill($sampleData);
        $sample->save();

        if($sample){
            session(['toast_message'=>'Sample update Successful']);
        } else {
            session(['toast_message'=>'Sample update failed', 'toast_error'=>1]);
        }

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cd4Sample  $cd4Sample
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cd4Sample $sample)
    {
        if(($sample->status_id == 1 || $sample->status_id == 2) && $sample->datetested == null){
            $sample->delete();
            session(['toast_message' => 'Sample Successfully deleted']);
        } else {
            abort(403);
        }
        return back();
    }

    public function dispatch($state=null){
        $data = Lookup::cd4_lookups();
        $data['samples'] = Cd4Sample::when($state, function($query) use ($state) {
                            if($state == 1)
                                return $query->where('status_id', '=', 5);
                            if($state == 2)
                                return $query->where('status_id', '=', 6);
                            if($state == 3)
                                return $query->where('status_id', '=', 1);
                        })->where('repeatt', '=', 0)->paginate(20);
        $data = (object) $data;
        // dd($data);
        return view('tables.cd4-samples', compact('data'))->with('pageTitle', 'Samples Summary');
    }

    /**
     * Print the specified resource from storage.
     *
     * @param  \App\Cd4Sample  $sample
     * @return \Illuminate\Http\Response
     */
    public function print(Cd4Sample $sample){
        $sample->dateresultprinted = gmdate('Y-m-d');
        $sample->printedby = auth()->user()->id;
        $sample->status_id = 6;
        $sample->save();

        return redirect('cd4/sample/printresult/'.$sample->id);
    }

    public function printresult(Cd4Sample $sample){
        // dd($sample);
        return view('exports.cd4_sample', compact('sample'));
    }

    public function facility($facility){
        ini_set("memory_limit", "-1");
        $data = Lookup::cd4_lookups();
        $data['samples'] = Cd4Sample::where('facility_id', '=', $facility)->paginate(20);
        $facility = ViewFacility::find($facility);
        $data = (object) $data;
        
        return view('tables.cd4-samples', compact('data'))->with('pageTitle', $facility->name.' Samples');
    }

    public function search(Request $request){
        $search = $request->input('search');
        $samples = Cd4Sample::where('id', 'like', '%'.$search.'%')->paginate(10);

        $samples->setPath(url()->current());
        return $samples;
    }

    public function searchresult(Cd4Sample $sample) {
        $samples[] = $sample;
        
        $data = Lookup::cd4_lookups();
        $data['samples'] = $samples;
        $data['search'] = true;
        $data = (object) $data;
        // dd($data);
        return view('tables.cd4-samples', compact('data'))->with('pageTitle', 'Samples Summary');
    }
}
