<?php

namespace App\Http\Controllers;

use App\Muser;
use Illuminate\Http\Request;

class MuserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $musers = Muser::all();
        return view('tables.musers', ['musers' => $musers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('forms.musers');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $muser = new Muser;
        $muser->fill($request->except(['_token']));
        $muser->save();
        session(['toast_message' => 'The user has been added to the list of users receiving alerts.']);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Muser  $muser
     * @return \Illuminate\Http\Response
     */
    public function show(Muser $muser)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Muser  $muser
     * @return \Illuminate\Http\Response
     */
    public function edit(Muser $muser)
    {
        return view('forms.musers', ['muser' => $muser]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Muser  $muser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Muser $muser)
    {
        $muser->fill($request->except(['_token']));
        $muser->save();
        session(['toast_message' => 'The user has been updated.']);
        return redirect('/muser');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Muser  $muser
     * @return \Illuminate\Http\Response
     */
    public function destroy(Muser $muser)
    {
        $muser->delete();
        session(['toast_message' => 'The user has been deleted.']);
        return back();
    }
}
