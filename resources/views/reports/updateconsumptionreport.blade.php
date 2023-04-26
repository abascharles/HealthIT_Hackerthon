@extends('layouts.master')

@section('css_scripts')
    
@endsection

@section('custom_css')
	<style type="text/css">
		.input-edit {
            background-color: #FFFFCC;
        }
        .input-edit-danger {
            background-color: #f2dede;
        }
	</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="hpanel" style="margin-top: 1em;margin-right: 6%;">
            <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                <form action="/saveconsumption" method="POST" class="form-horizontal" >
                    @csrf
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><center>Consumed in the month</center></label>
                        <div class="col-sm-8">
                            <label class="col-sm-4 control-label badge badge-info">
                                <center>{{ date("F", mktime(0, 0, 0, $period->month, 1, Date('Y'))) }}, {{ $period->year }}</center>
                            </label>
                        </div>
                    </div>
                    <div class="alert alert-danger">
                        <center><i class="fa fa-bolt"></i> Please enter {{ $machine->machine }} {{ $type->name }} values below. <strong>(Tests:{{ number_format($machine->tests_done($type->name, $period->year, $period->month)) }})</strong></center>
                        <input type="hidden" name="consumption" value="{{ $consumption->id }}">
                        <input type="hidden" name="machine[]" value="{{ $machine->id }}">
                        <input type="hidden" name="tests[{{$machine->machine}}][{{$type->name}}]" value="{{ $machine->tests_done($type->name, $period->year, $period->month) }}">
                        <input type="hidden" name="year" value="{{ $period->year }}">
                        <input type="hidden" name="month" value="{{ $period->month }}">
                        <input type="hidden" name="type" value="{{ $type->name }}">
                    </div>
                    <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                        <thead>               
                            <tr>
                                <th rowspan="2">NAME OF COMMODITY</th>
                                <th rowspan="2">UNIT OF ISSUE</th>
                                <th rowspan="2">BEGINNING BALANCE</th>
                                <th colspan="2">QUANTITY RECEIVED FROM CENTRAL WAREHOUSE(KEMSA/SCMS/RDC)</th>
                                <th rowspan="2">QUANTITY USED</th>
                                <th rowspan="2">LOSSES / WASTAGE</th>
                                <th colspan="2">ADJUSTMENTS</th>
                                <th rowspan="2">ENDING BALANCE (PHYSICAL COUNT)</th>
                            </tr>
                            <tr>
                                <th>Quantity</th>
                                <th>Lot No.</th>
                                <th>Positive<br />(Received other source)</th>
                                <th>Negative<br />(Issued Out)</th>
                            </tr>
                        </thead>
                        <tbody>
                        	@foreach ($machine->kits as $kit)
                                    
                                    @php
                                        $delivery = $kit->getDeliveries($type->id, $period->year, $period->month);
                                        /* dd($delivery);
                                        dd($consumption->details->where('kit_id', $kit->id)); */
                                    @endphp
                                    <tr>
                                        <td>{{ $kit->name }}</td>
                                        <td>{{ $kit->unit }}</td>
                                        <td>
                                            <input class="form-control input-edit" type="number" name="begining_balance[{{$machine->machine}}][{{$type->name}}][{{$kit->id}}]" value="{{ $consumption->details->where('kit_id', $kit->id)->first()->begining_balance ?? 0 }}" onchange="computevaluesforotherkits('{{ $type->id }}', '{{ $kit->alias }}', '{{ $kit->id }}', '{{ $machine->machine }}', this, 'begining_balance')">
                                        </td>
                                        <td>
                                            {{ round($delivery->quantity, 2) }}
                                            <input type="hidden" name="received[{{$machine->machine}}][{{$type->name}}][{{$kit->id}}]" value="{{ round($delivery->quantity, 2) }}">
                                        </td>
                                        <td>{{ $delivery->lotno }}</td>
                                        <td>
                                            {{ round($consumption->details->where('kit_id', $kit->id)->first()->used ?? 0, 2) }}
                                            <input type="hidden" name="used[{{$machine->machine}}][{{$type->name}}][{{$kit->id}}]" value="{{ $consumption->details->where('kit_id', $kit->id)->first()->used ?? 0 }}">
                                        </td>
                                        <td>
                                            <input class="form-control input-edit" type="number" name="wasted[{{$machine->machine}}][{{$type->name}}][{{$kit->id}}]" min="0" value="{{ $consumption->details->where('kit_id', $kit->id)->first()->wasted ?? 0 }}" required="true" onchange="computevaluesforotherkits('{{ $type->id }}', '{{ $kit->alias }}', '{{ $kit->id }}', '{{ $machine->machine }}', this, 'wasted')">
                                        </td>
                                        <td>
                                            <input class="form-control input-edit" type="number" name="positive_adjustment[{{$machine->machine}}][{{$type->name}}][{{$kit->id}}]" min="0" value="{{ $consumption->details->where('kit_id', $kit->id)->first()->positive_adjustment ?? 0 }}" required onchange="computevaluesforotherkits('{{ $type->id }}', '{{ $kit->alias }}', '{{ $kit->id }}', '{{ $machine->machine }}', this, 'positive_adjustment')">
                                        </td>
                                        <td>
                                            <input class="form-control input-edit" type="number" name="negative_adjustment[{{$machine->machine}}][{{$type->name}}][{{$kit->id}}]" value="{{ $consumption->details->where('kit_id', $kit->id)->first()->negative_adjustment ?? 0 }}"  min="0" required onchange="computevaluesforotherkits('{{ $type->id }}', '{{ $kit->alias }}', '{{ $kit->id }}', '{{ $machine->machine }}', this, 'negative_adjustment')">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control input-edit" name="ending_balance[{{$machine->machine}}][{{$type->name}}][{{$kit->id}}]"  min="0" value="{{ $consumption->details->where('kit_id', $kit->id)->first()->ending_balance ?? 0 }}">
                                        </td>
                                    </tr>
                                    @endforeach
                        </tbody>
                    </table>
                    <div class="col-sm-12">
                        <center>
                        <button class="btn btn-success" type="submit" name="saveTaqman" value="saveTaqman">Submit Kit Consumption</button>
                        <button class="btn btn-primary" type="submit" name="discard" value="add">Discard Changes</button>
                        </center>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @component('/forms/scripts')
        @slot('js_scripts')
            
        @endslot


        @slot('val_rules')
           
        @endslot

    @endcomponent
    <script type="text/javascript">
        $(function(){
            
        });

        const computevaluesforotherkits = (testtype, kitalias, kit, machine, element, type) => {  
            if (kitalias == 'qualkit') {
                $.get("{{ url('consumption') }}", {type:testtype, kit:kit, value:element.value, elementtype:type, year:'{{$period->year}}', month:'{{$period->month}}'}, function(data) {
                    data.forEach(function(val,index) {
                        $('input[name="' + val.element + '"').val(val.value);
                        let domElementValue = val.value;
                        $('input[name="' + val.element + '"').val(domElementValue.toFixed(2));
                        console.log(val);
                        computeEndingBalance(type, val);
                    });
                });
            }
        }

        const computeEndingBalance = (element, val) => {
            let beginingDOMElement = val.element.replace(element, "begining_balance");
            let begining_balance = $('input[name="' + beginingDOMElement + '"').val();
            let receivedDOMElement = val.element.replace(element, "received");
            let received = $('input[name="' + receivedDOMElement + '"').val();
            let usedDOMElement = val.element.replace(element, "used");
            let used = $('input[name="' + usedDOMElement + '"').val();
            let wastedDOMElement = val.element.replace(element, "wasted");
            let wasted = $('input[name="' + wastedDOMElement + '"').val();
            let positive_adjustmentDOMElement = val.element.replace(element, "positive_adjustment");
            let positive_adjustment = $('input[name="' + positive_adjustmentDOMElement + '"').val();
            let negative_adjustmentDOMElement = val.element.replace(element, "negative_adjustment");
            let negative_adjustment = $('input[name="' + negative_adjustmentDOMElement + '"').val();
            let endingpositives = (parseFloat(begining_balance)+parseFloat(received)+parseFloat(positive_adjustment));

            console.log('<<--------------------------------------------------------------->>>');
            console.log(begining_balance + ' - ' + received + ' - ' + positive_adjustment);
            console.log(wasted + ' - ' + used + ' - ' + negative_adjustment);
            console.log('<<--------------------------------------------------------------->>>');

            let endingnegatives = (parseFloat(wasted)+parseFloat(used)+parseFloat(negative_adjustment));
            let ending = (endingpositives-endingnegatives);
            
            let endingelement = val.element.replace(element, "ending_balance");
            $('input[name="' + endingelement + '"').val(ending.toFixed(2));
        }
    </script>
@endsection