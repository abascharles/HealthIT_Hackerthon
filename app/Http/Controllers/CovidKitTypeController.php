<?php

namespace App\Http\Controllers;

use App\CovidKitType;
use App\Machine;
use Illuminate\Http\Request;

class CovidKitTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $covid_kit_types = CovidKitType::with(['machine'])->get();
        return view('tables.covid_kit_types', compact('covid_kit_types'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $machines = Machine::all();
        return view('forms.covid_kit_type', compact('machines'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $covidKitType = new CovidKitType;
        $covidKitType->fill($request->all());
        $covidKitType->save();
        session(['toast_message' => 'The covid kit type has been created.']);
        return back();        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CovidKitType  $covidKitType
     * @return \Illuminate\Http\Response
     */
    public function show(CovidKitType $covidKitType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CovidKitType  $covidKitType
     * @return \Illuminate\Http\Response
     */
    public function edit(CovidKitType $covidKitType)
    {
        $machines = Machine::all();
        return view('forms.covid_kit_type', compact('machines', 'covidKitType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CovidKitType  $covidKitType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CovidKitType $covidKitType)
    {
        $covidKitType->fill($request->all());
        $covidKitType->save();
        session(['toast_message' => 'The covid kit type has been created.']);
        return redirect('covid_kit_type');        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CovidKitType  $covidKitType
     * @return \Illuminate\Http\Response
     */
    public function destroy(CovidKitType $covidKitType)
    {
        //
    }
}
