<?php

namespace App\Observers;

use \App\OldModels\Worksheet as OldWorksheet;
use \App\Worksheet;

class WorksheetObserver
{

	/*public function created(Worksheet $worksheet)
	{
		$old_worksheet = new OldWorksheet;
		$old_worksheet->id = $worksheet->id;
		$this->my_worker($worksheet, $old_worksheet);
	}

	public function updated(Worksheet $worksheet)
	{
		$old_worksheet = OldWorksheet::find($worksheet->id);
		$this->my_worker($worksheet, $old_worksheet);
	}

	public function deleting(Worksheet $worksheet)
	{
		$old_worksheet = OldWorksheet::find($worksheet->id);
		$old_worksheet->delete();
	}

	private function my_worker($worksheet, $old_worksheet)
	{
		$old_worksheet->id = $worksheet->id;
		$old_worksheet->type = $worksheet->machine_type;
		$old_worksheet->lab = $worksheet->lab_id;
		$old_worksheet->status = $worksheet->status_id;
		$old_worksheet->runby = $worksheet->runby;
		$old_worksheet->updatedby = $worksheet->uploaded_by;
		$old_worksheet->reviewedby = $worksheet->reviewedby;
		$old_worksheet->review2by = $worksheet->reviewedby2;
		$old_worksheet->createdby = $worksheet->createdby;
		$old_worksheet->cancelledby = $worksheet->cancelledby;
		$old_worksheet->HIQCAPNo = $worksheet->hiqcap_no;
		$old_worksheet->Spekkitno = $worksheet->spekkit_no;
		$old_worksheet->Rackno = $worksheet->rack_no;
		$old_worksheet->Lotno = $worksheet->lot_no;
		$old_worksheet->samplepreplotno = $worksheet->sample_prep_lot_no;
		$old_worksheet->bulklysislotno = $worksheet->bulklysis_lot_no;
		$old_worksheet->controllotno = $worksheet->control_lot_no;
		$old_worksheet->calibratorlotno = $worksheet->calibrator_lot_no;
		$old_worksheet->amplificationkitlotno = $worksheet->amplification_kit_lot_no;
		
		$old_worksheet->negcontrolresult = $worksheet->neg_control_result;
		$old_worksheet->poscontrolresult = $worksheet->pos_control_result;
		$old_worksheet->negcontrolinterpretation = $worksheet->neg_control_interpretation;
		$old_worksheet->poscontrolinterpretation = $worksheet->pos_control_interpretation;

		$old_worksheet->cdcworksheetno = $worksheet->cdcworksheetno;
		$old_worksheet->kitexpirydate = $worksheet->kitexpirydate;
		$old_worksheet->sampleprepexpirydate = $worksheet->sampleprepexpirydate;
		$old_worksheet->bulklysisexpirydate = $worksheet->bulklysisexpirydate;
		$old_worksheet->controlexpirydate = $worksheet->controlexpirydate;
		$old_worksheet->calibratorexpirydate = $worksheet->calibratorexpirydate;
		$old_worksheet->amplificationexpirydate = $worksheet->amplificationexpirydate;

		$old_worksheet->datecut = $worksheet->datecut;
		$old_worksheet->datereviewed = $worksheet->datereviewed;
		$old_worksheet->review2date = $worksheet->datereviewed2;
		$old_worksheet->datecancelled = $worksheet->datecancelled;
		$old_worksheet->daterun = $worksheet->daterun;
		$old_worksheet->synched = $worksheet->synched;

		$old_worksheet->save();
	}*/




}