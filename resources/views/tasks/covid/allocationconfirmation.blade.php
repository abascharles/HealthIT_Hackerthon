@extends('layouts.tasks')

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
        <div class="alert alert-warning">
            <center>
                <strong> A Consumption report was already submitted for the week of this allocation. Check below the changes that will be effected. </strong>
            </center>
        </div>
        @foreach($covidkits as $machinekey => $kits)
            @php
                $machine = \App\Machine::find($machinekey);
                if ($machine) {
                    $machinename = $machine->machine . ' Kits';
                } else {            
                    $machinename = $machinekey;
                    if ($machinekey == '')
                        $machinename = 'Consumables';
                }
            @endphp
            <div class="hpanel" style="margin-top: 1em;margin-right: 2%;">
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    <div class="alert alert-info">
                        <center><i class="fa fa-bolt"></i> COVID-19 <strong>{{ ucfirst($machinename) }}</strong> consumption report for the week starting {{ $consumption->start_of_week }} and ending {{ $consumption->end_of_week }}.
                        @if($machine)
                            <strong>(Week`s Tests:{{ number_format($machine->getCovidTestsDone($consumption->start_of_week, $consumption->end_of_week)) }})</strong>
                        @endif
                        </center>
                    </div>

                    <table class="table table-striped table-bordered table-hover data-table-modified" style="font-size: 10px;margin-top: 1em;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Material No</th>
                                <th>Product Description</th>
                                <th>Begining Balance</th>
                                <th style="color: red;">Original Received</th>
                                <th style="color: red;">New Received</th>
                                <th>Used</th>
                                <th>Positive Adjustment</th>
                                <th>Negative Adjustment</th>
                                <th>Losses/Wastage</th>
                                <th style="color: red;">Original Ending Balance</th>
                                <th style="color: red;">New Ending Balance</th>
                                <th>Requested</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($kits as $kitkey => $kit)
                            <tr>
                                <td>{{ $kitkey+1 }}</td>
                                <td>{{ $kit->material_no ?? '' }}</td>
                                <td>{{ $kit->product_description ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->begining_balance ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->received ?? '' }}</td>
                                <td>{{ $detail_kits[$kit->id] ?? 0 }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->kits_used ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->positive ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->negative ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->wastage ?? '' }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->ending ?? '' }}</td>
                                @php
                                    $orig_received = $kit->specific_details($consumption->id)->received ?? 0;
                                    $orig_end = $kit->specific_details($consumption->id)->ending ?? 0;
                                    $new_received = $detail_kits[$kit->id] ?? 0;
                                    $new_end = (($orig_end - $orig_received) + $new_received);
                                    if ($new_end < 0)
                                        $new_end = 0;
                                @endphp
                                <td>{{ $new_end }}</td>
                                <td>{{ $kit->specific_details($consumption->id)->requested ?? '' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach

        <div class="hpanel" style="margin-top: 1em;margin-right: 2%;margin-bottom: 5em;">
            <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                <div class="row">
                    <form action="/covidkits/allocation" method="POST" class="form-horizontal" id="covid_allocation">
                        @csrf                        
                        @foreach($allocation['machine'] as $key => $value)
                        <input type="hidden" name="machine[{{ $key }}]" value="{{ $value }}">
                        @endforeach
                        @foreach($allocation['received'] as $key => $value)
                        <input type="hidden" name="received[{{ $key }}]" value="{{ $value }}">
                        @endforeach
                        @foreach($allocation['datereceived'] as $key => $value)
                        <input type="hidden" name="datereceived[{{ $key }}]" value="{{ $value }}">
                        @endforeach
                        <input type="hidden" name="response" value="{{ $allocation['response'] }}">
                        <input type="hidden" name="consumption_confirmation" value="true">
                        <div>
                            <center>
                            <button class="btn btn-warning btn-lg" type="submit" name="amend" value="true"> Amend to the new version(s) </button>
                            <button class="btn btn-primary btn-lg" type="submit" name="keep" value="true"> Keep the original version(s) </button>
                            </center>
                        </div>
                    </form>
                </div>
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
    </script>
@endsection