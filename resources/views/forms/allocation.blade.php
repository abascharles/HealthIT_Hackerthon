@extends('layouts.tasks')

@section('css_scripts')

@endsection

@section('custom_css')
    <style type="text/css">
        .hpanel .panel-body .spacing {
            margin-bottom: 1em;
        }
        .hpanel .panel-body .bottom {
            border-bottom: 1px solid #eaeaea;
        }
    </style>
@endsection

@section('content')
<div class="row">
        <div class="col-md-12">
            <div class="hpanel" style="margin-top: 1em;margin-right: 20%;margin-bottom: 3em;">
                <div class="alert alert-danger">
                    <center><i class="fa fa-bolt"></i> Please note that you CANNOT access the main system until the below pending tasks have been completed.</center>
                </div>

                @php
                    $currentmonth = date('m');
                    $year = date('Y');
                @endphp

                <form action="/allocation" method="POST" class="form-horizontal">                    
                @csrf
                {{-- Kits form --}}
                @foreach($data->machines as $machine)
                    @foreach($data->testtypes as $testtypeKey => $testtype)
                    <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                        <div class="alert alert-info">
                            <center>Allocation for {{ $machine->machine}}, {{ $testtypeKey }}</center>
                        </div>
                        <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                            <thead>               
                                <tr>
                                    <th>Name of Commodity</th>
                                    <th>Average Monthly Consumption(AMC)</th>
                                    <th>Months of Stock(MOS)</th>
                                    <th>Ending Balance</th>
                                    <th>Recommended Quantity to Allocate (by System)</th>
                                    <th>Quantity Allocated by Lab</th>
                                </tr>
                            </thead>
                            <tbody>
                                <input type="hidden" name="allocation-{{ $machine->id }}-{{ $testtype }}" value="{{ $machine->id }}" />
                      
                            @php
                                $testtypeKey = $testtypeKey;
                                $tests = $machine->testsforLast3Months()->$testtypeKey;
                                $qualamc = 0;
                            @endphp
                            @foreach($machine->kits as $kit)

                                @php
                                    $test_factor = json_decode($kit->testFactor);
                                    $factor = json_decode($kit->factor);
                                    if ($kit->alias == 'qualkit')
                                        $qualamc = (($tests / $test_factor->$testtypeKey) / 3);
                                    if ($machine->id == 2)
                                        $amc = round($qualamc * $factor->$testtypeKey);
                                    else
                                        $amc = round($qualamc * $factor);

                                @endphp
                                    
                                <tr>
                                    <td>{{ str_replace("REPLACE", "", $kit->name) }}</td>
                                    <td>{{ round($amc) }}</td>
                                    @forelse($kit->lastMonthConsumption($testtype) as $consumption)
                                        @php
                                            $mos = @($consumption->ending / $amc);
                                        @endphp
                                        <td>
                                        @if(is_nan($mos))
                                            {{ 0 }}
                                        @else
                                            {{ round($mos) }}
                                        @endif
                                        </td>
                                        <td>{{ $consumption->ending }}</td>
                                        @php
                                            $recommended = ($amc * 2) - $consumption->ending;
                                            if ($recommended < 0)
                                                $recommended = 0;
                                        @endphp
                                        <td>{{ round($recommended) }}</td>
                                    @empty
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endforelse
                                    <td><input class="form-control input-edit" type="number" step="any" min="0" name="allocate-{{ $testtype }}-{{ $kit->id }}" id="{{ $testtype }}-{{ $kit->id }}" required></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                        <div class="form-group">
                            <label class="col-md-4 control-label">{{ $testtypeKey }}, {{ $machine->machine}} Allocation Comments</label>
                            <div class="col-md-8">
                                <textarea name="allocationcomments-{{ $machine->id }}-{{ $testtype }}" class="form-control" required="true"></textarea>
                            </div>                            
                        </div>
                    </div>
                    @endforeach
                @endforeach
                {{-- Kits form --}}
                {{-- Consumables form --}}
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    <div class="alert alert-warning">
                        <center>Consumable Allocation (This is for all the equipments)</center>
                    </div>
                    <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                        <thead>               
                            <tr>
                                <th>Name of Consumable</th>
                                <th>Unit of Issue</th>
                                <th>Last Month Allocation</th>
                                <th>Quantity Allocated by Lab</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data->generalconsumables as $consumable)
                            <tr>
                                <td>{{ $consumable->name ?? '' }}</td>
                                <td>{{ $consumable->unit ?? '' }}</td>
                                <td></td>
                                <td><input class="form-control input-edit" type="number" min="0" name="consumable-{{ $consumable->id }}" value="0" required></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Consumables Allocation Comments</label>
                            <div class="col-md-8">
                                <textarea name="consumablecomments" class="form-control" required="true"></textarea>
                            </div>                            
                        </div>
                    </div>
                {{-- Consumables form --}}
                <center>
                    <button type="submit" name="kits-form" class="btn btn-primary btn-lg" value="true" style="margin-top: 2em;margin-bottom: 2em; width: 200px; height: 30px;">Allocate</button>
                </center>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function(){
            @foreach($data->machines as $machine)
                @foreach($data->testtypes as $testtypeKey => $testtype)
                    qualkitval = 0;
                    @foreach($machine->kits as $kit)
                        @if($kit->alias == 'qualkit')
                            $("#{{ $testtype }}-{{ $kit->id }}").change(function(){
                                qualkitval = $(this).val();
                                computevalues("{{ $testtype }}", "{{ $machine->id }}", qualkitval);
                            });
                        @endif
                    @endforeach
                @endforeach
            @endforeach
        });
        function computevalues(testtype, machine, qualvalue) {
            @foreach($data->machines as $machine)
                if ("{{ $machine->id }}" == machine) {
                    @foreach($data->testtypes as $testtypeKey => $testtype)
                        if("{{ $testtype }}" == testtype) {
                            @foreach($machine->kits as $kit)
                                @if($kit->alias != 'qualkit')
                                    @php
                                        $factor = json_decode($kit->factor);
                                        if ($machine->id == 2)
                                            $factor = $factor->$testtypeKey;
                                    @endphp
                                    $("#" + testtype + "-{{ $kit->id }}").val(qualvalue * {{ $factor }});
                                @endif
                            @endforeach
                        }
                    @endforeach
                }
            @endforeach
        }
    </script>
@endsection