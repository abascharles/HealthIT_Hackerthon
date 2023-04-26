<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Abbotdeliveries;

class AbbottDeliveriesController extends Controller
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

    public function restore($id) {
        $delivery = Abbotdeliveries::onlyTrashed()->find($id);
        if(null !== $delivery){
            $delivery->restore();
            if($delivery) { print("Successfully restored the delivery entry"); } 
            else { print("Restoration of the delivery entry failed"); }
        } else {
            print("This delivery is not deleted");
        }
    }

    public function delete($id){
        $this->destroy($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delivery = Abbotdeliveries::find($id);
        if(null !== $delivery){
            $delivery->delete();
            if($delivery) { print("Successfully deleted the delivery entry"); } 
            else { print("Deletion of the delivery entry failed"); }
        } else {
            print("This delivery does not exist or was soft deleted");
        }        
    }

    public function recompute($id) {
        $delivery = Abbotdeliveries::find($id);
        dd($delivery);
    }
}
