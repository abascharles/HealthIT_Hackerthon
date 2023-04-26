<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Mother;
use App\Batch;
use App\Viralbatch;
use App\Patient;
use App\Viralpatient;

use App\Sample;
use App\Viralsample;
use App\Worksheet;
use App\Viralworksheet;

use App\CovidSample;
use App\CovidPatient;

use App\Facility;
use App\QuarantineSite;

use App\Observers\MotherObserver;
use App\Observers\BatchObserver;
use App\Observers\ViralbatchObserver;
use App\Observers\PatientObserver;
use App\Observers\ViralpatientObserver;

use App\Observers\SampleObserver;
use App\Observers\ViralsampleObserver;
use App\Observers\WorksheetObserver;
use App\Observers\ViralworksheetObserver;

use App\Observers\CovidPatientObserver;
use App\Observers\CovidSampleObserver;

use App\Observers\FacilityObserver;
use App\Observers\QuarantineSiteObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(env('APP_SECURE_URL')) \Illuminate\Support\Facades\URL::forceScheme('http');

        // dd(url('') . ' ' . url()->full() . " " . url()->current() . " " . $_SERVER['HTTP_HOST'] . " " . $_SERVER['REQUEST_URI'] . " " . $_SERVER['SERVER_PORT']);
        // if(env('APP_URL') == url('') && env('APP_SECURE_URL')) \Illuminate\Support\Facades\URL::forceScheme('https');

        // \Illuminate\Support\Facades\URL::forceRootUrl(env('APP_URL'));

        if(env('APP_SECURE_PORT')) \Illuminate\Support\Facades\URL::forceRootUrl(url('') . ':' .  env('APP_SECURE_PORT'));



        Mother::observe(MotherObserver::class);

        Batch::observe(BatchObserver::class);
        Viralbatch::observe(ViralbatchObserver::class);
        
        Patient::observe(PatientObserver::class);
        Viralpatient::observe(ViralpatientObserver::class);


        CovidSample::observe(CovidSampleObserver::class);
        CovidPatient::observe(CovidPatientObserver::class);


        if(in_array(env('APP_LAB'), [1,7])) \App\DrSample::observe(\App\Observers\DrSampleObserver::class);


        if(env('DOUBLE_ENTRY')){
        
            Sample::observe(SampleObserver::class);
            Viralsample::observe(ViralsampleObserver::class);
            
            Worksheet::observe(WorksheetObserver::class);
            Viralworksheet::observe(ViralworksheetObserver::class);

        }

        
        Facility::observe(FacilityObserver::class);
        QuarantineSite::observe(QuarantineSiteObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
