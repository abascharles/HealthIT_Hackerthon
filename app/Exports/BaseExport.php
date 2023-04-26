<?php

namespace App\Exports;

use DB;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BaseExport implements FromQuery, Responsable, WithHeadings
{
	use Exportable;
    use RequestFilters;


	protected $fileName;
	// protected $writerType = Excel::CSV;
	protected $writerType = Excel::XLSX;
	protected $sql;
    protected $request;
    protected $facility_query;

    public function __construct()
    {
        $this->fileName = 'download.xlsx';
        $this->facility_query = null;
        $user = auth()->user();
        if($user && $user->is_facility) $this->facility_query = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}')";
        if($user && $user->is_partner) $this->facility_query = "(partner_id='{$user->facility_id}')";
    }

    public function headings() : array
    {
    	return [];
    }

    public function query()
    {
    	return null;
    }



}
