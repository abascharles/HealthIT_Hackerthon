<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mpdf\Mpdf;

class LabTracker extends Mailable
{
    use Queueable, SerializesModels;

    public $lab;
    public $path;
    public $title;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->path = storage_path('app/lablogs/monthlabtracker ' . $data->year .  $data->month .'.pdf');
        
        if(!is_dir(storage_path('app/lablogs'))) mkdir(storage_path('app/lablogs'), 0777, true);

        // if(file_exists($this->path)) unlink($this->path);
        
        $mpdf = new Mpdf();
        // $this->lab = \App\Lab::find(env('APP_LAB'));
        $this->lab = \App\Lab::find(env('APP_LAB'));
        // $lab = $this->lab;
        $pageData = ['data' => $data, 'lab' => $this->lab, 'download' => false];
        $view_data = view('exports.mpdf_labtracker', $pageData)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($this->path, \Mpdf\Output\Destination::FILE);

        $this->title = strtoupper($this->lab->labname . ' monthly lab tracker for '. date("F", mktime(null, null, null, $data->month)) . ' ' .$data->year);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->attach($this->path, ['as' => $this->title]);

        return $this->subject($this->title)->view('emails.labtracker');
    }
}
