<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ViralInterLabSampleExport implements FromCollection, WithHeadings
{

	private $import_data = NULL;
	public function __construct($data)
	{
		$this->import_data = $data;
	}
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $collection = $this->import_data;
        return new Collection($collection);
    }

    public function headings(): array
    {
    	return [
    		'Viral Batches'
    	];
    }
}
