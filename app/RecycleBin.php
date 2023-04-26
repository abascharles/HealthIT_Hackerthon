<?php

namespace App;

class RecycleBin
{
    

    public static function send_sms_person($sample, $tel)
    {
        // English
        if($sample->preferred_language == 1){
            if($sample->rcategory == 1 || $sample->rcategory == 2){
                if($sample->age > 15 && $sample->age < 24){
                    $message = $sample->patient_name . ", Congratulations your VL is good, remember to keep your appointment date!!!";
                }
                else{
                    $message = $sample->patient_name . ", Congratulations!Your VL is good! Continue taking your drugs and keeping your appointment as instructed by the doctor.";                        
                }
            }
            else if($sample->rcategory == 3 || $sample->rcategory == 4){
                if($sample->age > 15 && $sample->age < 24){
                    $message = $sample->patient_name . ", Your VL results are ready. Please come to the facility as soon you can!";
                }
                else{
                    $message = $sample->patient_name . ", Your VL results are ready. Please visit the health facility as soon as you can.";                        
                }
            }
            else if($sample->rcategory == 5 || $sample->receivedstatus == 2){
                $message = $sample->patient_name . " Jambo,  please come to the clinic as soon as you can! Thank you.";
            }
        }
        // Kiswahili
        else{
            if($sample->rcategory == 1 || $sample->rcategory == 2){
                if($sample->age > 15 && $sample->age < 24){
                    $message = $sample->patient_name . ", Pongezi! Matokeo yako ya VL iko kiwango kizuri! Endelea kuzingatia maagizo!";
                }
                else{
                    $message = $sample->patient_name . ", Pongezi! Matokeo yako ya VL iko kiwango kizuri! Endelea kuzingatia maagizo ya daktari. Kumbuka tarehe yako ya kuja cliniki!";                        
                }
            }
            else if($sample->rcategory == 3 || $sample->rcategory == 4){
                if($sample->age > 15 && $sample->age < 24){
                    $message = $sample->patient_name . ", Matokeo yako ya VL yako tayari. Tafadhali tembelea kituo!";
                }
                else{
                    $message = $sample->patient_name . ", Matokeo yako ya VL yako tayari. Tafadhali tembelea kituo cha afya umwone daktari!";                        
                }
            }
            else if($sample->rcategory == 5 || $sample->receivedstatus == 2){
                $message = $sample->patient_name . " Jambo, kuja kliniki utakapoweza. Asante.";
            }             
        }

        if(!$message){
            print_r($sample);
            die();
            return;
        }

        $client = new Client(['base_uri' => self::$sms_url]);

        $response = $client->request('post', '', [
            'auth' => [env('SMS_USERNAME'), env('SMS_PASSWORD')],
            'http_errors' => false,
            'json' => [
                'sender' => env('SMS_SENDER_ID'),
                'recipient' => $tel,
                'message' => $message,
            ],
        ]);

        $body = json_decode($response->getBody());
        if($response->getStatusCode() == 201){
            // $s = Viralsample::find($sample->id);
            // $s->time_result_sms_sent = date('Y-m-d H:i:s');
            // $s->save();
        }
    }

    public static function nyumbani()
    {
        ini_set("memory_limit", "-1");

        $samples = ViralsampleView::where(['datedispatched' => '2018-11-14', 'datetested' => '2018-11-14'])->get();

        foreach ($samples as $key => $s) {
            // $sample = Viralsample::find($s->id);
            // $worksheet = $sample->worksheet;
            $worksheet = Viralworksheet::find($s->worksheet_id);

            if(strtotime($worksheet->created_at) < strtotime('2018-11-12') && $worksheet->dateuploaded == '2018-11-14'){
                $worksheet->datereviewed = $worksheet->daterun = date('Y-m-d', strtotime($worksheet->created_at . ' +1day'));
                $worksheet->save();

                $viralsamples = $worksheet->sample;

                foreach ($viralsamples as $sample) {
                    $sample->datetested = $sample->dateapproved = $worksheet->daterun;
                    $sample->pre_update();

                    $batch = $sample->batch;
                    $batch->datedispatched = $worksheet->daterun;
                    $batch->pre_update();
                }
            }
        }
    }

    public static function nyumba()
    {
        ini_set("memory_limit", "-1");

        $batches = Viralbatch::with(['sample'])->where(['batch_complete' => '2'])->where('datereceived', '<', '2018-09-01')->get();

        foreach ($batches as $key => $batch) {
            // $dt = $batch->sample->max('datetested');
            $batch->batch_complete = 1;
            $batch->datedispatched = date('Y-m-d', strtotime($batch->datereceived . ' +2days'));
            $batch->pre_update();
        }
    }

    public static function nyumba2()
    {
        ini_set("memory_limit", "-1");

        $batches = Viralbatch::with(['sample'])->whereNull('datereceived')->where(['datedispatched' => '2018-11-29'])->get();

        foreach ($batches as $key => $batch) {
            $dt = $batch->sample->max('datetested');
            $batch->datereceived = date('Y-m-d', strtotime($dt . ' -2days'));
            $batch->datedispatched = date('Y-m-d', strtotime($dt . ' +1days'));
            $batch->pre_update();
        }
    }

    public static function kisumu()
    {
        ini_set("memory_limit", "-1");

        $batches = Viralbatch::whereNull('datereceived')->where(['batch_complete' => '2'])->get();

        foreach ($batches as $key => $batch) {
            // $dt = $batch->sample->max('datetested');
            $batch->datereceived = date('Y-m-d', strtotime($batch->created_at . ' +2days'));
            $batch->datedispatched = date('Y-m-d', strtotime($batch->created_at . ' +3days'));
            $batch->pre_update();
        }
    }

    // public static function cpgh()
    // {
    //     ini_set("memory_limit", "-1");

    //     $batches = Viralbatch::where('datereceived', '<', '2018-01-01')->where('batch_complete', 0)->get();

    //     foreach ($batches as $batch) {
    //         $samples = $batch->sample;

    //         foreach ($samples as $sample) {
    //             if($sample->repeatt == 1 && !$sample->has_rerun){
    //                 $sample->repeatt = 0;
    //                 $sample->save();
    //             }
    //         }

    //         $batch->datedispatched = date('Y-m-d', strtotime($batch->datereceived . ' +2days'));
    //         $batch->batch_complete = 1;
    //         $batch->save();
    //     }
    // }

    public static function cpgh()
    {
        ini_set("memory_limit", "-1");

        $batches = Batch::where('datereceived', '<', '2018-01-01')->where('batch_complete', 0)->get();

        foreach ($batches as $batch) {
            $samples = $batch->sample;

            foreach ($samples as $sample) {
                if($sample->repeatt == 1 && !$sample->has_rerun){
                    $sample->repeatt = 0;
                    $sample->save();
                }
            }

            $batch->datedispatched = date('Y-m-d', strtotime($batch->datereceived . ' +2days'));
            $batch->batch_complete = 1;
            $batch->save();
        }
    }

    public static function page_links($base, $page=NULL, $last_page=NULL, $date_start=NULL, $date_end=NULL)
    {
        $str = "";
        $datestring = "";

        if($date_start){
            $datestring .= '/' . $date_start;
            if($date_end){
                $datestring .= '/' . $date_end;
            }
        }
        $next = $page+1;
        $previous = $page-1;

        if($page != 1){
            $str .= "<a href='" . url($base . '/1' . $datestring) . "'>First Page</a> |";
            $str .= "<a href='" . url($base . '/' . $previous . $datestring) . "'>Prev</a> |";
        }

        $str .= "<a href='" . url($base . '/' . $page . $datestring) . "'>{$page}</a> |";

        if($page < $last_page ){
            $str .= "<a href='" . url($base . '/' . $next . $datestring) . "'>Next</a> | ";
            $str .= "<a href='" . url($base . '/' . $last_page . $datestring) . "'>Last Page</a>";
        }
        return $str;
    }



    public static function batch_status($batch_id, $batch_complete, $pre='', $approval=false){

    	if($approval){
    		$url = "<td><a href='" . url($pre . 'batch/site_approval/' . $batch_id) . "'>View Samples For Approve</a></td>";
    	}
    	else{
    		$url = "<td><a href='" . url($pre . 'batch/' . $batch_id) . "'>View</a>";

    		if($batch_complete==1){
    			$url .= "| <a href='" . url($pre . 'batch/summary/' . $batch_id) . "'><i class='fa fa-print'></i> Summary</a> | 
    			<a href='" . url($pre . 'batch/individual/' . $batch_id) . "'><i class='fa fa-print'></i> Individual </a> |
    			 <a href='" . url($pre . 'batch/email/' . $batch_id) . "'><i class='fa fa-print'></i> Email </a>"; 
    		}

    		$url .= "</td>";
    	}

        if($batch_complete == 0){
            return "<td>In Process</td>" . $url;
        }
        else{
            return "<td>Complete</td>" . $url;
        }
    }

    

	public static function synch_vl_batches()
	{
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');
		$my = new MiscViral;
		$my->save_tat(ViralsampleView::class, Viralsample::class);

		while (true) {
			$batches = Viralbatch::with(['sample.patient:id,national_patient_id'])->where('synched', 0)->limit(10)->get();
			if($batches->isEmpty()) break;

			$response = $client->request('post', 'synch/viralbatches', [
				'headers' => [
					'Accept' => 'application/json',
				],
				'form_params' => [
					'batches' => $batches->toJson(),
				],

			]);

			$body = json_decode($response->getBody());

			foreach ($body->batches as $key => $value) {
				$update_data = ['national_batch_id' => $value->national_batch_id, 'synched' => 1, 'datesynched' => $today,];
				Viralbatch::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->samples as $key => $value) {
				$update_data = ['national_sample_id' => $value->national_sample_id, 'synched' => 1, 'datesynched' => $today,];
				Viralsample::where('id', $value->original_id)->update($update_data);
			}
		}
	}

	public static function synch_eid_batches()
	{
		$client = new Client(['base_uri' => self::$base]);
		$today = date('Y-m-d');
		$my = new Misc;
		$my->save_tat(SampleView::class, Sample::class);

		while (true) {
			$batches = Batch::with(['sample.patient:id,national_patient_id'])->where('synched', 0)->limit(10)->get();
			if($batches->isEmpty()) break;

			$response = $client->request('post', 'synch/batches', [
				'headers' => [
					'Accept' => 'application/json',
				],
				'form_params' => [
					'batches' => $batches->toJson(),
				],

			]);

			$body = json_decode($response->getBody());

			foreach ($body->batches as $key => $value) {
				$update_data = ['national_batch_id' => $value->national_batch_id, 'synched' => 1, 'datesynched' => $today,];
				Batch::where('id', $value->original_id)->update($update_data);
			}

			foreach ($body->samples as $key => $value) {
				$update_data = ['national_sample_id' => $value->national_sample_id, 'synched' => 1, 'datesynched' => $today,];
				Sample::where('id', $value->original_id)->update($update_data);
			}
		}
	}

    /*public function index($batch_complete=4, $page=NULL, $date_start=NULL, $date_end=NULL)
    {
        $myurl = url('batch/index/' . $batch_complete . '/' . $page . '/');
        $user = auth()->user();
        $facility_user = false;
        if($user->user_type_id == 5) $facility_user=true;

        $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}')";

        $my = new Misc;
        $b = Batch::selectRaw('count(id) as mycount')
            ->when($date_start, function($query) use ($date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate('batches.datereceived', '>=', $date_start)
                    ->whereDate('batches.datereceived', '<=', $date_end);
                }
                return $query->whereDate('batches.datereceived', $date_start);
            })
            ->when($facility_user, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->when(true, function($query) use ($batch_complete){
                if($batch_complete < 4) return $query->where('batch_complete', $batch_complete);
            })
            ->get()
            ->first();

        $page_limit = env('PAGE_LIMIT', 10);

        if($page == NULL || $page == 'null'){
            $page=1;
        }

        $last_page = ceil($b->mycount / $page_limit);
        $last_page = (int) $last_page;

        $offset = ($page-1) * $page_limit;

        $batches = Batch::select(['batches.*', 'facilitys.name'])
            ->leftJoin('facilitys', 'facilitys.id', '=', 'batches.facility_id')
            ->when($date_start, function($query) use ($date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate('batches.datereceived', '>=', $date_start)
                    ->whereDate('batches.datereceived', '<=', $date_end);
                }
                return $query->whereDate('batches.datereceived', $date_start);
            })
            ->when($facility_user, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->when(true, function($query) use ($batch_complete){
                if($batch_complete < 4) return $query->where('batch_complete', $batch_complete);
            })
            ->orderBy('datereceived', 'desc')
            ->limit($page_limit)
            ->offset($offset)
            ->get();

        if($batches->isEmpty()){
            return view('tables.batches', ['rows' => null, 'links' => null, 'myurl' => $myurl, 'pre' => '']);
        }

        $batch_ids = $batches->pluck(['id'])->toArray();
        $subtotals = $my->get_subtotals($batch_ids, false);
        $rejected = $my->get_rejected($batch_ids, false);
        $currentdate=date('d-m-Y');

        $table_rows = "";

        foreach ($batches as $key => $batch) {

            $neg = $this->checknull($subtotals->where('batch_id', $batch->id)->where('result', 1));
            $pos = $this->checknull($subtotals->where('batch_id', $batch->id)->where('result', 2));
            $failed = $this->checknull($subtotals->where('batch_id', $batch->id)->where('result', 3));
            $redraw = $this->checknull($subtotals->where('batch_id', $batch->id)->where('result', 5));
            $noresult = $this->checknull($subtotals->where('batch_id', $batch->id)->where('result', 0));

            $rej = $this->checknull($rejected->where('batch_id', $batch->id));
            $total = $neg + $pos + $failed + $redraw + $noresult + $rej;

            $result = $pos + $neg + $redraw + $failed;

            $datereceived=date("d-M-Y",strtotime($batch->datereceived));

            if($batch->batch_complete == 0){
                $max = $currentdate;
            }
            else{
                $max=date("d-M-Y",strtotime($batch->datedispatched));
            }

            $delays = $my->working_days($datereceived, $max);

            $table_rows .= "<tr> 
            <td>{$batch->id}</td>
            <td>{$batch->name}</td>
            <td>{$batch->datereceived}</td>
            <td>" . $batch->created_at->toDateString() . "</td>
            <td>{$delays}</td>
            <td></td>
            <td>{$total}</td>
            <td>{$rej}</td>
            <td>{$result}</td>
            <td>{$noresult}</td>" . $my->batch_status($batch->id, $batch->batch_complete) . "
            </tr>";
        }
        $base = '/batch/index/' . $batch_complete;

        $links = $my->page_links($base, $page, $last_page, $date_start, $date_end);

        return view('tables.batches', ['rows' => $table_rows, 'links' => $links, 'myurl' => $myurl, 'pre' => '']);
    }*/

    
    /*public function index($batch_complete=4, $page=NULL, $date_start=NULL, $date_end=NULL)
    {
        $myurl = url('viralbatch/index/' . $batch_complete . '/' . $page . '/');
        $user = auth()->user();
        $my = new MiscViral;
        $facility_user = false;

        if($user->user_type_id == 5) $facility_user=true;
        $string = "(user_id='{$user->id}' OR facility_id='{$user->facility_id}')";
        
        $b = Viralbatch::selectRaw('count(id) as mycount')
            ->when($date_start, function($query) use ($date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate('viralbatches.datereceived', '>=', $date_start)
                    ->whereDate('viralbatches.datereceived', '<=', $date_end);
                }
                return $query->whereDate('viralbatches.datereceived', $date_start);
            })
            ->when($facility_user, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->when(true, function($query) use ($batch_complete){
                if($batch_complete < 4) return $query->where('batch_complete', $batch_complete);
            })
            ->get()
            ->first();

        $page_limit = env('PAGE_LIMIT', 10);

        if($page == NULL || $page == 'null'){
            $page=1;
        }

        $last_page = ceil($b->mycount / $page_limit);
        $last_page = (int) $last_page;

        $offset = ($page-1) * $page_limit;

        $batches = Viralbatch::select('viralbatches.*', 'facilitys.name')
            ->leftJoin('facilitys', 'facilitys.id', '=', 'viralbatches.facility_id')
            ->when($date_start, function($query) use ($date_start, $date_end){
                if($date_end)
                {
                    return $query->whereDate('viralbatches.datereceived', '>=', $date_start)
                    ->whereDate('viralbatches.datereceived', '<=', $date_end);
                }
                return $query->whereDate('viralbatches.datereceived', $date_start);
            })
            ->when($facility_user, function($query) use ($string){
                return $query->whereRaw($string);
            })
            ->when(true, function($query) use ($batch_complete){
                if($batch_complete < 4) return $query->where('batch_complete', $batch_complete);
            })
            ->orderBy('datereceived', 'desc')
            ->limit($page_limit)
            ->offset($offset)
            ->get();

        if($batches->isEmpty()){
            return view('tables.batches', ['rows' => null, 'links' => null, 'myurl' => $myurl, 'pre' => 'viral']);
        }

        $batch_ids = $batches->pluck(['id'])->toArray();

        $noresult_a = $my->get_totals(0, $batch_ids, false);
        $redraw_a = $my->get_totals(5, $batch_ids, false);
        $failed_a = $my->get_totals(3, $batch_ids, false);
        $detected_a = $my->get_totals(2, $batch_ids, false);
        $undetected_a = $my->get_totals(1, $batch_ids, false);

        $rejected = $my->get_rejected($batch_ids, false);
        $currentdate=date('d-m-Y');

        $table_rows = "";

        foreach ($batches as $key => $batch) {

            $undetected = $this->checknull($undetected_a->where('batch_id', $batch->id));
            $detected = $this->checknull($detected_a->where('batch_id', $batch->id));
            $failed = $this->checknull($failed_a->where('batch_id', $batch->id));
            $redraw = $this->checknull($redraw_a->where('batch_id', $batch->id));
            $noresult = $this->checknull($noresult_a->where('batch_id', $batch->id));

            $rej = $this->checknull($rejected->where('batch_id', $batch->id));
            $total = $undetected + $detected + $failed + $redraw + $noresult + $rej;

            $result = $detected + $undetected + $redraw + $failed;

            $datereceived=date("d-M-Y",strtotime($batch->datereceived));

            if($batch->batch_complete == 0){
                $max = $currentdate;
            }
            else{
                $max=date("d-M-Y",strtotime($batch->datedispatched));
            }

            $delays = $my->working_days($datereceived, $max);

            $table_rows .= "<tr> 
            <td>{$batch->id}</td>
            <td>{$batch->name}</td>
            <td>{$batch->datereceived}</td>
            <td>" . $batch->created_at->toDateString() . "</td>
            <td>{$delays}</td>
            <td></td>
            <td>{$total}</td>
            <td>{$rej}</td>
            <td>{$result}</td>
            <td>{$noresult}</td>" . $my->batch_status($batch->id, $batch->batch_complete) . "
            </tr>";
        }

        $links = $my->page_links($page, $last_page, $date_start, $date_end);

        return view('tables.batches', ['rows' => $table_rows, 'links' => $links, 'myurl' => $myurl, 'pre' => 'viral']);
    }*/
}
