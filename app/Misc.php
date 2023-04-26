<?php

namespace App;

use GuzzleHttp\Client;

use App\Common;
use App\CancerSample;
use App\CancerSampleView;
use App\Sample;
use App\SampleView;
use App\Lookup;


class Misc extends Common
{

	public static function requeue($worksheet_id, $daterun, $type='eid')
	{
        if ($type == 'eid') {
            $samples_array = SampleView::where(['worksheet_id' => $worksheet_id])->where('site_entry', '!=', 2)->get()->pluck('id');
            $samples = Sample::whereIn('id', $samples_array)->get();

            Sample::whereIn('id', $samples_array)->update(['repeatt' => 0, 'datetested' => $daterun]);
        } else {
            $samples_array = CancerSampleView::where(['worksheet_id' => $worksheet_id])->where('site_entry', '!=', 2)->get()->pluck('id');
            $samples = CancerSample::whereIn('id', $samples_array)->get();

            CancerSample::whereIn('id', $samples_array)->update(['repeatt' => 0, 'datetested' => $daterun]);
        }
        

		// Default value for repeatt is 0

		foreach ($samples as $sample) {
            if(!$sample->result){
                $sample->result = 3;
                $sample->repeatt = 1;
                $sample->save();
            }
            $a = true;
			if($sample->parentid == 0){
                if (session('testingSystem') == 'HPV') {
                    if($sample->result == 3){
                        $sample->repeatt = 1;
                        $sample->save();
                    }
                } else {
                    if($sample->result == 2 || $sample->result == 3){
                        $sample->repeatt = 1;
                        $sample->save();
                    }
                }				
			}
			else{
                $original = $sample->parent;

				if($sample->run == 2){
                    if (session('testingSystem') == 'HPV') {
                        if( ($sample->result == 3 && $original->result == 3) )
                        {
                            $sample->repeatt = 1;
                            $sample->save();
                        }
                    } else {
                        if( ($sample->result == 3 && $original->result == 3) || 
                            ($sample->result == 2 && $original->result == 3) || 
                            ($sample->result != 2 && $original->result == 2) )
                        {
                            $sample->repeatt = 1;
                            $sample->save();
                        }
                    }
					
				}

				else if($sample->run == 3){
					$second = self::check_run($original->id, 2);
                    if (session('testingSystem') == 'HPV') {
                        if( ($sample->result == 3 && $second->result == 3 && $original->result == 3) )
                        {
                            $sample->repeatt = 1;
                            $sample->save();
                        }
                    } else {
                        if( ($sample->result == 3 && $second->result == 3 && $original->result == 3) ||
                            ($sample->result == 3 && $second->result == 2 && $original->result == 3) ||
                            ($original->result == 2 && $second->result == 1 && $sample->result == 2) ||
                            ($original->result == 2 && $second->result == 3 && $sample->result == 3) )
                        {
                            $sample->repeatt = 1;
                            $sample->save();
                        }
                    }
					
				}
				else if($sample->run == 4){
					$second = self::check_run($original->id, 2);
					$third = self::check_run($original->id, 3);
					if( ($sample->result == 3 && $second->result == 3 && $third->result == 3 && $original->result == 3))
					{
						$sample->repeatt = 1;
						$sample->save();
					}
				}
                else{
                    if($sample->result == 3) $sample->result=5;
                    $sample->save();
                }
			}
		}
		return true;
	}

    public static function sample_result($result, $error=null)
    {
        $str = strtolower($result);


        if(\Str::contains($str, ['non']) && \Str::contains($str, ['reactive'])){
            $res = 1;
        }
        else if(\Str::contains($str, ['not']) && \Str::contains($str, ['detected'])){
            $res = 1;
        }
        else if(\Str::contains($result, ['1', '>']) || \Str::contains($str, ['detected', 'reactive'])){
            $res = 2;
        }
        else if(\Str::contains($str, ['invalid'])){
            $res = 3;
        }
        else if(\Str::contains($str, ['valid', 'passed'])){
            $res = 6;
        }
        else if(\Str::contains($str, ['collect', '5'])){
            $res = 5;
        }
        else{
            return ['result' => 3, 'interpretation' => $error];
        }

        return ['result' => $res, 'interpretation' => $result];
    }

    public static function  hpv_sample_result($result, $error=null)
    {
        $target1 = strtolower($result['target_1']);
        $target2 = strtolower($result['target_2']);
        $target3 = strtolower($result['target_3']);
        $overall_result = NULL;

        if(\Str::contains($target1, ['positive']) || \Str::contains($target2, ['positive']) || \Str::contains($target3, ['positive'])){
            $res = 2;
            if (\Str::contains($target1, ['positive']))
                $overall_result = $target1;
            if (\Str::contains($target2, ['positive']))
                $overall_result = $target2;
            if (\Str::contains($target3, ['positive']))
                $overall_result = $target3;
        }
        else if (\Str::contains($target1, ['negative']) || \Str::contains($target2, ['negative']) || \Str::contains($target3, ['negative'])) {
            $res = 1;
            if (\Str::contains($target1, ['negative']))
                $overall_result = $target1;
            if (\Str::contains($target2, ['negative']))
                $overall_result = $target2;
            if (\Str::contains($target3, ['negative']))
                $overall_result = $target3;
        }
        else if(\Str::is($target1, 'valid') && \Str::is($target2, 'valid') && \Str::is($target3, 'valid')){
            $res = 6;
            $overall_result = $target1;
        }
        else if(\Str::is($target1, 'invalid') && \Str::is($target2, 'invalid') && \Str::is($target3, 'invalid')){
            $res = 3;
            $overall_result = $target1;
        }
        else{
            return ['result' => 3, 'interpretation' => $error];
        }

        return ['result' => $res, 'interpretation' => $overall_result, 'target_1' => $target1, 'target_2' => $target2, 'target_3' => $target3];
    }

	public static function save_repeat($sample_id)
	{
		$original = Sample::find($sample_id);
		if($original->run == 5) return false;

		$sample = new Sample;
		$fields = \App\Lookup::samples_arrays();
		$sample->fill($original->only($fields['sample_rerun']));
		$sample->run++;
		if($sample->parentid == 0) $sample->parentid = $original->id;

        $s = Sample::where(['parentid' => $sample->parentid, 'run' => $sample->run])->first();
        if($s) return $s;
		
		$sample->save();
		return $sample;
	}

	public static function check_batch($batch_id, $issample=FALSE)
	{
		if($issample){
			$sample = Sample::find($batch_id);
			$batch_id = $sample->batch_id;
		}
		$double_approval = \App\Lookup::$double_approval; 

        Sample::whereRaw("(result is null or result = 5)")
            ->where('repeatt', 0)
            ->where('batch_id', $batch_id)
            ->whereNotNull('dateapproved')
            ->when((in_array(env('APP_LAB'), $double_approval)), function($query){
                return $query->whereNotNull('dateapproved2');
            })            
            ->update(['result' => 5, 'labcomment' => 'Failed Test']);

		if(in_array(env('APP_LAB'), $double_approval)){
			$where_query = "( (receivedstatus=2 and repeatt=0) OR  (result > 0 AND (repeatt = 0 or repeatt is null) AND ((approvedby IS NOT NULL AND approvedby2 IS NOT NULL) or (dateapproved IS NOT NULL AND dateapproved2 IS NOT NULL)) ))";
		}
		else{
			$where_query = "( (receivedstatus=2 and repeatt=0) OR  (result > 0 AND (repeatt = 0 or repeatt is null) AND (approvedby IS NOT NULL OR dateapproved IS NOT NULL)) )";
		}
		$total = Sample::where('batch_id', $batch_id)->where('parentid', 0)->get()->count();
		$tests = Sample::where('batch_id', $batch_id)
		->whereRaw($where_query)
		->get()
		->count();

		if($total == $tests){
            Sample::where('batch_id', $batch_id)->whereNull('repeatt')->update(['repeatt' => 0]);
            $b = \App\Batch::find($batch_id);
            if($b->batch_complete == 0){
                $b->batch_complete = 2; 
                $b->save();
                return true;
            }
		}
        return false;
	}

	public static function check_previous($sample_id)
	{
		$lab = auth()->user()->lab_id;
		$samples = Sample::select('samples.*')
		->join('batches', 'samples.batch_id', '=', 'batches.id')
		->where(['batches.lab_id' => $lab, 'samples.parentid' => $sample_id])
		->get();

		return $samples;
	}

	public static function check_run($sample_id, $run=2)
	{
		$lab = auth()->user()->lab_id;
        if (session('testingSystem') == 'HPV') {
            $sample = CancerSample::select('cancer_samples.*')
                ->where(['cancer_samples.lab_id' => $lab, 'cancer_samples.parentid' => $sample_id, 'run' => $run])
                ->first();
        } else {
            $sample = Sample::select('samples.*')
                ->join('batches', 'samples.batch_id', '=', 'batches.id')
                ->where(['batches.lab_id' => $lab, 'samples.parentid' => $sample_id, 'run' => $run])
                ->get()
                ->first();
        }

		return $sample;
	}
	

    public static function get_subtotals($batch_id=NULL, $complete=true)
    {

        $samples = Sample::selectRaw("count(samples.id) as totals, batch_id, result")
            ->join('batches', 'batches.id', '=', 'samples.batch_id')
            ->when($batch_id, function($query) use ($batch_id){
                if (is_array($batch_id)) {
                    return $query->whereIn('batch_id', $batch_id);
                }
                else{
                    return $query->where('batch_id', $batch_id);
                }
            })
            ->when($complete, function($query){
                return $query->where('batch_complete', 2);
            })
            ->where('repeatt', 0)
            ->whereRaw("(receivedstatus != 2 or receivedstatus is null)")
            ->groupBy('batch_id', 'result')
            ->get();

        return $samples;
    }

    public static function get_rejected($batch_id=NULL, $complete=true)
    {
        $samples = Sample::selectRaw("count(samples.id) as totals, batch_id")
            ->join('batches', 'batches.id', '=', 'samples.batch_id')
            ->when($batch_id, function($query) use ($batch_id){
                if (is_array($batch_id)) {
                    return $query->whereIn('batch_id', $batch_id);
                }
                return $query->where('batch_id', $batch_id);
            })
            ->when($complete, function($query){
                return $query->where('batch_complete', 2);
            })
            ->where('receivedstatus', 2)
            ->groupBy('batch_id')
            ->get();

        return $samples;
    }



    public static function get_maxdatemodified($batch_id=NULL, $complete=true)
    {
        $samples = Sample::selectRaw("max(datemodified) as mydate, batch_id")
            ->join('batches', 'batches.id', '=', 'samples.batch_id')
            ->when($batch_id, function($query) use ($batch_id){
                if (is_array($batch_id)) {
                    return $query->whereIn('batch_id', $batch_id);
                }
                return $query->where('batch_id', $batch_id);
            })
            ->when($complete, function($query){
                return $query->where('batch_complete', 2);
            })
            ->where('receivedstatus', '!=', 2)
            ->groupBy('batch_id')
            ->get();

        return $samples;
    }

    public static function get_maxdatetested($batch_id=NULL, $complete=true)
    {
        $samples = Sample::selectRaw("max(datetested) as mydate, batch_id")
            ->join('batches', 'batches.id', '=', 'samples.batch_id')
            ->when($batch_id, function($query) use ($batch_id){
                if (is_array($batch_id)) {
                    return $query->whereIn('batch_id', $batch_id);
                }
                return $query->where('batch_id', $batch_id);
            })
            ->when($complete, function($query){
                return $query->where('batch_complete', 2);
            })
            ->where('receivedstatus', '!=', 2)
            ->groupBy('batch_id')
            ->get();

        return $samples;
    }

    public static function clean_dob()
    {
    	$samples = Sample::where('age', '>', 36)->with(['patient'])->get();

    	foreach ($samples as $sample) {
    		// $patient = $sample->patient;
    		// $patient->dob = null;
    		// $patient->pre_update();

    		$sample->age=0;
    		$sample->pre_update();
    	}
    }

    public static function delete_empty_batches()
    {
    	$batches = \App\Batch::selectRaw("batches.id, count(samples.id) as mycount")
    					->leftJoin('samples', 'samples.batch_id', '=', 'batches.id')
    					->groupBy('batches.id')
    					->having('mycount', 0)
    					->get();

    	// return $batches->count();

    	foreach ($batches as $key => $batch) {
    		$batch->delete();
    	}
    }

    public static function patient_sms()
    {
        ini_set("memory_limit", "-1");
    	$samples = SampleView::whereNotNull('patient_phone_no')
    				->where('patient_phone_no', '!=', '')
    				->whereNull('time_result_sms_sent')
    				->where(['batch_complete' => 1, 'repeatt' => 0])
    				->where('datereceived', '>', date('Y-m-d', strtotime('-3 months')))
    				->get();

    	foreach ($samples as $key => $sample) {
    		self::send_sms($sample);
    		// break;
    	}
    }

    public static function send_sms($sample)
    {
		// English
		if($sample->preferred_language == 1){
			if($sample->result == 2){
				$message = $sample->patient_name . " Jambo, baby's results are ready. Please come to the clinic when you can. Thank You";
			}
            if($sample->result == 1){
                $message = $sample->patient_name . "  Jambo, baby's results are ready. Remember to keep your appointment date! Thank you";
            }
			else if($sample->result == 3 || $sample->result == 5){
				$message = $sample->patient_name . " Jambo, please come to the clinic with baby as soon as you can! Thank you";
			}
			else{
				if($sample->receivedstatus == 2){
					$message = $sample->patient_name . " Jambo, please come to the clinic with baby as soon as you can! Thank you";
				}
				// else{
				// 	$message = $sample->patient_name . " Jambo, baby's results are ready. Remember to keep your appointment date! Thank you"; 	
				// }
			}
		}
		// Kiswahili
		else{
			if($sample->result == 2){
				$message = $sample->patient_name . " Jambo, matokeo ya mtoto yako tayari. Tafadhali kuja kliniki utakapoweza. Asante.";
			}
			else if($sample->result == 3 || $sample->result == 5){
				$message = $sample->patient_name . " Jambo, kuja kliniki na mtoto utakapoweza. Asante";
			}
            if($sample->result == 1){
                $message = $sample->patient_name . " Jambo, matokeo ya mtoto tayari. Kumbuka tarehe yako ya kuja cliniki. Asante";
            }
			else{
				if($sample->receivedstatus == 2){
					$message = $sample->patient_name . " Jambo, kuja kliniki na mtoto utakapoweza. Asante";
				}
				// else{
				// 	$message = $sample->patient_name . " Jambo, kuja kliniki utakapoweza. Asante.";
				// }
			}    			
		}

		if(!isset($message)){
			print_r($sample);
			return;
		}

        if(!preg_match('/[2][5][4][7][0-9]{8}/', $sample->patient_phone_no)) return;

        $response = self::sms($sample->patient_phone_no, $message);

        if($response){
            $s = Sample::find($sample->id);
            $s->time_result_sms_sent = date('Y-m-d H:i:s');
            $s->save();
        }
    }

    public static function sms_test()
    {
        self::sms('254702266217', 'This is a successful test.');
    }

    public static function get_worksheet_samples($machine_type, $temp_limit=null, $entered_by=null)
    {
        $machines = Lookup::get_machines();
        $machine = $machines->where('id', $machine_type)->first();

        $test = in_array(env('APP_LAB'), Lookup::$worksheet_received);
        $user = auth()->user();
        \App\Batch::where(['received_by' => $user->id, 'input_complete' => 0])->update(['input_complete' => 1]);

        if($machine == NULL || $machine->eid_limit == NULL) return false;

        $limit = $temp_limit ?? $machine->eid_limit;
        
        $year = date('Y') - 1;
        if(date('m') < 7) $year --;
        $date_str = $year . '-12-31';        

        if($test){
            $repeats = SampleView::selectRaw("samples_view.*, facilitys.name, users.surname, users.oname")
                ->leftJoin('users', 'users.id', '=', 'samples_view.user_id')
                ->leftJoin('facilitys', 'facilitys.id', '=', 'samples_view.facility_id')
                ->where('datereceived', '>', $date_str)
                ->where('site_entry', '!=', 2)
                ->where('parentid', '>', 0)
                ->whereNull('datedispatched')
                ->whereRaw("(worksheet_id is null or worksheet_id=0)")
                // ->where('input_complete', true)
                ->whereIn('receivedstatus', [1, 3])
                ->whereRaw('((result IS NULL ) OR (result=0 ))')
                ->orderBy('samples_view.id', 'desc')
                ->limit($limit)
                ->get();
            $limit -= $repeats->count();
        }

        $samples = SampleView::selectRaw("samples_view.*, facilitys.name, users.surname, users.oname, IF(parentid > 0 OR parentid=0, 0, 1) AS isnull")
            ->leftJoin('users', 'users.id', '=', 'samples_view.user_id')
            ->leftJoin('facilitys', 'facilitys.id', '=', 'samples_view.facility_id')
            ->where('datereceived', '>', $date_str)
            ->when($test, function($query) use ($user){
                // return $query->where('received_by', $user->id)->where('parentid', 0);
                return $query->where('parentid', 0)
                	->whereRaw("((received_by={$user->id} && sample_received_by IS NULL) OR  sample_received_by={$user->id})");
            })
            ->when($entered_by, function($query) use ($entered_by){
                // return $query->where('received_by', $user->id)->where('parentid', 0);
                // dd($query);
                if(is_array($entered_by)){
                    $str = '(';
                    foreach ($entered_by as $key => $value) {
                        $str .= $value . ', ';
                    }
                    $str = substr($str, 0, -2) . ')';
                    return $query->where('parentid', 0)
                    ->whereRaw("((received_by IN {$str} && sample_received_by IS NULL) OR  sample_received_by IN {$str})");
                }
                return $query->where('parentid', 0)
                    ->whereRaw("((received_by={$entered_by} && sample_received_by IS NULL) OR  sample_received_by={$entered_by})");
            })
            ->where('site_entry', '!=', 2)
            ->whereNull('datedispatched')
            ->whereRaw("(worksheet_id is null or worksheet_id=0)")
            // ->where('input_complete', true)
            // ->where('parentid', '>', 0)
            ->whereIn('receivedstatus', [1, 3])
            ->whereRaw('((result IS NULL ) OR (result =0 ))')            
            ->orderBy('run', 'desc')
            // ->orderBy('isnull', 'asc')
            ->orderBy('highpriority', 'desc')
            ->orderBy('datereceived', 'asc')
            ->when((!in_array(env('APP_LAB'), [8])), function($query){
                return $query->orderBy('time_received', 'asc');
            })
            ->orderBy('site_entry', 'asc')
            ->when((env('APP_LAB') == 2), function($query){
                return $query->orderBy('facilitys.id', 'asc');
            })  
            ->orderBy('batch_id', 'asc')     
            ->limit($limit)
            ->get();

        // dd($samples);

        if($test && $repeats->count() > 0) $samples = $repeats->merge($samples);
        $count = $samples->count();        

        $create = false;
        if($count == $machine->eid_limit) $create = true;
        if($temp_limit && $count == $temp_limit) $create = true;
        if(in_array(env('APP_LAB'), [9, 5])) $create = true;

        return [
        	'count' => $count, 'limit' => $temp_limit,
            'create' => $create, 'machine_type' => $machine_type, 'machine' => $machine, 'samples' => $samples
        ];

    }

    public static function send_to_mlab()
    {
    	ini_set('memory_limit', "-1");
        $min_date = date('Y-m-d', strtotime('-2 month'));
    	$batches = \App\Batch::join('facilitys', 'batches.facility_id', '=', 'facilitys.id')
    			->select("batches.*")
    			->with(['facility'])
    			->where('sent_to_mlab', 0)
    			->where('smsprinter', 1)
    			->where('batch_complete', 1)
				->where('datedispatched', '>', $min_date)
    			->get();

    	foreach ($batches as $batch) {
    		$samples = $batch->sample;

    		foreach ($samples as $sample) {
    			if($sample->repeatt == 1) continue;

    			$client = new Client(['base_uri' => self::$mlab_url]);

    			$post_data = [
						'source' => '1',
						'result_id' => "{$sample->id}",
						'result_type' => '2',
						'request_id' => '',
						'client_id' => $sample->patient->patient,
						'age' => $sample->my_string_format('age'),
						'gender' => $sample->patient->gender,
						'result_content' => $sample->my_string_format('result'),
						'units' => '0',
						'mfl_code' => "{$batch->facility->facilitycode}",
						'lab_id' => "{$batch->lab_id}",
						'date_collected' => $sample->datecollected ?? '0000-00-00',
						'cst' => '0',
						'cj' => '0',
						'csr' => "{$sample->rejectedreason}",
						'lab_order_date' => $sample->datetested ?? '0000-00-00',
					];

				$response = $client->request('post', '', [
					// 'debug' => true,
					'http_errors' => false,
					'json' => $post_data,
				]);
				$body = json_decode($response->getBody());
				// print_r($body);
				if($response->getStatusCode() > 399){
					// print_r(json_decode($sample->toJson()));
					print_r($post_data);
					print_r($body);
					return null;
				}
    		}
    		$batch->sent_to_mlab = 1;
    		$batch->save();
    		// break;
    	}
    }

    public static function eid_worksheets($year = null)
    {

        if(!$year) $year = date('Y');
        $data = SampleView::selectRaw("year(daterun) as year, month(daterun) as month, machine_type, result, count(*) as tests ")
            ->join('worksheets', 'worksheets.id', '=', 'samples_view.worksheet_id')
            ->where('site_entry', '!=', 2)
            ->whereYear('daterun', $year)
            ->where(['samples_view.lab_id' => env('APP_LAB')])
            ->groupBy('year', 'month', 'machine_type', 'result')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->orderBy('machine_type', 'asc')
            ->orderBy('result', 'asc')
            ->get();
        $worksheets = Worksheet::selectRaw("year(daterun) as year, month(daterun) as month, machine_type, count(*) as worksheets ")
            ->whereYear('daterun', $year)
            ->groupBy('year', 'month', 'machine_type')
            ->get();

        $results = [1 => 'Negative', 2 => 'Positive', 3 => 'Failed', 4 => 'Unknown', 5 => 'Collect New Sample'];
        $machines = [1 => 'Roche', 2 => 'Abbott'];

        $rows = [];

        for ($i=1; $i < 13; $i++) { 
            foreach ($machines as $mkey => $mvalue) {
                $row = ['Year of Testing' => $year, 'Month of Testing' => date('F', strtotime("{$year}-{$i}-1")), ];
                $row['Machine'] = $mvalue;
                $total = 0;

                foreach ($results as $rkey => $rvalue) {
                    $row[$rvalue] = $data->where('result', $rkey)->where('machine_type', $mkey)->where('month', $i)->first()->tests ?? 0;
                    $total += $row[$rvalue];
                }

                $row['Total'] = $total;
                $row['No. Of Worksheets'] = $worksheets->where('machine_type', $mkey)->where('month', $i)->first()->worksheets ?? 0;

                $rows[] = $row;
            }
            if($year == date('Y') && $i == date('m')) break;
        }

        $file = 'eid_worksheets_data';

        return Common::csv_download($rows, $file);
    }

    public static function check_patients_list(){
        // $patientsList = ['13805-2018-E02288', '13576-2018-285', '13745-2018-644', '13582-EXP214/2018', '13718-00198/18', '14446/2018/002', '16707-2018-0002', '12976-2018-0014', '18515-2018-0015', '16273/00186/18', '13989/2018/0026', '15204-2018-136', '13528-2018-00093', '14379-2017-0027', '13897-18-647', '15758-2018-0057', '15758/2017/0074', '13960-2018-00137', '13897-18-648', '14061-2017-00081', '14586-2018-0017', 'E0890/18', '15197-18-002', '13740-2017-476', '14555-2018-0072', '14947-2018-0193', '15753-2018-0039', '15732-2018-005', '14555-2018-0084', '13897-18-650', '17979-HEI-389', 'HEI/2018/00450', '14103/2017/0110', '14720-2017-0008', '163642018014', '13576-2017-301', '19990-2018-0001', '14555-2018-0091', '14098-272', '16364-2018-0016', '13805-2018-E02366', '15301-2018-021', '14519-2018-004', '14203-2018-0013', '15758-2018-0074', '138052018-E02367', '13738-2018-E00066', '15758-2018-0079', '15783-2018-0006', '1360/04/18', '11634/2018/0/30', '14753-2018-0001', '15758-2018-0081', '14102/2018/282', '14102/2018/284', '13999/1/00254', '14058-2018-2590', '147792018-0034', '13467-2018-372', '15204-2018-0202', '15758-2018-0085', '13989-2018-0045', '139172017008', '13222-000084', '14082-2018-0385', '14701-2018-036', '13805-2018-E02396', '15138-2018-019', '1416620180064', '13640-2018-56', '13805-2018-E02401', '13576-2017-0299', '14872-2018-0024', '13752-11-011', '15204-2018-0233', '14607-2018-029', '12978-2017-0013', '15834-2018-0136', '14701-2018-0045', '13656-0937', '14506-2017-0003'];
        // $found = [];
        // $missmatch = [];
        
        // foreach ($patientsList as $key => $given) {
        //     $patient = \App\Patient::where('patient', '=', $given)->first();
        //     if ($patient)
        //         $found[] = $given;
        //     else
        //         $missmatch[] = $given;
        // }
        // dd($found);        
    }

    public static function getCovidConsumptionDailyReport()
    {
        \DB::table('test_dump')->truncate();
        $consumptions = CovidConsumption::with('details')->get();
        foreach ($consumptions as $key => $consumption) {
            $plartform_test = json_decode($consumption->tests);
            foreach ($plartform_test as $platformkey => $platform) {
                $platform = (array)$platform;
                $key = array_key_first($platform);
                $machine = Machine::where('machine', $key)->first();
                $week_tests = $platform[$machine->machine];
                $worksheets = CovidWorksheet::/*with('sample')->*/where('machine_type', $machine->id)
                                    ->whereRaw("`daterun` BETWEEN '{$consumption->start_of_week}' AND '{$consumption->end_of_week}'")->orderBy('daterun')->get();
                
                foreach ($worksheets as $worksheetkey => $worksheet) {
                    $day_tests = $worksheet->sample->count();
                    $kits = $consumption->details->first();
                    $incoming = [
                            'date' => $worksheet->daterun,
                            'tests' => $day_tests,
                            'platform' => $key,
                            'begining_balance' => ($kits->begining_balance * ($day_tests/$week_tests)),
                            'received' => ($kits->received * ($day_tests/$week_tests)),
                            'kits_used' => ($kits->kits_used * ($day_tests/$week_tests)),
                            'positive' => ($kits->positive * ($day_tests/$week_tests)),
                            'negative' => ($kits->negative * ($day_tests/$week_tests)),
                            'wastage' => ($kits->wastage * ($day_tests/$week_tests)),
                            'ending' => ($kits->ending * ($day_tests/$week_tests)),
                        ];
                    $records = \DB::table('test_dump')->where('date', $worksheet->daterun)->get();
                    if ($records->isEmpty()){
                        \DB::table('test_dump')->insert($incoming);
                    } else {
                        $record = $records->first();
                        \DB::table('test_dump')
                        ->where('date', $record->date)
                        ->update([
                            'tests' => $record->tests += $incoming['tests'],
                            'begining_balance' => $record->begining_balance += $incoming['begining_balance'],
                            'received' => $record->received += $incoming['received'],
                            'kits_used' => $record->kits_used += $incoming['kits_used'],
                            'positive' => $record->positive += $incoming['positive'],
                            'negative' => $record->negative += $incoming['negative'],
                            'wastage' => $record->wastage += $incoming['wastage'],
                            'ending' => $record->ending += $incoming['ending'],
                        ]);
                    }
                }
            }
        }
    }
}
