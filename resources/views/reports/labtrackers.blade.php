@extends('layouts.master')

@component('/tables/css')
@endcomponent

@section('custom_css')
	<style type="text/css">
		.input-edit {
            background-color: #FFFFCC;
        }
	</style>
@endsection

@section('content')
@php
    $prevmonth = date('m')-1;
@endphp

<div class="content">
    <div class="row">
        <div class="col-lg-12">
        	<div class="row" style="margin-bottom: 1em;">
	            <!-- Year -->
	            <div class="col-md-6">
	                <center><h5>Year Filter</h5></center>
	                @for ($i = 0; $i <= 9; $i++)
	                    @php
	                        $year=Date('Y')-$i
	                    @endphp
	                    <a href='{{ url("lablogs/$year") }}'>{{ Date('Y')-$i }}</a> |
	                @endfor
	            </div>
	            <!-- Year -->
	            <!-- Month -->
	            <div class="col-md-6">
	                <center><h5>Month Filter</h5></center>
	                @for ($i = 1; $i <= 12; $i++)
	                    <a href='{{ url("lablogs/null/$i") }}'>{{ date("F", strtotime(date("Y") ."-". $i ."-01")) }}</a> |
	                @endfor
	            </div>
	            <!-- Month -->
	        </div>
            <div class="hpanel">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#labs-performance"><strong>A.) Lab Performance Report ({{ date("F", mktime(0, 0, 0, $data->month, 1, Date('Y'))) }}, {{ $data->year }})</strong></a></li>
                    <li class=""><a data-toggle="tab" href="#labs-equipment"><strong>B.) Lab Equipment Reports ({{ date("F", mktime(0, 0, 0, $data->month, 1, Date('Y'))) }}, {{ $data->year }})</strong></a></li>
                </ul>
                <div class="tab-content">
                    <div id="labs-performance" class="tab-pane active">
                    @forelse($data->performance as $performance)
			            <div class="alert alert-warning">
			                <center>
			                    <font color="#4183D7">
			                    @if($performance->testtype == 1)
			                    	EID
			                    @else
			                    	VL - (@if($performance->sampletype == 1) Plasma @else DBS @endif)
			                    @endif
			                    </font>
			                </center>
			            </div>
	                    <div class="panel-body">
	                        <table class="table table-striped table-bordered table-hover" style="font-size: 10px;margin-top: 1em;">
			                    <thead>
			                        <tr>
			                            <th>Period</th>
			                            <th>#Received Samples</th>
			                            <th>#Rejected Samples</th>
			                            <th># Logged in System</th>
			                            <th># NOT Logged in System</th>
			                            <th># Tested</th>
			                            <th>Reasons for Backlog</th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                        <tr>
			                            <th>
			                                {{ date("F", mktime(0, 0, 0, $performance->month, 1, Date('Y'))) }}, {{ $performance->year }}
			                            </th>
			                            <td>
			                                {{ $performance->received }}
			                            </td>
			                            <td>
			                                {{ $performance->rejected }}
			                            </td>
			                            <td>
			                                {{ $performance->loggedin }}
			                            </td>
			                            <td>
			                                {{ $performance->notlogged }}
			                            </td>
			                            <td>
			                            	@if($performance->testtype == 1)
						                    	{{ $data->eidcount }}
						                    @elseif($performance->testtype == 2)
						                    	@if($performance->sampletype == 1)
						                    		{{ $data->vlplasmacount }}
						                    	@else 
						                    		{{ $data->vldbscount }}
						                    	@endif
						                    @endif
			                            </td>
			                            <td>
			                                {{ $performance->reasonforbacklog }}
			                            </td>
			                        </tr>
			                        <tr>
			                        	<td colspan="7">
			                        		<strong>Rejected Reasons: </strong><br>
			                        	@if($performance->testtype == 1)
			                        		@foreach($data->eidrejected as $key => $rejected)
				                        		{{ $rejected->name }}&nbsp;;&nbsp;
					                    	@endforeach
					                    @elseif($performance->testtype == 2)
					                    	@if($performance->sampletype == 1)
					                    		@foreach($data->vlplasmarejected as $key => $rejected)
					                        		{{ $rejected->name }}&nbsp;;&nbsp;
						                    	@endforeach
					                    	@else 
					                    		@foreach($data->vldbsrejected as $key => $rejected)
					                        		{{ $rejected->name }}&nbsp;;&nbsp;
						                    	@endforeach
					                    	@endif
					                    @endif
			                        	</td>
			                        </tr>
			                    </tbody>
			                </table>
	                    </div>
	                @empty
	                	<div class="panel-body">
	                		<div class="alert alert-warning">No data available</div>
	                	</div>
	                @endforelse
                    </div>
                    <div id="labs-equipment" class="tab-pane">
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover data-table-long" style="font-size: 10px;margin-top: 1em;">
			                    <thead>
			                        <tr>
			                            <th>#</th>
			                            <th>Equipment</th>
			                            <th>Date of breakdown</th>
			                            <th>Date Reported</th>
			                            <th>Date Fixed</th>
			                            <th>Downtime (Days)</th>
			                            <th># of samples Not Run</th>
			                            <th># of failed Runs</th>
			                            <th># of reagents wasted</th>
			                            <th>Breakdown Reason</th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                    @forelse($data->equipments as $key => $equipment)
			                        <tr>
			                            <td>{{ $key+1 }}</td>
			                            <td>{{ $equipment->equipment->name ?? '' }}</td>
			                            <td>
			                            	@isset($equipment->datebrokendown)
			                            		{{ date('d M, Y', strtotime($equipment->datebrokendown)) }}
			                            	@else
			                            		''
			                            	@endisset
			                            </td>
			                            <td>
			                            	@isset($equipment->datereported)
			                            		{{ date('d M, Y', strtotime($equipment->datereported)) }}
			                            	@else
			                            		''
			                            	@endisset
			                            </td>
			                            <td>
			                            	@isset($equipment->datefixed)
			                            		{{ date('d M, Y', strtotime($equipment->datefixed)) }}
			                            	@else
			                            		''
			                            	@endisset</td>
			                            <td>{{ $equipment->downtime ?? '' }}</td>
			                            <td>{{ $equipment->samplesnorun ?? '' }}</td>
			                            <td>{{ $equipment->failedruns ?? '' }}</td>
			                            <td>{{ $equipment->reagentswasted ?? '' }}</td>
			                            <td>{{ $equipment->breakdownreason ?? '' }}</td>
			                        </tr>
			                    @empty
			                    	<tr><td colspan="10"><center>No Data Available</center></td></tr>
			                    @endforelse
			                    </tbody>
			                </table>
			            <form class="form-horizontal" id="submit" method="post" action="{{ url('lablogs') }}">
			            	@csrf()
			            	<input type="hidden" name="month" value="{{ $data->month }}" />
			            	<input type="hidden" name="year" value="{{ $data->year }}" />
			            	<button type="submit" class="btn btn-info">Generate Report</button>
			            </form>
                        </div>
                    </div>
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
   		$().ready(function(){
   			
   		});
   	</script>
@endsection