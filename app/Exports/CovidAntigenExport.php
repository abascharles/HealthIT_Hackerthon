<?php

namespace App\Exports;

use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CovidAntigenExport implements WithMultipleSheets, Responsable
{
	use Exportable;

	protected $fileName;
	// protected $writerType = Excel::CSV;
	protected $writerType = Excel::XLSX;
	protected $from_date;
	protected $to_date;

	public function __construct($request)
	{
		$this->from_date = $request->input('date_filter');
		$this->to_date = $request->input('date_filter_to');


		$this->fileName = 'Antigen_Results_upload_from_' . $this->from_date . '_to_' . $this->to_date . '.xlsx';
	}


	public function sheets(): array
	{
		$sheets = [];
		$sheets[] = new CovidAntigenResults($this->from_date, $this->to_year);
		return $sheets;
	}

}
