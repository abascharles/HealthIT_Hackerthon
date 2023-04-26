@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<style type="text/css">
    .spacing-div-form {
        margin-top: 15px;
    }
</style>
<div class="content">
    <div class="row">
        <div class="col-md-6 alert alert-warning">
            <center>
                {{ $consumption->month }}, {{ $consumption->year }} <br>
                @isset($consumption->platform->output)
                {!! $consumption->platform->output !!}
                @endisset
            </center>
        </div>
        <div class="col-md-6">
            <table class="table table-striped table-bordered">
                <tr>
                    <th>Test Type</th>
                    <th>Total No. of tests Done</th>
                </tr>
                <tr>
                    <td>{{ $consumption->testtype->name ?? '' }}</td>
                    <td>{{ $consumption->tests ?? '' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    {{-- @if(Auth::user()->eidvl_consumption_allowed) --}}
                    <form method="post" action="{{ url('reports/kitsconsumption/update') }}">
                        @csrf
                        @foreach($request as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div>
                            <button class="btn btn-primary" type="submit">Update Consumption Details</button>
                        </div>
                    </form>
                    {{-- @endif --}}
                	<table class="table table-striped table-bordered table-hover" style="font-size: 10px;margin-top: 1em;">
                		<thead>
                			<tr>
                				<th rowspan="2">#</th>
                                <th rowspan="2">DESCRIPTION OF GOODS</th>
                                <th rowspan="2">UNIT</th>
                				<th rowspan="2">BEGINING BALANCE</th>
                				<th colspan="2">QUANTITY RECEIVED FROM CENTRAL WAREHOUSE (KEMSA, SCMS/RDC)</th>
                				<th rowspan="2">QUANTITY USED</th>
                				<th rowspan="2">LOSSES/WASTAGES</th>
                				<th colspan="2">ADJUSTMENTS</th>
                				<th rowspan="2">ENDING BALANCE</th>
                				<th rowspan="2">QUANTITY REQUESTED</th>
                			</tr>
                			<tr>
                				<th>Quantity</th>
                				<th>Lot No.</th>
                				<th>Positive <br><font color="green">(Received other source)</font></th>
                				<th>Negative <br><font color="purple">(Issued out)</font></th>
                			</tr>
                		</thead>
                		<tbody>
                        @foreach($consumption->details as $key => $line)
                        @php
                            //dd($line);
                        @endphp
                            <tr>
                				<td>{{ $key+1 }}</td>
                                <td>{{ $line->kit->name ?? '' }}</td>
                                <td>{{ $line->kit->unit ?? '' }}</td>
                                <td>{{ $line->begining_balance ?? 0 }}</td>
                                <td>
                                    @php
                                        $netreceived = 0;
                                        if(null !== $deliveries){
                                            $delivery = $deliveries->where('kit_id', $line->kit_id)->first();
                                            $netreceived = ($delivery->received - $delivery->damaged);
                                        }
                                    @endphp
                                    {{ $netreceived }}
                                </td>
                                <td>{{ $delivery->lotno ?? '' }}</td>
                                <td>{{ $line->used ?? 0 }}</td>
                                <td>{{ $line->wasted ?? 0 }}</td>
                                <td>{{ $line->positive_adjustment ?? 0 }}</td>
                                <td>{{ $line->negative_adjustment ?? 0 }}</td>
                                <td>{{ $line->ending_balance ?? 0 }}</td>
                                <td>{{ $line->request ?? 0 }}</td>
                			</tr>
            			@endforeach
                		</tbody>
                	</table>
                </div>
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
            
        });
    </script>
@endsection