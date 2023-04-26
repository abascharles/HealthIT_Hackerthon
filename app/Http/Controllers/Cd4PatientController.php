<?php

namespace App\Http\Controllers;

use App\Cd4Patient;
use App\Cd4Sample;
use App\Lookup;
use Illuminate\Http\Request;

class Cd4PatientController extends Controller
{
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
     * @param  \App\Cd4Patient  $cd4Patient
     * @return \Illuminate\Http\Response
     */
    public function show(Cd4Patient $cd4Patient)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Cd4Patient  $cd4Patient
     * @return \Illuminate\Http\Response
     */
    public function edit(Cd4Patient $cd4Patient)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Cd4Patient  $cd4Patient
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cd4Patient $cd4Patient)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Cd4Patient  $cd4Patient
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cd4Patient $cd4Patient)
    {
        //
    }

    public function search_name(Request $request, $recordno=null) {
        if($request->method() == "POST"){
            $search = $request->input('search');
            $patients = Cd4Patient::where('patient_name', 'like', '%'.$search.'%')->paginate(10);
            
            $patients->setPath(url()->current());
            return $patients;
        } else {
            $patient = Cd4Patient::where('medicalrecordno', '=', $recordno)->first();
            $samples = Cd4Sample::where('patient_id', '=', $patient->id)->orderBy('datecollected', 'desc')->paginate(20);

            $data = Lookup::cd4_lookups();
            $data['samples'] = $samples;
            $data = (object) $data;
            // dd($data);
            return view('tables.cd4-samples', compact('data'))->with('pageTitle', 'Samples Summary');
        }
        
    }

    public function search_record_no(Request $request, $recordno=null) {
        if($request->method() == "POST"){
            $search = $request->input('search');
            $patients = Cd4Patient::where('medicalrecordno', 'like', '%'.$search.'%')->paginate(10);
            
            $patients->setPath(url()->current());
            return $patients;
        } else {
            $patient = Cd4Patient::where('medicalrecordno', '=', $recordno)->first();
            $samples = Cd4Sample::where('patient_id', '=', $patient->id)->orderBy('datecollected', 'desc')->paginate(20);

            $data = Lookup::cd4_lookups();
            $data['samples'] = $samples;
            $data = (object) $data;
            // dd($data);
            return view('tables.cd4-samples', compact('data'))->with('pageTitle', 'Samples Summary');
        }
        
    }

    public function new_patient(Request $request)
    {
        echo json_encode(Cd4Patient::where('medicalrecordno', '=', $request->input('patient'))->first());
    }
}
