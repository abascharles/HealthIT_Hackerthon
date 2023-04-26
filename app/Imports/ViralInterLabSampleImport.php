<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;
use App\Lookup;
use App\Facility;
use App\Viralpatient;
use App\Viralbatch;
use App\ViralsampleView;
use App\Viralsample;
use App\Viralworksheet;
use App\Exports\ViralInterLabSampleExport;
use Carbon\Carbon;

class ViralInterLabSampleImport implements ToCollection, WithHeadingRow
{
	private $receivedby, $machinetype, $sampletype, $calibrations;

	public function __construct($request)
	{
		$this->receivedby = $request->input('receivedby');
        $this->machinetype = $request->input('machinetype');
        $this->sampletype = $request->input('sampletype');
        $this->calibrations = $request->input('calibrations');
	}

    /*$u = \App\User::where('email', 'like', 'joelkith%')->first();
    $viralbatches = \App\Viralbatch::where('user_id', $u->id)->where('created_at', '>', date('Y-m-d'))->get();
    $batch_ids = $viralbatches->pluck('id')->toArray();
    \App\Viralsample::whereIn('batch_id', $batch_ids)->delete();
    \App\Viralbatch::whereIn('id', $batch_ids)->delete();
    \App\Viralworksheet::where('createdby', $u->id)->where('created_at', '>', date('Y-m-d'))->delete();

    $u = \App\User::where('email', 'like', 'joelkith%')->first(); $viralbatches = \App\Viralbatch::where('user_id', $u->id)->where('created_at', '>', date('Y-m-d'))->get(); $batch_ids = $viralbatches->pluck('id')->toArray(); \App\Viralsample::whereIn('batch_id', $batch_ids)->delete(); \App\Viralbatch::whereIn('id', $batch_ids)->delete(); \App\Viralworksheet::where('createdby', $u->id)->where('created_at', '>', date('Y-m-d'))->delete();
    */
    
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $receivedby = $this->receivedby;
        $machinetype = $this->machinetype;
        $sampletype = $this->sampletype;
        $calibrations = (int) $this->calibrations;
        
        # Filter out the duplicate patient samples and no null rows
        $unique_samples_collection = $collection/*->unique('specimenclientcode')*/->whereNotNull('specimenclientcode');
        
        # Create samples in batches and return samples with DB ids
        $stored_samples = $this->createSamples($unique_samples_collection, $receivedby);
        
        # Create worksheet for the created samples, this is the 93samples/worksheet configuration
        $imported_worksheets = $collection->chunk(93);
        // $imported_worksheets = $collection->chunk(93);
        $created_worksheets = [];
        foreach ($imported_worksheets as $imported_worksheet_key => $imported_worksheet) {
            // $imported_worksheet_sample_ids = $imported_worksheet->pluck('id');
            $imported_samples = $imported_worksheet->pluck('sample');
            $worksheet = $this->createWorksheet($receivedby, $machinetype, $sampletype, $calibrations);
            foreach($imported_samples as $sample) {
                $sample->worksheet_id = $worksheet->id;
                $sample->save();
            }
            $created_worksheets[] = $worksheet;
            $calibrations -= 1;
        }
        
        // dd($created_worksheets);
        session()->flash('temp_worksheets', $created_worksheets);
        session(['toast_message' => "The worksheet has been updated with the results."]);
        return collect($created_worksheets);
        // return back();

        /*
        * Old way of doing it
        *

            $batch = null;
           	$lookups = Lookup::get_viral_lookups();
           	// $dataArray = [];
           	// $countItem = $collection->count();
           	$counter = 0;
            $worksheet_counter = 0;
           	$receivedby = $this->receivedby;

           	foreach ($collection as $samplekey => $samplevalue) {
                // Formatting the dates from the excel data
           		$dob = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($samplevalue['dob']))->format('Y-m-d');
            	$initiation_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($samplevalue['art_init_date']))->format('Y-m-d');
            	$datecollected = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($samplevalue['datecollected']))->format('Y-m-d');
            	$datereceived = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($samplevalue['datereceived']))->format('Y-m-d');

           		$counter++;
                $sample_count = $counter % 100;
                if($sample_count == 1){
                    $worksheet = $this->createWorksheet($receivedby);
                    $worksheet_counter = 0;
                }

                $facility = Facility::where('facilitycode', '=', $samplevalue['mflcode'])->first();

                $existing = Viralpatient::existing($facility->id, $samplevalue['specimenclientcode'])->first();
                
                if ($existing){
                    $patient = $existing;
                } else {            
                    $patient = new Viralpatient();
                    $patient->patient = $samplevalue['specimenclientcode'];
                    $patient->facility_id = $facility->id;
                    $patient->sex = $lookups['genders']->where('gender', strtoupper($samplevalue['sex']))->first()->id;
                    $patient->dob = $dob;
                    // $patient->initiation_date = $initiation_date;
                    $patient->save();
                }

                $batch = $this->createBatch($facility, $receivedby, $datereceived);

                $existingSample = ViralsampleView::existing(['facility_id' => $facility->id, 'patient' => $patient->patient, 'datecollected' => $datecollected])->first();

                if($existingSample) continue;
                $worksheet_counter++;
            
                $sample = new Viralsample();
                $sample->batch_id = $batch->id;
                $sample->receivedstatus = $samplevalue['receivedstatus'];
                $sample->age = $samplevalue['age'];
                $sample->patient_id = $patient->id;
                $sample->pmtct = $samplevalue['pmtct'];
                $sample->dateinitiatedonregimen = $initiation_date;
                $sample->datecollected = $datecollected;
                $sample->regimenline = $samplevalue['regimenline'];
                $sample->prophylaxis = $lookups['prophylaxis']->where('code', $samplevalue['currentregimen'])->first()->id ?? 15;
                $sample->justification = $lookups['justifications']->where('rank_id', $samplevalue['justification'])->first()->id ?? 8;
                $sample->sampletype = $samplevalue['sampletype'];
		//uncommet if you want to capture recency number
 		//$sample->recency_number = $samplevalue['recencyno'];  
                if($worksheet_counter < 94) $sample->worksheet_id = $worksheet->id;             
                $sample->save();

                $batch_sample_count = $batch->sample->count();

                if($batch_sample_count > 9) $batch->full_batch();

           	}
        *
        * End of old way of doing it
        */
    }

    private function createSamples($samples, $receivedby)
    {
        $lookups = Lookup::get_viral_lookups();
        $processed_samples_ids = [];


        // Storing the samples first so that we maintain the order of the worksheet as the one in the excel
        foreach ($samples as $sample_key => $sample) {
            $existing_facility = Facility::where('facilitycode', '=', $sample['mflcode'])->first();
            if ($existing_facility){
                $facility = $existing_facility;
            } else {            
                $facility = new Facility();
                $facility->facilitycode = $sample['mflcode'];
                $facility->name = $sample['facilityname'];
                $facility->save();
            }

            $dob = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sample['dob']))->format('Y-m-d');
            $initiation_date = NULL;
            if (null !== $sample['art_init_date'])
                $initiation_date = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sample['art_init_date']))->format('Y-m-d');
            $datecollected = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($sample['datecollected']))->format('Y-m-d');

            $existing_patient = Viralpatient::existing($facility->id, $sample['specimenclientcode'])->first();
    
            if ($existing_patient){
                $patient = $existing_patient;
            } else {            
                $patient = new Viralpatient();
                $patient->patient = $sample['specimenclientcode'];
                $patient->facility_id = $facility->id;
                $patient->sex = $lookups['genders']->where('gender', strtoupper($sample['sex']))->first()->id ?? $sample['sex'];
                $patient->dob = $dob;
                $patient->initiation_date = $initiation_date;
                $patient->save();
            }
            $newsample = new Viralsample();
            $newsample->batch_id = 0;
            $newsample->receivedstatus = $sample['receivedstatus'];
            $newsample->age = $sample['age'];
            $newsample->patient_id = $patient->id;
            $newsample->pmtct = $sample['pmtct'];
            $newsample->dateinitiatedonregimen = $initiation_date;
            $newsample->datecollected = $datecollected;
            $newsample->regimenline = $sample['regimenline'];
            $newsample->prophylaxis = $lookups['prophylaxis']->where('code', $sample['currentregimen'])->first()->id ?? $sample['currentregimen'] ?? 15;
            $newsample->justification = $lookups['justifications']->where('rank_id', $sample['justification'])->first()->id ?? 8;
            $newsample->sampletype = $sample['sampletype'];
	//uncommet if you want to capture recency number	
	//$newsample->recency_number = $sample['recencyno'];
            $newsample->save();

            $sample['sample'] = $newsample;
        }

        # Group samples by facility since batches are created per facility
        $facilities_samples = $samples->groupBy('mflcode');
        foreach ($facilities_samples as $facility_sample_key => $facility_samples) {
            $facility = Facility::where('facilitycode', '=', $facility_sample_key)->first();
            
            # Chunk the facility samples to the required batch size
            $imported_batches = $facility_samples->chunk(10);
            foreach ($imported_batches as $imported_batch_key => $imported_batch) {
                $datereceived = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($imported_batch->max('datereceived')))->format('Y-m-d');
                $batch = $this->createBatch($facility, $receivedby, $datereceived);

                foreach ($imported_batch as $imported_sample_key => $imported_sample) {
                    $stored_sample = $imported_sample['sample'];
                    $stored_sample->batch_id = $batch->id;
                    $stored_sample->save();
                }
                $batch->full_batch();
            }
        }
        return $samples;
    }

    private function createBatch($facility, $receivedby, $datereceived)
    {
        $batch = Viralbatch::eligible($facility->id, $datereceived)->withCount(['sample'])->first();
        if($batch && $batch->sample_count < 10){
            unset($batch->sample_count);
        }
        else if($batch && $batch->sample_count > 9){
            unset($batch->sample_count);
            $batch->full_batch();
            $batch = new Viralbatch;
        }
        else{
            $batch = new Viralbatch;
        }
        $batch->user_id = $receivedby;
        $batch->lab_id = env('APP_LAB');
        $batch->received_by = $receivedby;
        $batch->site_entry = 0;
        $batch->entered_by = $receivedby;
        $batch->datereceived = $datereceived;
        $batch->facility_id = $facility->id;
        $batch->save();
        return $batch;
    }

    private function createWorksheet($receivedby, $machinetype, $sampletype, $calibrations=0)
    {
        $worksheet = new Viralworksheet();
        $worksheet->lab_id = env('APP_LAB');
        $worksheet->machine_type = $machinetype;
        $worksheet->sampletype = $sampletype;
        $worksheet->createdby = $receivedby;
        $worksheet->sample_prep_lot_no = 44444;
        $worksheet->bulklysis_lot_no = 44444;
        $worksheet->control_lot_no = 44444;
        $worksheet->amplification_kit_lot_no = 44444;
        $worksheet->sampleprepexpirydate = date('Y-m-d', strtotime("+ 6 Months"));
        $worksheet->bulklysisexpirydate = date('Y-m-d', strtotime("+ 6 Months"));
        $worksheet->controlexpirydate = date('Y-m-d', strtotime("+ 6 Months"));
        $worksheet->amplificationexpirydate = date('Y-m-d', strtotime("+ 6 Months"));
        if ($calibrations > 0) {
            $worksheet->calibrator_lot_no = 44444;
            $worksheet->calibratorexpirydate = date('Y-m-d', strtotime("+ 6 Months"));
            $worksheet->calibration = 1;
        }
        $worksheet->save();
        return $worksheet;
    }
}
