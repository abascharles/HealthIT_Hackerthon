<?php

namespace App\Imports;

use Str;
use \App\User;
use \App\Facility;
use \App\DrSample;
use \App\DrExtractionWorksheet;
use \App\DrWorksheet;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DrWorksheetImport implements OnEachRow, WithHeadingRow
{

	private $drExtractionWorksheet;


	public function __construct($worksheet_id)
	{
        $drExtractionWorksheet = DrExtractionWorksheet::find($worksheet_id);
		if(!$drExtractionWorksheet) $drExtractionWorksheet = new DrExtractionWorksheet;
		$drExtractionWorksheet->id = $worksheet_id;
		$drExtractionWorksheet->lab_id = env('APP_LAB');
		$drExtractionWorksheet->createdby = User::where('email','like', 'joelkith%')->first()->id ?? null;
		$drExtractionWorksheet->save();
		$this->drExtractionWorksheet = $drExtractionWorksheet;
	}

    
    public function onRow(Row $row)
    {
        $row_array = $row->toArray();
        $rowObject = json_decode(json_encode($row->toArray()));

        if(!$rowObject->lab_id) return;

        if(isset($rowObject->gel_results)){
        	$this->drExtractionWorksheet->status_id = 2;
        	$this->drExtractionWorksheet->date_gel_documentation = date('Y-m-d');
        	$this->drExtractionWorksheet->save();
        }

        $extraction_worksheets = session('extraction_worksheets');
        $extraction_worksheets['w_' . $this->drExtractionWorksheet->id][] = $rowObject->lab_id;

        foreach($extraction_worksheets as $w_no => $extraction_worksheet){
            if($w_no == 'w_' . $this->drExtractionWorksheet->id) continue;

            if(in_array($rowObject->lab_id, $extraction_worksheet)){
                $duplicates = session('duplicates');
                $duplicates[] = [
                    'lab_id' => $rowObject->lab_id,
                    'f_w_no' => $w_no,
                    's_w_no' => 'w_' . $this->drExtractionWorksheet->id,
                ];
                session(['duplicates' => $duplicates]);
            }
        }
        session(['extraction_worksheets' => $extraction_worksheets]);

        if(isset($rowObject->worksheet_id)) return; 

        $drSample = DrSample::find($rowObject->lab_id);
        if(!$drSample) return;

        $drSample->extraction_worksheet_id = $this->drExtractionWorksheet->id;

        if(isset($rowObject->gel_results)){
        	if($rowObject->gel_results == '-') $drSample->passed_gel_documentation = false;
        	else $drSample->passed_gel_documentation = true;
        }
        $drSample->save();
    }


}