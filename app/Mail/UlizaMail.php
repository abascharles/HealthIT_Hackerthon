<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UlizaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $uliza_clinical_form;
    public $view_name;
    public $subject_title;
    public $ulizaAdditionalInfo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($uliza_clinical_form=null, $view_name=null, $subject_title='NASCOP', $ulizaAdditionalInfo=null)
    {
        $this->uliza_clinical_form = $uliza_clinical_form;
        $this->view_name = $view_name;
        $this->subject_title = $subject_title;
        $this->ulizaAdditionalInfo = $ulizaAdditionalInfo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // if($this->view_name == 'received_clinical_form'){
        if(in_array($this->view_name, ['received_clinical_form', 'new_clinical_form', 'case_referral'])){
            $this->uliza_clinical_form->entry_pdf();
            $this->attach($this->uliza_clinical_form->entry_path, ['as' => 'Clinical Form.pdf']);
        }
        else if(in_array($this->view_name, ['drt_approved', 'feedback_facility'])){
            $this->uliza_clinical_form->completed_pdf();
            $this->attach($this->uliza_clinical_form->completed_path, ['as' => 'Completed Clinical Form.pdf']);
        }
        return $this->subject($this->subject_title)->view('uliza.mail.' . $this->view_name);
    }
}
