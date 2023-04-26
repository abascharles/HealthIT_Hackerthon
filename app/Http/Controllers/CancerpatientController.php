<?php

namespace App\Http\Controllers;

use App\CancerPatient;
use App\CancerSampleView;
use Illuminate\Http\Request;

class CancerpatientController extends Controller
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
        $patient = CancerPatient::find($id);
        
        $user = auth()->user();
        $facility_user = false;
        if ($user->facility_id)
            $facility_user = true;
        $samples = CancerSampleView::with(['facility', 'worksheet', 'user' => function($query) use ($facility_user) {
                                    $query->when(!$facility_user, function($query) {
                                            return $query->whereNotIn('users.user_type_id', [5]);
                                    });
                                }])
                                ->when($facility_user, function($query) use ($user) {
                                    return $query->where('facility_id', $user->facility_id)
                                                ->orWhere('user_id', $user->id);
                                })
                                ->where('patient_id', $patient->id)
                                ->orderBy('created_at', 'DESC')->paginate();
        
        $data['samples'] = $samples;
        $data['param'] = false;
        
        return view('tables.cancer_samples', $data)->with('pageTitle', 'HPV Search Patient Samples');
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

    public function search(Request $request/*, $facility_id=null, $female=false*/)
    {
        $user = auth()->user();

        $string = null;
        if($user->is_facility) $string = "(facility_id='{$user->facility_id}')";
        else if($user->is_partner) $string = "(facilitys.partner='{$user->facility_id}')";
        

        $search = $request->input('search');
        $search = addslashes($search);
        
        $patients = CancerPatient::select('cancer_patients.id', 'cancer_patients.patient', 'facilitys.name', 'facilitys.facilitycode')
            ->join('facilitys', 'facilitys.id', '=', 'cancer_patients.facility_id')
            ->whereRaw("patient like '" . $search . "%'")
            ->when($string, function($query) use ($string){
                return $query->whereRaw($string);
            })
            // ->when($facility_id, function($query) use ($facility_id){
            //     return $query->where('facility_id', $facility_id);
            // })
            // ->when($female, function($query){
            //     return $query->where('sex', 2);
            // })
            ->paginate(10);

        $patients->setPath(url()->current());
        return $patients;
    }

}
