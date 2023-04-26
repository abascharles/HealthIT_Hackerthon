<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AllocationReview extends Mailable
{
    use Queueable, SerializesModels;

    public $allocationReactionCounts, $lab, $from, $to, $approved, $rejected;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($allocationReactionCounts, $lab, $from, $to, $approved, $rejected)
    {
        dd($allocationReactionCounts);
        $this->allocationReactionCounts = $allocationReactionCounts;
        $this->lab = $lab;
        $this->from = $from;
        $this->to = $to;
        $this->approved = $approved;
        $this->rejected = $rejected;
        // dd($this->data);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.allocationreview');
    }
}
