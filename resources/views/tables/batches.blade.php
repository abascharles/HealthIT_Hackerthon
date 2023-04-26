@extends('layouts.master')

@component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

<div class="content">

    @if(!isset($site_approval) && !isset($display_delayed) && !isset($emr_approval))

        <div class="row">
            <div class="col-md-12">
                Click To View: 
                {{--<a href="{{ url($pre . 'batch/index') }}">--}}
                <a href="{{ $myurl2 }}">
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
                <a href="{{ $myurl2 }}/6">
                    Batches Meeting Lab TAT 
                    @if($pre == 'viral')
                        (10 days)
                    @else
                        (5 days)
                    @endif

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

        @if(auth()->user()->user_type_id != 5)

            <form action="{{ url($pre . 'batch/index') }}" class="my_form" method="POST" >
                @csrf

                @isset($to_print)
                    <input type="hidden" name="to_print" value="1">
                @endisset

                <input type="hidden" name="batch_complete" value="{{ $batch_complete }}">

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
                        <div class="form-group">              
                            <button class="btn btn-primary" name="submit_type" value="excel" type='submit'>Download as Excel</button> 
                        </div>                
                    </div>
                </div>
            </form>
        @endif

    @endif
    
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    @if(isset($site_approval))
                        Site Entry Batches Awaiting Approval [{{ $batches->count() }}]
                    @elseif(isset($emr_approval))
                        Emr Entry Batches Awaiting Approval [{{ $batches->count() }}]
                    @else
                        Batches table
                    @endif
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        @if(auth()->user()->is_lab_user() && isset($batch_complete) && $batch_complete == 5)
                        <form method="post" action="{{ url($pre . 'batch/destroy_multiple/') }}" onsubmit="return confirm('Are you sure you want to delete the selected batches?');">
                            @csrf
                        @endif
                            <table class="table table-striped table-bordered table-hover @isset($datatable) data-table @endisset" >
                                <thead>
                                    <tr class="colhead">
                                        @if(auth()->user()->is_lab_user() && isset($batch_complete) && $batch_complete == 5)
                                            <th rowspan="2"  id="check_all">CheckBox</th>
                                        @endif
                                        <th rowspan="2">Batch No</th>

                                        @if(isset($batch_complete) && $batch_complete == 1)
                                            <th>Print Multiple</th>
                                        @endif

                                        <th rowspan="2">Facility</th>
                                        <th colspan="2">Date</th>
                                        <th rowspan="2">Entered By</th>
                                        <th rowspan="2">Received By</th>
                                        <th colspan="4"># of samples</th>

                                        @if(isset($batch_complete) && $batch_complete == 1)
                                            <th rowspan="1">Date</th>
                                        @endif

                                        <th rowspan="1">TAT</th>
                                        <th rowspan="2">Status</th>

                                        @if(isset($batch_complete) && $batch_complete == 1)
                                            <th rowspan="2">Email</th>
                                        @endif
                                        
                                        <th rowspan="2">Task</th>
                                    </tr>
                                    <tr>

                                        @if(isset($batch_complete) && $batch_complete == 1)
                                            <th id="check_all">Check All</th>
                                        @endif

                                        <th>Received</th>
                                        <th>Entered</th>
                                        <th>Total</th>
                                        <th>Rejected</th>
                                        <th>Results</th>
                                        <th>No Result</th>

                                        @if(isset($batch_complete) && $batch_complete == 1)
                                            <th>Dispatched</th>
                                        @endif

                                        <th>(Dys)</th>            
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($batches as $batch)
                                        <tr>
                                            @if(auth()->user()->is_lab_user() && isset($batch_complete) && $batch_complete == 5)
                                                <td>
                                                    <div align='center'>
                                                        <input name='batches[]' type='checkbox' class='checks' value='{{ $batch->id }}' />
                                                    </div>
                                                </td>  
                                            @endif
                                            <td>
                                                <a href="{{ url($pre . 'batch/' . $batch->id) }} ">
                                                    {{ $batch->id }}
                                                </a>
                                            </td>

                                            @if(isset($batch_complete) && $batch_complete == 1)
                                                <td> 
                                                    <div align="center">
                                                        <input name="batch_ids[]" type="checkbox" class="checks" value="{{ $batch->id }}"  />
                                                    </div>
                                                </td>
                                            @endif

                                            <td> {{ $batch->name }} </td>
                                            <td> {{ $batch->datereceived }} </td> 
                                            <td> {{ $batch->datecreated }} </td>
                                            @if($batch->creator == ' ')
                                                <td> {{ $batch->entered_by ?? '' }} </td>
                                            @else
                                                <td> {{ $batch->creator }} </td>
                                            @endif
                                            <td> {{ $batch->receptor }} </td>
                                            <td> {{ $batch->total }} </td>
                                            <td> {{ $batch->rejected }} </td>
                                            <td> {{ $batch->result }} </td>
                                            <td> {{ $batch->noresult }} </td>

                                            @if(isset($batch_complete) && $batch_complete == 1)
                                                <td> {{ $batch->datedispatched }} </td>
                                            @endif
                                            

                                            <td> {{ $batch->tat() }} </td>
                                            <td> 
                                                @if($batch->batch_complete)
                                                    <strong><div style='color: #00ff00;'>Complete</div></strong>
                                                @else
                                                    <strong><div style='color: #ff0000;'>In-Process</div></strong>
                                                @endif
                                            </td>

                                            @if(isset($batch_complete) && $batch_complete == 1)
                                                @if($batch->sent_email)
                                                    <td><strong><div style='color: #00ff00;'>Y</div></strong> </td>
                                                @else
                                                    <td><strong><div style='color: #ff0000;'>N</div></strong></td>
                                                @endif                                                
                                            @endif


                                            <td> 
                                                @if($batch->approval)
                                                    <a href="{{ url($pre . 'batch/site_approval/' . $batch->id) }}">View Samples For Approval ({{ $batch->sample_count ?? 0 }}) </a> |
                                                    <a href="{{ url($pre . 'batch/site_approval_group/' . $batch->id) }}">Approve Samples Group ({{ $batch->sample_count ?? 0 }}) </a> |
                                                @else
                                                    <a href="{{ url($pre . 'batch/' . $batch->id) }}" target="_blank">View</a>

                                                    @if($batch->batch_complete == 1)
                                                        | <a href="{{ url($pre . 'batch/summary/' . $batch->id) }}" target="_blank"><i class='fa fa-print'></i> Summary</a> 
                                                        | <a href="{{ url($pre . 'batch/individual/' . $batch->id) }}" target="_blank"><i class='fa fa-print'></i> Individual </a> 
                                                        | <a href="{{ url($pre . 'batch/envelope/' . $batch->id) }}"><i class='fa fa-envelope'></i> Envelope </a>
                                                        | <a href="{{ url($pre . 'batch/email/' . $batch->id) }}"><i class='fa fa-envelope'></i> Email </a>
                                                    @endif
                                                    @if(in_array(env('APP_LAB'), [5]) && $batch->batch_complete == 0)
                                                        | <a href="{{ url($pre . 'batch/labels/' . $batch->id) }}" target="_blank"><i class='fa fa-print'></i> Print Labels</a>                                                     
                                                    @endif

                                                @endif
                                            </td>
                                        </tr>


                                    @endforeach

                                    @if(isset($batch_complete) && $batch_complete == 1)
                                        <tr>
                                            <td colspan="5"> 
                                                <center>
                                                    <button class="btn btn-success" type="submit" name="print_type" value="summary">Print Summaries of the Selected Batches</button>
                                                </center>
                                            </td>
                                            <td colspan="5"> 
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
                                    @endif

                                    @if(auth()->user()->is_lab_user() && isset($batch_complete) && $batch_complete == 5)
                                        <tr>
                                            <td colspan="1"> </td>  
                                            <td colspan="5"> 
                                                <center>
                                                    <button class="btn btn-warning" type="submit" name="print_type" value="summary">Delete Selected Batches (This is permanent and cannot be reversed)</button>
                                                </center>
                                            </td>
                                            <td colspan="10"> </td>                                            
                                        </tr>

                                    @endif
                                </tbody>
                            </table>
                        @if(auth()->user()->is_lab_user() && isset($batch_complete) && $batch_complete == 5)
                        </form>
                        @endif
                    </div>

                    {{-- {!!  $links !!} --}}
                    @empty($datatable)
                        {{ $batches->links() }}
                    @endempty
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