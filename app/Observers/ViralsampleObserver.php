<?php

namespace App\Observers;

use \App\OldModels\Viralpatient;
use \App\OldModels\Viralsample as OldSample;

use \App\Viralsample;


class ViralsampleObserver
{


    /**
     * Listen to the Viralsample created event.
     * Create duplicate entries in the old database.
     *
     * @param  \App\Viralsample  $viralsample
     * @return void
     */
    /*
	public function created(Viralsample $viralsample)
	{
		$viralsample->load(['batch', 'patient']);

		$old_sample = new OldSample;
		$old_sample->ID = $viralsample->id;
		$patient = new Viralpatient;
		$this->my_worker($viralsample, $patient, $old_sample);
	}

	public function updated(Viralsample $viralsample)
	{
		$viralsample->load(['batch', 'patient']);

		$old_sample = OldSample::find($viralsample->id);
		// if()
		$patient = $old_sample->patient;
		$this->my_worker($viralsample, $patient, $old_sample);
	}

	public function deleting(Viralsample $viralsample)
	{
		$viralsample->load(['batch', 'patient']);

		$old_sample = OldSample::find($viralsample->id);
		$patient = $old_sample->patient;
		$old_sample->delete();
		$patient->delete();
	}



	private function my_worker($viralsample, $patient, $old_sample)
	{

		$patient->age = $viralsample->age ?? null;
		$patient->pmtct = $viralsample->pmtct;
		$patient->prophylaxis = $viralsample->prophylaxis;
		$patient->gender = $viralsample->patient->sex;
		$patient->dob = $viralsample->patient->dob;
		$patient->initiationdate = $viralsample->patient->initiation_date;
		$patient->labtestedin = $viralsample->batch->lab_id;
		$patient->save();




		$old_sample->patientAUTOid = $viralsample->patient->id;
		$old_sample->AMRSlocation = $viralsample->amrs_location;
		$old_sample->provideridentifier = $viralsample->provider_identifier;
		$old_sample->orderno = $viralsample->order_no;
		$old_sample->vlrequestno = $viralsample->vl_test_request_no;
		$old_sample->receivedstatus = $viralsample->receivedstatus;

		$old_sample->justification = $viralsample->justification;
		$old_sample->age2 = $viralsample->age_category;
		$old_sample->justification = $viralsample->justification;
		$old_sample->otherjustification = $viralsample->other_justification;
		$old_sample->sampletype = $viralsample->sampletype;
		$old_sample->prophylaxis = $viralsample->prophylaxis;
		$old_sample->regimenline = $viralsample->regimenline;
		$old_sample->dilutionfactor = $viralsample->dilutionfactor;
		$old_sample->dilutiontype = $viralsample->dilutiontype;
		$old_sample->comments = $viralsample->comments;
		$old_sample->labcomment = $viralsample->labcomment;
		$old_sample->parentid = $viralsample->parentid;
		$old_sample->rejectedreason = $viralsample->rejectedreason;
		$old_sample->reason_for_repeat = $viralsample->reason_for_repeat;
		$old_sample->interpretation = $viralsample->interpretation;
		$old_sample->result = $viralsample->result;
		$old_sample->units = $viralsample->units;
		$old_sample->worksheet = $viralsample->worksheet_id;

		$old_sample->flag = $viralsample->flag;
		$old_sample->run = $viralsample->run;
		$old_sample->repeatt = $viralsample->repeatt;

		$old_sample->approvedby = $viralsample->approvedby;
		$old_sample->approved2by = $viralsample->approvedby2;
		$old_sample->datecollected = $viralsample->datecollected;
		$old_sample->datetested = $viralsample->datetested;
		$old_sample->datemodified = $viralsample->datemodified;
		$old_sample->dateapproved = $viralsample->dateapproved;
		$old_sample->dateapproved2 = $viralsample->dateapproved2;

		$old_sample->batchno = $viralsample->batch->id;
		$old_sample->facility = $viralsample->batch->facility_id;
		$old_sample->highpriority = $viralsample->batch->highpriority;
		$old_sample->inputcomplete = $viralsample->batch->input_complete;
		$old_sample->batchcomplete = $viralsample->batch->batch_complete;
		$old_sample->siteentry = $viralsample->batch->site_entry;
		$old_sample->sentemail = $viralsample->batch->sent_email;
		$old_sample->printedby = $viralsample->batch->printedby;
		$old_sample->userid = $viralsample->batch->user_id;
		$old_sample->labtestedin = $viralsample->batch->lab_id;
		$old_sample->facility = $viralsample->batch->facility_id;
		$old_sample->datedispatchedfromfacility = $viralsample->batch->datedispatchedfromfacility;
		$old_sample->datereceived = $viralsample->batch->datereceived;
		$old_sample->datebatchprinted = $viralsample->batch->datebatchprinted;
		$old_sample->datedispatched = $viralsample->batch->datedispatched;
		$old_sample->dateindividualresultprinted = $viralsample->batch->dateindividualresultprinted;

		$old_sample->patient = $viralsample->patient->patient;
		$old_sample->ARTstartdate = $viralsample->patient->initiation_date;
		$old_sample->fullnames = $viralsample->patient->patient_name;
		$old_sample->caregiverphoneno = $viralsample->patient->caregiver_phone;
		$old_sample->dateinitiatedontreatment = $viralsample->patient->dateinitiatedontreatment;
		$old_sample->synched = $viralsample->synched;
		$old_sample->datesynched = $viralsample->datesynched;

		if($viralsample->worksheet_id && $viralsample->worksheet_id != 0) $old_sample->inworksheet = 1;
		$old_sample->save();

	}
	*/

}