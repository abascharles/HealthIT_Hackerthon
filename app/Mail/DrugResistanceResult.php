<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use Mpdf\Mpdf;
use App\DrSample;
use App\Lab;
use App\Lookup;

class DrugResistanceResult extends Mailable
{
    use Queueable, SerializesModels;

    public $drSample;
    public $result_path;
    public $result_title;
    public $lab;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(DrSample $drSample)
    {
        $this->result_path = storage_path('app/batches/dr/individual-' . $drSample->id . '.pdf');
        if(!is_dir(storage_path('app/batches/dr'))) mkdir(storage_path('app/batches/dr/'), 0777, true);
        if(file_exists($this->result_path)) unlink($this->result_path);

        $this->drSample = $drSample;
        $drSample->load(['dr_call.call_drug']);
        $data = Lookup::get_dr();
        $data['sample'] = $drSample;
        $mpdf = new Mpdf();
        $view_data = view('exports.mpdf_dr_result', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($this->result_path, \Mpdf\Output\Destination::FILE);

        $this->result_title = "Drug Resistance Genotype Report.pdf";
        $this->lab = Lab::find(env('APP_LAB'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->attach($this->result_path, ['as' => $this->result_title]);
        return $this->view('emails.dr_dispatch');
    }
}
