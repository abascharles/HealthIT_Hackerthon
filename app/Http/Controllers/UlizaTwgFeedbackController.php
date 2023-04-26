<?php

namespace App\Http\Controllers;

use App\UlizaTwgFeedback;
use App\UlizaClinicalForm;
use App\UlizaAdditionalInfo;
use App\User;
use DB;
use Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\UlizaMail;

class UlizaTwgFeedbackController extends Controller
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
    public function create($id)
    {
        $user = auth()->user();
        $ulizaClinicalForm = UlizaClinicalForm::findOrFail($id);
        if(!$user->uliza_admin && $user->twg_id != $ulizaClinicalForm->twg_id) abort(403);
        if($user->uliza_reviewer && !in_array($user->id, $ulizaClinicalForm->reviewers)) abort(403);
        $view = true;
        if(Str::contains(url()->current(), ['create'])) $view = false;
        $userFeedback = $ulizaClinicalForm->feedback()->where(['user_id' => $user->id])->first();

        $reasons = DB::table('uliza_reasons')->orderBy('name', 'ASC')->get();
        $recommendations = DB::table('uliza_recommendations')->orderBy('name', 'ASC')->get();
        $feedbacks = DB::table('uliza_facility_feedbacks')->orderBy('name', 'ASC')->get();
        $regimens = DB::table('viralregimen')->get();
        $reviewers = User::where(['user_type_id' => 104, 'twg_id' => $ulizaClinicalForm->twg_id])->get();
        return view('uliza.clinical_review', compact('view', 'userFeedback', 'reasons', 'recommendations', 'feedbacks', 'regimens', 'ulizaClinicalForm', 'reviewers'));       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $clinical_form = UlizaClinicalForm::findOrFail($request->input('uliza_clinical_form_id'));
        $user = auth()->user();
        $ulizaTwgFeedback = $clinical_form->feedback()->where(['user_id' => $user->id])->first();
        if(!$ulizaTwgFeedback) $ulizaTwgFeedback = new UlizaTwgFeedback;
        $ulizaTwgFeedback->fill($request->except(['reviewer_id', 'reviewers', 'requested_info']));
        $ulizaTwgFeedback->user_id = $user->id;
        $ulizaTwgFeedback->save();

        $twg = $clinical_form->twg;

        $clinical_form->status_id = 2;
        if($ulizaTwgFeedback->recommendation_id == 3 && !$user->uliza_reviewer) $clinical_form->status_id = 4;
        if($user->uliza_reviewer){
            $unfilledFeedback = $clinical_form->feedback()->whereNull('recommendation_id')->first();
            if(!$unfilledFeedback) $clinical_form->status_id = 3;
        }
        // if($request->input('reviewer_id')) $clinical_form->fill($request->only(['reviewer_id']));
        $clinical_form->save();
        session(['toast_message' => 'The feedback has been saved.']);

        /*if($request->input('reviewer_id')){
            Mail::to([$clinical_form->reviewer->email])->send(new UlizaMail($clinical_form, 'case_referral', 'NASCOP ' . $clinical_form->subject_identifier));
        }*/

        if($request->input('reviewers')){
            $reviewers = User::whereIn('id', $request->input('reviewers'))->get();

            foreach ($reviewers as $key => $reviewer) {
                $clinical_form->feedback()->firstOrCreate(['user_id' => $reviewer->id]);

                Mail::to([$reviewer->email])->send(new UlizaMail($clinical_form, 'case_referral', 'NASCOP ' . $clinical_form->subject_identifier));
            }
        }

        if($request->input('requested_info')){
            // Mail::to([$clinical_form->reviewer->email])->send(new UlizaMail($clinical_form, 'additional_info', 'NASCOP ' . $form->subject_identifier));
            $ulizaAdditionalInfo = new UlizaAdditionalInfo;
            $ulizaAdditionalInfo->requested_info = $request->input('requested_info');
            $ulizaAdditionalInfo->uliza_clinical_form_id = $clinical_form->id;
            $ulizaAdditionalInfo->save();

            if($ulizaTwgFeedback->recommendation_id == 1){
                Mail::to([$clinical_form->facility_email])->send(new UlizaMail($clinical_form, 'additional_info', 'Clinical Summary Form Additional Information Notification ' . $clinical_form->subject_identifier, $ulizaAdditionalInfo));
            }            
            else if($ulizaTwgFeedback->recommendation_id == 5){
                Mail::to($twg->email_array)->send(new UlizaMail($clinical_form, 'additional_info_twg', 'Clinical Summary Form Additional Information Notification ' . $clinical_form->subject_identifier, $ulizaAdditionalInfo));
            }
        }

        // Technical reviewer has given recommendations
        if($ulizaTwgFeedback->recommendation_id == 6){
            Mail::to($twg->email_array)->send(new UlizaMail($clinical_form, 'technical_feedback_provided', $clinical_form->subject_identifier));
        }

        // Feedback is given to the facility
        if($ulizaTwgFeedback->recommendation_id == 3){
            if($ulizaTwgFeedback->facility_recommendation_id == 4){
                Mail::to([$clinical_form->facility_email])->send(new UlizaMail($clinical_form, 'drt_approved', 'DRT Approved by NASCOP ' . $clinical_form->subject_identifier));
            }
            else{
                Mail::to([$clinical_form->facility_email])->send(new UlizaMail($clinical_form, 'feedback_facility', 'NASCOP Feedback For ' . $clinical_form->subject_identifier));
            }
        }
        return redirect('uliza-form');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UlizaTwgFeedback  $ulizaTwgFeedback
     * @return \Illuminate\Http\Response
     */
    public function show(UlizaTwgFeedback $ulizaTwgFeedback)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UlizaTwgFeedback  $ulizaTwgFeedback
     * @return \Illuminate\Http\Response
     */
    public function edit(UlizaTwgFeedback $ulizaTwgFeedback)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UlizaTwgFeedback  $ulizaTwgFeedback
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UlizaTwgFeedback $ulizaTwgFeedback)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UlizaTwgFeedback  $ulizaTwgFeedback
     * @return \Illuminate\Http\Response
     */
    public function destroy(UlizaTwgFeedback $ulizaTwgFeedback)
    {
        //
    }
}
