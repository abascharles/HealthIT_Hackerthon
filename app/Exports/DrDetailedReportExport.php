<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use DB;
use \App\DrSample;

class DrDetailedReportExport implements FromArray, Responsable
{
    use Exportable;
    use RequestFilters;


    protected $fileName;
    // protected $writerType = Excel::CSV;
    protected $writerType = Excel::XLSX;
    protected $sql;
    protected $request;
    protected $facility_query;


    function __construct($request)
    {
        $this->fileName = $this->get_name('DR Detailed', $request) . '.xlsx';
        $this->facility_query = null;
        $user = auth()->user();
        if($user && $user->is_facility) $this->facility_query = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}')";
        if($user && $user->is_partner) $this->facility_query = "(partner_id='{$user->facility_id}')";

		$this->request = $request;

		$this->sql = "
            dr_samples.id, facilitycode, view_facilitys.name AS `facility`, view_facilitys.county, patient, viralpatients.nat, dob, age, 
            datecollected, datereceived, datetested, datedispatched
		";
    }

    /*public function headings() : array
    {
        return [
            'CCC Lab ID', 'Original Specimen ID', 'Date of Collection', 'Date Tested', 'Final Result', 'HIV-1 Subtype',
            'NRTI Mutation(s)', 'NNRTI Mutation(s)', 'PI Mutation(s)', 'INSTI Mutation(s)', 'Comments'
        ];
    }

    public function map($row): array
    {
        return [
            $row->nat, $row->patient, $row->datecollected, $row->datetested, '', '', 
        ];
    }*/


    public function array(): array
    {		
        $string = $this->facility_query;

        $samples = DrSample::selectRaw($this->sql)
            ->with(['dr_call'])
            ->leftJoin('viralpatients', 'dr_samples.patient_id', '=', 'viralpatients.id')
            ->leftJoin('view_facilitys', 'viralpatients.facility_id', '=', 'view_facilitys.id')
            ->when($string, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->where(['status_id' => 1, 'control' => 0, 'repeatt' => 0])
            ->when(true, $this->date_filter($this->request, 'datetested'))
            ->when(true, $this->divisions_filter($this->request))
            ->get();

        $rows = [];
        $rows[] = [
            'CCC Lab ID', 'Original Specimen ID', 'NAT Number', 'Age',
            'MFL Code', 'Facility', 'County',
            'Date of Collection', 'Date Received', 'Date Tested', 
            'Final Result', 'HIV-1 Subtype',
            'NRTI Mutation(s)', 'NNRTI Mutation(s)', 'PI Mutation(s)', 'INSTI Mutation(s)', 'Comments'
        ];

        foreach ($samples as $key => $sample) {

            $nrti = $sample->dr_call->where('drug_class_id', 3)->first()->mutations_string ?? '';
            $nnrti = $sample->dr_call->where('drug_class_id', 2)->first()->mutations_string ?? '';
            $pi = $sample->dr_call->where('drug_class_id', 4)->first()->mutations_string ?? '';
            $insti = $sample->dr_call->where('drug_class_id', 1)->first()->mutations_string ?? '';

            $rows[] = [
                $sample->id, $sample->patient, $sample->nat, $sample->age,
                $sample->facilitycode, $sample->facility, $sample->county, 
                $sample->datecollected, $sample->datereceived, $sample->datetested, 
                '', '', 
                $nrti, $nnrti, $pi, $insti, ''
            ];
        }
        return $rows;
    }
}
