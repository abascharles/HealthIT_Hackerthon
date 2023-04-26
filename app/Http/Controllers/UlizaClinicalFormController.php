<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\UlizaMail;
use App\Notifications\UlizaNotification;
use App\UlizaClinicalForm;
use App\UlizaTwgFeedback;
use App\UlizaClinicalVisit;
use App\UlizaAdditionalInfo;
use App\UlizaTwg;
use App\County;
use DB;
use Illuminate\Http\Request;

class UlizaClinicalFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $statuses = DB::table('uliza_case_statuses')->get();
        if(!$user) return redirect('uliza/uliza');
        $fclass = UlizaTwgFeedback::class;
        $forms = UlizaClinicalForm::with(['view_facility', 'twg'])
        ->when(true, function($query) use ($user, $fclass){
            if($user->uliza_secretariat) return $query->where('twg_id', $user->twg_id);
            if($user->uliza_reviewer) return $query->whereIn('id', $fclass::select('uliza_clinical_form_id')->where('user_id', $user->id));
        })
        ->when($request->input('twg_id'), function($query) use($request){
            return $query->where('twg_id', $request->input('twg_id'));
        })
        ->when($request->input('status_id'), function($query) use($request){
            return $query->where('status_id', $request->input('status_id'));
        })
        ->when($request->input('start_date') || $request->input('end_date'), function($query) use($request){
            return $query->whereBetween('created_at', [$request->input('start_date'), $request->input('end_date')]);
        })
        ->when($request->input('county_id') || $request->input('subcounty_id'), function($query) use($request){
            $query->select('uliza_clinical_forms.*')
                ->join('view_facilitys', 'view_facilitys.id', '=', 'uliza_clinical_forms.facility_id');

            if($request->input('subcounty_id'))$query->where('subcounty_id', $request->input('subcounty_id'));
            if($request->input('county_id'))$query->where('county_id', $request->input('county_id'));
            return $query;
        })
        ->where('draft', false)
        ->orderBy('uliza_clinical_forms.id', 'desc')
        ->get();
        $counties = DB::table('countys')->get();
        $subcounties = DB::table('districts')->get();
        $twgs = DB::table('uliza_twgs')->get();
        return view('uliza.tables.cases', compact('forms', 'statuses', 'counties', 'subcounties', 'twgs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $reasons = DB::table('uliza_reasons')->where('public', 1)->get();
        $regimens = DB::table('viralregimen')->get();
        return view('uliza.clinicalform', compact('reasons', 'regimens'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {        
        $form = null;
        if($request->input('id')) $form = UlizaClinicalForm::find($request->input('id'));
        else{
            $duplicate = UlizaClinicalForm::where($request->only(['facility_id', 'cccno']))->where('status_id', '<', 3)->first();
            if($duplicate) abort(400, 'The clinical form already exists.');
        }
        if(!$form) $form = new UlizaClinicalForm;
        $form->fill($request->except('clinical_visits'));
        $f = $form->view_facility;
        $county = County::find($f->county_id);
        $twg = UlizaTwg::where('default_twg', 1)->first();
        $form->twg_id = $county->twg_id ?? $twg->id ?? null;
        $form->save();
        $twg = $form->twg;

        $visits = $request->input('clinical_visits');

        foreach ($visits as $key => $value) {            
            $visit = new UlizaClinicalVisit;
            if(is_array($value)) $visit->fill($value);
            else{
                $visit->fill(get_object_vars($value));
            }
            // $visit->uliza_clinical_form_id = $form->id;
            // $visit->save();
            $form->visit()->save($visit);
        }

        if($form->draft){
            Mail::to([$form->facility_email])->send(new UlizaMail($form, 'draft_mail', 'Draft Clinical Summary Form ' . $form->subject_identifier));
            // $user = \App\User::where('email', 'like', 'joel%')->first();
            // $user->facility_email = $form->facility_email;
            // $user->notify(new UlizaNotification('uliza-form/' . $form->id . '/edit'));
        }else{
            
            Mail::to([$form->facility_email])->send(new UlizaMail($form, 'received_clinical_form', 'Clinical Summary Form Notification ' . $form->subject_identifier));

            if($twg) Mail::to($twg->email_array)->send(new UlizaMail($form, 'new_clinical_form', 'Clinical Summary Form Notification ' . $form->subject_identifier));
        }

        return response()->json(['status' => 'ok', 'form' => $form], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UlizaClinicalForm  $ulizaClinicalForm
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $ulizaClinicalForm->entry_pdf(null, true);
        $ulizaClinicalForm = UlizaClinicalForm::findOrFail($id);
        $ulizaClinicalForm->entry_pdf(null, true);

        // $reasons = DB::table('uliza_reasons')->where('public', 1)->get();
        // $regimens = DB::table('viralregimen')->get();
        // return view('uliza.exports.clinical_form', compact('reasons', 'regimens', 'ulizaClinicalForm'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UlizaClinicalForm  $ulizaClinicalForm
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $reasons = DB::table('uliza_reasons')->get();
        $regimens = DB::table('viralregimen')->get();
        $ulizaClinicalForm = UlizaClinicalForm::find($id);
        return view('uliza.clinicalform', compact('reasons', 'regimens', 'ulizaClinicalForm'));      
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UlizaClinicalForm  $ulizaClinicalForm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $form = UlizaClinicalForm::find($id);
        $form->fill($request->except('clinical_visits'));
        $form->save();
        return response()->json(['status' => 'ok'], 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UlizaClinicalForm  $ulizaClinicalForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(UlizaClinicalForm $ulizaClinicalForm)
    {
        
    }
}
