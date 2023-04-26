<?php

namespace App\Http\Controllers;

use App\QuarantineSite;
use Illuminate\Http\Request;

class QuarantineSiteController extends Controller
{

    public function __construct(){
        // if(!in_array(env('APP_LAB'), [1,2,3,6])) abort(403);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quarantine_sites = QuarantineSite::all();
        return view('tables.quarantine_sites', compact('quarantine_sites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('forms.quarantine_site');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $quarantineSite = QuarantineSite::where($request->only('name'))->first();
        if(!$quarantineSite) $quarantineSite = new QuarantineSite;
        $quarantineSite->fill($request->only(['name', 'email']));
        $quarantineSite->pre_update();
        session(['toast_message' => 'The quarantine site has been created.']);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\QuarantineSite  $quarantineSite
     * @return \Illuminate\Http\Response
     */
    public function show(QuarantineSite $quarantineSite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\QuarantineSite  $quarantineSite
     * @return \Illuminate\Http\Response
     */
    public function edit(QuarantineSite $quarantineSite)
    {
        return view('forms.quarantine_site', ['quarantine_site' => $quarantineSite]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\QuarantineSite  $quarantineSite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuarantineSite $quarantineSite)
    {
        $quarantineSite->fill($request->only(['name', 'email']));
        $quarantineSite->pre_update();
        session(['toast_message' => 'The quarantine site has been updated.']);
        return redirect('/quarantine_site');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QuarantineSite  $quarantineSite
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuarantineSite $quarantineSite)
    {
        //
    }
}
