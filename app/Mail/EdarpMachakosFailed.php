<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EdarpMachakosFailed extends Mailable
{
    use Queueable, SerializesModels;

    public $file_path;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($file_path)
    {
        $this->file_path = $file_path;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->attach($this->file_path);
        return $this->view('emails.edarp_delayed_samples');
    }
}
