@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Create Worksheet
                </h2>
            </div>
        </div>
    </div>


   <div class="content">
            
        @if(($create || in_array(env('APP_LAB'), [8, 9])) && $count)


            @if (isset($worksheet))
                <form action="{{ url('/viralworksheet/' . $worksheet->id) }}" class="form-horizontal" method="POST" target="_blank">
                    @method('PUT')
            @else

                @if(env('APP_LAB') == 8)
            
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="hpanel">
                                <div class="panel-body"> 
                                    <div class="alert alert-warning">
                                        <center>
                                            Please select the samples that you want to enter the worksheet.
                                        </center>
                                    </div>
                                <br />
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ url('/viralworksheet') }}" class="form-horizontal" method="POST" target="_blank" id='worksheets_form'>

                @endif

                <!-- The partial view below has forms for deleting samples hence the form will start after this row for other labs -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hpanel">
                            <div class="panel-heading">
                                <center>Viralload Samples</center>
                            </div>
                            <div class="panel-body">
                                @include('shared/viralsamples-partial')
                            </div>
                        </div>
                    </div>                
                </div>
                
                @if(env('APP_LAB') != 8)
                    <form action="{{ url('/viralworksheet') }}" class="form-horizontal" method="POST" target="_blank" id='worksheets_form'>
                @endif

                <input type="hidden" value="{{ $machine_type }}" name="machine_type" >
                <input type="hidden" value="{{ $sampletype }}" name="sampletype" >

                @if($calibration)
                    <input type="hidden" value="1" name="calibration" >
                @endif

                @if($limit)
                    <input type="hidden" value="{{ $limit }}" name="limit" >
                @endif

                @if($entered_by)
                    @if(is_array($entered_by))
                        @foreach($entered_by as $value)
                            <input type="hidden" value="{{ $value }}" name="entered_by[]" >
                        @endforeach
                    @else
                        <input type="hidden" value="{{ $entered_by }}" name="entered_by" >
                    @endif
                @endif
            @endif

            @csrf

            <div class="row">
                <div class="col-lg-9 col-lg-offset-1">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center>Worksheet Information</center>
                        </div>
                        <div class="panel-body">

                            @if($machine_type == 1)

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Lot No</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"  name="lot_no" type="text" value="{{ $worksheet->lot_no ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Date Cut</label>
                                    <div class="col-sm-8">
                                        <div class="input-group date date_cut">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->datecut ?? '' }}" name="datecut">
                                        </div>
                                    </div>                            
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">HIQCAP Kit No</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"  name="hiqcap_no" type="text" value="{{ $worksheet->hiqcap_no ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Rack No</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"  name="rack_no" type="text" value="{{ $worksheet->rack_no ?? '' }}">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Spek Kit No</label>
                                    <div class="col-sm-8">
                                        <input class="form-control"  name="spekkit_no" type="text" value="{{ $worksheet->spekkit_no ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">KIT EXP</label>
                                    <div class="col-sm-8">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->kitexpirydate ?? '' }}" name="kitexpirydate">
                                        </div>
                                    </div>                            
                                </div>

                            @else

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Sample Prep</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="sample_prep_lot_no" type="text" value="{{ $worksheet->sample_prep_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->sampleprepexpirydate ?? '' }}" name="sampleprepexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Bulk Lysis Buffer</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="bulklysis_lot_no" type="text" value="{{ $worksheet->bulklysis_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->bulklysisexpirydate ?? '' }}" name="bulklysisexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Control</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="control_lot_no" type="text" value="{{ $worksheet->control_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->controlexpirydate ?? '' }}" name="controlexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Calibrator</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="calibrator_lot_no" type="text" value="{{ $worksheet->calibrator_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->calibratorexpirydate ?? '' }}" name="calibratorexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Amplification Kit</label>
                                    <div class="col-sm-3">
                                        <input class="form-control"  name="amplification_kit_lot_no" type="text" value="{{ $worksheet->amplification_kit_lot_no ?? '' }}">
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date_exp">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text"  class="form-control" value="{{ $worksheet->amplificationexpirydate ?? '' }}" name="amplificationexpirydate" placeholder="Expiry Date">
                                        </div>
                                    </div>                            
                                </div>

                            @endif

                            <div class="form-group cdc-only">
                                <label class="col-sm-4 control-label">CDC Worksheet No (Lab Defined)</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="cdcworksheetno" type="text" value="{{ $worksheet->cdcworksheetno ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Save & Print Worksheet</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            </form>
        @else

            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-body"> 
                            <div class="alert alert-warning">
                                <center>
                                    There are only {{ $count }} samples that qualify to be in a worksheet.
                                </center>
                            </div>
                        <br />
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center>Viralload Samples</center>
                        </div>
                        <div class="panel-body">
                            @include('shared/viralsamples-partial')
                        </div>
                    </div>
                </div>                
            </div>

        @endif

        
    </div>

@endsection

@section('scripts')

    @component('/forms/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot



        $(".date_cut").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: "-7d",
            endDate: "+0d",
            format: "yyyy-mm-dd"
        });

        $(".date_exp").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: "-5d",
            endDate: "+5y",
            format: "yyyy-mm-dd"
        });

        @if(env('APP_LAB') != 2)
            $(".cdc-only").hide();
        @endif

    @endcomponent

@endsection
