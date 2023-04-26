@extends('layouts.tasks')

@section('css_scripts')
    
@endsection

@section('custom_css')
	<style type="text/css">
		.input-edit {
            background-color: #FFFFCC;
        }
	</style>
@endsection

@section('content')
@php
    $currentmonth = date('m');
    $prevmonth = date('m')-1;
    $year = date('Y');
    $prevyear = $year;
    if ($currentmonth == 1) {
        $prevmonth = 12;
        $prevyear -= 1;
    }
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="hpanel" style="margin-top: 1em;margin-right: 18%;">
        <form action="{{ url('/performancelog') }}" method="POST" class='form-horizontal'>
            @csrf
            @foreach($data->sampletypes as $sampletype)
            <div class="alert alert-warning">
                    <center>
                        <font color="#4183D7">
                        @if ($sampletype == 'EID')
                            {{ $sampletype }}
                        @else
                            VL ({{ $sampletype }})
                        @endif
                        </font>
                    </center>
            </div>
            <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
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
                                {{ date("F", mktime(0, 0, 0, $prevmonth, 1, Date('Y'))) }}, {{ $prevyear }}
                            </th>
                            <td>
                                <input class="form-control input-sm" id="{{ $sampletype }}received" name="{{ $sampletype }}received" type="text" value="{{ $data->logs->$sampletype->received }}" disabled="true">
                                <input name="{{ $sampletype }}received" type="hidden" value="{{ $data->logs->$sampletype->received }}">
                            </td>
                            <td>
                                <input class="form-control input-sm" id="{{ $sampletype }}rejected" name="{{ $sampletype }}rejected" type="text" value="{{ $data->logs->$sampletype->rejected }}" disabled="true">
                                <input name="{{ $sampletype }}rejected" type="hidden" value="{{ $data->logs->$sampletype->rejected }}" >
                            </td>
                            <td>
                                <input class="form-control input-sm" id="{{ $sampletype }}logged" name="{{ $sampletype }}logged" type="text" value="{{ $data->logs->$sampletype->logged }}" disabled="true">
                                <input name="{{ $sampletype }}logged" type="hidden" value="{{ $data->logs->$sampletype->logged }}">
                            </td>
                            <td>
                                <input class="form-control input-sm input-edit" id="{{ $sampletype }}notlogged" name="{{ $sampletype }}notlogged" type="text" value="" required>
                            </td>
                            <td>
                                <input class="form-control input-sm" id="{{ $sampletype }}tested" name="{{ $sampletype }}tested" type="text" value="{{ $data->logs->$sampletype->tested }}" disabled="true">
                                <input name="{{ $sampletype }}tested" type="hidden" value="{{ $data->logs->$sampletype->tested }}">
                            </td>
                            <td>
                                <textarea class="form-control input-sm input-edit" id="{{ $sampletype }}reason" name="{{ $sampletype }}reason" cols="100" required></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endforeach
            <center>
                <button class="btn btn-success" type="submit" name="submit" value="submit" style="margin-bottom: 3em;margin-top: 1em;font-size: 14px;">Submit Lab Activity Log</button>
            </center>
        </form>
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
@endsection