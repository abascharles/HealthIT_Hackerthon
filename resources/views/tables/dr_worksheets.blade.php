@extends('layouts.master')

@component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

<div class="content">
    <div class="row">
        <div class="col-md-12">
            Click To View: 
            <a href="{{ url('dr_worksheet/index/0') }}" title="All Worksheets">
                All Worksheets
            </a> |
            <a href="{{ url('dr_worksheet/index/1') }}" title="In-Process Worksheets">
                In-Process Worksheets
            </a> |
            <a href="{{ url('dr_worksheet/index/2') }}" title="Tested Worksheets">
                Tested Worksheets
            </a> |
            <a href="{{ url('dr_worksheet/index/3') }}" title="Approved Worksheets">
                Approved Worksheets
            </a> |
            <a href="{{ url('dr_worksheet/index/4') }}" title="Cancelled Worksheets">
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
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Worksheets
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" >
                        <thead>
                            <tr>
                                    <th> W No </th>
                                    <th> Extraction W No </th>
                                    <th> Date Created </th>
                                    <th> Created By </th>
                                    <th> Reviewed By </th>
                                    <th> Status </th>
                                    <th> Hyrax Status </th>
                                    <th> # Samples </th>
                                    <!-- <th> Date Run </th> -->
                                    <th> Date Updated </th>
                                    <th> Date Reviewed </th>
                                    <th> Task </th>                 
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($worksheets as $key => $worksheet)
                                <tr>
                                    <td>{{ $worksheet->id }} </td>
                                    <td>{{ $worksheet->extraction_worksheet_id }} </td>
                                    <td> {{ $worksheet->my_date_format('created_at') }} </td>
                                    <td> {{ $worksheet->creator->full_name ?? '' }} </td>
                                    <td> {{ $worksheet->reviewer->full_name ?? '' }} </td>

                                    <td> {!! $worksheet_statuses->where('id', $worksheet->status_id)->first()->output ?? '' !!} </td>
                                    <td> {!! $dr_plate_statuses->where('id', $worksheet->exatype_status_id)->first()->output ?? $worksheet->exatype_status_id !!} </td>

                                    <td> {{ $worksheet->sample_count }} </td>

                                    <td> {{ $worksheet->my_date_format('dateuploaded') }} </td>
                                    <td> {{ $worksheet->my_date_format('datereviewed') }} </td>
                                    <td> 
                                        @include('shared.dr_links', ['worksheet' => $worksheet])
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $worksheets->links() }}
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