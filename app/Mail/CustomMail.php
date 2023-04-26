<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use \App\Lab;

class CustomMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $facility;
    public $lab;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $facility=null)
    {
        $this->email = $email;
        $this->facility = $facility;
        $this->lab = Lab::find(env('APP_LAB'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        foreach ($this->email->attachment as $key => $attachment) {
            $this->attach($attachment->path, ['as' => $attachment->download_name]);            
        }
        
        $view_name = 'emails.' . $this->email->id;
        $from = env('MAIL_FROM_NAME');
        if($this->email->from_name != '') $from = $this->email->from_name;
        return $this->subject($this->email->subject)->from(env('MAIL_FROM_ADDRESS'), $from)->view($view_name);
    }
}
