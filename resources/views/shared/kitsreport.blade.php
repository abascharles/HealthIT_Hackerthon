@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<style type="text/css">
    .spacing-div-form {
        margin-top: 15px;
    }
</style>
@php
    $viraltestingSys = '';
    $eidtestingSys = '';
    if(Session('testingSystem') == 'Viralload') {
        $viraltestingSys = 'checked';
    } else {
        $eidtestingSys = 'checked';
    }
@endphp
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#kits-deliveries"><strong>A.) KITS DELIVERIES (RECEIVED KITS ) REPORT</strong></a></li>
                    <li class=""><a data-toggle="tab" href="#kits-consumption"><strong>B.) SUBMITTED MONTHLY KITS CONSUMPTION REPORTS</strong></a></li>
                </ul>
                <div class="tab-content">
                    <div id="kits-deliveries" class="tab-pane active">
                        <div class="panel-body">
                            <div class="alert alert-warning">
                                Please select the parameters from the options below to generate the Kits Deliveries query.
                            </div>
                            <div style="margin-top: 2em;">
                            <form action="{{ url('/reports/kitdeliveries') }}" class="form-horizontal" method="POST" id='reports_kits'>
                                @csrf
                                <div class="form-group spacing-div-form">
                                    <label class="col-sm-3 control-label">Select Test Type</label>
                                    <div class="col-sm-9">
                                        <label> <input type="radio" name="types" value="eid" class="i-checks" {{ $eidtestingSys }} required> EID </label>
                                        <label> <input type="radio" name="types" value="viralload" class="i-checks" {{ $viraltestingSys }} required> VIRALLOAD </label>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group spacing-div-form">
                                    <label class="col-sm-3 control-label">Kit Source</label>
                                    <div class="col-sm-9">
                                        <label> <input type="radio" name="source" value="scms" class="i-checks" required> SCMS </label>
                                        <label> <input type="radio" name="source" value="kemsa" class="i-checks" checked required> KEMSA </label>
                                        <label> <input type="radio" name="source" value="lab" class="i-checks" required> Other Lab </label>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group spacing-div-form">
                                    <label class="col-sm-3 control-label">Platform</label>
                                    <div class="col-sm-9">
                                        <label> <input type="radio" name="platform" value="abbott" class="i-checks" required> ABBOTT </label>
                                        <label> <input type="radio" name="platform" value="taqman" class="i-checks" required> TAQMAN </label>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group spacing-div-form">
                                    <label class="col-sm-3 control-label">
                                        Received in the <br/>
                                        NB: Quarter & Year Options show cummulative values ���
                                    </label>
                                    <div class="col-sm-9">
                                        <label> <input type="radio" name="period" value="monthly" required> Month </label>
                                        <label> <input type="radio" name="period" value="quarterly" required> Quarter </label>
                                        <label> <input type="radio" name="period" value="yearly" required> Year </label>
                                    </div>
                                    <div class="row" id="periodSelection" style="display: none;">
                                        <div class="col-md-9 col-md-offset-3" id="monthSelection">
                                            <table cellpadding="1" cellspacing="1" class="table table-condensed">
                                                <tbody>
                                                    <tr>
                                                        <th>Select Year and Month </th>
                                                        <td>
                                                            <select class="form-control" id="year" name="year" required>
                                                                <option selected="true" disabled="true">Select a Year</option>
                                                                @for ($i = 6; $i >= 0; $i--)
                                                                    @php
                                                                        $year=Date('Y')-$i
                                                                    @endphp
                                                                <option value="{{ $year }}">{{ $year }}</option>
                                                                @endfor
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control" id="month" name="month" required>
                                                                <option selected="true" disabled="true">Select a Month</option>
                                                                @for ($i = 1; $i <= 12; $i++)
                                                                    <option value="{{ $i }}">{{ date("F", mktime(0, 0, 0, $i, 1, Date('Y'))) }}</option>
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
                                                            <select class="form-control" id="year" name="year" required>
                                                                <option selected="true" disabled="true">Select a Year</option>
                                                                @for ($i = 6; $i >= 0; $i--)
                                                                    @php
                                                                        $year=Date('Y')-$i
                                                                    @endphp
                                                                <option value="{{ $year }}">{{ $year }}</option>
                                                                @endfor
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select class="form-control" id="quarter" name="quarter" required>
                                                                <option selected="true" disabled="true">Select a Quarter</option>
                                                                @for ($i = 1; $i <= 4; $i++)
                                                                    <option value="{{ $i }}">Q{{ $i }}</option>
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
                                                            <select class="form-control" id="year" name="year" required>
                                                                <option selected="true" disabled="true">Select a Year</option>
                                                                @for ($i = 6; $i >= 0; $i--)
                                                                    @php
                                                                        $year=Date('Y')-$i
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
                                </div>
                                <hr />
                                <div class="form-group spacing-div-form">
                                    <label class="col-sm-3 control-label">Format</label>
                                    <div class="col-sm-9">
                                        <label> <input type="radio" name="format" value="html" class="i-checks" required> HTML </label>
                                        <label> <input type="radio" name="format" value="excel" class="i-checks" required> EXCEL </label>
                                    </div>
                                </div>
                                <hr />
 
                                 <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-default" id="generate_report">Generate Report</button>
                                        <button type="reset" class="btn btn-default">Reset Options</button>
                                    </center>
                                </div>  
                            </form>
                            </div>
                        </div>
                    </div>
                    <div id="kits-consumption" class="tab-pane">
                        <div class="panel-body">
                            <div class="alert alert-warning">
                                Please select the parameters from the options below to generate the Submitted Kits Consumption query.
                            </div>
                            <div style="margin-top: 2em;">
                            <form action="{{ url('/reports/kitsconsumption') }}" class="form-horizontal" method="POST" id='reports_consumption'>
                                @csrf
                                <div class="form-group spacing-div-form">
                                    <label class="col-sm-3 control-label">Select Test Type</label>
                                    <div class="col-sm-9">
                                        <label> <input type="radio" name="types" value="eid" class="i-checks"> EID </label>
                                        <label> <input type="radio" name="types" value="viralload" class="i-checks"> VIRALLOAD </label>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group spacing-div-form">
                                    <label class="col-sm-3 control-label">Platform</label>
                                    <div class="col-sm-9">
                                        <label> <input type="radio" name="platform" value="abbott" class="i-checks"> ABBOTT </label>
                                        <label> <input type="radio" name="platform" value="taqman" class="i-checks"> TAQMAN </label>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group spacing-div-form">
                                    <label class="col-sm-3 control-label">Month of Consumption</label>
                                    <div class="col-sm-9">
                                        <div class="col-sm-6">
                                            <select class="form-control" id="month" name="month">
                                                <option selected="true" disabled="true">Select a Month</option>
                                                @for ($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}">{{ date("F", mktime(0, 0, 0, $i, 1, Date('Y'))) }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="year" name="year">
                                                <option selected="true" disabled="true">Select a Year</option>
                                                @for ($i = 0; $i <= 6; $i++)
                                                    @php
                                                        $year=Date('Y')-$i
                                                    @endphp
                                                <option value="{{ $year }}">{{ $year }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr />
 
                                 <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-default" id="generate_report">Generate Report</button>
                                        <button type="reset" class="btn btn-default">Reset Options</button>
                                    </center>
                                </div>  
                            </form>
                            </div>
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
        $(document).ready(function(){
            // $('.period').click(function(){
            $('input[name="period"]').change(function(){
                period = $(this).val();
                $('#periodSelection').show();
                $('#monthSelection').hide();
                $('#quarterSelection').hide();
                $('#yearSelection').hide();
                if (period == 'monthly') {
                    $('#monthSelection').show();
                } else if (period == 'quarterly') {
                    $('#quarterSelection').show();
                } else if (period == 'yearly') {
                    $('#yearSelection').show();
                }
            });

        });
    </script>
@endsection