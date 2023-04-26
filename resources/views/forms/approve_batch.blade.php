@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')
 
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Approve/Reject Batch No {{ $batch->id }}
                </div>
                <div class="panel-body">

                @if ($errors->any())
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-danger">
                                <center>
                                    The following errors were encountered: <br />
                                    @foreach ($errors->all() as $error)
                                        {{ $error }} <br />
                                    @endforeach
                                </center>
                            </div>
                        </div>                
                    </div>

                    <br />
                @endif

                    <form action="{{ url('/batch/site_approval_group/' . $batch->id) }}" class="form-horizontal" method="POST" id='approve_batch_form'>
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="received_by" value="{{ auth()->user()->id }}">

                        <div class="alert alert-warning">
                            <center>
                                Please fill the date received before proceeding. <br />
                                For every rejected sample, please fill the rejected reason.
                            </center>
                        </div>
                        <br />

                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Batch:</strong> {{ $batch->id  ?? '' }}</p>
                            </div>
                            <div class="col-md-8">
                                <p><strong>Facility:</strong> {{ ($batch->view_facility->facilitycode . ' - ' . $batch->view_facility->name . ' (' . $batch->view_facility->county . ')') ?? '' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p>
                                    <strong>Entry Type: </strong>
                                    @switch($batch->site_entry)
                                        @case(0)
                                            {{ 'Lab Entry' }}
                                            @break
                                        @case(1)
                                            {{ 'Site Entry' }}
                                            @break
                                        @case(2)
                                            {{ 'POC Entry' }}
                                            @break
                                        @default
                                            @break
                                    @endswitch
                                </p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Date Entered:</strong> {{ $batch->my_date_format('created_at') }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Entered By:</strong> 
                                    @if($batch->creator)
                                        @if($batch->creator->full_name != ' ')
                                            {{ $batch->creator->full_name }}
                                        @else
                                            {{ $batch->creator->facility->name ?? '' }} {{ $batch->entered_by ?? '' }}
                                        @endif
                                    @else
                                        {{ $batch->entered_by ?? '' }}
                                    @endif
                                </p>
                            </div>
                            

                            <div class="form-group">
                                <label class="col-md-4 control-label">Date Received
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="datereceived" required class="form-control" name="datereceived" value="{{ $batch->datereceived ?? '' }}">
                                    </div>
                                </div>                            
                            </div>                      
                        </div>
                        <br />
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th colspan="17"><center> Sample Log</center></th>
                                    </tr>
                                    <tr>
                                        <th colspan="2"></th>
                                        <th colspan="5">Patient Information</th>
                                        <th colspan="2">Sample Information</th>
                                        <th colspan="7">Mother Information</th>
                                        <th>Rejected_Reason</th>
                                    </tr>
                                    <tr> 
                                        <th>No</th>
                                        <th id="check_all">Check All</th>

                                        <th>Patient ID</th>
                                        <th>Sex</th>
                                        <th>DOB</th>
                                        <th>Age (Months)</th>
                                        <th>Infant Prophylaxis</th>

                                        <th>Date Collected</th>
                                        <th>Spots</th>

                                        <th>CCC #</th>
                                        <th>Age</th>
                                        <th>Last Vl</th>
                                        <th>PMTCT Intervention</th>
                                        <th>Feeding Type</th>
                                        <th>Entry Point</th>
                                        <th>Task</th>
                                        <th>*if rejected</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @foreach($samples as $key => $sample)
                                        <tr>
                                            <td> {{ $key+1 }} </td>
                                            <td>
                                                <div align='center'>
                                                    <input name='samples[]' type='checkbox' class='checks sample_ids' value='{{ $sample->id }}' />
                                                </div>
                                            </td>

                                            <td> {!! $sample->patient->hyperlink !!} </td>
                                            <td> {{ $sample->patient->gender }} </td>
                                            <td> {{ $sample->patient->my_date_format('dob') }} </td>
                                            <td> {{ $sample->age }} </td>
                                            <td>
                                                @foreach($iprophylaxis as $iproph)
                                                    @if($sample->regimen == $iproph->id)
                                                        {{ $iproph->name }}
                                                    @endif
                                                @endforeach
                                            </td>

                                            <td> {{ $sample->my_date_format('datecollected') }} </td>
                                            <td> 
                                                <input class="form-control" name="spots[]" number="number" min=1 max=5 type="text">
                                            </td>

                                            <td> {{ $sample->patient->mother->ccc_no ?? '' }} </td>
                                            <td> {{ $sample->mother_age }} </td>
                                            <td> {{ $sample->mother_last_result }} </td>
                                            <td>
                                                @foreach($interventions as $intervention)
                                                    @if($sample->mother_prophylaxis == $intervention->id)
                                                        {{ $intervention->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($feedings as $feeding)
                                                    @if($sample->feeding == $feeding->id)
                                                        {{ $feeding->feeding }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($entry_points as $entry_point)
                                                    @if($sample->patient->entry_point == $entry_point->id)
                                                        {{ $entry_point->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ url('/sample/' . $sample->id . '/edit') }} ">View</a> |
                                                <a href="{{ url('/sample/' . $sample->id . '/edit') }} ">Edit</a>
                                            </td>
                                            <td style="width: 1000px;">
                                                <select class="form-control" name="rejectedreason[]" id="rejectedreason_{{$sample->id}}">

                                                    <option></option>
                                                    @foreach ($rejectedreasons as $rejectedreason)
                                                        <option value="{{ $rejectedreason->id }}">
                                                            {{ $rejectedreason->name }}
                                                        </option>
                                                    @endforeach

                                                </select>                                                
                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>

                        <div class="row">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Lab Comments</label>
                                <div class="col-sm-8"><textarea  class="form-control" name="labcomment"></textarea></div>
                            </div>

                            <div class="col-sm-10 col-sm-offset-1">
                                <button class="btn btn-success" type="submit" id="accept_samples" name="submit_type" value="accepted">Mark Selected Samples As Accepted For Testing</button>
                                <button class="btn btn-danger" type="submit" id="reject_samples" name="submit_type" value="rejected">Mark Selected Samples As Rejected [Ensure you selected the rejected reason]</button>
                            </div>                        
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/forms/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot

        $(".date").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        $("#check_all").on('click', function(){
            var str = $(this).html();
            if(str == "Check All"){
                $(".checks").prop('checked', true);
                $(this).html("Uncheck All");
            }
            else{
                $(".checks").prop('checked', false); 
                $(this).html("Check All");           
            }
        });

        /*
        $(".sample_ids").change(function() {
            if(this.checked) {
                val = this.value();
                rej_id = 'rejectedreason_' + val;
                $(rej_id).attr("required", "required");  
                $(rej_id).addClass("requirable");  
            }
            else{
                val = this.value();
                rej_id = 'rejectedreason_' + val;
                $(rej_id).removeAttr("required");  
                $(rej_id).removeClass("requirable");              
            }
        });

        $('#approve_batch_form').submit(function( event ){
            event.preventDefault();
        });

        $('#accept_samples').submit(function( event ){
            
        });

        */

    @endcomponent

    <script type="text/javascript">
        $(document).ready(function(){
            
        });
        
    </script>

@endsection