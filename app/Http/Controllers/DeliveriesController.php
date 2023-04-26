<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Deliveries;
use App\DeliveryDetail;
use App\Kits;
use App\Machine;
use App\TestType;
use App\User;

class DeliveriesController extends Controller
{
    public function addKitDeliveries(Request $request, $platform = null)
    {
        if ($platform) {
            $testtype = TestType::find($request->input('type'));
            $kit = Kits::find($request->input('kit'));
            $machine = $kit->load('machine.kits')->machine;
            $kits = $machine->kits;
            $data = [];
            foreach ($kits as $key => $kitvalue) {
                $type = $testtype->name;
                $factor = $kitvalue->multiplier_factor->$type ?? $kitvalue->multiplier_factor;
                $data[] = [
                            'element' => $request->input('elementtype').'['.$machine->machine.']['.$type.']['.$kitvalue->id.']',
                            'value' => round($request->input('value')*$factor, 2)
                        ];
            }
            return response()->json($data);
        }

    	$model = new Deliveries;
    	$period = collect($model->getMissingDeliveries())->first();
    	if ($request->method() == 'POST') {
            $period = collect($model->getMissingDeliveries())->first();
            $from = $period->year . '-' . $period->month . '-1';
            $to = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d', strtotime("+1 Month", strtotime($from))))));
            $period->startMonthDiff = $from;
            $period->endMonthDiff = $to;
            
    		$data = [
    				'machines' => Machine::whereIn('id', $request->input('machine'))->get(),
                	'period' => $period,
                	'types' => TestType::get(),
                	'users' => User::where('user_type_id', '<', 5)->get()
    			];
    		
    		return view('tasks.deliveries.platformkits', $data)->with('pageTitle', 'Kit Deliveries');
    	}    	

    	$machines = new Machine;

    	$data = [
                'machines' => $machines->missingDeliveries($period->year, $period->month),
                'period' => $period
            ];
        
        return view('tasks.newkitsdeliveries', $data)->with('pageTitle', 'Kit Deliveries');
    }

    public function saveDeliveries(Request $request)
    {
    	$model = new Deliveries;
    	$period = collect($model->getMissingDeliveries())->first();
    	$fields = ['expiry', 'lotno', 'received', 'damaged'];
    	$machines = Machine::whereIn('id', $request->input('machine'))->get();
    	$types = TestType::get();
    	$formdata = $request->only($fields);
    	foreach ($machines as $machineskey => $machine) {
    		foreach ($types as $typeskey => $typesvalue) {
	    		$data[$machine->machine][$typeskey] = [
	    					'year' => $period->year,
	    					'month' => $period->month,
	    					'type' => $typesvalue->id,
	    					'machine' => $machine->id,
	    					'lab_id' => env('APP_LAB'),
	    					'receivedby' => $request->input('receivedby'),
	    					'datereceived' => $request->input('datereceived'),
	    					'enteredby' => auth()->user()->id,
	    					'dateentered' => date('Y-m-d')
	    				];
	    		foreach ($fields as $fieldskey => $fieldsvalue) {
	    			foreach ($machine->kits as $kitskey => $kitsvalue) {
	    				$data[$machine->machine][$typeskey]['details'][$kitsvalue->id]['kit_id'] = $kitsvalue->id;
	    				$data[$machine->machine][$typeskey]['details'][$kitsvalue->id]['kit_type'] = Kits::class;
	    				$data[$machine->machine][$typeskey]['details'][$kitsvalue->id][$fieldsvalue] = $formdata[$fieldsvalue][$machine->machine][$typesvalue->name][$kitsvalue->id];
	    			}
	    		}
	    	}
    	}

    	// Inserting the deliveries
    	foreach ($data as $machinekey => $machinevalue) {
    		foreach ($machinevalue as $deliverykey => $delivery) {
	    		if (Deliveries::duplicate($delivery['year'], $delivery['month'], $delivery['type'], $delivery['machine'], $delivery['lab_id'])->get()->isEmpty()) {
	    			$details = $delivery['details'];
		    		unset($delivery['details']);
		    		$savedelivery = Deliveries::create($delivery);
		    		$this->saveDeliveryDetails($savedelivery, $details);
	    		}
	    	}
    	}
    	
    	$model->submitNullDeliveries($period->year, $period->month);
    	return redirect()->route('pending');
    }

    private function saveDeliveryDetails($delivery, $details)
    {
    	foreach ($details as $key => $detail) {
    		$detail['delivery_id'] = $delivery->id;
    		DeliveryDetail::create($detail);
    	}
    	return true;
    }
}
