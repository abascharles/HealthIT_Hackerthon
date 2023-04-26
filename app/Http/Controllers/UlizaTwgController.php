<?php

namespace App\Http\Controllers;

use App\UlizaTwg;
use App\County;
use Illuminate\Http\Request;

class UlizaTwgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $twgs = UlizaTwg::with(['county'])->get();
        return view('uliza.tables.twgs', compact('twgs'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $counties = County::orderBy('name', 'asc')->get();
        return view('uliza.forms.twg', compact('counties'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ulizaTwg = new UlizaTwg;
        $ulizaTwg->fill($request->except(['counties']));
        $ulizaTwg->save();

        $counties = $request->input('counties');
        if($counties) County::whereIn('id', $counties)->update(['twg_id' => $ulizaTwg->id]);
        session(['toast_message' => 'The TWG has been created']);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UlizaTwg  $ulizaTwg
     * @return \Illuminate\Http\Response
     */
    public function show(UlizaTwg $ulizaTwg)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UlizaTwg  $ulizaTwg
     * @return \Illuminate\Http\Response
     */
    public function edit(UlizaTwg $ulizaTwg)
    {
        $counties = County::orderBy('name', 'asc')->get();
        return view('uliza.forms.twg', compact('counties', 'ulizaTwg'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UlizaTwg  $ulizaTwg
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UlizaTwg $ulizaTwg)
    {
        $ulizaTwg->fill($request->except(['counties']));
        $ulizaTwg->save();

        County::where(['twg_id' => $ulizaTwg->id])->update(['twg_id' => null]);
        $counties = $request->input('counties');
        if($counties) County::whereIn('id', $counties)->update(['twg_id' => $ulizaTwg->id]);
        session(['toast_message' => 'The TWG has been updated']);
        return redirect('uliza-twg');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UlizaTwg  $ulizaTwg
     * @return \Illuminate\Http\Response
     */
    public function destroy(UlizaTwg $ulizaTwg)
    {
        //
    }
}
