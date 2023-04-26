@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<style type="text/css">
    .spacing-div-form {
        margin-top: 15px;
    }
    .input-edit {
        background-color: #FFFFCC;
    }
    .input-edit-danger {
        background-color: #f2dede;
    }
</style>
@php
    $globaltesttype = $data->testtype;
    $replace = 'Quantitative';
    if($globaltesttype == 'EID')
        $replace = 'Quanlitative';
    $globaltesttypevalue = 1;
    if($globaltesttype == 'VL')
        $globaltesttypevalue = 2;

    $counter = 0;
@endphp
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="row" style="margin-bottom: 1em;">
                    <a href="{{ url('printallocation/' . $data->parent_allocation->id . '/' . $data->testtype) }}" class="btn btn-primary btn-lg pull-right" style="margin-right: 2em;">Print</a>
                </div>
                @foreach ($data->allocations as $allocation)
                @if($allocation->approve == 2)
                <form action="{{ url('/kitallocation/'. $allocation->id . '/edit') }}" class="form-horizontal" method="POST" >
                    @csrf
                    @method('PUT')
                <div class="panel-body">
                    @php
                        if ($allocation->approve == 0) $badge = 'warning';
                        else if ($allocation->approve == 1) $badge = 'success';
                        else if ($allocation->approve == 2) $badge = 'danger';
                    @endphp
                    <div class="alert alert-{{ $badge }}">
                        <center>Allocation for {{ $allocation->machine->machine ?? ''}} @if ($allocation->machine) , @endif {{ ucfirst(strtolower($globaltesttype)) }}</center>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Name of Commodity</th>
                                    @if ($globaltesttype != 'CONSUMABLES')
                                    <th>Average Monthly Consumption(AMC)</th>
                                    <th>Months of Stock(MOS)</th>
                                    <th>Ending Balance</th>
                                    <th>Recommended Quantity to Allocate (by System)</th>
                                    @endif
                                    <th>Quantity Allocated by Lab</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                if ($globaltesttype != 'CONSUMABLES') {
                                    $tests = $allocation->machine->testsforLast3Months()->$globaltesttype;
                                    $qualamc = 0;
                                }
                            @endphp
                            @foreach($allocation->breakdowns as $detail)
                                @php
                                    if ($globaltesttype != 'CONSUMABLES') {
                                        $test_factor = json_decode($detail->breakdown->testFactor);
                                        $factor = json_decode($detail->breakdown->factor);
                                        if ($detail->breakdown->alias == 'qualkit')
                                            $qualamc = (($tests / $test_factor->$globaltesttype) / 3);

                                        if ($allocation->machine->id == 2)
                                            $amc = $qualamc * $factor->$globaltesttype;
                                        else
                                            $amc = $qualamc * $factor;

                                        $ending = 0;
                                        $consumption = $detail->breakdown->consumption
                                                            ->where('month', $data->last_month)->where('year', $data->last_year)
                                                            ->where('testtype', $globaltesttypevalue)->pluck('ending');
                                        foreach($consumption as $value) {
                                            $ending += $value;
                                        }
                                        $mos = @($ending / $amc);
                                    }
                                @endphp
                                <tr>
                                    <td>{{ str_replace("REPLACE", $replace, $detail->breakdown->name) }}</td>
                                    @if ($globaltesttype != 'CONSUMABLES')
                                    <td>{{ round($amc, 1) }}</td>
                                    <td>
                                    @if(is_nan($mos))
                                        {{ 0 }}
                                    @else
                                        {{ round($mos) }}
                                    @endif
                                    </td>
                                    <td>{{ $ending }}</td>
                                    <td>{{ round(($amc * 2) - $ending, 1) }}</td>
                                    @endif
                                    <td>
                                        @if ($allocation->approve == 2)
                                    <input class="form-control input-edit" type="text" name="{{ $detail->id }}" id="{{ $allocation->id }}-{{ $detail->breakdown->alias ?? $detail->breakdown->consumption }}" value="{{ $detail->allocated }}" />
                                        @else
                                            {{ $detail->allocated }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    <div class="form-group" style="margin-bottom: 2em;">
                        <label class="col-md-4 control-label">Lab Comments</label>
                        <div class="col-md-8">
                            <textarea @if($allocation->approve != 2) disabled @endif class="form-control input-edit" name="allocationcomments">{{ $allocation->allocationcomments }}</textarea>
                        </div>                            
                    </div>
                    <br />
                    <div class="form-group" style="margin-top:2em;">
                        <label class="col-md-4 control-label">Allocation Committee Feedback</label>
                        <div class="col-md-8">
                            <textarea disabled class="form-control">{{ $allocation->issuedcomments }}</textarea>
                        </div>                            
                    </div>
                    
                    @if($allocation->approve == 2)
                    <center>
                        <button type="submit" name="allocation-form" class="btn btn-primary btn-lg" value="true" style="margin-top: 2em;margin-bottom: 2em; width: 200px; height: 30px;">Save {{ ucfirst(strtolower($globaltesttype)) }} Allocations</button>
                    </center>
                    </form>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent
    <script type="text/javascript">
        $(document).ready(function(){
            @if($globaltesttype != 'CONSUMABLES')
                @foreach ($data->allocations as $allocation)
                    @if($allocation->approve == 2)
                        @foreach($allocation->breakdowns as $detail)
                            @if($detail->breakdown->alias == 'qualkit')
                                $("#{{ $allocation->id }}-{{ $detail->breakdown->alias }}").change(function(){
                                    qualkitval = $(this).val();
                                    computevalues("{{ $allocation->id }}", qualkitval);
                                });
                            @endif
                        @endforeach
                    @endif
                @endforeach
            @endif
        });
        
        function computevalues(allocation, qualkitvalue) {
            @foreach ($data->allocations as $allocation)
                if("{{ $allocation->id }}" == allocation) {
                    @foreach($allocation->breakdowns as $detail)
                        @if($detail->breakdown->alias != 'qualkit')
                            @php
                                $factor = json_decode($detail->breakdown->factor);
                                if(isset($factor->$globaltesttype))
                                    $factor = $factor->$globaltesttype;
                            @endphp
                            $("#" + allocation + "-{{ $detail->breakdown->alias }}").val(qualkitvalue * {{ $factor }});
                        @endif
                    @endforeach
                }
            @endforeach
        }
    </script>
@endsection