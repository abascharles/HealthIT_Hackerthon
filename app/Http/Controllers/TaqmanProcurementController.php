<?php

namespace App\Http\Controllers;

use App\Taqmanprocurement;
use Illuminate\Http\Request;

class TaqmanProcurementController extends Controller
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function recomputeending($id) {
        $procurement = Taqmanprocurement::find($id);
        $qualkit = $procurement->endingqualkit;
        
        foreach ($this->taqmanKits as $key => $kits) {
            $column = 'ending'.$kits['alias'];
            $procurement->$column = $qualkit * $kits['factor'];
        }
        $procurement->save();
        $string = "Update of taqman ending balances failed";
        if ($procurement)
            $string = "Update of taqman ending balances successful";

        print($string);
    }
}
