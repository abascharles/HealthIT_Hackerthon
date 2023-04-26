@extends('layouts.master')

@section('content')
<style type="text/css">
	.list-group {
		margin-top: 10px;
    	margin-bottom: 10px;
	}
	.list-group-item {
		margin-bottom: 4px;
    	margin-top: 4px;
	}
</style>
@php
	// $userstoday = $data->users->today ?? 0;
	// $usersanotherday = $data->users->another_day ?? 0;
	// $totalusers = $userstoday + $usersanotherday;
	// $userspercentage = ($userstoday == 0) ? 0 : round(($userstoday/$totalusers)*100, 2);

	$eidsamplessite = $data->eid_samples->site ?? 0;
	$eidsampleslab = $data->eid_samples->lab ?? 0;
	$eidsamplestotal = $eidsamplessite + $eidsampleslab;
	$eidsamplespercentage = ($eidsampleslab == 0) ? 0 : round(($eidsampleslab/$eidsamplestotal)*100, 2);
	$vlsamplessite = $data->vl_samples->site ?? 0;
	$vlsampleslab = $data->vl_samples->lab ?? 0;
	$vlsamplestotal = $vlsamplessite + $vlsampleslab;
	$vlsamplespercentage = ($vlsampleslab == 0) ? 0 : round(($vlsampleslab/$vlsamplestotal)*100, 2);

	$eidbatchsite = $data->eid_batches->site ?? 0;
	$eidbatchlab = $data->eid_batches->lab ?? 0;
	$eidbatchtotal = $eidbatchsite + $eidbatchlab;
	$vlbatchsite = $data->vl_batches->site ?? 0;
	$vlbatchlab = $data->vl_batches->lab ?? 0;
	$vlbatchtotal = $vlbatchsite + $vlbatchlab;

	$eidworksheetinprocess = $data->eid_worksheets->inprocess ?? 0;
	$eidworksheettested = $data->eid_worksheets->tested ?? 0;
	$eidworksheetrest = $data->eid_worksheets->rest ?? 0;
	$eidworksheetsemitotal = $eidworksheetinprocess + $eidworksheettested;
	$eidworksheettotal = $eidworksheetinprocess + $eidworksheettested + $eidworksheetrest;
	$eidworksheetpercentage = ($eidworksheetinprocess == 0) ? 0 : round(($eidworksheetinprocess/$eidworksheetsemitotal)*100, 2);
	$vlworksheetinprocess = $data->vl_worksheets->inprocess ?? 0;
	$vlworksheettested = $data->vl_worksheets->tested ?? 0;
	$vlworksheetrest = $data->vl_worksheets->rest ?? 0;
	$vlworksheetsemitotal = $vlworksheetinprocess + $vlworksheettested;
	$vlworksheettotal = $vlworksheetinprocess + $vlworksheettested + $vlworksheetrest;
	$vlworksheetpercentage = ($vlworksheetinprocess == 0) ? 0 : round(($vlworksheetinprocess/$vlworksheetsemitotal)*100, 2);
@endphp
<div class="p-lg">
	<div class="content animate-panel" data-child="hpanel">
		<!-- EID Widgets -->
	    <div class="row">
		    <!-- <div class="col-lg-4">
                <div class="hpanel stats">
                    <div class="panel-body h-200">
                        <div class="stats-title pull-left">
                            <h4>Users Activity Today</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-share fa-4x"></i>
                        </div>
                        <div class="m-t-xl">
                            <h3 class="m-b-xs">{{-- $userstoday + $usersanotherday --}}</h3>
                            <div class="progress m-t-xs full progress-small">
                                <div style="width: {{-- $userspercentage --}}%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{-- $userspercentage --}}" role="progressbar" class=" progress-bar progress-bar-success">
                                    <span class="sr-only">{{-- $userspercentage --}}% Complete (success)</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <small class="stats-label">Logged In</small>
                                    <h4>{{-- $userstoday --}}</h4>
                                </div>

                                <div class="col-xs-6">
                                    <small class="stats-label">% Logged In</small>
                                    <h4>{{-- $userspercentage --}}%</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <div class="col-lg-4">
                <div class="hpanel stats">
                    <div class="panel-body h-200">
                        <div class="stats-title pull-left">
                            <h4>EID Samples Activity</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-graph1 fa-4x"></i>
                        </div>
                        <div class="m-t-xl">
                            <h3 class="m-b-xs">{{ number_format($eidsamplestotal) }}</h3>
		                    <span class="font-bold no-margins">
		                        Samples Added Today
		                    </span>
                            <div class="progress m-t-xs full progress-small">
                                <div style="width: {{ $eidsamplespercentage }}%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{ $eidsamplespercentage }}" role="progressbar" class=" progress-bar progress-bar-success">
                                    <span class="sr-only">{{ $eidsamplespercentage }}% Complete (success)</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <small class="stats-label">Added at the Lab</small>
                                    <h4>{{ number_format($eidsampleslab) }}</h4>
                                </div>

                                <div class="col-xs-6">
                                    <small class="stats-label">Add at site</small>
                                    <h4>{{ number_format($eidsamplessite) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hpanel stats">
                    <div class="panel-body h-200">
                        <div class="stats-title pull-left">
                            <h4>EID Batches History</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-battery fa-4x"></i>
                        </div>
                        <div class="clearfix"></div>
                        <div class="m-t-xs">
                            <div class="row">
                                <div class="col-xs-6">
                                    <small class="stat-label">Lab Pending Dispatch</small>
                                    <h4>{{ number_format($eidbatchlab) }} <i class="fa fa-level-up text-success"></i></h4>
                                </div>
                                <div class="col-xs-6">
                                    <small class="stat-label">Site Pending Dispatch</small>
                                    <h4>{{ number_format($eidbatchsite) }} <i class="fa fa-level-up text-success"></i></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hpanel stats">
                    <div class="panel-body h-200">
                        <div class="stats-title pull-left">
                            <h4>EID Worksheets History</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-cash fa-4x"></i>
                        </div>
                        <div class="m-t-xl">
                            <h3 class="m-b-xs">{{ $eidworksheettotal }}</h3>
		                    <span class="font-bold no-margins">
		                        Worksheet History
		                    </span>
                            <div class="progress m-t-xs full progress-small">
                                <div style="width: {{ $eidworksheetpercentage }}%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{ $eidworksheetpercentage }}" role="progressbar" class=" progress-bar progress-bar-success">
                                    <span class="sr-only">{{ $eidworksheetpercentage }}% Complete (success)</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <small class="stats-label">In-processes</small>
                                    <h4>{{ $eidworksheetinprocess }}</h4>
                                </div>

                                <div class="col-xs-6">
                                    <small class="stats-label">Pending Approval</small>
                                    <h4>{{ $eidworksheettested }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<!-- EID Widgets -->
		<!-- VL Widgets -->
		<div class="row">
		    <div class="col-lg-4">
                <div class="hpanel stats">
                    <div class="panel-body h-200">
                        <div class="stats-title pull-left">
                            <h4>VL Samples Activity</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-graph1 fa-4x"></i>
                        </div>
                        <div class="m-t-xl">
                            <h3 class="m-b-xs">{{ number_format($vlsamplestotal) }}</h3>
		                    <span class="font-bold no-margins">
		                        Samples Added Today
		                    </span>
                            <div class="progress m-t-xs full progress-small">
                                <div style="width: {{ $vlsamplespercentage }}%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{ $vlsamplespercentage }}" role="progressbar" class=" progress-bar progress-bar-success">
                                    <span class="sr-only">{{ $vlsamplespercentage }}% Complete (success)</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <small class="stats-label">Added at the Lab</small>
                                    <h4>{{ number_format($vlsampleslab) }}</h4>
                                </div>

                                <div class="col-xs-6">
                                    <small class="stats-label">Add at site</small>
                                    <h4>{{ number_format($vlsamplessite) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hpanel stats">
                    <div class="panel-body h-200">
                        <div class="stats-title pull-left">
                            <h4>VL Batches History</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-battery fa-4x"></i>
                        </div>
                        <div class="clearfix"></div>
                        <div class="m-t-xs">
                            <div class="row">
                                <div class="col-xs-6">
                                    <small class="stat-label">Lab Pending Dispatch</small>
                                    <h4>{{ number_format($vlbatchlab) }} <i class="fa fa-level-up text-success"></i></h4>
                                </div>
                                <div class="col-xs-6">
                                    <small class="stat-label">Site Pending Dispatch</small>
                                    <h4>{{ number_format($vlbatchsite) }} <i class="fa fa-level-up text-success"></i></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="hpanel stats">
                    <div class="panel-body h-200">
                        <div class="stats-title pull-left">
                            <h4>VL Worksheets History</h4>
                        </div>
                        <div class="stats-icon pull-right">
                            <i class="pe-7s-cash fa-4x"></i>
                        </div>
                        <div class="m-t-xl">
                            <h3 class="m-b-xs">{{ number_format($vlworksheettotal) }}</h3>
		                    <span class="font-bold no-margins">
		                        Worksheet History
		                    </span>
                            <div class="progress m-t-xs full progress-small">
                                <div style="width: {{ $vlworksheetpercentage }}%" aria-valuemax="100" aria-valuemin="0" aria-valuenow="{{ $vlworksheetpercentage }}" role="progressbar" class=" progress-bar progress-bar-success">
                                    <span class="sr-only">{{ $vlworksheetpercentage }}% Complete (success)</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-6">
                                    <small class="stats-label">In-processes</small>
                                    <h4>{{ number_format($vlworksheetinprocess) }}</h4>
                                </div>

                                <div class="col-xs-6">
                                    <small class="stats-label">Pending Approval</small>
                                    <h4>{{ number_format($vlworksheettested) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</div>
		<!-- VL Widgets -->
    </div>
</div>
@endsection()

@section('scripts')

@endsection