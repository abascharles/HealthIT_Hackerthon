@extends('layouts.master')
    @component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
    @endcomponent
@section('content')
<div class="content">


        <form action="{{ url('traveller/print_multiple') }}" class="my_form" method="POST" >
            @csrf

            <div class="row">
                <!-- <div class="col-md-9">  -->
                    <!-- <div class="form-group"> -->

                        <label class="col-sm-1 control-label">From:</label>
                        <div class="col-sm-4">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" id="from_date" name="from_date" class="form-control">
                            </div>
                        </div> 

                        <label class="col-sm-1 control-label">To:</label>
                        <div class="col-sm-4">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" id="to_date" name="to_date" class="form-control">
                            </div>
                        </div> 

                        <div class="col-sm-2">                
                            <button class="btn btn-primary" id="date_range" name="submit_type" value="date_range" type='submit'>Print</button>  
                        </div>                         
                    <!-- </div>  -->
                <!-- </div> -->
            </div>
        </form>

    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="mytable">
                            <thead>
                                <tr class="colhead">
                                    <th rowspan="2">Lab ID</th>
                                    <th rowspan="2">Patient Name</th>
                                    <th rowspan="2">National ID/PP</th>
                                    <th rowspan="2">Sex</th>
                                    <th rowspan="2">Age</th>
                                    <th colspan="4"><span style="text-align: center;"> Date </span></th>
                                    <th colspan="3"><span style="text-align: center;"> Results </span></th>
                                    <!-- <th colspan="3">Results</th>                                     -->
                                    <th rowspan="2">Task</th>
                                </tr>
                                <tr>
                                    <th>Collected</th>
                                    <th>Received</th>
                                    <th>Tested</th>
                                    <th>Dispatched</th>  

                                    <th>PCR</th>    
                                    <th>IgM</th>    
                                    <th>IgG/IgM</th>    
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
                'url' : "{{ url('traveller/filter/') }}",
                'type' : 'POST'
            },
            'columns' : [
                { 'data' : 'id' },
                { 'data' : 'patient_name' },
                { 'data' : 'id_passport' },
                { 'data' : 'sex' },
                { 'data' : 'age' },
                { 'data' : 'datecollected' },
                { 'data' : 'datereceived' },
                { 'data' : 'datetested' },
                { 'data' : 'datedispatched' },
                { 'data' : 'result', 'orderable' : false, 'searchable' : false },
                { 'data' : 'igm_result', 'orderable' : false, 'searchable' : false },
                { 'data' : 'igg_igm_result', 'orderable' : false, 'searchable' : false },
                { 'data' : 'action', 'orderable' : false, 'searchable' : false},
            ],
            'order' : [[0, 'desc']]
        } );

    @endcomponent

@endsection