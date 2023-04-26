<?php

namespace App\Http\Controllers;

use DB;
use App\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $columns = parent::_columnBuilder(['#', 'Name', 'County', 'Contact Person 1', 'Position', 'Email', 'Contact Person 2', 'Position', 'Email', 'Edit Contacts']);
        $districts = District::select('districts.*', 'countys.name as county', 'provinces.name as province')
                ->join('countys', 'countys.id', '=', 'districts.county')
                ->join('provinces', 'provinces.id', '=', 'districts.province')
                ->get();
        $table = '';
        foreach ($districts as $key => $value) {
            $table .= '<tr>';
            $table .= '<td>'.($key+1).'</td>';
            $table .= '<td>'.$value->name.'</td>';
            $table .= '<td>'.$value->county.'</td>';
            $table .= '<td>'.$value->subcounty_person1.'</td>';
            $table .= '<td>'.$value->subcounty_position1.'</td>';
            $table .= '<td>'.$value->subcounty_email1.'</td>';
            $table .= '<td>'.$value->subcounty_person2.'</td>';
            $table .= '<td>'.$value->subcounty_position2.'</td>';
            $table .= '<td>'.$value->subcounty_email2.'</td>';
            $table .= "<td> <a href='".url('district/' . $value->id . '/edit')."'>Edit Contacts </a></td>";
            $table .= '</tr>';
        }
        return view('tables.display', ['row' => $table, 'columns' => $columns])->with('pageTitle', 'Districts');
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
    public function edit(District $district)
    {
        return view('forms.districts', compact('district'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, District $district)
    {
        $district->fill($request->all());
        $district->save();
        session(['toast_message' => 'The subcounty contact details have been updated.']);
        return redirect('district');
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



    public function search(Request $request)
    {
        $search = $request->input('search');
        $district = DB::table('districts')->select('id', 'name')
            ->whereRaw("(name like '%" . $search . "%')")
            ->paginate(10);
        return $district;
    }
}
