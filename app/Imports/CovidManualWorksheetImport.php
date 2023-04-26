<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use \App\MiscCovid;
use \App\CovidSample;
use \App\CovidKitType;
use Str;
use Exception;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class CovidManualWorksheetImport implements OnEachRow, WithHeadingRow
{
	protected $worksheet;
	protected $cancelled;
    protected $daterun;
    protected $covid_kit_type;

	public function __construct($worksheet, $request)
	{
        $cancelled = false;
        if($worksheet->status_id == 4) $cancelled =  true;
        $worksheet->fill($request->except(['_token', 'upload']));
        $this->covid_kit_type = $worksheet->kit_type;
        $this->cancelled = $cancelled;
        $this->worksheet = $worksheet;
        $this->daterun = $request->input('daterun');
        session(['positive_control' => [], 'negative_control' => []]);
	}


    public function onRow(Row $row)
    {
        $row = json_decode(json_encode($row->toArray()));
        $today = $datemodified = $datetested = date("Y-m-d");
        $positive_control = session('positive_control');
        $negative_control = session('negative_control');

        $target1 = $this->covid_kit_type->target1;
        $target2 = $this->covid_kit_type->target2;
        $control_gene = $this->covid_kit_type->control_gene;

        if(!isset($row->sample_name)){
            session(['toast_message' => 'Sample Name column is missing', 'toast_error' => 1]);
            return;
        }
        if(!isset($row->target_name)){
            session(['toast_message' => 'Target Name column is missing', 'toast_error' => 1]);
            return;
        }
        if(!isset($row->ct)){
            session(['toast_message' => 'CT column is missing', 'toast_error' => 1]);
            return;
        }
        $sample_id = $row->sample_name;

        $target_column = null;
        if($row->target_name == $target1) $target_column = 'target1';
        else if($target2 && $row->target_name == $target2) $target_column = 'target2';

        if(!is_numeric($sample_id)){
            $sample_id = strtolower($sample_id);
            if(Str::contains($sample_id, ['nc', 'neg'])){
                if($row->target_name == $control_gene && !is_numeric($row->ct)){
                    $negative_control = ['interpretation' => 'Failed', 'result' => 3];
                }else if($target_column){
                    $negative_control[$target_column] = $row->ct;
                }
                session(['negative_control' => $negative_control]);
                return;
            }
            else if(Str::contains($sample_id, ['pc', 'pos'])){
                if($row->target_name == $control_gene && !is_numeric($row->ct)){
                    $positive_control = ['interpretation' => 'Failed', 'result' => 3];
                }else if($target_column){
                    $positive_control[$target_column] = $row->ct;
                }
                session(['positive_control' => $positive_control]);
                return;
            }
        }

        $sample = CovidSample::find($sample_id);
        if(!$sample && env('APP_LAB') == 25) $sample = CovidSample::where('kemri_id', $sample_id)->first();
        if(!$sample) return;
        if($sample->result == 3) return;

        $a = true;

        if($cancelled) $sample->worksheet_id = $worksheet->id;
        else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) return;

        if($row->target_name == $control_gene && !is_numeric($row->ct)){
            $sample->interpretation = 'Internal Control Failed';
            $sample->result = 3;
            $sample->repeatt = 1;
            $sample->pre_update();
            return;
        }else if(!$target_column) return;

        $sample->$target_column = $row->ct;
        
        $sample->calc_result();
    }

    /**
    * @param Collection $collection
    */
    /*public function collection(Collection $collection)
    {
    	$worksheet = $this->worksheet;
    	$cancelled = $this->cancelled;

        $today = $datemodified = $datetested = date("Y-m-d");
        $positive_control = $negative_control = [];

        $target1 = $this->covid_kit_type->target1;
        $target2 = $this->covid_kit_type->target2;
        $control_gene = $this->covid_kit_type->control_gene;


        foreach ($collection as $key => $row) {
            if(!isset($row->sample_name)){
                session(['toast_message' => 'Sample Name column is missing', 'toast_error' => 1]);
                return;
            }
            if(!isset($row->target_name)){
                session(['toast_message' => 'Target Name column is missing', 'toast_error' => 1]);
                return;
            }
            if(!isset($row->ct)){
                session(['toast_message' => 'CT column is missing', 'toast_error' => 1]);
                return;
            }
            $sample_id = $row->sample_name;

            $target_column = null;
            if($row->target_name == $target1) $target_column = 'target1';
            else if($target2 && $row->target_name == $target2) $target_column = 'target2';

            if(!is_numeric($sample_id)){
                $sample_id = strtolower($sample_id);
                if(Str::contains($sample_id, ['nc', 'neg'])){
                    if($row->target_name == $control_gene && !is_numeric($row->ct)){
                        $negative_control = ['interpretation' => 'Failed', 'result' => 3];
                    }else if($target_column){
                        $negative_control[$target_column] = $row->ct;
                    }
                }
                else if(Str::contains($sample_id, ['pc', 'pos'])){
                    if($row->target_name == $control_gene && !is_numeric($row->ct)){
                        $positive_control = ['interpretation' => 'Failed', 'result' => 3];
                    }else if($target_column){
                        $positive_control[$target_column] = $row->ct;
                    }
                }
            }

            $sample = CovidSample::find($sample_id);
            if(!$sample) continue;
            if($sample->result == 3) continue;

            $a = true;

            if($cancelled) $sample->worksheet_id = $worksheet->id;
            else if($sample->worksheet_id != $worksheet->id || $sample->dateapproved) continue;

            if($row->target_name == $control_gene && !is_numeric($row->ct)){
                $sample->interpretation = 'Internal Control Failed';
                $sample->result = 3;
                $sample->repeatt = 1;
                $sample->pre_update();
                continue;
            }else if(!$target_column) continue;

            $sample->$target_column = $row->ct;
            
            $sample->calc_result();
        }

        CovidSample::where(['worksheet_id' => $worksheet->id])->whereNull('result')->update(['repeatt' => 1]);

        $worksheet->neg_control_interpretation = $negative_control['interpretation'] ?? null;
        $worksheet->neg_control_result = $negative_control['result'] ?? null;

        if(!$worksheet->neg_control_result){
            if((isset($negative_control['target1']) && is_numeric($negative_control['target1']))  || (isset($negative_control['target2']) && is_numeric($negative_control['target2']))){
                $worksheet->neg_control_result = 2;
            }
            else if(isset($negative_control['target1']) || isset($negative_control['target2'])){
                $worksheet->neg_control_result = 1;
            }
        }

        $worksheet->pos_control_interpretation = $positive_control['interpretation'] ?? null;
        $worksheet->pos_control_result = $positive_control['result'] ?? null;

        if(!$worksheet->pos_control_result){
            if((isset($positive_control['target1']) && is_numeric($positive_control['target1']))  || (isset($positive_control['target2']) && is_numeric($positive_control['target2']))){
                $worksheet->pos_control_result = 2;
            }
            else if(isset($positive_control['target1']) || isset($positive_control['target2'])){
                $worksheet->pos_control_result = 1;
            }
        }

        $worksheet->daterun = $datetested;
        $worksheet->uploadedby = auth()->user()->id;
        $worksheet->save();

        session(['toast_message' => "The worksheet has been updated with the results."]);
    }*/
}
