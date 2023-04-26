@extends('layouts.master')

    @component('/tables/css')
        <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
    @endcomponent

@section('content')


<br />

<div class="row">
    <form method="POST" action="{{ url('datatable/download_sms_excel/' . $type) }} ">
        @csrf
        <div class="form-group">

            <label class="col-sm-1 control-label">From:</label>
            <div class="col-sm-4">
                <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" id="from_date" name="from_date" required class="form-control">
                </div>
            </div> 

            <label class="col-sm-1 control-label">To:</label>
            <div class="col-sm-4">
                <div class="input-group date">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text" id="to_date" name="to_date" required class="form-control">
                </div>
            </div> 

            <div class="col-sm-2">                
                <button class="btn btn-primary" type="submit">Filter</button>  
            </div>                         
        </div>
    </form>
</div>

<br />

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="mytable">
                            <thead>
                                <tr>
                                    <th>Facility</th>
                                    <th>Patient</th>
                                    <th>Full Name</th>
                                    <th>Age</th>
                                    <th>Phone #</th>
                                    <th>Date Collected</th>
                                    <th>Date Tested</th>
                                    <th>Result</th>
                                    <th>Date Dispatched</th>
                                    <th>Date SMS Sent</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts') 

    @component('/tables/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot

        $(".date").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });

        var dt = $('#mytable').DataTable( {
            'responsive' : true,
            'processing' : true,
            'serverSide' : true,
            'ajax' : {
                'url' : "{{ url('datatable/sms_log/' . $type) }}",
                'type' : 'POST'
            },
            'columns' : [
                { 'data' : 'facilityname' },
                { 'data' : 'patient' },
                { 'data' : 'patient_name' },
                { 'data' : 'age' },
                { 'data' : 'patient_phone_no' },
                { 'data' : 'datecollected' },
                { 'data' : 'datetested' },
                { 'data' : 'result' },
                { 'data' : 'datedispatched' },
                { 'data' : 'time_result_sms_sent' },
                { 'data' : 'action',  'orderable' : false, 'searchable' : false},
            ],
            'order' : [[8, 'desc']]

        } );

    @endcomponent

@endsection