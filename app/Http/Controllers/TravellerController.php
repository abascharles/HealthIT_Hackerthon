<?php

namespace App\Http\Controllers;

use App\Traveller;
use App\Datatable;
use DB;
use Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TravellerImport;
use Illuminate\Http\Request;

class TravellerController extends Controller
{

    /*public $patient_sms_columns = [
        ['db' => 'id', 'dt' => 'DT_RowId' ],
        ['db' => 'patient_name', 'dt' => 0 ],
        ['db' => 'id_passport', 'dt' => 1 ],
        ['db' => 'sex', 'dt' => 2 ],
        ['db' => 'age', 'dt' => 3 ],
        ['db' => 'datecollected', 'dt' => 4 ],
        ['db' => 'datereceived', 'dt' => 5, ],
        ['db' => 'datetested', 'dt' => 6, ],
        ['db' => 'datedispatched', 'dt' => 7, ],
        ['db' => 'result', 'dt' => 8, ],
        ['db' => 'igm_result', 'dt' => 9, ],
        ['db' => 'igg_igm_result', 'dt' => 10, ],
    ];*/

    public $patient_sms_columns = [
        ['db' => 'id', 'dt' => 0 ],
        ['db' => 'patient_name', 'dt' => 1 ],
        ['db' => 'id_passport', 'dt' => 2 ],
        ['db' => 'sex', 'dt' => 3 ],
        ['db' => 'age', 'dt' => 4 ],
        ['db' => 'datecollected', 'dt' => 5 ],
        ['db' => 'datereceived', 'dt' => 6, ],
        ['db' => 'datetested', 'dt' => 7, ],
        ['db' => 'datedispatched', 'dt' => 8, ],
        ['db' => 'result', 'dt' => 9, ],
        ['db' => 'igm_result', 'dt' => 10, ],
        ['db' => 'igg_igm_result', 'dt' => 11, ],
    ];

    public function filter(Request $request)
    {
        $draw = $request->input('draw');
        $search = $request->input('search');
        if($search && $search['value'] != '' && strlen($search['value']) < 2){
            return [
                'draw' => $draw ? intval($draw) : 0,
                'recordsTotal' => $recordsTotal ?? 0,
                'recordsFiltered' => $recordsFiltered ?? 0,
                'data' => [],
            ];
        }

        $query = Traveller::select(array_column($this->patient_sms_columns, 'db'));
        Datatable::limit($request, $query);
        Datatable::order($request, $query, $this->patient_sms_columns);
        Datatable::filter($request, $query, $this->patient_sms_columns);

        // DB::enableQueryLog();
        $rows = $query->get();
        $data = [];

        foreach ($rows as $row) {
            $d = [];
            foreach ($row->toArray() as $key => $value) {
                if($key == 'id'){
                    $d['DT_RowId'] = 'row_' . $value; 
                    $d['id'] = $value;
                }
                else{
                    if(Str::contains($key, 'result')){
                        $col = $key . '_name';
                        $d[$key] = $row->$col;
                    }
                    else if($key == 'sex'){
                        $d[$key] = $row->gender;
                    }
                    else{
                        $d[$key] = $value;
                    }
                }
            }
            $d['action'] = $row->edit_link . "| <br /> <a href='/traveller/{$row->id}'> Result </a> ";
            $data[] = $d;
        }

        // Records total
        $recordsTotal = Traveller::selectRaw('COUNT(id) as my_count')->first()->my_count;

        // Records filtered
        $query = Traveller::selectRaw('COUNT(id) as my_count');
        Datatable::filter($request, $query, $this->patient_sms_columns);
        $recordsFiltered = $query->first()->my_count;

        return [
            'draw' => $draw ? intval($draw) : 0,
            'recordsTotal' => $recordsTotal ?? 0,
            'recordsFiltered' => $recordsFiltered ?? 0,
            'data' => $data,
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $results = DB::table('results')->get();
        // $samples = Traveller::orderBy('id', 'desc')->paginate();
        // return view('tables.travellers', compact('samples', 'results'));
        return view('tables.travellers');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('forms.upload_travellers');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $filename_array = explode('.', $request->file('upload')->getClientOriginalName());
        $file_name =  \Str::random(40) . '.' . array_pop($filename_array);
        $path = $request->upload->storeAs('public/site_samples/traveller', $file_name); 
        $travel_import = new TravellerImport;
        Excel::import($travel_import, $path);
        if(session('toast_error')) return back();
        session(['toast_message' => 'The upload has been made.']);
        return redirect('/traveller'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Traveller  $traveller
     * @return \Illuminate\Http\Response
     */
    public function show(Traveller $traveller)
    {
        $results = DB::table('results')->get();
        return view('exports.mpdf_traveller_samples', ['samples' => [$traveller], 'print' => true]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Traveller  $traveller
     * @return \Illuminate\Http\Response
     */
    public function edit(Traveller $traveller)
    {
        $results = DB::table('results')->get();
        return view('forms.traveller', compact('traveller', 'results'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Traveller  $traveller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Traveller $traveller)
    {
        $traveller->fill($request->all());
        $traveller->save();
        session(['toast_message' => 'The update has been made.']);
        return redirect('traveller');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Traveller  $traveller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Traveller $traveller)
    {
        $traveller->delete();
        session(['toast_message' => 'The deletion has been made.']);
        return redirect('traveller');
    }


    public function print_multiple(Request $request)
    {
        $date_column = 'datetested';
        $date_start = $request->input('from_date');
        $date_end = $request->input('to_date');
        $samples = Traveller::
            when($date_start, function($query) use ($date_column, $date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate($date_column, '>=', $date_start)
                    ->whereDate($date_column, '<=', $date_end);
                }
                return $query->whereDate($date_column, $date_start);
            })
            ->get();
        return view('exports.mpdf_traveller_samples', ['samples' => $samples, 'print' => true]);
    }






    
}
