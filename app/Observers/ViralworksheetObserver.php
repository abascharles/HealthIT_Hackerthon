<?php

namespace App\Observers;

use \App\OldModels\Viralworksheet as OldWorksheet;
use \App\Viralworksheet;

class ViralworksheetObserver
{
	/*
	public function created(Viralworksheet $viralworksheet)
	{
		$old_worksheet = new OldWorksheet;
		$old_worksheet->id = $viralworksheet->id;
		$this->my_worker($viralworksheet, $old_worksheet);
	}

	public function updated(Viralworksheet $viralworksheet)
	{
		$old_worksheet = OldWorksheet::find($viralworksheet->id);
		$this->my_worker($viralworksheet, $old_worksheet);
	}

	public function deleting(Viralworksheet $viralworksheet)
	{
		$old_worksheet = OldWorksheet::find($viralworksheet->id);
		$old_worksheet->delete();
	}

	private function my_worker($viralworksheet, $old_worksheet)
	{
		$old_worksheet->id = $viralworksheet->id;
		$old_worksheet->type = $viralworksheet->machine_type;
		$old_worksheet->lab = $viralworksheet->lab_id;
		$old_worksheet->status = $viralworksheet->status_id;
		$old_worksheet->calibration = $viralworksheet->calibration;
		$old_worksheet->runby = $viralworksheet->runby;
		$old_worksheet->updatedby = $viralworksheet->uploaded_by;
		$old_worksheet->reviewedby = $viralworksheet->reviewedby;
		$old_worksheet->review2by = $viralworksheet->reviewedby2;
		$old_worksheet->createdby = $viralworksheet->createdby;
		$old_worksheet->cancelledby = $viralworksheet->cancelledby;
		$old_worksheet->HIQCAPNo = $viralworksheet->hiqcap_no;
		$old_worksheet->Spekkitno = $viralworksheet->spekkit_no;
		$old_worksheet->Rackno = $viralworksheet->rack_no;
		$old_worksheet->Lotno = $viralworksheet->lot_no;
		$old_worksheet->samplepreplotno = $viralworksheet->sample_prep_lot_no;
		$old_worksheet->bulklysislotno = $viralworksheet->bulklysis_lot_no;
		$old_worksheet->controllotno = $viralworksheet->control_lot_no;
		$old_worksheet->calibratorlotno = $viralworksheet->calibrator_lot_no;
		$old_worksheet->amplificationkitlotno = $viralworksheet->amplification_kit_lot_no;
		
		$old_worksheet->negcontrolresult = $viralworksheet->neg_control_result;
		$old_worksheet->highposcontrolresult = $viralworksheet->highpos_control_result;
		$old_worksheet->lowposcontrolresult = $viralworksheet->lowpos_control_result;
		
		$old_worksheet->negcontrolinterpretation = $viralworksheet->neg_control_interpretation;
		$old_worksheet->highposcontrolinterpretation = $viralworksheet->highpos_control_interpretation;
		$old_worksheet->lowposcontrolinterpretation = $viralworksheet->lowpos_control_interpretation;
		
		$old_worksheet->negunits = $viralworksheet->neg_units;
		$old_worksheet->hpcunits = $viralworksheet->hpc_units;
		$old_worksheet->lpcunits = $viralworksheet->lpc_units;

		$old_worksheet->cdcworksheetno = $viralworksheet->cdcworksheetno;
		$old_worksheet->kitexpirydate = $viralworksheet->kitexpirydate;
		$old_worksheet->sampleprepexpirydate = $viralworksheet->sampleprepexpirydate;
		$old_worksheet->bulklysisexpirydate = $viralworksheet->bulklysisexpirydate;
		$old_worksheet->controlexpirydate = $viralworksheet->controlexpirydate;
		$old_worksheet->calibratorexpirydate = $viralworksheet->calibratorexpirydate;
		$old_worksheet->amplificationexpirydate = $viralworksheet->amplificationexpirydate;

		$old_worksheet->datecut = $viralworksheet->datecut;
		$old_worksheet->datereviewed = $viralworksheet->datereviewed;
		$old_worksheet->review2date = $viralworksheet->datereviewed2;
		$old_worksheet->datecancelled = $viralworksheet->datecancelled;
		$old_worksheet->daterun = $viralworksheet->daterun;
		$old_worksheet->synched = $viralworksheet->synched;

		$old_worksheet->save();
	}

	*/


}