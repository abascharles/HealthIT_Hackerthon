<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DelayedBatches extends Mailable
{
    use Queueable, SerializesModels;

    public $batches;
    public $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($batches, $type)
    {
        $this->batches = $batches;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.delayed_batches');
    }
}
