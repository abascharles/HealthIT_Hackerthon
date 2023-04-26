@extends('layouts.master')

@component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')
 
<div class="content">
    <div class="row">
        <div class="col-md-12">
            Click To View: 
            <a href="{{ url($link_extra . 'pool/index/0') }}" title="All Pools">
                All Pools
            </a> |
            <a href="{{ url($link_extra . 'pool/index/1') }}" title="In-Process Pools">
                In-Process Pools
            </a> |
            <a href="{{ url($link_extra . 'pool/index/12') }}" title="In-Process Pools">
                In-Process Pools (With Reruns)
            </a> |
            <a href="{{ url($link_extra . 'pool/index/2') }}" title="Tested Pools">
                Tested Pools
            </a> |
            <a href="{{ url($link_extra . 'pool/index/3') }}" title="Approved Pools">
                Approved Pools
            </a> |
            <a href="{{ url($link_extra . 'pool/index/4') }}" title="Cancelled Pools">
                Cancelled Pools
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
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Pools
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" >
                        <thead>
                            <tr class="colhead">
                                <th>W No</th>
                                <th>Date Created</th>
                                <th>Created By</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Worksheets</th>
                                <th>Task</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pools as $key => $pool)
                                <tr>
                                    <td>{{ $pool->id }} </td>
                                    <td> {{ $pool->my_date_format('created_at') }} </td>
                                    <td> {{ $pool->creator->full_name ?? '' }} </td>
                                    <td> {!! $pool->machine !!} </td>
                                    <td> {!! $pool->status !!} </td>
                                    <td>
                                        @foreach($pool->worksheet as $worksheet)
                                            {!! $worksheet->hyper_link !!} <br />
                                        @endforeach
                                    </td>
                                    <td> 
                                        <a href="{{ url('covid_pool/' . $pool->id) }}" title="Click to Show Worksheet Pool" target='_blank'>
                                            Show
                                        </a> | 
                                        <a href="{{ url('covid_pool/print/' . $pool->id) }}" title="Click to Download Worksheet Pool" target='_blank'>
                                            Print
                                        </a> | 
                                        <a href="{{ url('covid_pool/cancel/' . $pool->id) }}" title="Click to Cancel Worksheet Pool">
                                            Cancel
                                        </a> | 

                                        <!-- $pool->mylinks  -->
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $pools->links() }} 
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

        });
        
    </script>

@endsection