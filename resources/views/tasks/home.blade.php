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
            <div class="hpanel" style="margin-top: 1em;margin-right: 20%;">
            	<div class="alert alert-danger">
	                <center><i class="fa fa-bolt"></i> Please note that you CANNOT access the main system until the below pending tasks have been completed.</center>
	            </div>
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                @if($allowedCovidConsumption)
                    @if($covidconsumption == 0)
                        <div class="alert alert-danger spacing bottom">
                            <strong><a href="{{ url('covidkits') }}">Click to Submit Last Week`s COVID Consumptions</a></strong>
                            @if(sizeof($time) > 1)
                            <strong><p style="margin-left: 1.5em;"><font color="#CCCCCC" style="color: black;">Your lab needs to update consumptions for the last {{ sizeof($time) }} weeks. Click on the link above to update them</font></p></strong>
                            @endif
                            @foreach($time as $key => $week)
                            <p style="margin-left: 3em;"><font color="#CCCCCC" style="color: black;">Update consumptions for week {{ $week->week }} ({{ $week->week_start }} - {{ $week->week_end }})</font></p>
                            @endforeach
                        </div>
                    @else 
                        <div class="alert alert-success spacing bottom">
                            <strong><a href="#" style="color: black;">Previous COVID consumptions Submitted</a></strong>
                        </div>
                    @endif
                @endif
                @if($allowedEIDVLConsumption)
                    <!-- Kit and kits consumption -->
                    @if(sizeof($deliveries) > 0)
                        <div class="alert alert-warning spacing  bottom">
                        	<strong><a href="{{ url('kitsdeliveries') }}">Click to Add Kit Deliveries for the previous {{ sizeof($deliveries) }} month(s)</a></strong>
                        	<p style="margin-left: 3em;">
                                <font color="#CCCCCC">
                                @foreach($deliveries as $delivery)
                                    RECEIVED IN {{ date("F", mktime(0, 0, 0, $delivery->month, 1, Date('Y'))) }}, {{ $delivery->year }}<br />
                                @endforeach
                                </font>
                            </p>
                        </div>
                    @elseif (sizeof($deliveries) == 0)
                        <div class="alert alert-success spacing bottom">
                            <strong><a href="#">All Previous Deliveries Submitted</a></strong>
                        </div> 
                        @if(sizeof($consumptions) > 0)
                            <div class="alert alert-warning spacing  bottom">
                                <strong><a href="{{ url('consumption') }}">Click to Add Kit Consumptions for the previous {{ sizeof($consumptions) }} month(s)</a></strong>
                                <p style="margin-left: 3em;">
                                    <font color="#CCCCCC">
                                    @foreach($consumptions as $consumption)
                                        COSUMPTIONS IN {{ date("F", mktime(0, 0, 0, $consumption->month, 1, Date('Y'))) }}, {{ $consumption->year }}<br />
                                    @endforeach
                                    </font>
                                </p>
                            </div>
                        @else
                            <div class="alert alert-success spacing bottom">
                                <strong><a href="#">All Previous Consumptions Submitted</a></strong>
                            </div>
                        @endif
                    @endif
                    <!-- Kit and kits consumption -->
                @endif
                @if($allowedEquipmentUtilization)
                    <!-- Lab performance Report -->
                    @if ($performance == 0)
                        <div class="alert alert-warning spacing bottom">
                            <strong><a href="{{ url('performancelog') }}">Click to Submit Monthly Lab Performance Log ( Tracker )</a></strong>
                            <p style="margin-left: 3em;"><font color="#CCCCCC">Log on any Back logs and Reasons for the previous month</font></p>
                        </div>
                    @else 
                        <div class="alert alert-success spacing bottom">
                            <strong><a href="#">{{ date("F", mktime(0, 0, 0, $prevmonth, 1, Date('Y'))) }}, {{ $prevyear }} Lab Performance Log ( Tracker ) Submitted</a></strong>
                        </div>
                    @endif
                    <!-- Lab performance Report -->

                    <!-- Lab equipment Report -->
                   @if ($equipment == 0)
                        <div class="alert alert-warning spacing bottom">
                            <strong><a href="{{ url('equipmentlog') }}">Click to Submit Monthly Equipment Log ( Tracker )</a></strong>
                            <p style="margin-left: 3em;"><font color="#CCCCCC"> Log on any Equipment Breakdown in Previous Month</font></p>
                        </div>
                    @else
                        <div class="alert alert-success spacing bottom">
                            <strong><a href="#">{{ date("F", mktime(0, 0, 0, $prevmonth, 1, Date('Y'))) }}, {{ $prevyear }} Lab Monthly Equipment Log ( Tracker ) Submitted</a></strong>
                        </div>
                    @endif
                    <!-- Lab equipment Report -->
                @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

@endsection