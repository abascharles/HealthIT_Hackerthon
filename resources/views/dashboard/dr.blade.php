@extends('layouts.master')

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

                        <select class="form-control filters" id="filter_groupby">
                            <option></option>
                            <!-- <option value="1"> Partner </option>       -->
                            <option value="2"> County </option>      
                            <option value="3"> Subcounty </option>      
                            <!-- <option value="4"> Ward </option>       -->
                            <option value="5"> Facility </option>      
                            <option value="6"> Project </option>      
                            <option value="7"> Drug Class </option>      
                            <option value="8"> Drug </option>      
                            <!-- <option value="">  </option>       -->
                        </select>



                        <select class="form-control filters" id="filter_drug_class" multiple="multiple">
                            <option></option>
                            @foreach($drug_classes as $drug_class)
                                <option value="{{ $drug_class->id }}"> {{ $drug_class->name }} </option>
                            @endforeach                            
                        </select>

                        <select class="form-control filters" id="filter_drug" multiple="multiple">
                            <option></option>
                            @foreach($drugs as $drug)
                                <option value="{{ $drug->id }}"> {{ $drug->name }} </option>
                            @endforeach                            
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
                        <center> Heat Map </center>
                    </div>
                    <div class="panel-body no-padding">
                        <div id="heat_map"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center> Resistance by Drug </center>
                    </div>
                    <div class="panel-body no-padding">
                        <div id="resistance_by_drug"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center> Heat Map (Current) </center>
                    </div>
                    <div class="panel-body no-padding">
                        <div id="heat_map_current"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="alert alert-success">
                        <center> Resistance by Drug (Current) </center>
                    </div>
                    <div class="panel-body no-padding">
                        <div id="resistance_by_drug_current"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()

@section('scripts')
<script src="{{ asset('vendor/highcharts/highcharts.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/data.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/series-label.js' )}}"></script>
<script src="{{ asset('vendor/highcharts/modules/exporting.js' )}}"></script>

<script type="text/javascript">

    function reload_page()
    {
        $("#resistance_by_drug").html("<center><div class='loader'></div></center>");
        $("#heat_map").html("<center><div class='loader'></div></center>");

        $("#resistance_by_drug_current").html("<center><div class='loader'></div></center>");
        $("#heat_map_current").html("<center><div class='loader'></div></center>");


        $("#resistance_by_drug").load("{{ url('dr_dashboard/drug_resistance') }}");
        $("#heat_map").load("{{ url('dr_dashboard/heat_map') }}");

        $("#resistance_by_drug_current").load("{{ url('dr_dashboard/drug_resistance/1') }}");
        $("#heat_map_current").load("{{ url('dr_dashboard/heat_map/1') }}");

    }

    $().ready(function(){

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