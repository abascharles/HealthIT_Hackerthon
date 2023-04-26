@extends('layouts.master')

@component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

<div class="content">

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

        <form action="{{ url('/traveller/print_multiple') }}" method="POST" class="my_form">
            @csrf

            @isset($to_print)
                <input type="hidden" name="to_print" value="1">
            @endisset


            <div class="row">

                <div class="alert alert-success">
                    <center>
                        Select facility and/or partner and/or subcounty. <br />
                        If you wish to get for a particular day, set only the From field. Set the To field also to get for a date range. <br />
                        Click on filter to get the list of batches based on selected criteria. <br />
                        The Download As Excel depends on all the selected criteria.
                    </center>
                </div>
                
                <br />

            </div>

            <br />


            <div class="row">

                <div class="col-md-8"> 
                    <div class="form-group">

                        <label class="col-sm-1 control-label">From:</label>
                        <div class="col-sm-4">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" id="date_start" name="date_start" class="form-control">
                            </div>
                        </div> 

                        <label class="col-sm-1 control-label">To:</label>
                        <div class="col-sm-4">
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input type="text" id="date_end" name="date_end" class="form-control">
                            </div>
                        </div> 

                        <div class="col-sm-2">                
                            <button class="btn btn-primary" id="date_range" name="submit_type" value="date_range" type='submit'>Filter</button>  
                        </div>                         
                    </div> 
                </div>

                <div class="col-md-4">
                    <div class="form-group">              
                        <button class="btn btn-primary" name="submit_type" value="excel" type='submit'>Download as Excel</button> 
                        <button class="btn btn-primary" name="submit_type" value="email" type='submit'>Email Results</button> 
                        <button class="btn btn-primary" name="submit_type" value="multiple_results" type='submit'>Download Results</button> 
                    </div>                
                </div>
            </div>

        </form>


    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    Covid-19 Traveller Samples
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr class="colhead">
                                    <th rowspan="2">Lab ID</th>
                                    <th rowspan="2">Patient Name</th>
                                    <th rowspan="2">National ID/PP</th>
                                    <th rowspan="2">Sex</th>
                                    <th rowspan="2">Age</th>
                                    <th colspan="4">Date</th>
                                    <th colspan="3">Results</th>                                    
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
                            <tbody>
                                @foreach($samples as $sample)
                                    <tr>
                                        <td> {{ $sample->id }} </td>
                                        <td> {{ $sample->patient_name }} </td>
                                        <td> {{ $sample->id_passport }} </td>
                                        <td> {{ $sample->gender }} </td>
                                        <td> {{ $sample->age }} </td>
                                        <td> {{ $sample->my_date_format('datecollected') }} </td>
                                        <td> {{ $sample->my_date_format('datereceived') }} </td>

                                        <td> {{ $sample->my_date_format('datetested') }} </td>
                                        <td> {{ $sample->my_date_format('datedispatched') }} </td>

                                        <td> {!! $sample->get_prop_name($results, 'result', 'name_colour') !!} </td>
                                        <td> {!! $sample->get_prop_name($results, 'igm_result', 'name_colour') !!} </td>
                                        <td> {!! $sample->get_prop_name($results, 'igg_igm_result', 'name_colour') !!} </td>

                                        <td>
                                            {!! $sample->edit_link !!}  |
                                            <a href="/traveller/{{ $sample->id }}">Result</a> |
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $samples->links() }}
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
            
            set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

            // $("#check_all").on('click', function(){
            //     var str = $(this).html();
            //     if(str == "Check All"){
            //         $(this).html("Uncheck All");
            //         $(".checks").prop('checked', true);
            //     }
            //     else{
            //         $(this).html("Check All");
            //         $(".checks").prop('checked', false);           
            //     }
            // });

            $("#select_lab").select2({
                placeholder: "Select a Lab",
                allowClear: true
            }); 


            $(".select_tag").select2({
                placeholder: "Select One",
                allowClear: true
            }); 



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

            $("#check_all").on('click', function(){
                var str = $(this).html();
                if(str == "Check All"){
                    $(this).html("Uncheck All");
                    $(".checks").prop('checked', true);
                }
                else{
                    $(this).html("Check All");
                    $(".checks").prop('checked', false);           
                }
            });

        });
        
    </script>

@endsection