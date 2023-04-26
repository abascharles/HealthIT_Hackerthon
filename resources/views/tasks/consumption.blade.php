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
@php
    $currentmonth = date('m');
    $prevmonth = date('m')-1;
    $year = date('Y');
    $prevyear = $year;
    if ($currentmonth == 1) {
        $prevmonth = 12;
        $prevyear -= 1;
    }
    $toedit = ['wasted','pos','issued'];
    $plats = ['taqman','abbott'];
@endphp
<div class="row">
    <div class="col-md-12">
        <div class="hpanel" style="margin-top: 1em;margin-right: 6%;">
            <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                <form action="{{ url('/consumption') }}" method="POST" class='form-horizontal'>
                    @csrf
                <div class="form-group">
                    <div class="form-group" style="/*display: none;" id="platformDiv">
                        <label class="col-sm-4 control-label"><center>Platform</center></label>
                        <div class="col-sm-8">
                            <select class="form-control input-sm" required name="platform" id="platform">
                                <option value="" selected>Select Platform</option>
                                {{-- @if($data->taqproc == 0) --}}
                                <option value="1">COBAS/TAQMAN @if($data->taqproc > 0) <i>(Entry made for {{ date("F", mktime(0, 0, 0, $prevmonth, 1, Date('Y'))) }}, {{ $prevyear }} )</i> @endif</option>
                                {{-- @endif
                                @if($data->abbottproc == 0) --}}
                                <option value="2">ABBOTT @if($data->abbottproc > 0) <i>(Entry made for {{ date("F", mktime(0, 0, 0, $prevmonth, 1, Date('Y'))) }}, {{ $prevyear }} )</i> @endif</option>
                                {{-- @endif --}}
                            </select>
                        </div>
                    </div>                    
                </div>

                <!-- TAQMAN DIV -->
                    <div id="taqman" style="display: none;">
                        @foreach($data->testtypes as $types)
                            @php
                                $testtype = $types.'teststaq';
                                $tests = $data->$testtype;
                                $prevtaqman = 'prevtaqman'.$types;
                                $taqmandeliveries = 'taqmandeliveries'.$types;
                                $qualkitused = 0;
                                $used = null;
                            @endphp
                            <div class="alert alert-danger">
                                <center><i class="fa fa-bolt"></i> Please enter {{ $types }} values below. <strong>(Tests:{{ number_format($tests) }})</strong></center>
                            </div>
                            <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                                <thead>               
                                    <tr>
                                        <th rowspan="2">NAME OF COMMODITY</th>
                                        <th rowspan="2">UNIT OF ISSUE</th>
                                        <th rowspan="2">BEGINNING BALANCE</th>
                                        <th colspan="2">QUANTITY RECEIVED FROM CENTRAL WAREHOUSE(KEMSA/SCMS/RDC)</th>
                                        <th rowspan="2">QUANTITY USED</th>
                                        <th rowspan="2">LOSSES / WASTAGE</th>
                                        <th colspan="2">ADJUSTMENTS</th>
                                        <th rowspan="2">ENDING BALANCE (PHYSICAL COUNT)</th>
                                        <th rowspan="3">QUANTITY REQUESTED</th>
                                    </tr>
                                    <tr>
                                        <th>Quantity</th>
                                        <th>Lot No.</th>
                                        <th>Positive<br />(Received other source)</th>
                                        <th>Negative<br />(Issued Out)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <input type="hidden" name="taqman{{ $types }}tests" id="taqman{{ $types }}tests" value="{{ $tests }}">

                                    @foreach ($data->taqmanKits as $kits)
                                    
                                    @php
                                        $prefix = 'ending'.$kits['alias'];
                                        $received = $kits['alias'].'received';
                                        $damaged = $kits['alias'].'damaged';
                                        $lot = $kits['alias'].'lotno';

                                        if ($kits['alias'] == 'qualkit') {
                                            if ($types == 'VL') {
                                                $qualkitused = round(($tests / 42));
                                                $kits['name'] = $kits['VLname'];
                                            } else if ($types == 'EID') {
                                                $qualkitused = round(($tests / 44));
                                                $kits['name'] = $kits['EIDname'];
                                            }
                                        } else {
                                            $used = round($qualkitused * $kits['factor']);
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $kits['name'] }}</td>
                                        <td>{{ $kits['unit'] }}</td>
                                        <td>
                                            <input class="form-control input-edit" type="text" name="taqman{{ $types }}beginingbal{{ $kits['alias'] }}" id="taqman{{ $types }}beginingbal{{ $kits['alias'] }}" value="{{ $data->$prevtaqman->$prefix ?? 0 }}" disabled="true">
                                        </td>
                                        <td>{{ $data->$taqmandeliveries->$received ?? 0 }}</td>
                                        <td>{{ $data->$taqmandeliveries->$lot ?? '-' }}</td>
                                        <td>{{ $used ?? $qualkitused }}</td>
                                        <td>
                                            <input class="form-control input-edit" type="text" name="taqman{{ $types }}wasted{{ $kits['alias'] }}" id="taqman{{ $types }}wasted{{ $kits['alias'] }}" value="" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-edit" type="text" name="taqman{{ $types }}pos{{ $kits['alias'] }}" id="taqman{{ $types }}pos{{ $kits['alias'] }}" value="" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-edit" type="text" name="taqman{{ $types }}issued{{ $kits['alias'] }}" id="taqman{{ $types }}issued{{ $kits['alias'] }}" value="" required>
                                        </td>
                                        <td>
                                            @php
                                                if($kits['alias'] == 'qualkit') {
                                                    $endingbal = (@($data->$prevtaqman->$prefix+$data->$taqmandeliveries->$received) - @($qualkitused));
                                                } else {
                                                    $endingbal = (@($data->$prevtaqman->$prefix+$data->$taqmandeliveries->$received) - @($used));
                                                }
                                            @endphp
                                            
                                            <input class="form-control" type="text" id="taqman{{ $types }}ending{{ $kits['alias'] }}disabled" value="{{ $endingbal }}" disabled="true">
                                            
                                            <input type="hidden" name="taqman{{ $types }}ending{{ $kits['alias'] }}" id="taqman{{ $types }}ending{{ $kits['alias'] }}" value="{{ $endingbal }}">
                                        </td>
                                        <td>
                                            <input class="form-control input-edit-danger" type="text" name="taqman{{ $types }}request{{ $kits['alias'] }}" id="taqman{{ $types }}request{{ $kits['alias'] }}" value="" required>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"><center>Comments concerning Negative adjustments (e.g. where were the kits issued out/donated to and why)</center></label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control input-sm input-edit" id="taqman{{ $types }}issuedcomment" name="taqman{{ $types }}issuedcomment" cols="300"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"><center>Comments concerning Positive adjustments (e.g. where were the kits received from)</center></label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control input-sm input-edit" id="taqman{{ $types }}receivedcomment" name="taqman{{ $types }}receivedcomment" cols="300"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-sm-12">
                            <center>
                            <button class="btn btn-success" type="submit" name="saveTaqman" value="saveTaqman">Save Taqman Kit Consumption</button>
                            <button class="btn btn-primary" type="submit" name="discard" value="add">Discard Changes</button>
                            </center>
                        </div>
                    </div>
                <!-- TAQMAN DIV -->

                <!-- ABBOTT DIV -->
                    <div id="abbott" style="display: none;">
                        @foreach($data->testtypes as $types)
                            @php
                                $testtype = $types.'testsabbott';
                                $tests = $data->$testtype;
                                $prevabbott = 'prevabbott'.$types;
                                $abbottdeliveries = 'abbottdeliveries'.$types;
                                $qualkitused = 0;
                                $used = null;
                            @endphp
                            <div class="alert alert-warning">
                                <center><i class="fa fa-bolt"></i> Please enter {{ $types }} values below.<strong>(Tests:{{ number_format($tests) }})</strong></center>
                            </div>
                            <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                                <thead>               
                                    <tr>
                                        <th rowspan="2">NAME OF COMMODITY</th>
                                        <th rowspan="2">BEGINNING BALANCE</th>
                                        <th colspan="2">QUANTITY RECEIVED FROM CENTRAL WAREHOUSE(KEMSA/SCMS/RDC)</th>
                                        <th rowspan="2">QUANTITY USED</th>
                                        <th rowspan="2">LOSSES / WASTAGE</th>
                                        <th colspan="2">ADJUSTMENTS</th>
                                        <th rowspan="2">ENDING BALANCE (PHYSICAL COUNT)</th>
                                        <th rowspan="3">QUANTITY REQUESTED</th>
                                    </tr>
                                    <tr>
                                        <th>Quantity</th>
                                        <th>Lot No.</th>
                                        <th>Positive<br />(Received other source)</th>
                                        <th>Negative<br />(Issued Out)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <input type="hidden" name="abbott{{ $types }}tests" id="abbott{{ $types }}tests" value="{{ $tests }}">

                                    @foreach ($data->abbottKits as $kits)
                                    @php
                                        $prefix = 'ending'.$kits['alias'];
                                        $received = $kits['alias'].'received';
                                        $damaged = $kits['alias'].'damaged';
                                        $lot = $kits['alias'].'lotno';

                                        if ($kits['alias'] == 'qualkit') {
                                            if ($types == 'VL') {
                                                $qualkitused = round(($tests / 93));
                                                $kits['name'] = $kits['VLname'];
                                            } else if ($types == 'EID') {
                                                $qualkitused = round(($tests / 94));
                                                $kits['name'] = $kits['EIDname'];
                                            }
                                        } else {
                                            $used = round($qualkitused * $kits['factor'][$types]);
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $kits['name'] }}</td>
                                        <td>
                                            <input class="form-control input-edit" type="text" name="abbott{{ $types }}beginingbal{{ $kits['alias'] }}" id="abbott{{ $types }}beginingbal{{ $kits['alias'] }}" value="{{ $data->$prevabbott->$prefix ?? 0 }}" disabled="true">
                                        </td>
                                        <td>{{ $data->$abbottdeliveries->$received ?? 0 }}</td>
                                        <td>{{ $data->$abbottdeliveries->$lot ?? '-' }}</td>
                                        <td>{{ $used ?? $qualkitused }}</td>
                                        <td>
                                            <input class="form-control input-edit" type="text" name="abbott{{ $types }}wasted{{ $kits['alias'] }}" id="abbott{{ $types }}wasted{{ $kits['alias'] }}" value="" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-edit" type="text" name="abbott{{ $types }}pos{{ $kits['alias'] }}" id="abbott{{ $types }}pos{{ $kits['alias'] }}" value="" required>
                                        </td>
                                        <td>
                                            <input class="form-control input-edit" type="text" name="abbott{{ $types }}issued{{ $kits['alias'] }}" id="abbott{{ $types }}issued{{ $kits['alias'] }}" value="" required>
                                        </td>
                                        <td>
                                            @php
                                                if($kits['alias'] == 'qualkit') {
                                                    $endingbal = (@($data->$prevabbott->$prefix+$data->$abbottdeliveries->$received) - @($qualkitused));
                                                } else {
                                                    $endingbal = (@($data->$prevabbott->$prefix+$data->$abbottdeliveries->$received) - @($used));
                                                }
                                            @endphp
                                            <input class="form-control" type="text" id="abbott{{ $types }}ending{{ $kits['alias'] }}disabled" value="{{ $endingbal }}" disabled="true">
                                            
                                            <input type="hidden" name="abbott{{ $types }}ending{{ $kits['alias'] }}" id="abbott{{ $types }}ending{{ $kits['alias'] }}" value="{{ $endingbal }}">
                                        </td>
                                        <td>
                                            <input class="form-control input-edit-danger" type="text" name="abbott{{ $types }}request{{ $kits['alias'] }}" id="abbott{{ $types }}request{{ $kits['alias'] }}" value="" required>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"><center>Comments concerning Negative adjustments (e.g. where were the kits issued out/donated to and why)</center></label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control input-sm input-edit" id="abbott{{ $types }}issuedcomment" name="abbott{{ $types }}issuedcomment" cols="300"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-6 control-label"><center>Comments concerning Positive adjustments (e.g. where were the kits received from)</center></label>
                                        <div class="col-sm-6">
                                            <textarea class="form-control input-sm input-edit" id="abbott{{ $types }}receivedcomment" name="abbott{{ $types }}receivedcomment" cols="300"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="col-sm-12">
                            <center>
                            <button class="btn btn-success" type="submit" name="saveAbbott" value="saveAbbott">Save Abbott Kit Consumption</button>
                            <button class="btn btn-primary" type="submit" name="discard" value="add">Discard Changes</button>
                            </center>
                        </div>
                    </div>
                <!-- ABBOTT DIV -->
                </form>
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

        @foreach($plats as $platform)
            @if($platform == 'abbott')
                @foreach ($data->abbottKits as $kits)
                    @if($kits['alias'] == 'qualkit')
                        @foreach($data->testtypes as $types)
                            @foreach($toedit as $element)
                                $("#{{ $platform.$types.$element.$kits['alias'] }}").keyup(function(){
                                    lossesval = parseInt($("#{{ $platform.$types }}wasted{{ $kits['alias'] }}").val());
                                    posval = parseInt($("#{{ $platform.$types }}pos{{ $kits['alias'] }}").val());
                                    negval = parseInt($("#{{ $platform.$types }}issued{{ $kits['alias'] }}").val());

                                    if (isNaN(lossesval))
                                        lossesval = 0;
                                    if (isNaN(posval))
                                        posval = 0;
                                    if (isNaN(negval))
                                        negval = 0;
                                    
                                    @php
                                        $testtype = $types.'testsabbott';
                                        $tests = $data->$testtype;
                                        $prefix = 'ending'.$kits['alias'];
                                        $prevabbott = 'prevabbott'.$types;
                                        $abbottdeliveries = 'abbottdeliveries'.$types;
                                        $received = $kits['alias'].'received';
                                        $qualkitused = 0;
                                        if ($types == 'VL') {
                                            $qualkitused = round(($tests / 93));
                                        } else if ($types == 'EID') {
                                            $qualkitused = round(($tests / 94));
                                        }
                                        $endingbal = (@($data->$prevabbott->$prefix+$data->$abbottdeliveries->$received) - @($qualkitused));
                                    @endphp
                                        
                                        endingbal = parseInt({{ $endingbal }});
                                        
                                    @if($element == 'wasted')
                                        endingbal = (endingbal+posval)-(lossesval+negval);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}").val(endingbal);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}disabled").val(endingbal);
                                    @elseif($element == 'pos')
                                        endingbal = (endingbal+posval-lossesval-negval);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}").val(endingbal);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}disabled").val(endingbal);
                                    @elseif($element == 'issued')
                                        endingbal = (endingbal+posval-lossesval-negval);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}").val(endingbal);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}disabled").val(endingbal);
                                    @endif
                                    performCalculus($(this).val(),"{{ $platform }}","{{ $types }}","{{ $element }}",endingbal);
                                });
                            @endforeach
                            $("#{{ $platform.$types }}request{{ $kits['alias'] }}").keyup(function(){
                                prefillrequest($(this).val(),"{{ $types }}","{{ $platform }}");
                            });
                        @endforeach
                    @endif
                @endforeach
            @else
                @foreach ($data->taqmanKits as $kits)
                    @if($kits['alias'] == 'qualkit')
                        @foreach($data->testtypes as $types)
                            @foreach($toedit as $element)
                                $("#{{ $platform.$types.$element.$kits['alias'] }}").keyup(function() {
                                    lossesval = parseInt($("#{{ $platform.$types }}wasted{{ $kits['alias'] }}").val());
                                    posval = parseInt($("#{{ $platform.$types }}pos{{ $kits['alias'] }}").val());
                                    negval = parseInt($("#{{ $platform.$types }}issued{{ $kits['alias'] }}").val());

                                    if (isNaN(lossesval))
                                        lossesval = 0;
                                    if (isNaN(posval))
                                        posval = 0;
                                    if (isNaN(negval))
                                        negval = 0;
                                    
                                    @php
                                        $testtype = $types.'teststaq';
                                        $tests = $data->$testtype;
                                        $prefix = 'ending'.$kits['alias'];
                                        $prevtaqman = 'prevtaqman'.$types;
                                        $taqmandeliveries = 'taqmandeliveries'.$types;
                                        $received = $kits['alias'].'received';
                                        $qualkitused = 0;
                                        if ($types == 'VL') {
                                            $qualkitused = round(($tests / 42));
                                        } else if ($types == 'EID') {
                                            $qualkitused = round(($tests / 44));
                                        }
                                        $endingbal = (@($data->$prevtaqman->$prefix+$data->$taqmandeliveries->$received) - @($qualkitused));
                                    @endphp
                                        endingbal = parseInt({{ $endingbal }});
                                    @if($element == 'wasted')
                                        endingbal = (endingbal+posval-lossesval-negval);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}").val(endingbal);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}disabled").val(endingbal);
                                    @elseif($element == 'pos')
                                        endingbal = (endingbal+posval-lossesval-negval);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}").val(endingbal);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}disabled").val(endingbal);
                                    @elseif($element == 'issued')
                                        endingbal = (endingbal+posval-lossesval-negval);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}").val(endingbal);
                                        $("#{{ $platform.$types }}ending{{ $kits['alias'] }}disabled").val(endingbal);
                                    @endif
                                    performCalculus($(this).val(),"{{ $platform }}","{{ $types }}","{{ $element }}",endingbal);
                                });
                            @endforeach
                            $("#{{ $platform.$types }}request{{ $kits['alias'] }}").keyup(function(){
                                prefillrequest($(this).val(),"{{ $types }}","{{ $platform }}");
                            });
                        @endforeach
                    @endif
                @endforeach
            @endif
        @endforeach

        function performCalculus(value,platform,testtype,element,qualkitending) {
            if(platform == 'abbott') {
                @foreach ($data->abbottKits as $kits)
                    @if ($kits['alias'] != 'qualkit')
                        factor = {{ $kits['factor']['VL'] }};
                        if(testtype == 'EID')
                            factor = {{ $kits['factor']['EID'] }};
                        $("#"+platform+testtype+element+"{{ $kits['alias'] }}").val((value*factor).toFixed(2));
                        $("#"+platform+testtype+"ending{{ $kits['alias'] }}").val((qualkitending*factor).toFixed(2));
                        $("#"+platform+testtype+"ending{{ $kits['alias'] }}disabled").val((qualkitending*factor).toFixed(2));
                    @endif
                @endforeach
            } else {
                @foreach ($data->taqmanKits as $kits)
                    @if ($kits['alias'] != 'qualkit')
                        factor = {{ $kits['factor'] }};
                        $("#"+platform+testtype+element+"{{ $kits['alias'] }}").val((value*factor).toFixed(2));
                        $("#"+platform+testtype+"ending{{ $kits['alias'] }}").val((qualkitending*factor).toFixed(2));
                        $("#"+platform+testtype+"ending{{ $kits['alias'] }}disabled").val((qualkitending*factor).toFixed(2));
                    @endif
                @endforeach
            }
        }

        function prefillrequest(value,testtype,platform) {
            if(platform == 'abbott') {
                @foreach ($data->abbottKits as $kits)
                    @if ($kits['alias'] != 'qualkit')
                        factor = {{ $kits['factor']['VL'] }};
                        if(testtype == 'EID')
                            factor = {{ $kits['factor']['EID'] }};
                        $("#"+platform+testtype+"request{{ $kits['alias'] }}").val((value*factor).toFixed(2));
                    @endif
                @endforeach
            } else {
                @foreach ($data->taqmanKits as $kits)
                    @if ($kits['alias'] != 'qualkit')
                        factor = {{ $kits['factor'] }};
                        $("#"+platform+testtype+"request{{ $kits['alias'] }}").val((value*factor).toFixed(2));
                    @endif
                @endforeach
            }
        }
        

    @endcomponent
    <script type="text/javascript">
        $(function(){
            $("#platform").change(function(){
                platform = $(this).val();

                if (platform == 1) {
                    $("#abbott").hide();
                    $("#taqman").show();
                } else if (platform == 2) {
                    $("#taqman").hide();
                    $("#abbott").show();
                }
            });
        });
    </script>
@endsection