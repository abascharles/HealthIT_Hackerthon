<?php

namespace App\Observers;

use \App\OldModels\Patient;
use \App\OldModels\Mother;
use \App\OldModels\Sample as OldSample;

use \App\Sample;


class SampleObserver
{


    /**
     * Listen to the Sample created event.
     * Create duplicate entries in the old database.
     *
     * @param  \App\Sample  $sample
     * @return void
     */
    /*
	public function created(Sample $sample)
	{
		$sample->load(['batch', 'patient.mother']);

		$old_sample = new OldSample;
		$old_sample->ID = $sample->id;
		$patient = new Patient;
		$mother = new Mother;

		$this->my_worker($sample, $mother, $patient, $old_sample);
	}

	public function updated(Sample $sample)
	{
		$sample->load(['batch', 'patient.mother']);

		$old_sample = OldSample::find($sample->id);
		$patient = $old_sample->patient;
		$mother = $patient->mother;

		$this->my_worker($sample, $mother, $patient, $old_sample);
	}

	public function deleting(Sample $sample)
	{
		$old_sample = OldSample::find($sample->id);
		$patient = $old_sample->patient;
		$mother = $patient->mother;

		$old_sample->delete();
		$patient->delete();
		$mother->delete();

	}

	private function my_worker($sample, $mother, $patient, $old_sample)
	{		

		$mother->lastvl = $sample->mother_last_result;
		$mother->age = $sample->mother_age;
		$mother->feeding = $sample->feeding;
		$mother->prophylaxis = $sample->mother_prophylaxis;
		$mother->entry_point = $sample->patient->entry_point;
		$mother->status = $sample->patient->mother->hiv_status;
		$mother->cccno = $sample->patient->mother->ccc_no;
		$mother->synched = $sample->patient->mother->synched;
		$mother->datesynched = $sample->patient->mother->datesynched;
		$mother->facility = $sample->batch->facility_id;
		$mother->labtestedin = $sample->batch->lab_id;
		$mother->save();

		$patient->age = $sample->age;
		$patient->prophylaxis = $sample->regimen;
		$patient->ID = $sample->patient->patient;
		$patient->fullnames = $sample->patient->patient_name;
		$patient->gender = $sample->patient->sex;
		$patient->dob = $sample->patient->dob;
		$patient->mother = $mother->ID;
		$patient->labtestedin = $sample->batch->lab_id;
		$patient->save(); 


		$old_sample->patientAUTOid = $sample->patient->id;
		$old_sample->AMRSlocation = $sample->amrs_location;
		$old_sample->provideridentifier = $sample->provider_identifier;
		$old_sample->orderno = $sample->order_no;
		$old_sample->sampletype = $sample->sample_type;
		$old_sample->receivedstatus = $sample->receivedstatus;
		$old_sample->regimen = $sample->regimen;
		$old_sample->pcrtype = $sample->pcrtype;
		$old_sample->spots = $sample->spots;
		$old_sample->comments = $sample->comments;
		$old_sample->labcomment = $sample->labcomment;
		$old_sample->parentid = $sample->parentid;
		$old_sample->rejectedreason = $sample->rejectedreason;
		$old_sample->reason_for_repeat = $sample->reason_for_repeat;
		$old_sample->interpretation = $sample->interpretation;
		$old_sample->result = $sample->result;
		$old_sample->worksheet = $sample->worksheet_id;
		$old_sample->hei_validation = $sample->hei_validation;
		$old_sample->enrollmentCCCno = $sample->enrollment_ccc_no;
		$old_sample->enrollmentstatus = $sample->enrollment_status;
		$old_sample->referredfromsite = $sample->referredfromsite;
		$old_sample->otherreason = $sample->otherreason;
		$old_sample->flag = $sample->flag;
		$old_sample->run = $sample->run;
		$old_sample->repeatt = $sample->repeatt;
		$old_sample->eqa = $sample->eqa;
		$old_sample->approvedby = $sample->approvedby;
		$old_sample->approved2by = $sample->approvedby2;
		$old_sample->datecollected = $sample->datecollected;
		$old_sample->datetested = $sample->datetested;
		$old_sample->datemodified = $sample->datemodified;
		$old_sample->dateapproved = $sample->dateapproved;
		$old_sample->dateapproved2 = $sample->dateapproved2;

		$old_sample->batchno = $sample->batch->id;
		$old_sample->facility = $sample->batch->facility_id;
		$old_sample->highpriority = $sample->batch->highpriority;
		$old_sample->inputcomplete = $sample->batch->input_complete;
		$old_sample->batchcomplete = $sample->batch->batch_complete;
		$old_sample->siteentry = $sample->batch->site_entry;
		$old_sample->sentemail = $sample->batch->sent_email;
		$old_sample->printedby = $sample->batch->printedby;
		$old_sample->userid = $sample->batch->user_id;
		$old_sample->labtestedin = $sample->batch->lab_id;
		$old_sample->facility = $sample->batch->facility_id;
		$old_sample->datedispatchedfromfacility = $sample->batch->datedispatchedfromfacility;
		$old_sample->datereceived = $sample->batch->datereceived;
		$old_sample->datebatchprinted = $sample->batch->datebatchprinted;
		$old_sample->datedispatched = $sample->batch->datedispatched;
		$old_sample->dateindividualresultprinted = $sample->batch->dateindividualresultprinted;

		$old_sample->patient = $sample->patient->patient;
		$old_sample->fullnames = $sample->patient->patient_name;
		$old_sample->caregiverphoneno = $sample->patient->caregiver_phone;
		$old_sample->dateinitiatedontreatment = $sample->patient->dateinitiatedontreatment;
		$old_sample->synched = $sample->synched;
		$old_sample->datesynched = $sample->datesynched;

		if($sample->worksheet_id) $old_sample->Inworksheet = 1;
		if($sample->approvedby) $old_sample->approved = 1;
		if($sample->approvedby2) $old_sample->approved2 = 1;

		$old_sample->save();

	}

	*/


}