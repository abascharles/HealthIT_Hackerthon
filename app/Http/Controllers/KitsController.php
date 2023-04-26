<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Taqmandeliveries;
use App\Allocation;
use App\AllocationDetail;
use App\AllocationDetailsBreakdown;
use App\Deliveries;
use App\Machine;
use App\TestType;

use Mpdf\Mpdf;

class KitsController extends Controller
{    
	/**
     * The test types available.
     *
     * @var array
     */
	public $testtypes = NULL;

	/**
     * The months for allocations.
     *
     * @var array
     */
	public $allocation_months = NULL;

    /**
     * The last month of consumption.
     *
     * @var array
     */
    public $last_month = NULL;


	/**
     * The years for allocations.
     *
     * @var array
     */
	public $allocation_years = NULL;

    /**
     * The last year of consumption.
     *
     * @var array
     */
    public $last_year = NULL;

	/**
     * The years for allocations.
     *
     * @var array
     */
	public $years = NULL;

	public function __construct() {
		$this->testtypes = ['EID' => 1, 'VL' => 2];
		$this->years = [date('Y'), date('Y')-1];
        $this->last_month = date('m')-1;
        $this->last_year = date('Y');
        if (date('m') == 1) {
            $this->last_year -= 1;
            $this->last_month = 12;
        }
    }
    
    public function kits(Request $request)
    {
        if($request->method() == 'POST') {
            $year = $request->input('year');
            $model = Deliveries::where('year', '=', $year);
            if ($request->input('period') == 'monthly') {
                $month = $request->input('month');
                $model->where('month', '=', $month);
            } else if ($request->input('period') == 'quarterly') {
                $quarter = parent::_getQuarterMonths($request->input('quarter'));
                $model->whereIn('month', $quarter);
            }
            $deliveries = $model->get();
            if ($deliveries->isEmpty()) {
                session(['toast_message'=>'No Kits Deliveries were submitted for the selected criteria']);
                return back();
            }

            return view('reports.deliverieskits', ['deliveries' => $deliveries])->with('pageTitle', '');
        }
        
        $allocationSQL = "`allocations`.`id`, `year`, `month`, `testtype`,
                        COUNT(IF(approve=0, 1, NULL)) AS `pending`,
                        COUNT(IF(approve=1, 1, NULL)) AS `approved`,
                        COUNT(IF(approve=2, 1, NULL)) AS `rejected`";
        $data = [
            'allocations' => AllocationDetail::selectRaw($allocationSQL)->groupBy(['year','month','testtype','id'])
                                ->orderBy('id','desc')->orderBy('year','desc')->orderBy('month','desc')
                                ->join('allocations', 'allocations.id', '=', 'allocation_details.allocation_id')->get(),
            'badge' => function($value, $type) {
                $badge = "success";
                if ($type == 1) {// Pending approval
                    if ($value > 0)
                        $badge = "warning";
                } else if ($type == 2) {// Approved
                    if ($value == 0)
                        $badge = "warning";
                } else if ($type == 3) { // Rejected
                    if ($value > 0)
                        $badge = "danger";
                }
                return $badge;
            },
            'testtypes' => TestType::get(),
            'platforms' => Machine::whereNotIn('id', [0])->get()
        ];
        
        return view('reports.kitsreport', compact('data'))->with('pageTitle', 'Kits Reports');
    }

    public function allocation(Allocation $allocation, $type, $approval = null) {
        $type = strtoupper($type);
        if (!($type == 'EID' || $type == 'VL' || $type == 'CONSUMABLES')) abort(404);
        $allocation_details = $allocation->details->when($type, function($details) use ($type){
                                        if ($type == 'EID')
                                            return $details->where('testtype', 1);
                                        if ($type == 'VL')
                                            return $details->where('testtype', 2);
                                        if ($type == 'CONSUMABLES')
                                                return $details->where('testtype', NULL);
                                    })->when($approval, function($details) use ($approval) {
                                        return $details->where('approve', 2);
                                    });
        
        $data = (object)[
            'parent_allocation' => $allocation,
            'allocations' => $allocation_details,
            'last_year' => $this->last_year,
            'last_month' => $this->last_month,
            'testtype' => $type,
            'approval' => $approval,
        ];
        return view('reports.kitreports-allocations-details', compact('data'))->with('pageTitle', $data->testtype . ' Kits Allocations');
    }

    public function editallocation(Request $request, $allocation_details) {
        $allocation_details = AllocationDetail::findOrFail($allocation_details);
        $data = $request->except(['_method', '_token', 'allocationcomments', 'allocation-form']);
        foreach($data as $key => $breakdown) {
            $breakdown_data = AllocationDetailsBreakDown::find($key);
            $breakdown_data->allocated = $breakdown;
            $breakdown_data->pre_update();
        }
        $allocation_details->approve = 0;
        $allocation_details->allocationcomments = $request->input('allocationcomments');
        $allocation_details->submissions = $allocation_details->submissions + 1;
        $allocation_details->pre_update();
        $allocation = $allocation_details->allocation;
        $allocation->synched = 2;
        $allocation->save();
        session(['toast_message' => 'Allocation(s) edited successfully.']);
        \App\Synch::synch_allocations_updates();
        return redirect('reports/kits');
    }

    public function printallocation(Allocation $allocation, $testtype)
    {
        $type = strtoupper($testtype);
        if (!($type == 'EID' || $type == 'VL' || $type == 'CONSUMABLES')) abort(404);
        $allocation_details = $allocation->details->when($type, function($details) use ($type){
                                        if ($type == 'EID')
                                            return $details->where('testtype', 1);
                                        if ($type == 'VL')
                                            return $details->where('testtype', 2);
                                        if ($type == 'CONSUMABLES')
                                                return $details->where('testtype', NULL);
                                    });
        
        $data = [
            'parent_allocation' => $allocation,
            'allocations' => $allocation_details,
            'last_year' => $this->last_year,
            'last_month' => $this->last_month,
            'testtype' => $type,
        ];
        $fileName = strtoupper($type . ' ALLOCATION PRINTOUT ' . $this->last_year . ' '. date('F', mktime(null, null, null, $this->last_month)));
        // return view('exports.mpdf_allocation', $data);
        $mpdf = new Mpdf();
        $view_data = view('exports.mpdf_allocation', $data)->render();
        $mpdf->WriteHTML($view_data);
        $mpdf->Output($fileName.'.pdf', \Mpdf\Output\Destination::DOWNLOAD);
        // return view('exports.mpdf_allocation', compact('data'));
    }

    // public function 
}
