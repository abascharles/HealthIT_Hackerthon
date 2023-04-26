<?php

namespace App\Http\Controllers;

use App\UlizaAdditionalInfo;
use App\User;

use DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\UlizaMail;

class UlizaAdditionalInfoController extends Controller
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
     * @param  \App\UlizaAdditionalInfo  $ulizaAdditionalInfo
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $view = true;
        $ulizaAdditionalInfo = UlizaAdditionalInfo::findOrFail($id);
        $ulizaClinicalForm = $ulizaAdditionalInfo->clinical_form;
        $reasons = DB::table('uliza_reasons')->orderBy('name', 'ASC')->get();
        $recommendations = DB::table('uliza_recommendations')->orderBy('name', 'ASC')->get();
        $feedbacks = DB::table('uliza_facility_feedbacks')->orderBy('name', 'ASC')->get();
        $regimens = DB::table('viralregimen')->get();
        $reviewers = User::where(['user_type_id' => 104, 'twg_id' => $ulizaClinicalForm->twg_id])->get();
        return view('uliza.clinical_review', compact('view', 'reasons', 'recommendations', 'feedbacks', 'regimens', 'ulizaClinicalForm', 'reviewers', 'ulizaAdditionalInfo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UlizaAdditionalInfo  $ulizaAdditionalInfo
     * @return \Illuminate\Http\Response
     */
    public function edit(UlizaAdditionalInfo $ulizaAdditionalInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UlizaAdditionalInfo  $ulizaAdditionalInfo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UlizaAdditionalInfo $ulizaAdditionalInfo)
    {
        $ulizaAdditionalInfo->additional_info = $request->input('additional_info');
        $ulizaAdditionalInfo->save();
        $form = $ulizaAdditionalInfo->clinical_form;
        $user = auth()->user();
        $email_array = $form->twg->email_array;
        if($form->reviewer) $email_array = [$form->reviewer->email];
        if(!$user){
            Mail::to($email_array)->send(new UlizaMail($form, 'additional_info_submitted', 'Clinical Summary Form Additional Information Notification ' . $form->subject_identifier));

            return redirect('/uliza/clinicalform');
        }
        Mail::to($email_array)->send(new UlizaMail($form, 'additional_info_twg', 'NASCOP ' . $form->subject_identifier));
        session(['toast_message' => 'The additional info has been submitted.']);
        return redirect('uliza-form');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UlizaAdditionalInfo  $ulizaAdditionalInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(UlizaAdditionalInfo $ulizaAdditionalInfo)
    {
        //
    }
}
