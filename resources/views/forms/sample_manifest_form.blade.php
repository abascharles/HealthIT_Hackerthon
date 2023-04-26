@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('custom_css')
    <style type="text/css">
        .hpanel {
            margin-bottom: 4px;
        }
        .hpanel.panel-heading {
            padding-bottom: 2px;
            padding-top: 4px;
        }
    </style>
@endsection
@php
    $prefix = '';
    if(Session('testingSystem') == 'Viralload')
        $prefix = 'viral';
@endphp
@section('content')

    <div class="content">
        <div>

        <form action="{{ url('/'. $prefix .'batch/sample_manifest') }}" class="form-horizontal" method="POST" id="samples_manifest_form">
            @csrf

            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-body" style="padding-bottom: 6px;">

                            {{-- <div class="alert alert-warning">
                                <center>
                                    Please fill the form correctly. <br />
                                    Fields with an asterisk(*) are mandatory.
                                </center>
                            </div> --}}
     
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Facility 
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                <select class="form-control requirable" required name="facility_id" id="facility_id"></select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-4 control-label">From Date
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date date-normal">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="from" required class="form-control lockable requirable" value="" name="from">
                                    </div>
                                </div>                            
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-4 control-label">To Date
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date date-normal">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="to" required class="form-control lockable requirable" value="" name="to">
                                    </div>
                                </div>                            
                            </div>

                        </div>
                    </div>
                </div>
            </div>
                    
            <div class="row">
                <div class="col-lg-12">
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <center>
                            <div class="col-sm-10 col-sm-offset-1">
                                <button type="submit" class="btn btn-primary">Print Manifest</button>
                            </div>
                        </center>
                    </div>
                </div>
            </div>
        </form>

      </div>
    </div>

@endsection

@section('scripts')

    @component('/forms/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot


        @slot('val_rules')
           ,
            rules: {
                from: {
                    lessThan: ["#to", "From Date", "To Date"]
                }                               
            }
        @endslot
        $(".date-normal").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: "-6m",
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

    @endcomponent


    <script type="text/javascript">
        $(document).ready(function(){
            

        });
    </script>



@endsection
