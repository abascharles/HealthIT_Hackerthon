<?php

namespace App\Api\V1\Controllers;

use App\Allocation;
use App\AllocationDetail;
use App\AllocationDetailsBreakdown;
use App\CovidAllocation;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\ApiRequest;
use App\Api\V1\Requests\BlankRequest;

class AllocationController extends Controller
{
	use \Dingo\Api\Routing\Helpers;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ApiRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Allocation  $allocation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $sample = Sample::findOrFail($id);
        // $sample->load(['patient']);
        // $sample->batch;

        // return $sample;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Allocation  $allocation
     * @return \Illuminate\Http\Response
     */
    public function update(ApiRequest $request)
    {
        $allocationReactionCounts = null;
        $fields = json_decode($request->input('allocation'));
        $allocation_details = $fields->details;
        $allocation = Allocation::findOrFail($fields->original_allocation_id);
        $allocation->national_id = $fields->id;
        $unset_array = ['id', 'original_allocation_id', 'created_at', 'updated_at', 'details', 'orderdate'];
        foreach ($unset_array as $value) {
            unset($fields->$value);
        }
        $allocationReactionCounts = ['month' => $allocation->month, 'year' => $allocation->year];
        $allocation_details = $this->updateAllocationDetails($allocation_details, $allocationReactionCounts);
        $return = \App\Synch::sendAllocationReview($allocationReactionCounts);
        $allocation->fill(get_object_vars($fields));
        $allocation->synched = 1;
        $allocation->datesynched = date('Y-m-d');
        $allocation->save();
        
        return response()->json([
                'message' => 'The update was successful.',
                'status_code' => 200,
            ], 200);
    }

    protected function updateAllocationDetails($allocation_details, &$allocationReactionCounts) {
        $allocationReactionCounts = (object)['approved' => 0, 'rejected' => 0, 'month' => $allocationReactionCounts['month'], 'year' => $allocationReactionCounts['year']];
        foreach($allocation_details as $details) {
            $allocation_details_breakdown = $details->breakdowns;
            $new_alloc_details = AllocationDetail::findOrFail($details->original_allocation_detail_id);
            $new_alloc_details->national_id = $details->id;
            $unset_array = ['id', 'original_allocation_detail_id', 'allocation_id', 'created_at', 'updated_at', 'breakdowns'];
            foreach ($unset_array as $value) {
                unset($details->$value);
            }
            $new_alloc_details->fill(get_object_vars($details));
            $new_alloc_details->synched = 1;
            $new_alloc_details->datesynched = date('Y-m-d');
            $new_alloc_details->save();
            if ($new_alloc_details->approve == 1)
                $allocationReactionCounts->approved += 1;
            else if ($new_alloc_details->approve == 2)
                $allocationReactionCounts->rejected += 1;
            $allocation_detail_breakdown = $this->updateAllocationDetailBreakdown($allocation_details_breakdown);
        }
        return $new_alloc_details;
    }

    protected function updateAllocationDetailBreakdown($breakdowns) {
        foreach ($breakdowns as $breakdown) {
            $allocation_detail_breakdown = AllocationDetailsBreakdown::findOrFail($breakdown->original_allocation_details_breakdown_id);
            $allocation_detail_breakdown->national_id = $breakdown->id;
            $unset_array = ['id', 'original_allocation_details_breakdown_id', 'allocation_detail_id', 'created_at', 'updated_at'];
            foreach ($unset_array as $value) {
                unset($breakdown->$value);
            }            
            $allocation_detail_breakdown->fill(get_object_vars($breakdown));
            $allocation_detail_breakdown->synched = 1;
            $allocation_detail_breakdown->datesynched = date('Y-m-d');
            $allocation_detail_breakdown->save();
        }
        return $allocation_detail_breakdown;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Allocation  $allocation
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sample $sample)
    {
        //
    }

    public function allocation_details($allocation_date)
    {
        $allocations = CovidAllocation::with('platform')->where('allocation_date', $allocation_date)->get();
        return view('tasks.covid.allocationdetails', ['allocations' => $allocations])->render();
    }
}
?>