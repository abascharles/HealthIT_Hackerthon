@extends('layouts.master')

@component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')
 
<div class="content">
    <div class="row">
        <div class="col-md-12">
            Click To View: 
            <a href="{{ url('worksheet/index/0') }}" title="All Worksheets">
                All Worksheets
            </a> |
            <a href="{{ url('worksheet/index/1') }}" title="In-Process Worksheets">
                In-Process Worksheets
            </a> |
            <a href="{{ url('worksheet/index/2') }}" title="Tested Worksheets">
                Tested Worksheets
            </a> |
            <a href="{{ url('worksheet/index/3') }}" title="Approved Worksheets">
                Approved Worksheets
            </a> |
            <a href="{{ url('worksheet/index/4') }}" title="Cancelled Worksheets">
                Cancelled Worksheets
            </a>
        </div>
    </div>

    <br />

    <div class="row">
        <div class="col-md-4"> 
            <div class="form-group">
                <label class="col-sm-2 control-label">Select Date</label>
                <div class="col-sm-8">
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="filter_date" required class="form-control">
                    </div>
                </div> 

                <div class="col-sm-2">                
                    <button class="btn btn-primary" id="submit_date">Filter</button>  
                </div>                         
            </div> 
        </div>

        <div class="col-md-8"> 
            <div class="form-group">

                <label class="col-sm-1 control-label">From:</label>
                <div class="col-sm-4">
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="from_date" required class="form-control">
                    </div>
                </div> 

                <label class="col-sm-1 control-label">To:</label>
                <div class="col-sm-4">
                    <div class="input-group date">
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        <input type="text" id="to_date" required class="form-control">
                    </div>
                </div> 

                <div class="col-sm-2">                
                    <button class="btn btn-primary" id="date_range">Filter</button>  
                </div>                         
            </div> 

        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" >
                        <thead>
                            <tr class="colhead">
                                <th rowspan="2">#</th>
                                <th rowspan="2">Date Created</th>
                                <th rowspan="2">Created By</th>
                                <th rowspan="2">Type</th>
                                <th rowspan="2">Status</th>
                                <th colspan="6">Samples</th>
                                <th colspan="3">Date</th>
                                <th rowspan="2">Task</th>
                            </tr>
                            <tr>
                                <th>POS</th>
                                <th>NEG</th>
                                <th>Failed</th>
                                <th>Redraw</th>
                                <th>No Result</th>
                                <th>Total</th>
                                <th>Run</th>
                                <th>Updated</th>
                                <th>Reviewed</th>                
                            </tr>
                        </thead>
                    </table>
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
    @endcomponent

    <script type="text/javascript">
        $(document).ready(function(){
            localStorage.setItem("base_url", "{{ $myurl }}/");

            $(".date").datepicker({
                startView: 0,
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: true,
                autoclose: true,
                format: "yyyy-mm-dd"
            });

            $('#submit_date').click(function(){
                var d = $('#filter_date').val();
                window.location.href = localStorage.getItem('base_url') + d;
            });

            $('#date_range').click(function(){
                var from = $('#from_date').val();
                var to = $('#to_date').val();
                window.location.href = localStorage.getItem('base_url') + from + '/' + to;
            });

            $('.table').dataTable({
                dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
                buttons: [
                    {extend: 'copy',className: 'btn-sm'},
                    {extend: 'csv',title: 'Download', className: 'btn-sm'},
                    {extend: 'pdf', title: 'Download', className: 'btn-sm'},
                    {extend: 'print',className: 'btn-sm'}
                ],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('worksheetserverside') }}"
            });

        });
        
    </script>

@endsection