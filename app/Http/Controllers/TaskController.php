<?php

namespace App\Http\Controllers;

use App\Abbotdeliveries;
use App\Abbotprocurement;
use App\Allocation;
use App\AllocationDetail;
use App\AllocationDetailsBreakdown;
use App\Consumption;
use App\ConsumptionDetail;
use App\CovidConsumption;
use App\CovidConsumptionDetail;
use App\CovidKit;
use App\Deliveries;
use App\Taqmandeliveries;
use App\Taqmanprocurement;
use App\LabEquipmentTracker;
use App\LabPerformanceTracker;
use App\Requisition;
use App\User;
use App\SampleView;
use App\ViralsampleView;
use App\Kits;
use App\Machine;
use App\GeneralConsumables;
use App\Synch;

use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TaskController extends Controller
{
    protected $testtypes = ['EID' => 1, 'VL' => 2];
    protected $year;
    protected $previousYear;
    protected $month;
    protected $previousMonth;

    public function __construct(){
        $this->year = date('Y');
        $this->month = date('m');
        $this->previousYear = date('Y', strtotime("-1 Month", strtotime(date('Y-m-d'))));
        $this->previousMonth = date('m', strtotime("-1 Month", strtotime(date('Y-m-d'))));
    }

    public function index() 
    {
        if (!$this->pendingTasks()) {
            // \App\Common::send_lab_tracker($this->previousYear, $this->previousMonth);
            session(['pendingTasks'=> false]);
            return redirect()->route('home');
        }
        // return redirect('/home');
        if ($this->eligibleForEidVlConsumptions()) {
            $pendingDeliveries = $this->getDeliveries();
            $data['deliveries'] = $pendingDeliveries;
            if (empty($pendingDeliveries)){
                $pendingConsumptions = $this->getConsumptions();
                $data['consumptions'] = $pendingConsumptions;
            }
          		
    		
            $range = '';
            $quarter = parent::_getMonthQuarter(date('m'),$range);
            session(['range'=>$range, 'quarter'=>$quarter]);

        }

        if ($this->eligibleForEquipmentUtilization()) {
            $month = $this->previousMonth;
            $year = $this->previousYear;
            $data['equipment'] = LabEquipmentTracker::where('year', $year)->where('month', $month)->count();
            $data['performance'] = LabPerformanceTracker::where('year', $year)->where('month', $month)->count();
        }
  //       $data['requisitions'] = count($this->getRequisitions());
        if ($this->eligibleForCovidConsumptions()) {
            $data['covidconsumption'] = CovidConsumption::where('start_of_week', '=', $this->getPreviousWeek()->week_start)
                                        ->where('lab_id', '=', env('APP_LAB'))->count();
            $covidconsumption = new CovidConsumption;
            $data['time'] = $covidconsumption->getMissingConsumptions();
        }
        
        $data['currentmonth'] = date('m');
        $data['prevmonth'] = $this->previousMonth;
        $data['year'] = date('Y');
        $data['prevyear'] = $this->previousYear;
        $data['allowedEIDVLConsumption'] = $this->eligibleForEidVlConsumptions();
        $data['allowedCovidConsumption'] = $this->eligibleForCovidConsumptions();
        $data['allowedEquipmentUtilization'] = $this->eligibleForEquipmentUtilization();
        
        return view('tasks.home', $data)->with('pageTitle', 'Pending Tasks');
    }

    public function addKitDeliveries(Request $request, $platform = null)
    {
        $data = [
                'machines' => Machine::get(),
            ];

        return view('tasks.newkitsdeliveries', $data)->with('pageTitle', 'Kit Deliveries');
        if ($platform == null) {
            $users = User::where('user_type_id', '<', 5)->get();
            $data = [
                    'users' => $users,
                    
                    'pending_deliveries' => $this->getDeliveries(),
                ];
            
        } else {
            $deliveries = $this->submitNullDeliveries($platform);
            return redirect()->route('pending');
        }
    }

    /*
    public function addKitDeliveries(Request $request, $platform = null)
    {
        if ($platform == null) {
            $taqdeliveries = Taqmandeliveries::selectRaw("count(*) as entries")->whereYear('datereceived', '=', $this->year)->where('quarter', parent::_getMonthQuarter($this->month))->first()->entries;
            $abbottdeliveries = Abbotdeliveries::selectRaw("count(*) as entries")->whereYear('datereceived', '=', $this->year)->where('quarter', parent::_getMonthQuarter($this->month))->first()->entries;

            if ($request->saveTaqman) {
                $receivedby = $request->receivedby ?? NULL;
                $datereceived = $request->datereceived ?? NULL;
                $vreceivedby = $request->vreceivedby ?? NULL;
                $vdatereceived = $request->vdatereceived ?? NULL;
                $lab = auth()->user()->lab_id;
                $quarter = $request->quarter ?? NULL;
                $status = 1;
                $source = 3;
                $year = $this->year;
                $eidData = [
                            'testtype' => 1,'lab_id' => $lab,
                            'quarter' => $quarter,'year' => $year,'source' => $source,
                            'kitlotno' => $request->kitlotno,'expirydate' => $request->expirydate,
                            'qualkitreceived' => $request->rqualkit,'spexagentreceived' => $request->rspexagent,
                            'ampinputreceived' => $request->rampinput,'ampflaplessreceived' => $request->rampflapless,
                            'ampktipsreceived' => $request->rampktips,'ampwashreceived' => $request->rampwash,
                            'ktubesreceived' => $request->rktubes,'qualkitdamaged' => $request->dqualkit,
                            'spexagentdamaged' => $request->dspexagent,'ampinputdamaged' => $request->dampinput,
                            'ampflaplessdamaged' => $request->dampflapless,'ampktipsdamaged' => $request->dampktips,
                            'ampwashdamaged' => $request->dampwash,'ktubesdamaged' => $request->dktubes,
                            'receivedby' => $receivedby,'datereceived' => $datereceived,'status' => $status,
                            'enteredby' => auth()->user()->id,'dateentered' => date('Y-m-d')
                        ];
                $save = Taqmandeliveries::create($eidData);
                $vlData = [
                            'testtype' => 2,'lab_id' => $lab,
                            'quarter' => $quarter,'year' => $year,'source' => $source,
                            'kitlotno' => $request->vkitlotno,'expirydate' => $request->vexpirydate,
                            'qualkitreceived' => $request->vrqualkit,'spexagentreceived' => $request->vrspexagent,
                            'ampinputreceived' => $request->vrampinput,'ampflaplessreceived' => $request->vrampflapless,
                            'ampktipsreceived' => $request->vrampktips,'ampwashreceived' => $request->vrampwash,
                            'ktubesreceived' => $request->vrktubes,'qualkitdamaged' => $request->vdqualkit,
                            'spexagentdamaged' => $request->vdspexagent,'ampinputdamaged' => $request->vdampinput,
                            'ampflaplessdamaged' => $request->vdampflapless,'ampktipsdamaged' => $request->vdampktips,
                            'ampwashdamaged' => $request->vdampwash,'ktubesdamaged' => $request->vdktubes,
                            'receivedby' => $vreceivedby,'datereceived' => $vdatereceived,'status' => $status,
                            'enteredby' => auth()->user()->id,'dateentered' => date('Y-m-d')
                        ];
                $save = Taqmandeliveries::create($vlData);
                session(['toast_message'=>'The KIT delivery has EID/VL Taqman KITS SAVED SUCCESSFULLY.']);
                if ($abbottdeliveries > 0)
                    return redirect()->route('pending');
            } else if ($request->saveAbbott) {
                // dd($request->all());
                $receivedby = $request->areceivedby ?? NULL;
                $datereceived = $request->adatereceived ?? NULL;
                $vreceivedby = $request->vareceivedby ?? NULL;
                $vdatereceived = $request->vadatereceived ?? NULL;
                $lab = auth()->user()->lab_id;
                $quarter = $request->quarter;
                $status = 1;
                $source = 3;
                $year = $this->year;

                $eidData = [
                    "testtype" => 1,"lab_id" => $lab,
                    "quarter" => $quarter,"year" => $year,"source" => $source,
                    "qualkitlotno" => $request->vaqualkitlotno,"qualkitexpiry" => $request->aqualkitexpiry,
                    "qualkitreceived" => $request->arqualkit,"qualkitdamaged" => $request->adqualkit,
                    "controllotno" => $request->acontrollotno,"controlexpiry" => $request->acontrolexpiry,
                    "controlreceived" => $request->arcontrol,"controldamaged" => $request->adcontrol,
                    "bufferlotno" => $request->abufferlotno,"bufferexpiry" => $request->abufferexpiry,
                    "bufferreceived" => $request->arbuffer,"bufferdamaged" => $request->adbuffer,
                    "preparationlotno" => $request->apreparationlotno,"preparationexpiry" => $request->apreparationexpiry,
                    "preparationreceived" => $request->arpreparation,"preparationdamaged" => $request->adpreparation,
                    "adhesivereceived" => $request->aradhesive,"adhesivedamaged" => $request->adadhesive,
                    "deepplatereceived" => $request->ardeepplate,"deepplatedamaged" => $request->addeepplate,
                    "mixtubereceived" => $request->armixtube,"mixtubedamaged" => $request->admixtube,
                    "reactionvesselsreceived" => $request->arreactionvessels,"reactionvesselsdamaged" => $request->adreactionvessels,
                    "reagentreceived" => $request->arreagent,"reagentdamaged" => $request->adreagent,
                    "reactionplatereceived" => $request->arreactionplate,"reactionplatedamaged" => $request->adreactionplate,
                    "1000disposablereceived" => $request->ar1000disposable,"1000disposabledamaged" => $request->ad1000disposable,
                    "200disposablereceived" => $request->ar200disposable,"200disposabledamaged" => $request->ad200disposable,
                    "receivedby" => $receivedby,"datereceived" => $datereceived,"status" => $status,
                    "enteredby" => auth()->user()->id,"dateentered" => date('Y-m-d')
                ];
                $save = Abbotdeliveries::create($eidData);

                $vlData = [
                    "testtype" => 2,"lab_id" => $lab,
                    "quarter" => $quarter,"year" => $year,"source" => $source,
                    "qualkitlotno" => $request->vaqualkitlotno,"qualkitexpiry" => $request->vaqualkitexpiry,
                    "qualkitreceived" => $request->varqualkit,"qualkitdamaged" => $request->vadqualkit,
                    "controllotno" => $request->vacontrollotno,"controlexpiry" => $request->vacontrolexpiry,
                    "controlreceived" => $request->varcontrol,"controldamaged" => $request->vadcontrol,
                    "bufferlotno" => $request->vabufferlotno,"bufferexpiry" => $request->vabufferexpiry,
                    "bufferreceived" => $request->varbuffer,"bufferdamaged" => $request->vadbuffer,
                    "preparationlotno" => $request->vapreparationlotno,"preparationexpiry" => $request->vapreparationexpiry,
                    "preparationreceived" => $request->varpreparation,"preparationdamaged" => $request->vadpreparation,
                    "adhesivereceived" => $request->varadhesive,"adhesivedamaged" => $request->vadadhesive,
                    "deepplatereceived" => $request->vardeepplate,"deepplatedamaged" => $request->vaddeepplate,
                    "mixtubereceived" => $request->varmixtube,"mixtubedamaged" => $request->vadmixtube,
                    "reactionvesselsreceived" => $request->varreactionvessels,
                    "reactionvesselsdamaged" => $request->vadreactionvessels,
                    "reagentreceived" => $request->varreagent,"reagentdamaged" => $request->vadreagent,
                    "reactionplatereceived" => $request->varreactionplate,"reactionplatedamaged" => $request->vadreactionplate,
                    "1000disposablereceived" => $request->var1000disposable,"1000disposabledamaged" => $request->vad1000disposable,
                    "200disposablereceived" => $request->var200disposable,"200disposabledamaged" => $request->vad200disposable,
                    "receivedby" => $receivedby,"datereceived" => $datereceived,"status" => $status,
                    "enteredby" => auth()->user()->id,"dateentered" => date('Y-m-d')
                ];

                $save = Abbotdeliveries::create($vlData);
                session(['toast_message'=>'The KIT delivery has EID/VL ABBOTT KITS SAVED SUCCESSFULLY.']);

                if ($taqdeliveries > 0)
                    return redirect()->route('pending');
            }

            $users = User::where('user_type_id', '<', 5)->get();
            $data = [
                            'users' => $users,
                            'machines' => Machine::get(),
                            'pending_deliveries' => $this->getDeliveries(),
                        ];
            // dd($data); 
            return view('tasks.newkitsdeliveries', $data)->with('pageTitle', 'Kit Deliveries');
        } else {
            $deliveries = $this->submitNullDeliveries($platform);
            return redirect()->route('pending');
        }
    }
    */

    protected function submitNullDeliveries($platform) {
        if ($platform == 'abbott') {
            $deliveries = $this->submitNullDeliveriesAbbott();
        } else if ($platform == 'roche') {
            $deliveries = $this->submitNullDeliveriesRoche();
        } else if ($platform == 'all') {
            $deliveries = $this->submitNullDeliveriesAbbott();
            $deliveries = $this->submitNullDeliveriesRoche();
        } else {
            return back();
        }
        return $deliveries;
    }

    protected function submitNullDeliveriesAbbott(){
        foreach ($this->testtypes as $key => $type) {
            $model = new Abbotdeliveries();
            $nulldelivery = $this->submitNullDeliveriesModel($model, $type);
        }
        return $nulldelivery;
    }

    protected function submitNullDeliveriesRoche(){
        foreach ($this->testtypes as $key => $type) {
            $model = new Taqmandeliveries();
            $nulldelivery = $this->submitNullDeliveriesModel($model, $type);
        }
        return $nulldelivery;
    }

    protected function submitNullDeliveriesModel($model, $type) {
        $model->testtype = $type;
        $model->source = 3;
        $model->lab_id = env('APP_LAB');
        $model->quarter = parent::_getMonthQuarter($this->month);
        $model->year = $this->year;
        $model->dateentered = date('Y-m-d');
        $model->save();
        return $model;
    }

    public function consumption (Request $request, $guide=null)
    {
        if ($guide != null) {
            return redirect('http://lab-2.test.nascop.org/download/consumption');
        }

        $data['testtypes'] = ['EID', 'VL'];
        $previousMonth = $this->previousMonth;
        $year = $this->previousYear;
        $endingBalanceMonth = $previousMonth - 1;
        $endingBalanceYear = $year;
        if ($previousMonth == 1) {
            $endingBalanceMonth = 12;
            $endingBalanceYear = $year - 1;
        }

        if ($request->saveTaqman || $request->saveAbbott)
        {
            $insertData = [];
            $sub = ['ending','wasted','issued','request','pos'];
            if ($request->platform == 1 || $request->platform == '1') {
                $platform = 'taqman';
                foreach ($data['testtypes'] as $k => $v) {
                    $testtype = 2;
                    if ($v == 'EID') 
                        $testtype = 1;
                    $tests = $platform.$v.'tests';
                    $insertData[$v]['testtype'] = $testtype;
                    $insertData[$v]['tests'] = $request->$tests;
                    $insertData[$v]['month'] = $previousMonth;
                    $insertData[$v]['year'] = $year;
                    $insertData[$v]['datesubmitted'] = date('Y-m-d');
                    $insertData[$v]['submittedBy'] = Auth()->user()->id;
                    $insertData[$v]['lab_id'] = Auth()->user()->lab_id;
                    foreach ($sub as $key => $value) {
                        foreach ($this->taqmanKits as $keykit => $valuekit) {
                            $formValue = $platform.$v.$value.$valuekit['alias'];
                            $insertData[$v][$value.$valuekit['alias']] = $request->$formValue;
                        }
                    }
                    $comments = $platform.$v.'receivedcomment';
                    $issuedcomments = $platform.$v.'issuedcomment';
                    $insertData[$v]['comments'] = $request->$comments;
                    $insertData[$v]['issuedcomments'] = $request->$issuedcomments;
                }
                foreach ($insertData as $key => $value) {
                    $save = Taqmanprocurement::create($value);
                }
                $insertData = [];
            } else if ($request->platform == 2 || $request->platform == '2') {
                $platform = 'abbott';
                foreach ($data['testtypes'] as $k => $v) {
                    $testtype = 2;
                    if ($v == 'EID') 
                        $testtype = 1;
                    $tests = $platform.$v.'tests';
                    $insertData[$v]['testtype'] = $testtype;
                    $insertData[$v]['tests'] = $request->$tests;
                    $insertData[$v]['month'] = $previousMonth;
                    $insertData[$v]['year'] = $year;
                    $insertData[$v]['datesubmitted'] = date('Y-m-d');
                    $insertData[$v]['submittedBy'] = Auth()->user()->id;
                    $insertData[$v]['lab_id'] = Auth()->user()->lab_id;
                    foreach ($sub as $key => $value) {
                        foreach ($this->abbottKits as $keykit => $valuekit) {
                            $formValue = $platform.$v.$value.$valuekit['alias'];
                            $insertData[$v][$value.$valuekit['alias']] = $request->$formValue;
                        }
                    }
                    $comments = $platform.$v.'receivedcomment';
                    $issuedcomments = $platform.$v.'issuedcomment';
                    $insertData[$v]['comments'] = $request->$comments;
                    $insertData[$v]['issuedcomments'] = $request->$issuedcomments;
                }
                
                foreach ($insertData as $key => $value) {
                    $save = Abbotprocurement::create($value);
                }
                $insertData = [];
            }
        }

        $taqproc = Taqmanprocurement::selectRaw("count(*) as entries")->where('month', $previousMonth)->where('year', $year)->first()->entries;
        $abbottproc = Abbotprocurement::selectRaw("count(*) as entries")->where('month', $previousMonth)->where('year', $year)->first()->entries;

        if ($taqproc > 0 && $abbottproc > 0) {
            Consumption::truncate();
            ConsumptionDetail::truncate();
            ConsumptionDetailBreakdown::truncate();
            Artisan::call('transfer:consumptions');
            return redirect()->route('allocation');
            // return redirect()->route('pending');
        }
        
        $data['taqmanKits'] = $this->taqmanKits;
        $data['abbottKits'] = $this->abbottKits;
        $data['EIDteststaq'] = SampleView::selectRaw("COUNT(*) as totaltests")->join('worksheets', 'worksheets.id', '=', 'samples_view.worksheet_id')->whereYear("datetested", $year)->whereMonth("datetested", $previousMonth)->where('samples_view.lab_id', env('APP_LAB'))->where('receivedstatus', '=', 1)->whereIn('worksheets.machine_type',[1,3])->first()->totaltests;
        $data['EIDtestsabbott'] = SampleView::selectRaw("COUNT(*) as totaltests")->join('worksheets', 'worksheets.id', '=', 'samples_view.worksheet_id')->whereYear("datetested", $year)->whereMonth("datetested", $previousMonth)->where('samples_view.lab_id', env('APP_LAB'))->where('receivedstatus', '=', 1)->where('worksheets.machine_type',2)->first()->totaltests;

        $data['VLteststaq'] = ViralsampleView::selectRaw("count(*) as tests")->join('viralworksheets', 'viralworksheets.id', '=', 'viralsamples_view.worksheet_id')->whereYear("datetested", $year)->whereMonth("datetested", $previousMonth)->where('viralsamples_view.lab_id', env('APP_LAB'))->where('receivedstatus', '=', 1)->whereIn('viralworksheets.machine_type',[1,3])->first()->tests;
        $data['VLtestsabbott'] = ViralsampleView::selectRaw("count(*) as tests")->join('viralworksheets', 'viralworksheets.id', '=', 'viralsamples_view.worksheet_id')->whereYear("datetested", $year)->whereMonth("datetested", $previousMonth)->where('viralsamples_view.lab_id', env('APP_LAB'))->where('receivedstatus', '=', 1)->where('viralworksheets.machine_type',2)->first()->tests;
        
        foreach ($data['testtypes'] as $key => $value) {
            $data['prevabbott'.$value] = NULL;
            $data['prevtaqman'.$value] = NULL;
            $data['abbottdeliveries'.$value] = NULL;
            $data['taqmandeliveries'.$value] = NULL;
            $type = $key+1;

            foreach(Abbotprocurement::where('month', $endingBalanceMonth)->where('year', $endingBalanceYear)->where('testtype', $type)->get() as $key1 => $value1) {
                $data['prevabbott'.$value] = $value1;
            }

            foreach(Taqmanprocurement::where('month', $endingBalanceMonth)->where('year', $endingBalanceYear)->where('testtype', $type)->get() as $key1 => $value1) {
                $data['prevtaqman'.$value] = $value1;
            }
            foreach (Taqmandeliveries::whereRaw("YEAR(datereceived) = $year")->whereRaw("MONTH(datereceived) = $previousMonth")->where('testtype', $type)->get() as $key1 => $value1) {
                $data['taqmandeliveries'.$value] = $value1;
            }
            foreach (Abbotdeliveries::whereRaw("YEAR(datereceived) = $year")->whereRaw("MONTH(datereceived) = $previousMonth")->where('testtype', $type)->get() as $key1 => $value1) {
                $data['abbottdeliveries'.$value] = $value1;
            }
        }
        $data['taqproc'] = $taqproc;
        $data['abbottproc'] = $abbottproc;

        $data = (object) $data;
        dd($data);
        return view('tasks.consumption', compact('data'))->with('pageTitle', 'Lab Consumption::'.date("F", mktime(null, null, null, $previousMonth)).', '.$this->previousYear);
    }

    public function allocation(Request $request) {
        if ($request->method() == "GET") {
            $currentMonthAllocation = Allocation::where('year', '=', $this->year)->where('month', '=', $this->month)->where('lab_id', '=', env('APP_LAB'))->count();
            if ($currentMonthAllocation > 0){ // Ensure that allocation is not repeated if entered
                session(['toast_message' => 'Allocation for ' . date("F", mktime(null, null, null, $this->month)) . ', ' . $this->year . ' already completed']);
                return back();
            } else { // Ensure that that allocation once rejected is not done
                // $tasks = (object) $this->pendingTasks();
                // if ($tasks->submittedstatus == 1 && $tasks->labtracker == 1 && !$tasks->filledtoday){
                //     session(['toast_message' => 'Allocation for ' . date("F", mktime(null, null, null, $this->month)) . ', ' . $this->year . ' was skipped please wait till next month']);
                //     return back();
                // }
            }
            // If no allocation is not done and not declined, show the form to select the machines for allocations
            $machines = DB::table('machines')->where('id', '<>', 4)->get();
            return view('tasks.allocation', compact('machines'))->with('pageTitle', 'Lab Allocation::'.date("F", mktime(null, null, null, $this->month)).', '.$this->year);
        } else if ($request->method() == "POST") {
            if ($request->has(['machine-form'])){ // This is to fill the allocation form for the previously slected machines
                $machines = Machine::/*with(['kits', 'kits.consumption'])->*/whereIn('id',$request->input('machine'))->get();
                // dd($machines);
                $generalconsumables = GeneralConsumables::get();
                $data['machines'] = $machines;
                $data['testtypes'] = $this->testtypes;
                $data['generalconsumables'] = $generalconsumables;
                $data = (object) $data;
                // dd($data);
                return view('forms.allocation', compact('data'))->with('pageTitle', 'Lab Allocation::'.date("F", mktime(null, null, null, $this->month)).', '.$this->year);
            } else { // Save the allocations from the previous if section
                $saveAllocation = $this->saveAllocation($request);
                $save_null_allocation = $this->saveNullAllocation();
                $synch = Synch::synch_allocations();
                return redirect()->route('pending');
            }
        }
    }

    public function nullallocation()
    {
        $this->saveNullAllocation();
        $synch = Synch::synch_allocations();
        return redirect()->route('pending');
    }

    protected function saveAllocation($request) {
        $orderNumber = date('Y') . "-" . substr(date('F', mktime(0, 0, 0, date('m'), 10)), 0, 3);
        $form = $request->except(['_token', 'kits-form']);
        $allocation = Allocation::where('year', '=', $this->year)->where('month', '=', $this->month)->first();
        if (!$allocation){
            $allocation = Allocation::create([
                        'year' => $this->year,
                        'month' => $this->month,
                        'order_num' => $orderNumber,
                        'datesubmitted' => date('Y-m-d'),
                        'submittedby' => auth()->user()->full_name,
                        'lab_id' => env('APP_LAB'),
                    ]);
            $allocation_details = $this->saveAllocationDetails($allocation, $form);
        }
        return $allocation;
    }

    protected function saveAllocationDetails($allocation, $form_data) {
        foreach ($form_data as $key => $datum) {
            $column = explode('-', $key);
            $build = false;
            $machine_id = NULL;
            $testtype = NULL;
            if ($column[0] == 'allocation') { // Create a new allocation at this point
                $machine_id = $column[1];
                $testtype = $column[2];
                $allocationcomments = 'allocationcomments-'.$machine_id.'-'.$testtype;
                $build = true;
            } else if ($key == 'consumablecomments'){
                $allocationcomments = 'consumablecomments';
                $build = true;
            }
            if ($build) {
                $allocation_details = AllocationDetail::create([
                    'allocation_id' => $allocation->id,
                    'machine_id' => $machine_id,
                    'testtype' => $testtype,
                    'allocationcomments' => $form_data[$allocationcomments]]);
                $allocationDetailsBreakdown = $this->getAllocationDetailsBreakdownData($allocation_details, $machine_id, $testtype, $form_data);
            }
        }
        return $allocation_details;
    }

    // Format the Allocation Details data to fit laravel way to insert
    protected function getAllocationDetailsBreakdownData($allocation_details, $machine, $testtype, $form_data) {
        if (!$machine)
            $this->getConsumableAllocationData($allocation_details, $form_data);
        else {
            $kits = Kits::where('machine_id', '=', $machine)->get();
            $allocation_details_array = [];
            foreach ($kits as $key => $kit) {
                foreach ($form_data as $formkey => $form) {
                    $column = 'allocate-'.$testtype.'-'.$kit->id;
                    if ($column == $formkey){
                        $allocation_detail = AllocationDetailsBreakdown::create([
                            'allocation_detail_id' => $allocation_details->id,
                            'breakdown_id' => $kit->id,
                            'breakdown_type' => Kits::class,
                            'allocated' => $form
                        ]);
                    }
                }
            }
        }
        
        return true;
    }

    // Format the consumable allocation data to fit laravel way to insert
    protected function getConsumableAllocationData($allocation_detail, $form_data) {
        $consumables = GeneralConsumables::get();
        $consumables_array = [];
        foreach($consumables as $key => $consumable) {
            $column = 'consumable-'.$consumable->id;
            $allocation_details = AllocationDetailsBreakdown::create([
                'allocation_detail_id' => $allocation_detail->id,
                'breakdown_id' => $consumable->id,
                'breakdown_type' => GeneralConsumables::class,
                'allocated' => $form_data[$column]
            ]);
        }
        return true;
    }

    protected function saveNullAllocation()
    {
        foreach (Machine::get() as $key => $machine) {
            $machine->saveNullAllocation();
        }
        return true;
    }

    public function performancelog(Request $request)
    {
        if ($request->submit) {
            $lab = Auth()->user()->lab_id;
            $month = $this->previousMonth;
            $year = $this->previousYear;
            $today = date('Y-m-d');
            $user = Auth()->user()->id;
            
            $eidData = [
                    'lab_id' => $lab,
                    'month' => $month,
                    'year' => $year,
                    'dateemailsent' => NULL,
                    'datesubmitted' => $today,
                    'submittedBy' => $user,
                    'testtype' => 1,
                    'sampletype' => 1,
                    'received' => $request->EIDreceived,
                    'rejected' => $request->EIDrejected,
                    'loggedin' => $request->EIDlogged,
                    'notlogged' => $request->EIDnotlogged,
                    'tested' => $request->EIDtested,
                    'reasonforbacklog' => $request->EIDreason
                ];
            $save = LabPerformanceTracker::create($eidData);

            $plasmaData = [
                    'lab_id' => $lab,
                    'month' => $month,
                    'year' => $year,
                    'dateemailsent' => NULL,
                    'datesubmitted' => $today,
                    'submittedBy' => $user,
                    'testtype' => 2,
                    'sampletype' => 1,
                    'received' => $request->Plasmareceived,
                    'rejected' => $request->Plasmarejected,
                    'loggedin' => $request->Plasmalogged,
                    'notlogged' => $request->Plasmanotlogged,
                    'tested' => $request->Plasmatested,
                    'reasonforbacklog' => $request->Plasmareason
                ];
            $save = LabPerformanceTracker::create($plasmaData);

            $dbsData = [
                    'lab_id' => $lab,
                    'month' => $month,
                    'year' => $year,
                    'dateemailsent' => NULL,
                    'datesubmitted' => $today,
                    'submittedBy' => $user,
                    'testtype' => 2,
                    'sampletype' => 2,
                    'received' => $request->DBSreceived,
                    'rejected' => $request->DBSrejected,
                    'loggedin' => $request->DBSlogged,
                    'notlogged' => $request->DBSnotlogged,
                    'tested' => $request->DBStested,
                    'reasonforbacklog' => $request->DBSreason
                ];
            $save = LabPerformanceTracker::create($dbsData);
            session(['toast_message'=>'Lab Activity Log Successfully Submitted.']);
            $performance = LabPerformanceTracker::where('month', $month)->where('year', $year)->where('lab_id', $lab)->count();

            if ($performance > 0) {
                return redirect()->route('pending');
            }
        }

        $data['sampletypes'] = (object)['EID', 'Plasma', 'DBS'];
        $data['logs'] = (object)$this->getLabperformanceLog($data['sampletypes']);
        $data = (object) $data;
        // dd($data);
        $month = $this->previousMonth;
        return view('tasks.performancelog', compact('data'))->with('pageTitle', 'Lab Performance Log::'.date("F", mktime(null, null, null, $month)).', '.$this->previousYear);
    }

    public function equipmentlog(Request $request) {
        $month = $this->previousMonth;
        if ($request->submit) {
            $tracker = [];
            foreach ($request->equipmentid as $key => $value) {
                $tracker[] = [
                        'month' => $month,
                        'year' => $this->previousYear,
                        'lab_id' => auth()->user()->lab_id,
                        'equipment_id' => $value,
                        'datesubmitted' => date('Y-m-d'),
                        'submittedBy' => auth()->user()->id,
                        'datebrokendown' => ($request->datebrokendown[$key] == "") ? null : $request->datebrokendown[$key],
                        'datereported' => ($request->datereported[$key] == "") ? null : $request->datereported[$key],
                        'datefixed' => ($request->datefixed[$key] == "") ? null : $request->datefixed[$key],
                        'downtime' => ($request->downtime[$key] == "") ? null : $request->downtime[$key],
                        'samplesnorun' => ($request->samplesnorun[$key] == "") ? null : $request->samplesnorun[$key],
                        'failedruns' => ($request->failedruns[$key] == "") ? null : $request->failedruns[$key],
                        'reagentswasted' => ($request->reagentswasted[$key] == "") ? null : $request->reagentswasted[$key],
                        'breakdownreason' => ($request->breakdownreason[$key] == "") ? null : $request->breakdownreason[$key],
                        'othercomments' => ($request->otherreasons == "") ? null : $request->otherreasons
                    ];
            }
            
            foreach ($tracker as $key => $value) {
                $save = LabEquipmentTracker::create($value);
            }
            session(['toast_message'=>'Lab Equipment Log Successfully Submitted.']);
            $equipment = LabEquipmentTracker::where('month', $month)->where('year', $this->previousYear)->where('lab_id', auth()->user()->lab_id)->count();

            if ($equipment > 0)
                return redirect()->route('pending');
            
        }
        $data = DB::table('lab_equipment_mapping')->where('lab', '=', auth()->user()->lab_id)->get();
        
        return view('tasks.equipmentlog', compact('data'))->with('pageTitle', 'Lab Equipment Log::'.date("F", mktime(null, null, null, $month)).', '.$this->previousYear);
    }

    private function getDeliveries()
    {
        $model = new Deliveries;        
        return $model->getMissingDeliveries();
    }

    private function getConsumptions()
    {
        $model = new Consumption;
        return $model->getMissingConsumptions();
    }

 //    public function getKitsEntered(){
 //        // $delivery = new Deliveries;
 //        // $missing_deliveries = $delivery->getLastMissingDelivery();
 //        // return $missing_deliveries;
 //    	$quarter = parent::_getMonthQuarter($this->month);
 //    	return [
 //    		'taqkits' => self::__getifKitsEntered(1,$quarter,$this->year),
	// 		'abkits' => self::__getifKitsEntered(2,$quarter,$this->year)
	// 	];
	// }

    public function getConsumption()
    {
        return [
            'taqconsumption' => self::__getifConsumptionEntered(1,$this->previousMonth,$this->previousYear),
            'abconsumption' => self::__getifConsumptionEntered(2,$this->previousMonth,$this->previousYear)
        ];
    }

    public function getRequisitions()
    {
    	$currentmonth = $this->month;
    	$currentyear = $this->year;

    	$model = Requisition::whereRaw("MONTH(dateapproved) <> $currentmonth")->where('status', 1)->where('flag', 1)
    						->whereRaw("YEAR(dateapproved) = $currentyear")->whereNull('datesubmitted')->get();
    	return $model;
    }

    // public static function __getifKitsEntered($platform,$quarter,$currentyear){
    //     if ($platform==1)
    //         $model = Taqmandeliveries::where('flag', 1)->where('source', '<>', 2)->where('quarter', $quarter)->whereRaw("YEAR(dateentered) = $currentyear");

    //     if ($platform==2)
    //         $model = Abbotdeliveries::where('flag', 1)->where('source', '<>', 2)->where('quarter', $quarter)->whereRaw("YEAR(dateentered) = $currentyear");

    //     return $model->count();
    // }

    public static function __getifConsumptionEntered($platform,$month,$currentyear){
        if ($platform==1)
            $model = Taqmanprocurement::where('month', $month)->where('year', '=', $currentyear);

        if ($platform==2)
            $model = Abbotprocurement::where('month', $month)->where('year', '=', $currentyear);

        // return $model->toSql();
        return $model->count();
    }

    public function getLabperformanceLog($data) {
        $return = [];
        $data = (array)$data;
        foreach ($data as $key => $value) {
            if ($value == 'EID') {
                $return[$value] = (object)self::__getLogs('EID',null,$this->previousYear, $this->previousMonth);
            } else {
                if ($value == 'Plasma') {
                    $array = [1,2];
                } else if ($value == 'DBS'){
                    $array = [3,4];
                }
                $return[$value] = (object)self::__getLogs(null, $array,$this->previousYear, $this->previousMonth);
            }
        }
        return $return;
    }

    public static function __getLogs($type=null, $sampletypes=null, $year, $month)
    {
        $types = ['received', 'rejected', 'tested', 'logged'];
        $result = [];

        foreach ($types as $key => $value) {
            if ($type == 'EID') {
                $model = SampleView::where('flag', 1);
            } else {
                $model = ViralsampleView::where('flag', 1)->whereBetween('sampletype', $sampletypes);
            }
                        
            if ($value == 'received') {
                $column = 'datereceived';
                $model = $model->where('receivedstatus', '<>', 2);
            } else if ($value == 'rejected') {
                $column = 'datereceived';
                $model = $model->where('receivedstatus', '=', 2);
            } else if ($value == 'tested') {
                $column = 'datetested';
                $model = $model->where('receivedstatus', '<>', 2)->whereNotNull('datetested');
            } else if ($value == 'logged') {
                $column = 'created_at';
                // $model = $model->whereRaw("((parentid=0)||(parentid IS NULL))");
            }
            $result[$value] = $model->whereRaw("YEAR($column) = $year")->whereRaw("MONTH($column) = $month")->where('lab_id', env('APP_LAB'))->count();
        }
        return $result;
    }
}
