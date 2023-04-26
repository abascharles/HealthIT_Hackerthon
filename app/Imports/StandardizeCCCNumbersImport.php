<?php

namespace App\Imports;

use App\Viralsample;
use App\Viralpatient;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StandardizeCCCNumbersImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach($collection as $key => $item) {
            $item = (object) $item->toArray();
            $sample = Viralsample::where('national_sample_id', $item->national_system_id)->get();
            if (!$sample->isEmpty()) {
                $sample = $sample->first();
                $patient = $sample->patient;
                $patient->patient = $item->patient_ccc_no;
                $patient->save();
            }
        }
        return true;
    }
}
