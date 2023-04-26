<?php

namespace App\Imports;

use App\CancerSample;
use App\Misc;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TempImport implements ToCollection/*, WithHeadingRow*/
{

    private $targets = [
        'target_1' => 'Other HR HPV',
        'target_2' => 'HPV 16',
        'target_3' => 'HPV 18'
    ];
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        /********* TAQMAN Section ************/
        // $collection = $collection->groupBy('sample_id');
        // $newcollection = collect([]);
        // foreach ($collection as $key => $collection) {
        //     $data = $collection->first();
        //     foreach ($this->targets as $key => $target) {
        //         $RsTarget = $collection->where('target', $target);
        //         $data[$key] = $RsTarget->first()['result'] ?? 'Invalid';
        //     }
        //     $newcollection->push($data);
        // }

        // // Returning this back to collection (old habits)
        // $collection = $newcollection;
        // foreach ($collection as $key => $data) {
        //     if(!isset($data['sample_id'])) break;
            
        //     $sample_id = trim((int) $data['sample_id']);
        //     $interpretation = rtrim($data['flag'] ?? '');
        //     $control = rtrim($data['type']);
            
        //     $data_array = Misc::hpv_sample_result($data);
            
        //     if(\Str::contains($control, '+')){
        //         $positive_control = $data_array;
        //         continue;
        //     }
        //     else if(\Str::contains($control, '-')){
        //         $negative_control = $data_array;
        //         continue;
        //     }
            
        //     // $sample = CancerSample::find($sample_id);
        //     $sample = CancerSample::whereRaw("id = {$sample_id}")->get();
        //     if($sample->isEmpty()){
        //         continue;
        //     } else {
        //         echo "==******** Sample Found! ********==\n";
        //     }
        //     $sample = $sample->first();
        //     $sample->fill($data_array);
        //     $sample->save();
        // }
        /********* TAQMAN Section ************/

        /********* ABBOTT Section ************/
        // $results = collect([]);
        // $detailedresults = collect([]);
        // $this->getAbbottFormattedData($collection, $results, $detailedresults);
        // foreach ($results as $resultkey => $result) {
        //     // Do not proccess sample without sample identifier
        //     if(!isset($result['SAMPLE ID'])) break;

        //     $sample_id = (int) trim($result['SAMPLE ID']);
        //     $interpretation = rtrim($result['INTERPRETATION'] ?? '');
        //     $control = rtrim($result['SAMPLE TYPE']);

        //     // Getting the details of each sample result
        //     $details = $detailedresults->where('SAMPLE LOCATION', $result['SAMPLE LOCATION']);

        //     foreach ($this->targets as $targetkey => $target) {
        //         $RsTarget = $details->where('ASSAY NAME', $target);
        //         $result[$targetkey] = 'Invalid';
        //         if (!$RsTarget->isEmpty())
        //             $result[$targetkey] = $RsTarget->first()['ASSAY NAME'] . ' ' . $RsTarget->first()['RESULT'];
        //     }
        //     // $data_array = Misc::hpv_sample_result($result);
        //     // dd($data_array);
        //     if (\Str::contains($result['RESULT'], ['Not Detected'])) {
        //         $data_array = ['result' => 1, 'interpretation' => $result['RESULT']];
        //     } else if (\Str::contains($result['RESULT'], ['Passed', 'Valid'])) {
        //         $data_array = ['result' => 6, 'interpretation' => 'Valid'];
        //     } else if ($result['RESULT'] == NULL || \Str::contains($result['RESULT'], ['Failed', 'Invalid'])) {
        //         $data_array = ['result' => 3, 'interpretation' => $result['ERROR CODE/DESCRIPTION']];
        //     } else if (\Str::contains($result['RESULT'], ['HPV'])) {
        //         $data_array = ['result' => 2, 'interpretation' => $result['RESULT']];
        //     }

        //     if(\Str::contains($result['SAMPLE ID'], 'POS')){
        //         $positive_control = $data_array;
        //         continue;
        //     }
        //     else if(\Str::contains($result['SAMPLE ID'], 'NEG')){
        //         $negative_control = $data_array;
        //         continue;
        //     }
            
        //     $data_array = array_merge($data_array, ['target_1' => $result['target_1'], 'target_2' => $result['target_2'], 'target_3' => $result['target_3']]);
        //     $sample = CancerSample::find($sample_id);
        //     if(!$sample) continue;

        //     $sample->fill($data_array);
        //     // if($cancelled) $sample->worksheet_id = $worksheet->id;
        //     // else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;
                
        //     $sample->save();
        // }
        /********* ABBOTT Section ************/
    }

    private function getAbbottFormattedData($collection, &$results, &$detailedresults)
    {
        $titlecount = 0;
        $resultdata = $collection->whereNotNull(3);
        $titleArray = NULL;

        foreach($resultdata as $data) {
            if ($data[0] == "SAMPLE LOCATION"){
                $titleArray = $data;
                $titlecount++;
                continue;
            }
            $newdata = [];
            foreach ($data as $key => $item) {
                $newdata[$titleArray[$key]] = $data[$key];
            }


            if ($titlecount == 1){
                $results->push($newdata);
            } else {
                $detailedresults->push($newdata);
            }
        }
    }
}
