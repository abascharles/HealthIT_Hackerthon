<?php

namespace App\Mail;

use App\Batch;
use App\Lookup;

use Mpdf\Mpdf;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EidDispatch extends Mailable
{
    use Queueable, SerializesModels;

    public $batch;

    public $individual_path;
    public $summary_path;

    public $individual_title;
    public $summary_title;
    public $title;

    public $type;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Batch $batch, $view_name=null)
    {
        $this->type = "EID";
        $batch->load(['sample.patient.mother', 'facility', 'lab', 'receiver', 'creator']);
        $samples = $batch->sample;

        $this->batch = $batch;
        $sessionVar = md5('nasc0peId1234561987');
        $lab = env('APP_LAB');

        $this->individual_path = storage_path('app/batches/eid/individual-' . $batch->id . '.pdf');
        $this->summary_path = storage_path('app/batches/eid/summary-' . $batch->id . '.pdf');

        if(!is_dir(storage_path('app/batches/eid'))) mkdir(storage_path('app/batches/eid/'), 0777, true);

        if(file_exists($this->individual_path)) unlink($this->individual_path);
        if(file_exists($this->summary_path)) unlink($this->summary_path);

        $mpdf = new Mpdf();
        $data = Lookup::get_lookups();
        $data['to_pdf'] = true;
        $samples->load(['patient.mother', 'approver', 'batch.lab', 'batch.facility', 'batch.receiver', 'batch.creator']);
        $data['samples'] = $samples;
        $view_data = view('exports.mpdf_samples', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($this->individual_path, \Mpdf\Output\Destination::FILE);


        $mpdf = new Mpdf(['format' => 'A4-L']);
        $data = Lookup::get_lookups();
        $data = array_merge($data, ['batches' => [$batch]]);
        $view_data = view('exports.mpdf_samples_summary', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($this->summary_path, \Mpdf\Output\Destination::FILE);

        $this->title = "EID Results for Batch " . $batch->id . " for " . $batch->facility->name . " Received on " . $batch->my_date_format('datereceived');
        $this->individual_title = "Individual EID Results for Batch " . $batch->id . " for " . $batch->facility->name . " Received on " . $batch->my_date_format('datereceived') . ".pdf";
        $this->summary_title = "Summary EID Results for Batch " . $batch->id . " for " . $batch->facility->name . " Received on " . $batch->my_date_format('datereceived') . ".pdf";
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->attach($this->individual_path, ['as' => $this->individual_title]);
        $this->attach($this->summary_path, ['as' => $this->summary_title]);

        return $this->subject($this->title)->view('emails.eid_dispatch');
    }
}
