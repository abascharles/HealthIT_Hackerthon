<?php

namespace App\Imports;

use \App\Misc;
use \App\CancerSample;
use \App\CancerSampleView;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
// use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CancerWorksheetImport implements ToCollection/*, WithHeadingRow*/
{
	protected $worksheet;
	protected $cancelled;
    protected $daterun;
    private $targets = [
        'target_1' => 'Other HR HPV',
        'target_2' => 'HPV 16',
        'target_3' => 'HPV 18'
    ];

	public function __construct($worksheet, $request)
	{
        $cancelled = false;
        if($worksheet->status_id == 4) $cancelled =  true;
        $worksheet->fill($request->except(['_token', 'upload']));
        $this->cancelled = $cancelled;
        $this->worksheet = $worksheet;
        $this->daterun = $request->input('daterun', date('Y-m-d'));
	}

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
    	$worksheet = $this->worksheet;
    	$cancelled = $this->cancelled;
        $today = $datetested = $this->daterun;
        $positive_control = $negative_control = null;

        $sample_array = $doubles = [];

        // Processing Abbott worksheet
        if ($this->worksheet->machine_type == 2) {
            $results = collect([]);
            $detailedresults = collect([]);
            $this->getAbbottFormattedData($collection, $results, $detailedresults);
            foreach ($results as $resultkey => $result) {
                // Do not proccess sample without sample identifier
                if(!isset($result['SAMPLE ID'])) break;

                $sample_id = (int) trim($result['SAMPLE ID']);
                $interpretation = rtrim($result['INTERPRETATION'] ?? '');
                $control = rtrim($result['SAMPLE TYPE']);

                // Getting the details of each sample result
                $details = $detailedresults->where('SAMPLE LOCATION', $result['SAMPLE LOCATION']);

                foreach ($this->targets as $targetkey => $target) {
                    $RsTarget = $details->where('ASSAY NAME', $target);
                    $result[$targetkey] = 'Invalid';
                    if (!$RsTarget->isEmpty())
                        $result[$targetkey] = $RsTarget->first()['ASSAY NAME'] . ' ' . $RsTarget->first()['RESULT'];
                }
                // $data_array = Misc::hpv_sample_result($result);
                // dd($data_array);
                if (\Str::contains($result['RESULT'], ['Not Detected'])) {
                    $data_array = ['result' => 1, 'interpretation' => $result['RESULT']];
                } else if (\Str::contains($result['RESULT'], ['Passed', 'Valid'])) {
                    $data_array = ['result' => 6, 'interpretation' => 'Valid'];
                } else if ($result['RESULT'] == NULL || \Str::contains($result['RESULT'], ['Failed', 'Invalid'])) {
                    $data_array = ['result' => 3, 'interpretation' => $result['ERROR CODE/DESCRIPTION']];
                } else if (\Str::contains($result['RESULT'], ['HPV'])) {
                    $data_array = ['result' => 2, 'interpretation' => $result['RESULT']];
                }

                if(\Str::contains($result['SAMPLE ID'], 'POS')){
                    $positive_control = $data_array;
                    continue;
                }
                else if(\Str::contains($result['SAMPLE ID'], 'NEG')){
                    $negative_control = $data_array;
                    continue;
                }
                
                $data_array = array_merge($data_array, ['target_1' => $result['target_1'], 'target_2' => $result['target_2'], 'target_3' => $result['target_3'], 'datemodified' => $today, 'datetested' => $datetested]);
                $sample = CancerSample::find($sample_id);
                if(!$sample) continue;

                $sample->fill($data_array);
                if($cancelled) $sample->worksheet_id = $worksheet->id;
                else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;
                    
                $sample->save();
            }
        }
        // Processing C8800 worksheet
        else {
            // ->pluck();
            // dd($collection->pluck($collection->where(0, "Test")->first()));
            $collection = $collection->groupBy(1);
            $collection->pull('Sample ID');
            $newcollection = collect([]);
            foreach ($collection as $key => $collection) {
                $data = $collection->first();
                foreach ($this->targets as $key => $target) {
                    $RsTarget = $collection->where(5, $target);
                    $data[$key] = $RsTarget->first()[6] ?? 'Invalid';
                }
                $newcollection->push($data);
            }
            
            // Returning this back to collection (old habits)
            $collection = $newcollection;
            foreach ($collection as $key => $data) 
            {
                if(!isset($data['1'])) break;

                $sample_id = (int) trim($data['1']);
                $interpretation = rtrim($data['3'] ?? '');
                $control = rtrim($data['4']);
                $date_tested = $data['8'] ?? NULL;
                $date_tested =  (isset($date_tested)) ? date("Y-m-d", strtotime($data['8'])) :
                                date("Y-m-d");            

                $data_array = Misc::hpv_sample_result($data);

                if(\Str::contains($control, '+')){
                    $positive_control = $data_array;
                    continue;
                }
                else if(\Str::contains($control, '-')){
                    $negative_control = $data_array;
                    continue;
                }

                $data_array = array_merge($data_array, ['datemodified' => $today, 'datetested' => $datetested]);
                $sample = CancerSample::find($sample_id);
                if(!$sample) continue;

                $sample->fill($data_array);
                if($cancelled) $sample->worksheet_id = $worksheet->id;
                else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;
                    
                $sample->save();
            }   
        }
        
        CancerSample::where(['worksheet_id' => $worksheet->id, 'run' => 0])->update(['run' => 1]);
        CancerSample::where(['worksheet_id' => $worksheet->id])->whereNull('repeatt')->update(['repeatt' => 0]);
        CancerSample::where(['worksheet_id' => $worksheet->id])->whereNull('result')->update(['repeatt' => 1]);

        $worksheet->neg_control_interpretation = $negative_control['interpretation'] ?? null;
        $worksheet->neg_control_result = $negative_control['result'] ?? null;

        $worksheet->pos_control_interpretation = $positive_control['interpretation'] ?? null;
        $worksheet->pos_control_result = $positive_control['result'] ?? null;
        $worksheet->daterun = $datetested;
        $worksheet->uploadedby = auth()->user()->id;
        $worksheet->save();

        session(compact('doubles'));

        Misc::requeue($worksheet->id, $worksheet->daterun, 'hpv');
        session(['toast_message' => "The worksheet has been updated with the results."]);
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
