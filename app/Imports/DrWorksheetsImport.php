<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class DrWorksheetsImport  implements WithMultipleSheets 
{

	public function sheets(): array
    {
        session([
            'extraction_worksheets' => [
                'w_114' => [],
                'w_115' => [],
                'w_117' => [],
                'w_118' => [],
                'w_121' => [],
                'w_122' => [],
                'w_123' => [],
                'w_125' => [],
                'w_126' => [],
            ],
            'duplicates' => [],
            
        ]);
        return [
            'Worksheet 114' => new DrWorksheetImport(114),
            'Worksheet 115' => new DrWorksheetImport(115),
            'Worksheet 117' => new DrWorksheetImport(117),
            'Worksheet 118' => new DrWorksheetImport(118),
            'Worksheet 121' => new DrWorksheetImport(121),
            'Worksheet 122' => new DrWorksheetImport(122),
            'Worksheet 123' => new DrWorksheetImport(123),
            'Worksheet 125' => new DrWorksheetImport(125),
            'Worksheet 126' => new DrWorksheetImport(126),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // E.g. you can log that a sheet was not found.
        // info("Sheet {$sheetName} was skipped");
    }
}