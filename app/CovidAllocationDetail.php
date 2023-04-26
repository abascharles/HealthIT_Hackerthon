<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CovidAllocationDetail extends BaseModel
{
    public function kit()
    {
        return $this->belongsTo(CovidKit::class, 'material_number', 'material_no');
    }
    
    public function scopeExisting($query, $data)
    {
        return $query->where([
        					'covid_allocation_detail_id' => $data['covid_allocation_detail_id'],
        					'material_number' => $data['material_number'],
        				]);
    }

    public static function fillAllocationDetail($allocation, $trans_allocation)
    {
    	$kit = CovidKit::withTrashed()->where('material_no', $allocation->material_number)->get();
		if (!$kit->isEmpty()) {
			$data_existing = ['covid_allocation_detail_id' => $trans_allocation->id, 'material_number' => $kit->first()->material_no];
			if (CovidAllocationDetail::existing($data_existing)->get()->isEmpty())
				$model = CovidAllocationDetail::create([
						'covid_allocation_detail_id' => $trans_allocation->id,
						'material_number' => $kit->first()->material_no,
						'allocated_kits' => $allocation->allocated_kits,
					]);
		}
		return true;
    }
}
