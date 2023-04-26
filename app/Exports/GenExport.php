<?php

namespace App\Exports;

use DB;

class GenExport
{

	public function csv_save($data, $filename)
	{
		$fp = fopen($filename, 'w');

		$first = [];

		foreach ($data[0] as $key => $value) {
			$first[] = $key;
		}
		fputcsv($fp, $first);

		foreach ($data as $key => $value) {
			fputcsv($fp, $value);
		}
		fclose($fp);
	}

	public static function csv_download($data)
	{
		$fp = fopen('php://output', 'w');

		$first = [];

		foreach ($data[0] as $key => $value) {
			$first[] = $key;
		}
		fputcsv($fp, $first);

		foreach ($data as $key => $value) {
			fputcsv($fp, $value);
		}
		
		fclose($fp);
	}

}