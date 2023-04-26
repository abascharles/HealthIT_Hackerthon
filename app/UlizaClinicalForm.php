<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mpdf\Mpdf;
use DB;

class UlizaClinicalForm extends BaseModel
{

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');        
    }

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function additional_info()
    {
        return $this->hasMany('App\UlizaAdditionalInfo');
    }

    public function view_facility()
    {
        return $this->belongsTo(ViewFacility::class, 'facility_id');
    }

    public function twg()
    {
        return $this->belongsTo('App\UlizaTwg', 'twg_id');        
    }

    public function visit()
    {
        return $this->hasMany('App\UlizaClinicalVisit', 'uliza_clinical_form_id');
    }

    public function feedback()
    {
        return $this->hasMany('App\UlizaTwgFeedback', 'uliza_clinical_form_id');
    }

    public function getNatNumberAttribute()
    {
        return "Uliza-{$this->id}";
    }

    public function getSubjectIdentifierAttribute()
    {
        return 'CCC#: ' . $this->cccno . ' Uliza#: ' . $this->nat_number;
    }

    public function getReviewersAttribute()
    {
        return $this->feedback()->get()->pluck('user_id')->toArray();
    }

    public function getEntryPathAttribute()
    {
        return storage_path('app/batches/uliza/entry-' . $this->id . '.pdf');
    }

    public function getCompletedPathAttribute()
    {
        return storage_path('app/batches/uliza/completed-' . $this->id . '.pdf');
    }

    public function entry_pdf($file_path=null, $download=false)
    {
        if(!$file_path) $file_path = $this->entry_path;

        if(file_exists($file_path)) unlink($file_path);

        $file_name = explode('/', $file_path);
        $file_name = array_pop($file_name);

        $reasons = DB::table('uliza_reasons')->where('public', 1)->get();
        $regimens = DB::table('viralregimen')->get();
        $ulizaClinicalForm = $this;

        $mpdf = new Mpdf();
        $view_data = view('uliza.exports.clinical_form', compact('reasons', 'regimens', 'ulizaClinicalForm'))->render();
        $mpdf->WriteHTML($view_data);

        if($download) return $mpdf->Output($file_name, \Mpdf\Output\Destination::DOWNLOAD);
        else{
            $mpdf->Output($file_path, \Mpdf\Output\Destination::FILE);
        }
    }

    public function completed_pdf($file_path=null, $download=false)
    {
        if(!$file_path) $file_path = $this->completed_path;

        if(file_exists($file_path)) unlink($file_path);

        $file_name = explode('/', $file_path);
        $file_name = array_pop($file_name);

        $reasons = DB::table('uliza_reasons')->orderBy('name', 'ASC')->get();
        $recommendations = DB::table('uliza_recommendations')->orderBy('name', 'ASC')->get();
        $feedbacks = DB::table('uliza_facility_feedbacks')->orderBy('name', 'ASC')->get();
        $regimens = DB::table('viralregimen')->get();
        $completed = true;
        $ulizaClinicalForm = $this;
        unset($ulizaClinicalForm->feedback);


        $mpdf = new Mpdf();
        $view_data = view('uliza.exports.clinical_form', compact('reasons', 'recommendations', 'feedbacks', 'regimens', 'completed', 'ulizaClinicalForm'))->render();
        $mpdf->WriteHTML($view_data);

        if($download) return $mpdf->Output($file_name, \Mpdf\Output\Destination::DOWNLOAD);
        else{
            $mpdf->Output($file_path, \Mpdf\Output\Destination::FILE);
        }
    }

}
