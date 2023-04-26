@extends('layouts.master')

@section('css_scripts')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endsection()

@section('content')
<style type="text/css">
    .list-group {
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .list-group-item {
        margin-bottom: 4px;
        margin-top: 4px;
    }
</style>
<div class="p-lg">
    <div class="content animate-panel" data-child="hpanel" style="background-color: white;">
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body no-padding">

                        <div class="col-md-12"> 
                            <div class="form-group">

                                <label class="col-sm-1 control-label">From:</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="from_date" required class="form-control" value="{{ date('Y-m-d', strtotime('-' . date('z') .' days')) }}">
                                    </div>
                                </div> 

                                <label class="col-sm-1 control-label">To:</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="to_date" required class="form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                </div> 

                                <div class="col-sm-2">                
                                    <button class="btn btn-primary" id="date_range">Filter</button>  
                                </div>                         
                            </div> 

                        </div>

                        <select class="form-control filters" id="filter_groupby">
                            <option></option>
                            <!-- <option value="1"> Partner </option>       -->
                            <option value="2"> County </option>      
                            <option value="3"> Subcounty </option>      
                            <!-- <option value="4"> Ward </option>       -->
                            <option value="5"> Facility </option>      
                            <option value="6"> Project </option>   
                            <option value="11"> Year </option>      
                            <option value="12"> Month </option>      
                            <!-- <option value="">  </option>       -->
                        </select>


                        <select class="form-control filters" id="filter_county" multiple="multiple">
                            <option></option>
                            @foreach($counties as $county)
                                <option value="{{ $county->id }}"> {{ $county->name }} </option>
                            @endforeach                            
                        </select>

                        <select class="form-control filters" id="filter_subcounty" multiple="multiple">
                            <option></option>
                            @foreach($subcounties as $subcounty)
                                <option value="{{ $subcounty->id }}"> {{ $subcounty->name }} </option>
                            @endforeach                            
                        </select>

                        <select class="form-control filters" id="filter_project" multiple="multiple">
                            <option></option>
                            @foreach($dr_projects as $dr_project)
                                <option value="{{ $dr_project->id }}"> {{ $dr_project->name }} </option>
                            @endforeach                            
                        </select>

                        <select class="form-control filters" id="filter_facility">
                        </select>

                    </div>
                </div>
            </div>
        </div>
    <!-- <div class="animate-panel"  data-child="hpanel" data-effect="fadeInDown"> -->
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center> Testing </center>
                    </div>
                    <div class="panel-body no-padding">
                        <div id="testing"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center> Rejected </center>
                    </div>
                    <div class="panel-body no-padding">
                        <div id="rejected"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()

@section('scripts')
<script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('vendor/highcharts/highcharts.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/data.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/series-label.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/exporting.js' )}}"></script>

<script type="text/javascript">

    function reload_page()
    {
        $("#testing").html("<center><div class='loader'></div></center>");
        $("#rejected").html("<center><div class='loader'></div></center>");

        $("#testing").load("{{ url('dr_testing/testing') }}");
        $("#rejected").load("{{ url('dr_testing/rejected') }}");
    }

    $().ready(function(){

        $(".date").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });

        $("#filter_groupby").select2({
            placeholder: "Select Group By",
            allowClear: true
        }); 

        $("#filter_drug_class").select2({
            placeholder: "Select Drug Class",
            allowClear: true
        }); 

        $("#filter_drug").select2({
            placeholder: "Select Drug",
            allowClear: true
        }); 

        $("#filter_county").select2({
            placeholder: "Select County",
            allowClear: true
        }); 

        $("#filter_subcounty").select2({
            placeholder: "Select Subcounty",
            allowClear: true
        }); 

        $("#filter_project").select2({
            placeholder: "Select Project",
            allowClear: true
        }); 

        $('#date_range').click(function(){
            var from = $('#from_date').val();
            var to = $('#to_date').val();

            var posting = $.post( "{{ url('dr_dashboard/filter_date') }}", { 'start_date': from, 'end_date': to } );

            posting.done(function( data ) {
                // console.log(data);
                reload_page();
            });

            posting.fail(function( data ) {
                // console.log(data);
                // location.reload(true);
            });

        });

        set_select_facility("filter_facility", "{{ url('/facility/search') }}", 3, "Select Facility", false);
        
        reload_page();    

        $(".filters").change(function(){
            em = $(this).val();
            id = $(this).attr('id');

            var posting = $.post( "{{ url('dr_dashboard/filter_any') }}", { 'session_var': id, 'value': em } );

            posting.done(function( data ) {
                // console.log(data);
                reload_page();
            });

            posting.fail(function( data ) {
                // console.log(data);
                // location.reload(true);
            });
        }); 

    });
    
</script>
@endsection