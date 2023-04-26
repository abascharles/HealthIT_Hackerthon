<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BatchDeletedNotification extends Mailable
{
    use Queueable, SerializesModels;    

    public $batch;
    public $lab;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($batch)
    {
        $batch->load(['sample.patient']);
        $this->batch = $batch;
        $this->lab = \App\Lab::find(env('APP_LAB'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.batch_deleted_notification');
    }
}
