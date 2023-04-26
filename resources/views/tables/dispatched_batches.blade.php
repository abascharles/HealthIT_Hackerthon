@extends('layouts.master')

@component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

<div class="content">

    @empty($to_print)

        <div class="row">
            <div class="col-md-12">
                Click To View: 
                {{--<a href="{{ url($pre . 'batch/index') }}">--}}
                <a href="{{ $myurl }}">
                    All Batches
                </a> |
                <a href="{{ $myurl2 }}/0">
                    In-Process Batches
                </a> |
                <a href="{{ $myurl2 }}/2">
                    Awaiting Dispatch
                </a> |
                <a href="{{ $myurl2 }}/1">
                    Dispatched Batches
                </a> |
                <a href="{{ $myurl2 }}/5">
                    Batches Overdue for Receipt at Lab (10 days)
                </a> |
                <a href="{{ url('sample/sms_view') }}">
                    SMS Log
                </a>      
            </div>
        </div>

    @endempty

    <br />

    <form method="post" action="{{ url('/batch/index') }}" class='my_form'>
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

            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Facility</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="facility_id" id="facility_id">
                            <option></option>
                            @if(isset($facility) && $facility)
                                <option value="{{ $facility->id }}" selected>{{ $facility->facilitycode }} {{ $facility->name }}</option>
                            @endif
                        </select>
                    </div>                        
                </div> 
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Subcounty</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="subcounty_id" id="subcounty_id">
                            <option></option>
                            @foreach ($subcounties as $subcounty)
                                <option value="{{ $subcounty->id }}"

                                @if (isset($subcounty_id) && $subcounty_id == $subcounty->id)
                                    selected
                                @endif

                                > {{ $subcounty->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>                        
                </div> 
            </div>
            <div class="col-md-4"> 
                <div class="form-group">
                    <label class="col-sm-3 control-label">Select Partner</label>
                    <div class="col-sm-9">
                        <select class="form-control" name="partner_id" id="partner_id">
                            <option></option>
                            @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}"

                                @if (isset($partner_id) && $partner_id == $partner->id)
                                    selected
                                @endif

                                > {{ $partner->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>                        
                </div> 
            </div>
        </div>

        <br />

        <div class="row">

            <div class="col-md-9"> 
                <div class="form-group">

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
                        <button class="btn btn-primary" id="date_range" name="submit_type" value="date_range" type='submit'>Filter</button>  
                    </div>                         
                </div> 
            </div>


            <div class="col-md-3">
                @if(auth()->user()->user_type_id != 5)
                    <div class="form-group">              
                        <button class="btn btn-primary" name="submit_type" value="excel" type='submit'>Download as Excel</button> 
                    </div> 
                @endif               
            </div>
        </div>
    </form>
    
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Batches table
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <form  method="post" action="{{ url($pre . 'batch/summaries/') }}  " >
                            @csrf
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr class="colhead">
                                        <th rowspan="2">Batch No</th>
                                        <th rowspan="1">Print Multiple</th>
                                        <th rowspan="2">Facility</th>
                                        <th rowspan="1">Date</th>
                                        <th colspan="2"># of samples</th>
                                        <th colspan="2">Date</th>
                                        <th colspan="4">Test Outcomes</th>
                                        <th rowspan="1">Date</th>
                                        <th rowspan="1">TAT</th>
                                        <th rowspan="2">Email</th>
                                        <th rowspan="2">Individual Printed</th>
                                        <th rowspan="2">Summary Printed</th>
                                        <th rowspan="2">Date Viewed</th>
                                        <th rowspan="2">Task</th>
                                    </tr>
                                    <tr>
                                        <th id="check_all">Check All</th>
                                        <th>Received</th>
                                        <th>Received</th>
                                        <th>Rejected</th>
                                        <th>Tested</th>
                                        <th>Updated</th>
                                        <th>+</th>
                                        <th>-</th>
                                        <th>RD</th>
                                        <th>F</th>
                                        <th>Dispatched</th>
                                        <th>(Dys)</th> 
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($batches as $batch)
                                        <tr>
                                            <td> {{ $batch->id }} </td>
                                            <td> 
                                                <div align="center">
                                                    <input name="batch_ids[]" type="checkbox" class="checks" value="{{ $batch->id }}"  />
                                                </div>
                                            </td>
                                            <td> {{ $batch->name }} </td>
                                            <td> {{ $batch->datereceived }} </td> 
                                            <td> {{ $batch->total }} </td> 
                                            <td> {{ $batch->rejected }} </td>

                                            <td> {{ $batch->my_date_format('date_tested') }} </td> 
                                            <td> {{ $batch->my_date_format('date_modified') }} </td> 

                                            <td> {{ $batch->pos }} </td> 
                                            <td> {{ $batch->neg }} </td> 
                                            <td> {{ $batch->redraw }} </td> 
                                            <td> {{ $batch->failed }} </td> 

                                            <td> {{ $batch->datedispatched }} </td>
                                            <td> {{ $batch->tat() }} </td>

                                            @if($batch->sent_email)
                                                <td><strong><div style='color: #00ff00;'>Y</div> {{ $batch->my_date_format('dateemailsent') }} </strong> </td>
                                            @else
                                                <td><strong><div style='color: #ff0000;'>N</div></strong></td>
                                            @endif 

                                            @if($batch->dateindividualresultprinted)
                                                <td><strong><div style='color: #00ff00;'>Y</div> {{ $batch->my_date_format('dateindividualresultprinted') }} </strong> </td>
                                            @else
                                                <td><strong><div style='color: #ff0000;'>N</div></strong></td>
                                            @endif 


                                            @if($batch->datebatchprinted)
                                                <td><strong><div style='color: #00ff00;'>Y</div> {{ $batch->my_date_format('datebatchprinted') }} </strong> </td>
                                            @else
                                                <td><strong><div style='color: #ff0000;'>N</div></strong></td>
                                            @endif

                                            @if($batch->dateviewed)
                                                <td><strong><div style='color: #00ff00;'>Y</div> {{ $batch->my_date_format('dateviewed') }} </strong> </td>
                                            @else
                                                <td><strong><div style='color: #ff0000;'>N</div></strong></td>
                                            @endif


                                            <td> 
                                                <a href="{{ url($pre . 'batch/' . $batch->id) }}">View</a>
                                                | <a href="{{ url($pre . 'batch/summary/' . $batch->id) }}" target="_blank"><i class='fa fa-print'></i> Summary</a> 
                                                | <a href="{{ url($pre . 'batch/individual/' . $batch->id) }}" target="_blank"><i class='fa fa-print'></i> Individual </a> 
                                                {{--| <a href="{{ url($pre . 'batch/individual_pdf/' . $batch->id) }}" target="_blank"><i class='fa fa-print'></i> Individual PDF </a> --}}
                                                | <a href="{{ url($pre . 'batch/envelope/' . $batch->id) }}" target="_blank"><i class='fa fa-envelope'></i> Envelope </a>
                                                | <a href="{{ url($pre . 'batch/email/' . $batch->id) }}"><i class='fa fa-envelope'></i> Email </a>
                                            </td>
                                        </tr>

                                    @endforeach

                                    <tr>
                                        <td colspan="5"> 
                                            <center>
                                                <button class="btn btn-success" type="submit" name="print_type" value="summary">Print Summaries of the Selected Batches</button>
                                            </center>
                                        </td>
                                        <td colspan="6"> 
                                            <center>
                                                <button class="btn btn-success" type="submit" name="print_type" value="individual">Print Individual Results of the Selected Batches</button>
                                            </center>
                                        </td>
                                        <td colspan="5"> 
                                            <center>
                                                <button class="btn btn-success" type="submit" name="print_type" value="envelope">Print Envelopes for the Selected Batches</button>
                                            </center>
                                        </td>
                                    </tr>
                                        
                                </tbody>
                            </table>
                        </form>
                    </div>

                    {{-- {!!  $links !!} --}}

                    {{ $batches->links() }}
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
            localStorage.setItem("base_url", "{{ $myurl ?? '' }}/");

            $(".my_form select").select2({
                placeholder: "Select One",
                allowClear: true
            }); 

            set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

            $(".date").datepicker({
                startView: 0,
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: true,
                autoclose: true,
                format: "yyyy-mm-dd"
            });

            /*$('#submit_date').click(function(){
                var d = $('#filter_date').val();
                window.location.href = localStorage.getItem('base_url') + d;
            });

            $('#date_range').click(function(){
                var from = $('#from_date').val();
                var to = $('#to_date').val();
                window.location.href = localStorage.getItem('base_url') + from + '/' + to;
            });*/

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