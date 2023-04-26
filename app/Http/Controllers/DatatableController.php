<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Datatable;

class DatatableController extends Controller
{
	public $patient_sms_columns = [
		['db' => 'id', 'dt' => 'DT_RowId' ],
		['db' => 'facilityname', 'dt' => 0 ],
		['db' => 'patient', 'dt' => 1 ],
		['db' => 'patient_name', 'dt' => 2 ],
		['db' => 'age', 'dt' => 3 ],
		['db' => 'patient_phone_no', 'dt' => 4 ],
		['db' => 'datecollected', 'dt' => 5, ],
		['db' => 'datetested', 'dt' => 6, ],
		['db' => 'result', 'dt' => 7, ],
		['db' => 'datedispatched', 'dt' => 8, ],
		['db' => 'time_result_sms_sent', 'dt' => 9, ],
	];

	// $db_columns = [ ['db' => 'id', 'dt' => 'DT_RowId' ], ['db' => 'facilityname', 'dt' => 1 ], ['db' => 'patient', 'dt' => 2 ], ['db' => 'patient_name', 'dt' => 3 ], ['db' => 'age', 'dt' => 4 ], ['db' => 'patient_phone_no', 'dt' => 5 ], ['db' => 'datecollected', 'dt' => 6, ], ['db' => 'datetested', 'dt' => 7, ], ['db' => 'result', 'dt' => 8, ], ['db' => 'datedispatched', 'dt' => 9, ], ['db' => 'time_result_sms_sent', 'dt' => 10, ], ];


	public function sms_view($param='eid')
	{
		$url = url()->current();
		if(strpos($url, 'viral')) $param = 'vl';
		$tl = strtoupper($param);
		return view('tables.sms_view', ['type' => $param])->with('pageTitle', "{$tl} Patient SMS Log");
	}



    public function sms_log(Request $request, $param)
    {
    	$draw = $request->input('draw');
    	$search = $request->input('search');
    	if($search && $search['value'] != '' && strlen($search['value']) < 4){
	        return [
	        	'draw' => $draw ? intval($draw) : 0,
	        	'recordsTotal' => $recordsTotal ?? 0,
	        	'recordsFiltered' => $recordsFiltered ?? 0,
	        	'data' => [],
	        ];
    	}

        $user = auth()->user();
        $string = "1";
        if($user->user_type_id == 5) $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";

        $class = \App\Synch::$synch_arrays[$param]['sampleview_class'];
        $query = $class::select(array_column($this->patient_sms_columns, 'db'))->whereRaw($string)->whereNotNull('time_result_sms_sent');
        Datatable::limit($request, $query);
        Datatable::order($request, $query, $this->patient_sms_columns);
        Datatable::filter($request, $query, $this->patient_sms_columns);

        // DB::enableQueryLog();
        $rows = $query->get();
        $data = [];

        // return DB::getQueryLog();

        $links = [
        	'eid' => 'sample',
        	'vl' => 'viralsample',
        ];

        $results = [
        	'1' => 'Negative',
        	'2' => 'Positive',
        	'3' => 'Failed',
        	'5' => 'Collect New Sample',
        ];

        foreach ($rows as $row) {
        	$d = [];
        	foreach ($row->toArray() as $key => $value) {
        		if($key == 'id'){
        			$d['DT_RowId'] = 'row_' . $value; 
        		}
        		else{
        			$d[$key] = $value;
        		}
        	}
        	$d['action'] = "<a href='" . url($links[$param] . "/sms/{$row->id}") . "'> Resend SMS </a> ";
        	if($param == 'eid') $d['result'] = $results[$row->result] ?? '';
        	$data[] = $d;
        }

        // return $request->all();

    	/*$data->transform(function ($sample, $key) use ($param, $links){
    		if($param == 'eid') $sample->result = $sample->result_name;
    		$sample->action_link = "<a href='" url($links[$param] . "/sms/{$sample->id}") "'> ";

    		return $sample;
		});*/


        // Records total
        $recordsTotal = $class::selectRaw('COUNT(id) as my_count')->whereRaw($string)->whereNotNull('time_result_sms_sent')->first()->my_count;

        // Records filtered
        $query = $class::selectRaw('COUNT(id) as my_count')->whereRaw($string)->whereNotNull('time_result_sms_sent');
        Datatable::filter($request, $query, $this->patient_sms_columns);
        $recordsFiltered = $query->first()->my_count;

        return [
        	'draw' => $draw ? intval($draw) : 0,
        	'recordsTotal' => $recordsTotal ?? 0,
        	'recordsFiltered' => $recordsFiltered ?? 0,
        	'data' => $data,
        ];
    }

    public function download_sms_excel(Request $request, $param)
    {
        $user = auth()->user();
        $string = "1";
        if($user->user_type_id == 5) $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}' OR lab_id='{$user->facility_id}')";

        $class = \App\Synch::$synch_arrays[$param]['sampleview_class'];
        $rows = $class::select(array_column($this->patient_sms_columns, 'db'))
            ->whereRaw($string)
            ->whereNotNull('time_result_sms_sent')
            ->whereBetween('time_result_sms_sent', [$request->input('from_date'), $request->input('to_date')])
            ->get();

        $data = [];

        $results = [
            '1' => 'Negative',
            '2' => 'Positive',
            '3' => 'Failed',
            '5' => 'Collect New Sample',
        ];

        foreach ($rows as $row) {
            $d = [];
            foreach ($row->toArray() as $key => $value) {
                if($key == 'id'){
                    $d['Lab ID'] = $value; 
                }
                else{
                    $d[$key] = $value;
                }
            }
            if($param == 'eid') $d['result'] = $results[$row->result] ?? '';
            $data[] = $d;
        }
        return \App\Common::csv_download($data, $param . '-sms-log');
    }

}
