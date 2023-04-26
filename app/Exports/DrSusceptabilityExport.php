<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

use DB;
use \App\MiscDr;
use \App\DrSample;


class DrSusceptabilityExport implements FromArray, WithEvents, Responsable
{
    use Exportable;
    use RequestFilters;

    
    public $request;
    protected $fileName;

    public function __construct($request)
    {
        ini_set('memory_limit', '-1');
        $this->fileName = $this->get_name('DR Susceptablity Report', $request) . '.xlsx';
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        }); 
        $this->request = $request;
    }

    public function array(): array
    {
        $request = $this->request;
        $cell_array = MiscDr::$call_array;
        // dd($cell_array);
        $regimen_classes = DB::table('regimen_classes')->get();
        $date_column = "datetested";
        $user = auth()->user();
        $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}')";

        $samples = DrSample::select('dr_samples.*', 'viralpatients.patient', 'dr_samples.age', 'facilitycode', 'view_facilitys.name AS facility', 'view_facilitys.county')
            ->where(['status_id' => 1, 'control' => 0, 'repeatt' => 0])
            ->leftJoin('viralpatients', 'dr_samples.patient_id', '=', 'viralpatients.id')
            ->leftJoin('view_facilitys', 'viralpatients.facility_id', '=', 'view_facilitys.id')
            ->with(['dr_call.call_drug'])
            ->when(($user->user_type_id == 5), function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->when(true, $this->date_filter($request, $date_column))
            ->when(true, $this->divisions_filter($request))
            ->get();

        $top = ['', '', '', '', '', '', 'Drug Classes', ];
        $second = ['Sequence ID', 'Original Sample ID', 'Nat Number', 'Age', 'MFL Code', 'Facility', 'County',];

        foreach ($regimen_classes as $key => $value) {
            $top[] = $value->drug_class;
            $second[] = $value->short_name;
        }

        $rows[0] = $top;
        $rows[1] = $second;

        $other_columns = 8;

        foreach ($samples as $sample_key => $sample) {
            $row = [$sample->id, $sample->patient, $sample->nat, $sample->age, $sample->facilitycode, $sample->facility, $sample->county, ];

            foreach ($regimen_classes as  $regimen_key => $regimen) {
                $call = '';

                $regimen_index = $regimen_key + $other_columns;

                foreach ($sample->dr_call as $dr_call) {
                    foreach ($dr_call->call_drug as $call_drug) {
                        if($call_drug->short_name_id != $regimen->id) continue;

                        $call = $call_drug->call;
                        if($regimen_index < 27){
                            $cell_array[$call]['cells'][] = chr(64 + $regimen_index) . ($sample_key + 3);
                        }
                        else{
                            $new_key = $regimen_index - 26;
                            $cell_array[$call]['cells'][] = 'A' . chr(64 + $new_key) . ($sample_key + 3);
                        }
                    }
                }
                $row[] = $call;
            }
            $rows[] = $row;
        }
        session(['cell_array' => $cell_array]);
        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => [self::class, 'afterSheet'],
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
        $cell_array = session()->pull('cell_array');
        foreach ($cell_array as $my_call) {
            foreach ($my_call['cells'] as $my_cell) {
                $colour = ltrim($my_call['resistance_colour'], '#');

                $event->sheet->styleCells($my_cell, [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_PATTERN_LIGHTUP,
                        'startColor' => [
                            'argb' => $colour,
                        ],
                        'endColor' => [
                            'argb' => $colour,
                        ],
                    ]
                ]);
            }
        }
    }
}
