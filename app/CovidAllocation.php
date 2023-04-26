<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CovidAllocation extends BaseModel
{
	public function platform()
	{
		return $this->belongsTo('App\Machine', 'machine_id', 'id');
	}

	public function details()
	{
		return $this->hasMany('App\CovidAllocationDetail', 'covid_allocation_detail_id', 'id');
	}

    public function scopeExisting($query, $data)
    {
        return $query->where([
        					'allocation_date' => $data['allocation_date'],
        					'allocation_type' => $data['allocation_type'],
        					'lab_id' => $data['lab_id']
        				]);
    }


    public static function fillAllocations()
    {
    	$allocations = HCMPCovidAllocations::where('lab_id', env('APP_LAB'))->get();
    	foreach ($allocations as $key => $allocation) {			
			$data_existing = ['allocation_date' => $allocation->allocation_date, 'allocation_type' => $allocation->allocation_type, 'lab_id' => $allocation->lab_id];
			$existing = CovidAllocation::existing( $data_existing )->get();
			if ($existing->isEmpty()) {
				$lab = Lab::find($allocation->lab_id);
				$kit = CovidKit::withTrashed()->where('material_no', $allocation->material_number)->first();
				$trans_allocation = CovidAllocation::create([
					'allocation_date' => $allocation->allocation_date,
					'allocation_type' => $allocation->allocation_type,
					'lab_id' => env('APP_LAB'),
					'machine_id' => $kit->machine ?? NULL,
					'comments' => $allocation->comments,
					'received' => $allocation->received,
					'responded' => $allocation->responded,
					'respond_count' => $allocation->respond_count,
					'date_responded' => $allocation->date_responded,
				]);
			} else {
				$trans_allocation = $existing->first();
			}
			// dd($trans_allocation);
			$detail = CovidAllocationDetail::fillAllocationDetail($allocation, $trans_allocation);
		}
		return true;
    }
}
