<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UrgentCommunication extends Mailable
{
    use Queueable, SerializesModels;

    public $lab;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->lab = \App\Lab::find(env('APP_LAB'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.update_on_vl');
    }
}
