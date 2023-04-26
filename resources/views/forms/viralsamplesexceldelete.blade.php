@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

   <div class="content">
        <div>
        @if(Auth::user()->user_type_id == 0)
        <form action="{{ url('/viralsample/exceluploaddelete') }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="form-group"><label class="col-sm-4 control-label">Excel to upload</label>
                            <div class="col-sm-8">
                                <input type="file" name="excelupload" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <center>
                        <div class="col-sm-8 col-sm-offset-2">
                            <button class="btn btn-success" type="submit" name="submit_type" value="release">Upload Samples</button>
                        </div>
                    </center>
                </div>
            </div>
        </div>
        </form>
        @endif

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
                dob: {
                    lessThan: ["#datecollected", "Date of Birth", "Date Collected"],
                    lessThanTwo: ["#initiation_date", "Date of Birth", "ART Inititation Date"]
                },
                initiation_date:{
                    GreaterThanSpecific: ["1990-01-01", "Date of Initiating ART"]
                },
                datecollected: {
                    lessThan: ["#datedispatched", "Date Collected", "Date Dispatched From Facility"],
                    lessThanTwo: ["#datereceived", "Date Collected", "Date Received"]
                },
                datedispatched: {
                    lessThan: ["#datereceived", "Date Dispatched From Facility", "Date Received"]
                },
                age: {
                    required: '#dob:blank'
                }
            }
        @endslot


        //$(".date :not(.date-dob, .date-art, .date-dispatched)").datepicker({
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

        $(".date-dob").datepicker({
            startView: 2,
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: '-100y',
            endDate: "-1m",
            format: "yyyy-mm-dd"
        });

        $(".date-dispatched").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: "-6m",
            endDate: "+7d",
            format: "yyyy-mm-dd"
        });

        // $("#dateinitiatedontreatment").datepicker({
        $(".date-art").datepicker({
            startView: 2,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: '-24y',
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
        set_select_facility("lab_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

    @endcomponent


    <script type="text/javascript">
        
    </script>



@endsection
