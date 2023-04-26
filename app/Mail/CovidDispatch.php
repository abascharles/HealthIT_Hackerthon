<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use DB;
use Mpdf\Mpdf;

use App\Lookup;
use App\Lab;

class CovidDispatch extends Mailable
{
    use Queueable, SerializesModels;

    public $individual_path;
    public $quarantine_site;
    public $lab;
    public $samples;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($samples, $quarantine_site=null)
    {
        $this->individual_path = storage_path('app/batches/covid/individual-results.pdf');
        $this->quarantine_site = $quarantine_site;
        $this->samples = $samples;
        $this->lab = Lab::find(env('APP_LAB'));
        
        if(!is_dir(storage_path('app/batches/covid'))) mkdir(storage_path('app/batches/covid/'), 0777, true);

        set_time_limit(300);

        $mpdf = new Mpdf();
        $data = Lookup::covid_form();
        $data['samples'] = $samples;
        $view_data = view('exports.mpdf_covid_samples', $data)->render();
        ini_set("pcre.backtrack_limit", "500000000");
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($this->individual_path, 'F');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->attach($this->individual_path);

        return $this->subject("COVID-19 RESULTS")->view('emails.covid_dispatch');
    }
}
