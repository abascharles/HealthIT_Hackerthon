<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NoDataReport extends Mailable
{
    use Queueable, SerializesModels;

    public $type;
    public $noage;
    public $nogender;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($type, $noage, $nogender)
    {
        $this->type = $type;
        $this->noage = $noage;
        $this->nogender = $nogender;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->attach($this->noage);
        $this->attach($this->nogender);
        return $this->view('emails.no_data');
    }
}
