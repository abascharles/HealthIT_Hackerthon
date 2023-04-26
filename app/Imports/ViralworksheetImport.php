<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use \App\MiscViral;
use \App\Viralsample;
use Exception;

class ViralworksheetImport implements ToCollection
{
	protected $worksheet;
	protected $cancelled;
    protected $daterun;

	public function __construct($worksheet, $request)
	{
        $cancelled = false;
        if($worksheet->status_id == 4) $cancelled =  true;
        $worksheet->fill($request->except(['_token', 'upload']));
        $this->cancelled = $cancelled;
        $this->worksheet = $worksheet;
        $this->daterun = $request->input('daterun');
	}

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
    	$worksheet = $this->worksheet;
    	$cancelled = $this->cancelled;
        $today = $datetested = date("Y-m-d");
        $nc = $nc_int = $lpc = $lpc_int = $hpc = $hpc_int = $nc_units = $hpc_units = $lpc_units =  NULL;

        $my = new MiscViral;
        $sample_array = $doubles = [];

        if(env('APP_LAB') == 2 && $worksheet->id == 27677){
            // The Scanned Samples
            $sample_array = \App\ViralsampleView::select('id')->where('worksheet_id', 27691)->where('site_entry', '!=', 2)->get()->pluck('id')->toArray();
        
            $scanned_samples = Viralsample::join('viralbatches', 'viralsamples.batch_id', '=', 'viralbatches.id')
                        ->select('viralsamples.*', 'viralbatches.facility_id')
                        ->whereIn('viralsamples.id', $sample_array)
                        ->orderBy('run', 'desc')
                        ->orderBy('facility_id')->orderBy('batch_id', 'asc')
                        ->orderBy('viralsamples.id', 'asc')
                        ->get();

            $scanned_samples_array = $scanned_samples->pluck('id')->toArray();

            // The Actual Samples
            $sample_array = \App\ViralsampleView::select('id')->where('worksheet_id', 27677)->where('site_entry', '!=', 2)->get()->pluck('id')->toArray();
        
            $actual_samples = Viralsample::join('viralbatches', 'viralsamples.batch_id', '=', 'viralbatches.id')
                        ->select('viralsamples.*', 'viralbatches.facility_id')
                        ->whereIn('viralsamples.id', $sample_array)
                        ->orderBy('run', 'desc')
                        ->orderBy('facility_id')->orderBy('batch_id', 'asc')
                        ->orderBy('viralsamples.id', 'asc')
                        ->get();

            $actual_samples_array = $actual_samples->pluck('id')->toArray();

            foreach ($collection as $key => $value) 
            {
                if(!isset($value[1])) break;
                $sample_id = $value[1];
                $interpretation = $value[6];
                $result_array = MiscViral::sample_result($interpretation);

                MiscViral::dup_worksheet_rows($doubles, $sample_array, $sample_id, $interpretation);

                if(!is_numeric($sample_id)){
                    $control = $value[4];
                    if($control == 'HxV H (+) C'){
                        $hpc = $result_array['result'];
                        $hpc_int = $result_array['interpretation'];
                        $hpc_units = $result_array['units'];                        
                    }
                    else if($control == 'HxV L (+) C'){
                        $lpc = $result_array['result'];
                        $lpc_int = $result_array['interpretation'];
                        $lpc_units = $result_array['units'];
                    }
                    else if($control == '(-) C'){
                        $nc = $result_array['result'];
                        $nc_int = $result_array['interpretation']; 
                        $nc_units = $result_array['units']; 
                    }
                }

                $data_array = array_merge(['datemodified' => $today, 'datetested' => $today], $result_array);

                $sample_id = (int) $sample_id;
                $actual_key = array_search($sample_id, $scanned_samples_array);
                if($actual_key === false) continue;

                $sample = Viralsample::find($actual_samples_array[$actual_key]);
                if(!$sample) continue;

                $sample->fill($data_array);
                if($cancelled) $sample->worksheet_id = $worksheet->id;
                else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;
                $sample->save();
            }

        }

        // Abbott
        else if($worksheet->machine_type == 2)
        {
            $date_tested = $this->daterun;
            $datetested = MiscViral::worksheet_date($date_tested, $worksheet->created_at);            

            $bool = false;

            foreach ($collection as $key => $value) {
                if($value[5] == "RESULT"){
                    $bool = true;
                    continue;
                }

                if($bool){
                    $sample_id = $value[1];
                    $result = $value[5];
                    $interpretation = $value[6];
                    $error = $value[10];

                    $result_array = MiscViral::sample_result($result, $error);

                    MiscViral::dup_worksheet_rows($doubles, $sample_array, $sample_id, $interpretation);

                    if($sample_id == "HIV_NEG"){
                        $nc = $result_array['result'];
                        $nc_int = $result_array['interpretation']; 
                        $nc_units = $result_array['units']; 
                    }else if($sample_id == "HIV_HIPOS"){
                        $hpc = $result_array['result'];
                        $hpc_int = $result_array['interpretation'];
                        $hpc_units = $result_array['units'];
                    }else if($sample_id == "HIV_LOPOS"){
                        $lpc = $result_array['result'];
                        $lpc_int = $result_array['interpretation'];
                        $lpc_units = $result_array['units'];
                    }

                    $data_array = array_merge(['datemodified' => $today, 'datetested' => $datetested], $result_array);
                    // $search = ['id' => $sample_id, 'worksheet_id' => $worksheet->id];
                    // Viralsample::where($search)->update($data_array);

                    $sample = Viralsample::find($sample_id);
                    if(!$sample) continue;

                    $sample->fill($data_array);
                    if($cancelled) $sample->worksheet_id = $worksheet->id;
                    else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;

                    $sample->save();
                }
                if($bool && $value[5] == "RESULT") break;
            }
        }
        // C8800
        else if($worksheet->machine_type == 3){
            foreach ($collection as $key => $value) 
            {
                if(!isset($value[1])) break;
                $sample_id = $value[1];
                $interpretation = $value[6];
                $result_array = MiscViral::sample_result($interpretation);

                MiscViral::dup_worksheet_rows($doubles, $sample_array, $sample_id, $interpretation);

                if(!is_numeric($sample_id)){
                    $control = $value[4];
                    if($control == 'HxV H (+) C'){
                        $hpc = $result_array['result'];
                        $hpc_int = $result_array['interpretation'];
                        $hpc_units = $result_array['units'];                        
                    }
                    else if($control == 'HxV L (+) C'){
                        $lpc = $result_array['result'];
                        $lpc_int = $result_array['interpretation'];
                        $lpc_units = $result_array['units'];
                    }
                    else if($control == '(-) C'){
                        $nc = $result_array['result'];
                        $nc_int = $result_array['interpretation']; 
                        $nc_units = $result_array['units']; 
                    }
                }

                $datetested = $today;

                /*try {
                    $dt = Carbon::parse($value[12]);
                    $date_tested = $dt->toDateString();                    
                    $datetested = MiscViral::worksheet_date($date_tested, $worksheet->created_at);
                } catch (Exception $e) {
                    $datetested = $today;
                }*/

                $data_array = array_merge(['datemodified' => $today, 'datetested' => $datetested], $result_array);


                $sample_id = (int) $sample_id;
                $sample = Viralsample::find($sample_id);
                if(!$sample) continue;

                $sample->fill($data_array);
                if($cancelled) $sample->worksheet_id = $worksheet->id;
                else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;
                $sample->save();
            }
        }
        // Panther from Alupe
        else if($worksheet->machine_type == 4 && env('APP_LAB') == 3){
            foreach ($collection as $key => $value) 
            {
                $sample_id = (int) trim($value[0]);

                $interpretation = $value[3];

                MiscViral::dup_worksheet_rows($doubles, $sample_array, $sample_id, $interpretation);

                if($value[1] == "Control"){
                    $name = strtolower($value[0]);
                    $result_array = MiscViral::sample_result($interpretation);

                    if(\Str::contains($name, 'low')){
                        $lpc = $result_array['result'];
                        $lpc_int = $result_array['interpretation'];
                        $lpc_units = $result_array['units'];
                    }
                    else if(\Str::contains($name, 'high')){
                        $hpc = $result_array['result'];
                        $hpc_int = $result_array['interpretation'];
                        $hpc_units = $result_array['units'];
                    }
                    else if(\Str::contains($name, 'negative')){
                        $nc = $result_array['result'];
                        $nc_int = $result_array['interpretation']; 
                        $nc_units = $result_array['units'];
                    }
                    continue;
                }

                $result_array = MiscViral::sample_result($interpretation);
                $data_array = array_merge(['datemodified' => $today, 'datetested' => $datetested], $result_array);

                $sample = Viralsample::find($sample_id);
                if(!$sample) continue;

                $sample->fill($data_array);
                if($cancelled) $sample->worksheet_id = $worksheet->id;
                else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;

                $sample->save();
            }
        }
        // Panther
        else if($worksheet->machine_type == 4){
            foreach ($collection as $key => $value) 
            {
                $sample_id = (int) trim($value[0]);

                $interpretation = $value[4];

                MiscViral::dup_worksheet_rows($doubles, $sample_array, $sample_id, $interpretation);

                if($value[19] == "Control"){
                    $name = strtolower($value[20]);
                    $result_array = MiscViral::sample_result($interpretation);

                    if(\Str::contains($name, 'low')){
                        $lpc = $result_array['result'];
                        $lpc_int = $result_array['interpretation'];
                        $lpc_units = $result_array['units'];
                    }
                    else if(\Str::contains($name, 'high')){
                        $hpc = $result_array['result'];
                        $hpc_int = $result_array['interpretation'];
                        $hpc_units = $result_array['units'];
                    }
                    else if(\Str::contains($name, 'negative')){
                        $nc = $result_array['result'];
                        $nc_int = $result_array['interpretation']; 
                        $nc_units = $result_array['units'];
                    }
                    continue;
                }

                $result_array = MiscViral::sample_result($interpretation);
                $data_array = array_merge(['datemodified' => $today, 'datetested' => $datetested], $result_array);

                $sample = Viralsample::find($sample_id);
                if(!$sample) continue;

                $sample->fill($data_array);
                if($cancelled) $sample->worksheet_id = $worksheet->id;
                else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;

                $sample->save();
            }
        }
        // Taqman
        else
        {
            foreach ($collection as $key => $value) 
            {
                $date_tested=date("Y-m-d", strtotime($value[3]));
                $datetested = MiscViral::worksheet_date($date_tested, $worksheet->created_at);

                $sample_id = trim($value[4]);
                $interpretation = $value[8];
                $error = $value[10];

                MiscViral::dup_worksheet_rows($doubles, $sample_array, $sample_id, $interpretation);

                $result_array = MiscViral::sample_result($interpretation, $error);

                $sample_type = $value[5];

                if($sample_type == "NC"){
                    $nc = $result_array['result'];
                    $nc_int = $result_array['interpretation']; 
                    $nc_units = $result_array['units']; 
                }
                else if($sample_type == "HPC"){
                    $hpc = $result_array['result'];
                    $hpc_int = $result_array['interpretation'];
                    $hpc_units = $result_array['units'];
                }
                else if($sample_type == "LPC"){
                    $lpc = $result_array['result'];
                    $lpc_int = $result_array['interpretation'];
                    $lpc_units = $result_array['units'];
                }

                // $data_array = ['datemodified' => $today, 'datetested' => $datetested, 'interpretation' => $result_array['interpretation'], 'result' => $result_array['result'], 'units' => $result_array['units']];
                // $search = ['id' => $sample_id, 'worksheet_id' => $worksheet->id];
                // Viralsample::where($search)->update($data_array);

                $data_array = array_merge(['datemodified' => $today, 'datetested' => $datetested], $result_array);

                // $sample_id = substr($sample_id, 0, -1);
                $sample_id = (int) $sample_id;


                $sample = Viralsample::find($sample_id);
                if(!$sample) continue;

                $sample->fill($data_array);
                // $sample->worksheet_id = $worksheet->id;
                if($cancelled) $sample->worksheet_id = $worksheet->id;
                else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;
                
                $sample->save();

            }
        }

        /*if($doubles){
            session(['toast_error' => 1, 'toast_message' => "Worksheet {$worksheet->id} upload contains duplicate rows. Please fix and then upload again."]);
            $file = "Samples_Appearing_More_Than_Once_In_Worksheet_" . $worksheet->id;
        
            Excel::create($file, function($excel) use($doubles){
                $excel->sheet('Sheetname', function($sheet) use($doubles) {
                    $sheet->fromArray($doubles);
                });
            })->download('csv');
        }*/

        if($doubles) session(['doubles' => $doubles]);


        Viralsample::where(['worksheet_id' => $worksheet->id])->where('run', 0)->update(['run' => 1]);
        Viralsample::where(['worksheet_id' => $worksheet->id])->whereNull('repeatt')->update(['repeatt' => 0]);
        Viralsample::where(['worksheet_id' => $worksheet->id])->whereNull('result')->update(['repeatt' => 1]);

        $worksheet->neg_units = $nc_units;
        $worksheet->neg_control_interpretation = $nc_int;
        $worksheet->neg_control_result = $nc;

        $worksheet->hpc_units = $hpc_units;
        $worksheet->highpos_control_interpretation = $hpc_int;
        $worksheet->highpos_control_result = $hpc;

        $worksheet->lpc_units = $lpc_units;
        $worksheet->lowpos_control_interpretation = $lpc_int;
        $worksheet->lowpos_control_result = $lpc;

        $worksheet->daterun = $datetested;
        $worksheet->uploadedby = auth()->user()->id;

        $worksheet->save();

        MiscViral::requeue($worksheet->id, $worksheet->daterun);
        session(['toast_message' => "The worksheet has been updated with the results."]);
    }
}
