<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use \App\MiscCovid;
use \App\Cd4Sample;
use Carbon\Carbon;
use Exception;

class Cd4WorksheetImport implements ToCollection, WithHeadingRow
{
	protected $worksheet;
	protected $cancelled;
    protected $daterun;

	public function __construct($worksheet)
	{
        $this->worksheet = $worksheet;
	}

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
    	$worksheet = $this->worksheet;

        foreach ($collection as $key => $value) 
        {
            try {
                $daterun = Carbon::parse($value['date_analyzed']);
                $daterun = $daterun->toDateString() ?? date('Y-m-d');                
            } catch (Exception $e) {
                $daterun = null;
            }
            
            $sample = Cd4Sample::find($value['sample_id']);
            if($sample) {
                if ($value["average_cd3cd4_lymph"] != "") { 
                    $repeatt=0;
                } else { 
                    $repeatt=1;
                }

                if ($value["average_cd3cd4_abs_cnt"] != "") { 
                    $repeatt=0;
                } else { 
                    $repeatt=1;
                }
                // if ($value[9] != "") { 
                //     $repeatt=2;
                // } else { 
                //     $repeatt=1;
                // }

                // if ($value[10] != "") { 
                //     $repeatt=2;
                // } else { 
                //     $repeatt=1;
                // }

                // if ($value[11] != "") { 
                //     $repeatt=2;
                // } else { 
                //     $repeatt=1;
                // }
            
                // if ($value[12] != "") { 
                //     $repeatt=2;
                // } else { 
                //     $repeatt=1;
                // }
           
                // if ($value[21] != "") { 
                //     $repeatt=2;
                // } else { 
                //     $repeatt=1;
                // }

                $sample->THelperSuppressorRatio = $value['t_helpersuppressor_ratio'] ?? NULL;
                $sample->AVGCD3percentLymph = $value['average_cd3_lymph'] ?? NULL;
                $sample->AVGCD3AbsCnt = $value['average_cd3_abs_cnt'] ?? NULL;
                $sample->AVGCD3CD4percentLymph = $value['average_cd3cd4_lymph'] ?? NULL;
                $sample->AVGCD3CD4AbsCnt = $value['average_cd3cd4_abs_cnt'] ?? NULL;
                $sample->AVGCD3CD8percentLymph = $value['average_cd3cd8_lymph'] ?? NULL;
                $sample->AVGCD3CD8AbsCnt = $value['average_cd3cd8_abs_cnt'] ?? NULL;
                $sample->AVGCD3CD4CD8percentLymph = $value['average_cd3cd4cd8_lymph'] ?? NULL;
                $sample->AVGCD3CD4CD8AbsCnt = $value['average_cd3cd4cd8_abs_cnt'] ?? NULL;
                $sample->CD45AbsCnt = $value['cd45_abs_cnt'] ?? NULL;
                $sample->datemodified = date('Y-m-d');
                $sample->datetested = date('Y-m-d');
                $sample->status_id = 4;
                $sample->repeatt = $repeatt;
                // dd($sample);
                $sample->save();
            }

        }
    }
}
