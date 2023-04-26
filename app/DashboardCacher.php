<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use DB;

use App\Batch;
use App\CancerWorksheet;
use App\Viralbatch;
use App\Facility;
use App\FacilityContact;
use App\SampleView;
use App\ViralsampleView;
use App\CovidSampleView;
use App\Worksheet;
use App\Viralworksheet;
use App\Cd4Worksheet;
use App\Cd4Sample;

class DashboardCacher
{
    
    public static function tasks()
    {
    	self::tasks_cacher();

    	return [
    		'labname' => Cache::get('labname'),
    		'facilityServed' => Cache::get('facilityServed'),
    		'facilitiesWithoutEmails' => Cache::get('facilitiesWithoutEmails'),
    		'facilitiesWithoutG4s' => Cache::get('facilitiesWithoutG4s'),
    	];
    }

    public static function tasks_cacher()
    {
    	if(Cache::has('labname')) return true;

    	$minutes = 30;
    	$min_year = date('Y') - 2;
    	$lab_id = env('APP_LAB');
    	$labname =  \App\Lab::find(env('APP_LAB'))->name;

    	$facilityServed = Viralbatch::selectRaw("COUNT(DISTINCT facility_id) AS total")
    						->whereIn('site_entry', [1, 2])
    						->where('lab_id', $lab_id)
    						->whereYear('datereceived', '>', $min_year)
    						->get()->first()->total;

    	$facilitiesWithoutEmails = FacilityContact::selectRaw('COUNT(*) as total')
    		->whereRaw("( (email = '' or email is null) AND (ContactEmail = '' or ContactEmail is null) )")
    		->whereRaw("id in (SELECT DISTINCT facility_id FROM viralbatches WHERE site_entry in (1, 2) AND year(datereceived) > {$min_year} AND lab_id = {$lab_id})")
    		->get()->first()->total;

    	$facilitiesWithoutG4s = FacilityContact::selectRaw('COUNT(*) as total')
    		->whereRaw("(G4Sbranchname is null or G4Sbranchname = '')")
    		->whereRaw("(G4Slocation is null or G4Slocation = '')")
    		->whereRaw("id in (SELECT DISTINCT facility_id FROM viralbatches WHERE site_entry in (1, 2) AND year(datereceived) > {$min_year} AND lab_id = {$lab_id})")
    		->get()->first()->total;



        Cache::put('labname', $labname, $minutes);
        Cache::put('facilityServed', $facilityServed, $minutes);
        Cache::put('facilitiesWithoutEmails', $facilitiesWithoutEmails, $minutes);
        Cache::put('facilitiesWithoutG4s', $facilitiesWithoutG4s, $minutes);
    }

    public static function dashboard()
    {
        if (env('APP_LAB') == 7) self::nhrl_cacher();
        else{
            if (env('APP_LAB') == 1) self::nhrl_cacher();
        	self::cacher();
        }

        $data['get_style'] = function($val=null)
        {
            if($val && $val > 0) return 'background-color: #FDE3A7';
            return '';
        };

        $data['get_badge'] = function($val=null)
        {
            if($val && $val > 0) return 'danger';
            return 'success';
        };

        $data['rejectedAllocations'] = Cache::get('rejectedAllocations');

        if (session('testingSystem') == 'Viralload') {            
        	return array_merge($data, [
        		'pendingSamples' => Cache::get('vl_pendingSamples'),
        		'pendingSamplesOverTen' => Cache::get('vl_pendingSamplesOverTen'),
        		'batchesForApproval' => Cache::get('vl_batchesForApproval'),
        		'batchesNotReceived' => Cache::get('vl_batchesNotReceived'),
        		'batchesForDispatch' => Cache::get('vl_batchesForDispatch'),
        		'samplesForRepeat' => Cache::get('vl_samplesForRepeat'),
        		'rejectedForDispatch' => Cache::get('vl_rejectedForDispatch'),
        		'resultsForUpdate' => Cache::get('vl_resultsForUpdate'),
                'overduetesting' => Cache::get('vl_overduetesting'),
                'overduedispatched' => Cache::get('vl_overduedispatched'),
                'delayed_batches' => Cache::get('vl_delayed_batches'),
                'unreceived_batches' => Cache::get('vl_unreceived_batches'),
                'sample_manifest' => Cache::get('vl_pending_sample_manifest'),
                'emrForApproval' => Cache::get('vl_emrForApproval'),
                'prefix' => 'viral',
        	]);
        } else if (session('testingSystem') == 'EID'){
            return array_merge($data, [
                'pendingSamples' => Cache::get('eid_pendingSamples'),
                'pendingSamplesOverTen' => Cache::get('eid_pendingSamplesOverTen'),
                'batchesForApproval' => Cache::get('eid_batchesForApproval'),
                'batchesNotReceived' => Cache::get('eid_batchesNotReceived'),
                'batchesForDispatch' => Cache::get('eid_batchesForDispatch'),
                'samplesForRepeat' => Cache::get('eid_samplesForRepeat'),
                'rejectedForDispatch' => Cache::get('eid_rejectedForDispatch'),
                'resultsForUpdate' => Cache::get('eid_resultsForUpdate'),
                'overduetesting' => Cache::get('eid_overduetesting'),
                'overduedispatched' => Cache::get('eid_overduedispatched'),
                'delayed_batches' => Cache::get('eid_delayed_batches'),
                'unreceived_batches' => Cache::get('eid_unreceived_batches'),
                'sample_manifest' => Cache::get('eid_pending_sample_manifest'),
                'emrForApproval' => Cache::get('eid_emrForApproval'),
                'prefix' => '',
            ]);
        } else if (session('testingSystem') == 'Covid') {
            // self::cache_covid();
            return array_merge($data, [
                'covid_pending_receipt' => Cache::get('covid_pending_receipt'),
                'covid_pending_testing' => Cache::get('covid_pending_testing'),
                'covid_pending_results_update' => Cache::get('covid_pending_results_update'),
            ]);
        } else if (session('testingSystem') == 'CD4') {
            return array_merge($data, [
                'CD4samplesInQueue' => Cache::get('CD4samplesInQueue'),
                'CD4resultsForUpdate' => Cache::get('CD4resultsForUpdate'),
                'CD4resultsForDispatch' => Cache::get('CD4resultsForDispatch'),
                'CD4worksheetFor2ndApproval' => Cache::get('CD4worksheetFor2ndApproval')
            ]);
        } else if (session('testingSystem') == 'DR') {
            return array_merge($data, [
                'dr_pending_receipt' => Cache::get('dr_pending_receipt'),
                'dr_pending_testing' => Cache::get('dr_pending_testing'),
                'dr_pending_update' => Cache::get('dr_pending_update'),
                'dr_awaiting_hyrax' => Cache::get('dr_awaiting_hyrax'),
                'dr_requires_action' => Cache::get('dr_requires_action'),
                'dr_pending_approval' => Cache::get('dr_pending_approval'),
            ]);
        } else if (session('testingSystem') == 'HPV') {
            return array_merge($data, [
                'hpv_resultsForUpdate' => Cache::get('hpv_resultsForUpdate'),
                'hpv_pending_testing' => Cache::get('hpv_pending_testing'),
                'hpv_worksheets_for_approval' => Cache::get('hpv_worksheets_for_approval'),
                'hpv_samplesforRerun' => Cache::get('hpv_samplesforRerun'),
            ]);
        }
    }



	public static function pendingSamplesAwaitingTesting($over = false, $testingSystem = 'Viralload')
	{
        $year = date('Y') - 1;
        if(date('m') < 7) $year --;
        $date_str = $year . '-12-31';

        if ($testingSystem == 'Viralload') {
            if ($over == true) {
                $model = ViralsampleView::selectRaw('COUNT(id) as total')->whereNull('worksheet_id')
                                ->whereRaw("datediff(datereceived, datetested) > 14")->where('lab_id', '=', env('APP_LAB'))
                                ->where('site_entry', '<>', 2)
                                ->whereNull('result')->get()->first()->total;
            } else {
                $sampletype = ['plasma'=>[1,1],'EDTA'=>[2,2],'DBS'=>[3,4],'all'=>[1,4]];
                foreach ($sampletype as $key => $value) {
                    $model[$key] = ViralsampleView::selectRaw('COUNT(id) as total')
                        // ->whereNotIn('receivedstatus', ['0', '2'])
                        ->whereBetween('sampletype', [$value[0], $value[1]])
                        ->whereNull('worksheet_id')
                        ->whereNull('datedispatched')
                        ->where('datereceived', '>', $date_str)
                        ->whereRaw("(result is null or result = '0')")
                        ->where(['lab_id' => env('APP_LAB'), 'receivedstatus' => 1, 'input_complete' => 1])
                        ->where('site_entry', '<>', 2)
                        ->where('flag', '1')->get()->first()->total; 
                }
            }
        } elseif ($testingSystem == 'Covid') {
            if ($over == true) {
                $model = CovidSampleView::selectRaw('COUNT(id) as total')
                                ->whereNull('worksheet_id')->where('lab_id', '=', env('APP_LAB'))
                                ->whereRaw("datediff(datereceived, datetested) > 14")
                                ->where('site_entry', '<>', 2)
                                ->whereNull('result')->get()->first()->total;
            } else {
                $model = CovidSampleView::selectRaw('COUNT(id) as total')
                    ->whereNull('worksheet_id')
                    ->whereNull('datedispatched')
                    ->where('datereceived', '>', $date_str)
                    ->whereRaw("(result is null or result = '0')")
                    ->where(['lab_id' => env('APP_LAB'), 'receivedstatus' => 1, 'input_complete' => 1])
                    ->where('site_entry', '<>', 2)
                    ->where('flag', '1')->get()->first()->total;
            }
        } elseif ($testingSystem == 'HPV') {
            if ($over == true) {
                $model = CancerSampleView::selectRaw('COUNT(id) as total')
                                ->whereNull('worksheet_id')->where('lab_id', '=', env('APP_LAB'))
                                // ->whereRaw("datediff(datereceived, datetested) > 14")
                                ->where('site_entry', '<>', 2)
                                ->whereNull('result')->get()->first()->total;
            } else {
                $model = CancerSampleView::selectRaw('COUNT(id) as total')
                    ->whereNull('worksheet_id')
                    ->whereNull('datedispatched')
                    // ->where('datereceived', '>', $date_str)
                    ->whereRaw("(result is null or result = '0')")
                    ->where(['lab_id' => env('APP_LAB'), 'receivedstatus' => 1])
                    ->where('site_entry', '<>', 2)->get()->first()->total;
            }
        } else {
            if ($over == true) {
                $model = SampleView::selectRaw('COUNT(id) as total')
                                ->whereNull('worksheet_id')->where('lab_id', '=', env('APP_LAB'))
                                ->whereRaw("datediff(datereceived, datetested) > 14")
                                ->where('site_entry', '<>', 2)
                                ->whereNull('result')->get()->first()->total;
            } else {
                $model = SampleView::selectRaw('COUNT(id) as total')
                    ->whereNull('worksheet_id')
                    ->whereNull('datedispatched')
                    ->where('datereceived', '>', $date_str)
                    ->whereRaw("(result is null or result = '0')")
                    ->where(['lab_id' => env('APP_LAB'), 'receivedstatus' => 1, 'input_complete' => 1])
                    ->where('site_entry', '<>', 2)
                    ->where('flag', '1')->get()->first()->total;
            }
        }
        
        return $model;
	}

    public static function CD4pendingSamplesAwaitingTesting(){
        return Cd4Sample::selectRaw("COUNT(*) as total")->where('status_id', '=', 1)->where('repeatt', '=', 0)->first()->total;
    }

	public static function siteBatchesAwaitingApproval($testingSystem = 'Viralload')
	{
        if ($testingSystem == 'Viralload') {
            $model = ViralsampleView::selectRaw("COUNT(distinct batch_id) as total")
                        // ->where('lab_id', '=', env('APP_LAB'))
                        // ->where('flag', '=', '1')
                        // ->where('repeatt', '=', '0')
                        // ->whereRaw('(receivedstatus is null or receivedstatus=0)')
                        // ->where('site_entry', '=', '1')
                        ->where('lab_id', '=', env('APP_LAB'))
                        ->whereNull('receivedstatus')
                        ->where('site_entry', 1);
        } elseif ($testingSystem == 'Covid') {
            $model = CovidSampleView::selectRaw("COUNT(distinct batch_id) as total")
                        ->where('lab_id', '=', env('APP_LAB'))
                        ->whereNull('receivedstatus')
                        ->where('site_entry', 1);
        } else {
            $model = SampleView::selectRaw("COUNT(distinct batch_id) as total")
                    // ->where('lab_id', '=', env('APP_LAB'))
                    // ->where('flag', '=', '1')
                    // ->where('repeatt', '=', '0')
                    // ->whereRaw('(receivedstatus is null or receivedstatus=0)')
                    // ->where('site_entry', '=', '1')
                    ->where('lab_id', '=', env('APP_LAB'))
                    ->whereNull('receivedstatus')
                    ->where('site_entry', 1);
        }
        return $model->get()->first()->total ?? 0;
	}

	public static function batchCompleteAwaitingDispatch($testingSystem = 'Viralload')
	{
        if ($testingSystem == 'Viralload') {
            $model = Viralbatch::class;
        } else {
            $model = Batch::class;
        }
        return $model::selectRaw('COUNT(*) as total')->where(['lab_id' => env('APP_LAB'), 'batch_complete' => 2])->first()->total;
	}

    public static function cd4samplesAwaitingDispatch(){
        return Cd4Sample::selectRaw("COUNT(*) as total")->where('lab_id', '=', env('APP_LAB'))
                            ->where('status_id', '=', 5)->where('repeatt', '=', 0)->first()->total;
    }

    public static function cd4worksheetFor2ndApproval() {
        return Cd4Worksheet::selectRaw("COUNT(*) as total")->whereNotNull('reviewedby')->whereNull('reviewedby2')
                            ->where('status_id', '<>', 2)->first()->total;
    }

	public static function samplesAwaitingRepeat($testingSystem = 'Viralload')
	{
        $year = date('Y') - 1;
        if(date('m') < 7) $year --;
        $date_str = $year . '-12-31';

        if($testingSystem == 'Viralload') {
            $model = ViralsampleView::selectRaw('COUNT(*) as total')
                        ->whereBetween('sampletype', [1, 5])
                        ->whereNull('worksheet_id')
                        ->where('datereceived', '>', $date_str)
                        ->whereRaw("(result is null or result = '0')")
                        ->where('input_complete', '=', '1')
                        ->where('flag', '=', '1');
        }elseif ($testingSystem == 'Covid') {
            $model = CovidSampleView::selectRaw('COUNT(*) as total')
                        ->whereNull('worksheet_id')
                        ->where('datereceived', '>', $date_str)
                        ->where(function ($query) {
                            $query->whereNull('result')
                                ->orWhere('result', '=', 0);
                        });
        } elseif ($testingSystem == 'HPV') {
            $model = CancerSampleView::selectRaw('COUNT(*) as total')
                        ->whereNull('worksheet_id')
                        // ->where('datereceived', '>', $date_str)
                        ->where(function ($query) {
                            $query->whereNull('result')
                                ->orWhere('result', '=', 0);
                        });
        } else {
            $model = SampleView::selectRaw('COUNT(*) as total')
                        ->whereNull('worksheet_id')
                        ->where('datereceived', '>', $date_str)
                        ->where(function ($query) {
                            $query->whereNull('result')
                                  ->orWhere('result', '=', 0);
                        })
                        ->where('flag', '=', '1');
        }
		return $model->where('receivedstatus', '<>', 2)->where('receivedstatus', '<>', 0)
                ->where('lab_id', '=', env('APP_LAB'))->where('parentid', '>', '0')->get()->first()->total;
	}

	public static function rejectedSamplesAwaitingDispatch($testingSystem = 'Viralload')
	{
        $year = Date('Y')-3;
        if ($testingSystem == 'Viralload') {
            $model = ViralsampleView::selectRaw('count(*) as total')
                        ->where('receivedstatus', 2)
                        ->where('flag', '=', 1)
                        ->whereYear('datereceived', '>', $year)
                        ->whereNotNull('datereceived')
                        ->where('lab_id', '=', env('APP_LAB'))
                        ->where('site_entry', '<>', 2)
                        ->whereNull('datedispatched');
                        // ->where('datedispatched', '=', '')
                        // ->orWhere('datedispatched', '=', '0000-00-00')
                        // ->orWhere('datedispatched', '=', '1970-01-01')
                        // ->orWhereNotNull('datedispatched');
        } elseif ($testingSystem == 'Covid') {
            $model = CovidSampleView::selectRaw('count(*) as total')
                        ->where('receivedstatus', 2)
                        ->whereYear('datereceived', '>', $year)
                        ->whereNotNull('datereceived')
                        ->where('site_entry', '<>', 2)
                        ->where('lab_id', '=', env('APP_LAB'))
                        ->whereNull('datedispatched');
        } else {
            $model = SampleView::selectRaw('count(*) as total')
                        ->where('receivedstatus', 2)
                        ->whereYear('datereceived', '>', $year)
                        ->whereNotNull('datereceived')
                        ->where('site_entry', '<>', 2)
                        ->where('lab_id', '=', env('APP_LAB'))
                        ->whereNull('datedispatched');
        }
        
		return $model->get()->first()->total ?? 0;
	}

    public static function batchesMarkedNotReceived($testingSystem = 'Viralload')
    {
        $model = 0;
        if ($testingSystem == 'Viralload') {
            $model = ViralsampleView::selectRaw('count(distinct batch_id) as total')
                        ->where('receivedstatus', '=', '4')
                        ->where('lab_id', '=', env('APP_LAB'))
                        ->orWhereNull('receivedstatus')->get()->first();
        } else {
            # code...
        }
        
        return $model->total ?? 0;
    }

    public static function resultsAwaitingpdate($testingSystem = 'Viralload')
    {
        if ($testingSystem == 'Viralload') {
            $model = Viralworksheet::with(['creator']);
        } else if ($testingSystem == 'Eid') {
            $model = Worksheet::with(['creator']);
        } else if ($testingSystem == 'Covid') {
            $model = CovidWorksheet::with(['creator']);
        } else if ($testingSystem = 'HPV') {
            $model = CancerWorksheet::with(['creator']);
        } else {
            $model = Cd4Worksheet::with(['creator']);
        }

        return $model->selectRaw('count(*) as total')->where('lab_id', '=', env('APP_LAB'))->where('status_id', '=', '1')->first()->total ?? 0;
    }

    public static function overdue($level = 'testing',$testingSystem = 'Viralload') {
        if ($testingSystem == 'Viralload') {
            $model = ViralsampleView::selectRaw('count(*) as total');
        } elseif ($testingSystem == 'Covid') {
            $model = CovidSampleView::selectRaw('count(*) as total');
        } else {
            $model = SampleView::selectRaw('count(*) as total');
        }
        $year = Date('Y')-2;

        if ($level == 'testing') {
            $model = $model->whereNull('worksheet_id')
                            ->whereIn('receivedstatus', [1, 3])
                            ->whereRaw("(result is null or result = '0')");
        } else {
            $model = $model->whereNotNull('worksheet_id')->whereNull('datedispatched')->whereNull('datesynched');
        }

        return $model->where('repeatt', 0)
                        ->where('lab_id', '=', env('APP_LAB'))
                        ->whereYear('datereceived', '>', $year)
                        ->whereRaw("datediff(curdate(), datereceived) > 14")
                        ->get()->first()->total ?? 0;
    }
    public static function emrBatchesAwaitingApproval($testingSystem = 'Viralload')
    {
        if ($testingSystem == 'Viralload') {
            $model = ViralsampleView::selectRaw("COUNT(distinct batch_id) as total")
                ->where('lab_id', '=', env('APP_LAB'))
                ->whereNull('receivedstatus')
                ->whereNotNull('order_no');
        } elseif ($testingSystem == 'Covid') {
            $model = CovidSampleView::selectRaw("COUNT(distinct batch_id) as total")
                ->where('lab_id', '=', env('APP_LAB'))
                ->whereNull('receivedstatus')
                ->whereNotNull('order_no');
        } else {
            $model = SampleView::selectRaw("COUNT(distinct batch_id) as total")
                ->where('lab_id', '=', env('APP_LAB'))
                ->whereNull('receivedstatus')
                ->whereNotNull('order_no');
        }
        return $model->get()->first()->total ?? 0;
    }


    public static function delayed_batches($pre = null)
    {
        if($pre){
            $batch_class = \App\Viralbatch::class;
            $res_query = "result IS NOT NULL AND result != 'Failed' AND result != ''";
        }
        else{
            $batch_class = \App\Batch::class;
            $res_query = "result > 0";            
        }

        $delayed = $batch_class::selectRaw("{$pre}batches.*, COUNT({$pre}samples.id) AS `samples_count`")
            ->join("{$pre}samples", "{$pre}batches.id", '=', "{$pre}samples.batch_id")
            ->where(['batch_complete' => 0, 'lab_id' => env('APP_LAB')])
            ->when(true, function($query) use ($res_query){
                if(in_array(env('APP_LAB'), \App\Lookup::$double_approval)){
                    return $query->whereRaw("( receivedstatus=2 OR  ({$res_query} AND (repeatt = 0 or repeatt is null) AND approvedby IS NOT NULL AND approvedby2 IS NOT NULL) )");
                }
                return $query->whereRaw("( receivedstatus=2 OR  ({$res_query} AND (repeatt = 0 or repeatt is null) AND approvedby IS NOT NULL) )");
            })
            ->groupBy("{$pre}batches.id")
            ->havingRaw("COUNT({$pre}samples.id) > 0")
            ->get();

        return $delayed->count();
    }

    public static function unreceived_batches($pre = null)
    {
        if($pre){
            $batch_class = \App\Viralbatch::class;
        }
        else{
            $batch_class = \App\Batch::class;
        }

        return $batch_class::selectRaw("COUNT(id) AS my_count")
            ->where(['lab_id' => env('APP_LAB')])
            ->where('created_at', '<', date('Y-m-d', strtotime('-10 days')))
            ->whereNull('datereceived')
            ->whereNull('datedispatched')
            ->first()->my_count;
    }

    public static function pending_sample_manifest($pre = null) {
        if ($pre)
            $batch_class = Viralbatch::class;
        else 
            $batch_class = Batch::class;

        return $batch_class::selectRaw("COUNT(*) as `tobereceived`")->whereNull('datedispatchedfromfacility')
                    ->where('site_entry', 1)->whereRaw("YEAR(created_at) >= 2019")
                    ->first()->tobereceived;
    }

    public static function rejectedAllocations() {
        return AllocationDetail::where('approve', '=', 2)->count();
    }

    public static function worksheetAwaitingApproval($testingSystem = 'Viralload')
    {
        if ($testingSystem == 'Viralload')
            $model = Viralworksheet::class;
        if ($testingSystem == 'HPV')
            $model = CancerWorksheet::class;

        return $model::selectRaw("COUNT(*) as `total`")
                ->where('status_id', 2)
                ->first()->total;
    }

    public static function nhrl_cacher()
    {
        // if(Cache::has('dr_pending_receipt')) return true;

        $minutes = (15*60);

        $pending_receipt = DrSample::selectRaw("COUNT(dr_samples.id) as `my_count`")
            ->join('users', 'users.id', '=', 'dr_samples.user_id')
            ->where('user_type_id', 5)
            ->where('control', 0)
            ->whereNull('datereceived')
            ->first()->my_count;

        $pending_testing = DrSample::selectRaw("COUNT(dr_samples.id) as `my_count`")
            ->whereNotNull('datereceived')
            ->where('datereceived', '>', date('Y-m-d', strtotime('-3 months')))
            ->where('control', 0)
            ->whereNull('worksheet_id')
            ->first()->my_count;

        $pending_update = DrSample::selectRaw("COUNT(dr_samples.id) as `my_count`")
            ->whereNotNull('worksheet_id')
            ->where('control', 0)
            ->whereNull('datetested')
            ->first()->my_count;

        $awaiting_hyrax = DrSample::selectRaw("COUNT(dr_samples.id) as `my_count`")
            ->whereNotNull('worksheet_id')
            ->where('control', 0)
            ->whereIn('status_id', [4,5,7])
            ->first()->my_count;

        $requires_action = DrSample::selectRaw("COUNT(dr_samples.id) as `my_count`")
            ->whereNotNull('worksheet_id')
            ->where('control', 0)
            ->where('status_id', 6)
            ->first()->my_count;

        $pending_approval = DrSample::selectRaw("COUNT(dr_samples.id) as `my_count`")
            ->whereNotNull('worksheet_id')
            ->whereNull('datedispatched')
            ->where('control', 0)
            ->where('status_id', '<', 4)
            ->first()->my_count;


        Cache::put('dr_pending_receipt', $pending_receipt, $minutes);
        Cache::put('dr_pending_testing', $pending_testing, $minutes);
        Cache::put('dr_pending_update', $pending_update, $minutes);
        Cache::put('dr_awaiting_hyrax', $awaiting_hyrax, $minutes);
        Cache::put('dr_requires_action', $requires_action, $minutes);
        Cache::put('dr_pending_approval', $pending_approval, $minutes);
        // Cache::put('dr_', $pendingSamples, $minutes);
    }

    public static function cacher()
    {
        // Cache is now in seconds
    	$minutes = (5*60);

        if (session('testingSystem') == 'Viralload'){

            if(Cache::has('vl_pendingSamples')) return true;

    		$pendingSamples = self::pendingSamplesAwaitingTesting();
            $pendingSamplesOverTen = self::pendingSamplesAwaitingTesting(true);
    		$batchesForApproval = self::siteBatchesAwaitingApproval();
            $batchesNotReceived = self::batchesMarkedNotReceived();
    		$batchesForDispatch = self::batchCompleteAwaitingDispatch();
    		$samplesForRepeat = self::samplesAwaitingRepeat();
    		$rejectedForDispatch = self::rejectedSamplesAwaitingDispatch();
            $resultsForUpdate = self::resultsAwaitingpdate();
            $overduetesting = self::overdue('testing');
            $overduedispatched = self::overdue('dispatched');
            $delayed_batches = self::delayed_batches('viral');
            $unreceived_batches = self::unreceived_batches('viral');
            $pending_sample_manifest = self::pending_sample_manifest('viral');
            $emrForApproval = self::emrBatchesAwaitingApproval();

            Cache::put('vl_pendingSamples', $pendingSamples, $minutes);
            Cache::put('vl_pendingSamplesOverTen', $pendingSamplesOverTen, $minutes);
            Cache::put('vl_batchesForApproval', $batchesForApproval, $minutes);
            Cache::put('vl_batchesNotReceived', $batchesNotReceived, $minutes);
            Cache::put('vl_batchesForDispatch', $batchesForDispatch, $minutes);
            Cache::put('vl_samplesForRepeat', $samplesForRepeat, $minutes);
            Cache::put('vl_rejectedForDispatch', $rejectedForDispatch, $minutes);
            Cache::put('vl_resultsForUpdate', $resultsForUpdate, $minutes);
            Cache::put('vl_overduetesting', $overduetesting, $minutes);
            Cache::put('vl_overduedispatched', $overduedispatched, $minutes);
            Cache::put('vl_delayed_batches', $delayed_batches, $minutes);
            Cache::put('vl_unreceived_batches', $unreceived_batches, $minutes);
            Cache::put('vl_pending_sample_manifest', $pending_sample_manifest, $minutes);
            Cache::put('vl_emrForApproval', $emrForApproval, $minutes);

        }
        else if (session('testingSystem') == 'EID'){

            if(Cache::has('eid_pendingSamples')) return true;

            $pendingSamples2 = self::pendingSamplesAwaitingTesting(false, 'Eid');
            $pendingSamplesOverTen2 = self::pendingSamplesAwaitingTesting(true, 'Eid');
            $batchesForApproval2 = self::siteBatchesAwaitingApproval('Eid');
            $batchesNotReceived2 = self::batchesMarkedNotReceived('Eid');
            $batchesForDispatch2 = self::batchCompleteAwaitingDispatch('Eid');
            $samplesForRepeat2 = self::samplesAwaitingRepeat('Eid');
            $rejectedForDispatch2 = self::rejectedSamplesAwaitingDispatch('Eid');
            $resultsForUpdate2 = self::resultsAwaitingpdate('Eid');
            $overduetesting2 = self::overdue('testing','Eid');
            $overduedispatched2 = self::overdue('dispatched','Eid');
            $delayed_batches2 = self::delayed_batches();
            $unreceived_batches2 = self::unreceived_batches();
            $pending_sample_manifest2 = self::pending_sample_manifest();
            $emrForApproval2 = self::emrBatchesAwaitingApproval('Eid');

            // EID cache 
            Cache::put('eid_pendingSamples', $pendingSamples2, $minutes);
            Cache::put('eid_pendingSamplesOverTen', $pendingSamplesOverTen2, $minutes);
            Cache::put('eid_batchesForApproval', $batchesForApproval2, $minutes);
            Cache::put('eid_batchesNotReceived', $batchesNotReceived2, $minutes);
            Cache::put('eid_batchesForDispatch', $batchesForDispatch2, $minutes);
            Cache::put('eid_samplesForRepeat', $samplesForRepeat2, $minutes);
            Cache::put('eid_rejectedForDispatch', $rejectedForDispatch2, $minutes);
            Cache::put('eid_resultsForUpdate', $resultsForUpdate2, $minutes);
            Cache::put('eid_overduetesting', $overduetesting2, $minutes);
            Cache::put('eid_overduedispatched', $overduedispatched2, $minutes);
            Cache::put('eid_delayed_batches', $delayed_batches2, $minutes);
            Cache::put('eid_unreceived_batches', $unreceived_batches2, $minutes);
            Cache::put('eid_pending_sample_manifest', $pending_sample_manifest2, $minutes);
            Cache::put('eid_emrForApproval', $emrForApproval2, $minutes);

        }
        else if(env('APP_LAB') == 5 && session('testingSystem') == 'CD4'){
            if(Cache::has('CD4samplesInQueue')) return true;

            $CD4samplesInQueue = self::CD4pendingSamplesAwaitingTesting();
            $CD4resultsForUpdate = self::resultsAwaitingpdate('CD4');
            $CD4resultsForDispatch = self::cd4samplesAwaitingDispatch();
            $CD4worksheetFor2ndApproval = self::cd4worksheetFor2ndApproval();

            Cache::put('CD4samplesInQueue', $CD4samplesInQueue, $minutes);
            Cache::put('CD4resultsForUpdate', $CD4resultsForUpdate, $minutes);
            Cache::put('CD4resultsForDispatch', $CD4resultsForDispatch, $minutes);
            Cache::put('CD4worksheetFor2ndApproval', $CD4worksheetFor2ndApproval, $minutes);
        }
        else if(session('testingSystem') == 'Covid'){
            if(Cache::has('covid_pending_receipt')) return true;

            $pending_receipt = CovidSample::selectRaw('count(id) AS my_count')
                ->where(['lab_id' => env('APP_LAB')])
                ->whereNull('datereceived')->whereNull('datedispatched')->first()->my_count;
            $pending_testing = CovidSample::selectRaw('count(id) AS my_count')
                ->where(['lab_id' => env('APP_LAB'), 'receivedstatus' => 1, 'repeatt' => 0])
                ->where('datereceived', '>', date('Y-m-d', strtotime('-2 months')))
                ->whereNull('datetested')->whereNull('datedispatched')->whereNull('worksheet_id')->first()->my_count;
            $worksheets = self::resultsAwaitingpdate('Covid');

            Cache::put('covid_pending_receipt', $pending_receipt, $minutes);
            Cache::put('covid_pending_testing', $pending_testing, $minutes);
            Cache::put('covid_pending_results_update', $worksheets, $minutes);
        }
        else if(session('testingSystem') == 'HPV'){
            if(Cache::has('hpv_resultsForUpdate')) return true;
            $hpvresultsForUpdate = self::resultsAwaitingpdate(session('testingSystem'));
            $hpv_pending_testing = self::pendingSamplesAwaitingTesting(false, session('testingSystem'));
            $hpv_worksheets_for_approval = self::worksheetAwaitingApproval(session('testingSystem'));
            $hpv_samplesforRerun = self::samplesAwaitingRepeat(session('testingSystem'));
            Cache::put('hpv_resultsForUpdate', $hpvresultsForUpdate, $minutes);
            Cache::put('hpv_pending_testing', $hpv_pending_testing, $minutes);
            Cache::put('hpv_worksheets_for_approval', $hpv_worksheets_for_approval, $minutes);
            Cache::put('hpv_samplesforRerun', $hpv_samplesforRerun, $minutes);
        }

        // $rejectedAllocations = self::rejectedAllocations();
        
        // if(env('APP_LAB') != 7) $rejectedAllocations = self::rejectedAllocations();

        // Neutral Cache
        // if(env('APP_LAB') != 7) Cache::put('rejectedAllocations', $rejectedAllocations, $minutes);
    }

    public static function cache_covid()
    {
        if(Cache::has('covid_pending_receipt')) return true;

        $minutes = 5;

        $pending_receipt = CovidSample::selectRaw('count(id) AS my_count')
            ->where(['lab_id' => env('APP_LAB')])
            ->whereNull('datereceived')->whereNull('datedispatched')->first()->my_count;
        $pending_testing = CovidSample::selectRaw('count(id) AS my_count')
            ->where(['lab_id' => env('APP_LAB'), 'receivedstatus' => 1, 'repeatt' => 0])
            ->where('datereceived', '>', date('Y-m-d', strtotime('-2 months')))
            ->whereNull('datetested')->whereNull('datedispatched')->whereNull('worksheet_id')->first()->my_count;
        $worksheets = self::resultsAwaitingpdate('Covid');

        Cache::put('covid_pending_receipt', $pending_receipt, $minutes);
        Cache::put('covid_pending_testing', $pending_testing, $minutes);
        Cache::put('covid_pending_results_update', $worksheets, $minutes);
    }

    public static function clear_cache()
    {
    	Cache::forget('vl_pendingSamples');
    	Cache::forget('vl_pendingSamplesOverTen');
    	Cache::forget('vl_batchesForApproval');
    	Cache::forget('vl_batchesNotReceived');
    	Cache::forget('vl_batchesForDispatch');
    	Cache::forget('vl_samplesForRepeat');
    	Cache::forget('vl_rejectedForDispatch');
    	Cache::forget('vl_resultsForUpdate');
        Cache::forget('vl_overduetesting');
        Cache::forget('vl_overduedispatched');
        Cache::forget('vl_delayed_batches');
        Cache::forget('vl_emrForApproval');
        
        Cache::forget('eid_pendingSamples');
        Cache::forget('eid_pendingSamplesOverTen');
        Cache::forget('eid_batchesForApproval');
        Cache::forget('eid_batchesNotReceived');
        Cache::forget('eid_batchesForDispatch');
        Cache::forget('eid_samplesForRepeat');
        Cache::forget('eid_rejectedForDispatch');
        Cache::forget('eid_resultsForUpdate');
        Cache::forget('eid_overduetesting');
        Cache::forget('eid_overduedispatched');
        Cache::forget('eid_delayed_batches');
        Cache::forget('eid_emrForApproval');

        Cache::forget('covid_pending_receipt');
        Cache::forget('covid_pending_testing');
        Cache::forget('covid_pending_results_update');
    }


    public static function refresh_cache()
    {
        self::clear_cache();
        self::cacher();
    }

}
