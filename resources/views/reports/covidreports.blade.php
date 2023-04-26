@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('css_scripts')

@endsection

@section('custom_css')
    <style type="text/css">
        .form-horizontal .control-label {
                text-align: left;
            }
    </style>
@endsection

@section('content')
<div class="p-lg">
    <div class="content animate-panel" data-child="hpanel">
        <div class="row">
            <div class="col-lg-10 col-lg-offset-1">
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center>Test Outcome Report [ All Tested Samples ]</center>
                    </div>
                    <div class="panel-body">

                       <form action="/covidreports" method="POST" class="form-horizontal" id="covid_reports_form" >
                        @csrf
                        {{-- <div class="form-group">
                            <label class="col-sm-2 control-label">Select Period</label>
                            <div class="col-sm-10">
                                <!-- <select class="form-control" id="period">
                                    <option selected="true" disabled="true">Select Time Frame</option>
                                    <option value="weekly">Date Range</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="annually">Annually</option>
                                </select> -->
                                <label> <input type="radio" name="period" value="range"> Date Range </label>
                                <label> <input type="radio" name="period" value="monthly"> Monthly </label>
                                <label> <input type="radio" name="period" value="quarterly"> Quarterly </label>
                                <label> <input type="radio" name="period" value="annually"> Annually </label>
                            </div>
                            <div class="row" id="periodSelection" style="display: none;">
                                <div class="col-md-9  col-md-offset-3" id="rangeSelection">
                                    <table cellpadding="1" cellspacing="1" class="table table-condensed">
                                        <tbody>
                                            <tr>
                                                <th>Select Date Range From: </th>
                                                <td>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" id="fromDateCat" class="form-control lockable" name="fromDate">
                                                    </div>
                                                </td>
                                                <td><center>To:</center></td>
                                                <td>
                                                    <div class="input-group date">
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                        <input type="text" id="toDateCat" class="form-control lockable" name="toDate">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-9  col-md-offset-3" id="monthSelection">
                                    <table cellpadding="1" cellspacing="1" class="table table-condensed">
                                        <tbody>
                                            <tr>
                                                <th>Select Year and Month </th>
                                                <td>
                                                    <select class="form-control" id="year" name="year" style="width: 100%;">
                                                        <option selected="true" disabled="true">Select a Year</option>
                                                        @for ($i = 0; $i <= 6; $i++)
                                                            @php
                                                                $year=gmdate('Y')-$i
                                                            @endphp
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" id="month" name="month" style="width: 100%;">
                                                        <option selected="true" disabled="true">Select a Month</option>
                                                        @for($i = 1; $i <= 12; ++$i)
                                                            <option value="{{ $i }}">{{ date("F", strtotime(date("Y") ."-". $i ."-01")) }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>    
                                </div>
                                <div class="col-md-9  col-md-offset-3" id="quarterSelection">
                                    <table cellpadding="1" cellspacing="1" class="table table-condensed">
                                        <tbody>
                                            <tr>
                                                <th>Select Year and Quarter </th>
                                                <td>
                                                    <select class="form-control" id="year" name="year" style="width: 100%;">
                                                        <option selected="true" disabled="true">Select a Year</option>
                                                        @for ($i = 0; $i <= 6; $i++)
                                                            @php
                                                                $year=gmdate('Y')-$i
                                                            @endphp
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control" id="quarter" name="quarter" style="width: 100%;">
                                                        <option selected="true" disabled="true">Select a Quarter</option>
                                                        @for ($i = 1; $i <= 4; $i++)
                                                            <option value="Q{{ $i }}">Q{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>    
                                </div>
                                <div class="col-md-9  col-md-offset-3" id="yearSelection">
                                    <table cellpadding="1" cellspacing="1" class="table table-condensed">
                                        <tbody>
                                            <tr>
                                                <th>Select Year </th>
                                                <td>
                                                    <select class="form-control" id="year" name="year" style="width: 100%;">
                                                        <option selected="true" disabled="true">Select a Year</option>
                                                       @for ($i = 0; $i <= 6; $i++)
                                                            @php
                                                                $year=gmdate('Y')-$i
                                                            @endphp
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                        @endfor
                                                    </select>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>    
                                </div>
                            </div>
                        </div> --}}
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Filter
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="date_filter" required class="form-control" value="{{ date('Y-m-d') }}" name="date_filter">
                                </div>
                            </div>  
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="date_filter_to" required class="form-control" value="{{ date('Y-m-d') }}" name="date_filter_to">
                                </div>
                            </div>                            
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Select Report Type
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-9">
                                <label> <input type="radio" name="types" value="daily_results_submission" class="i-checks" required> Daily Laboratory Results Submission </label>
                                <label> <input type="radio" name="types" value="worksheet_machines" class="i-checks" required> Worksheet Machine Totals </label>
                                <label> <input type="radio" name="types" value="worksheet_report" class="i-checks" required> Worksheet Report </label>
                                <label> <input type="radio" name="types" value="nphl_results_submission" class="i-checks" required> Daily NPHL Results Submission </label>
                                <label> <input type="radio" name="types" value="nphl_results_submission_latest" class="i-checks" required> Air Travel Submission </label>
                                <label> <input type="radio" name="types" value="nphl_api_submission" class="i-checks" required> NPHL API Submission </label>
                                <label> <input type="radio" name="types" value="nphl_antigen_submission" class="i-checks" required> NPHL Antigen Submission </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <center>
                                <button type="submit" class="btn btn-default" id="generate_report">Generate Report</button>
                                <button class="btn btn-default">Reset Options</button>
                            </center>
                        </div>  
                        </form>                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @component('/forms/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot

        $(".date").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        set_select_facility("report_facility_search", "{{ url('facility/search') }}", 3, "Search for facility", false);
        set_select_facility("report_district_search", "{{ url('district/search') }}", 3, "Search for Sub-County", false);
        set_select_facility("report_county_search", "{{ url('county/search') }}", 1, "Search for County", false);
        set_select_facility("report_partner_search", "{{ url('partner/search') }}", 1, "Search for Partner", false);

    @endcomponent
    <script type="text/javascript">
        $(document).ready(function(){
            // $('.period').click(function(){
            $('input[name="period"]').change(function(){
                period = $(this).val();
                $('#periodSelection').show();
                $('#rangeSelection').hide();
                $('#monthSelection').hide();
                $('#quarterSelection').hide();
                $('#yearSelection').hide();
                if (period == 'range') {
                    $('#rangeSelection').show();
                } else if (period == 'monthly') {
                    $('#monthSelection').show();
                } else if (period == 'quarterly') {
                    $('#quarterSelection').show();
                } else if (period == 'annually') {
                    $('#yearSelection').show();
                }
            });
            $("#generate_report").click(function(e){
                // var selValue = $('input[name=category]:checked').val();
                // if (selValue == 'county') {
                //     category = $("#report_county_search").val();
                //     cat = 'County';
                // } else if (selValue == 'subcounty') {
                //     category = $("#report_district_search").val();
                //     cat = 'Sub-County';
                // } else if (selValue == 'partner') {
                //     category = $("#report_partner_search").val();
                //     cat = 'Partner';
                // } else if (selValue == 'facility') {
                //     category = $("#report_facility_search").val();
                //     cat = 'Facility';
                // }

                // if(category == '' || category == null || category == undefined) {
                //     e.preventDefault();
                //     set_warning("No "+cat+" Selected</br /></br />Please Select a "+cat+" from the dropdown");
                // }

                // var perValue = $('input[name=period]:checked').val();
                // alert(perValue);
                // var $radios = $('input[name="period"]');
            });
        });
    </script>
@endsection