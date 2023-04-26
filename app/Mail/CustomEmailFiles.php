<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomEmailFiles extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $lab;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
        $this->lab = \App\Lab::find(env('APP_LAB'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filename = storage_path('app/emails') . '/' . $this->email->id . '.txt';
        $blade = base_path('resources/views/emails') . '/' . $this->email->id . '.blade.php';
        $subject = $this->lab->labname . ' Email id ' . $this->email->id . ' sent at ' . $this->email->time_to_be_sent;

        $this->attach($filename, ['as' => 'raw_file.txt']);
        $this->attach($blade, ['as' => 'blade_file.blade.php']);
        return $this->subject($subject)->view('emails.test');
    }
}
