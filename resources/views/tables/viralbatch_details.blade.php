@extends('layouts.master')

    @component('/tables/css')
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
                    Batch Details
                </div>
                <div class="panel-body">
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
                        @if($batch->high_priority)
                            <div class="col-md-4">
                                <p><strong>Date Received:</strong> {{ $batch->my_date_format('datereceived')  ?? '' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Received By:</strong> {{ $batch->receiver->full_name ?? '' }}</p>
                            </div>
                            <div class="col-md-4">
                                <span class="alert alert-warning">High Priority Batch</span>
                            </div>
                        @else
                            <div class="col-md-4">
                                <p><strong>Date Received:</strong> {{ $batch->my_date_format('datereceived')  ?? '' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Received By:</strong> {{ $batch->receiver->full_name ?? '' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Date Dispatched:</strong> {{ $batch->my_date_format('datedispatched')  ?? '' }}</p>
                            </div>
                        @endif                        
                    </div>
                    @if(auth()->user()->user_type_id != 5)
                        <div class="row">
                            @if(($batch->site_entry == 1 || ($batch->site_entry == 0 && $batch->user_id == 0 && !$batch->batch_complete )) && (!$batch->datereceived || 
                            ($batch->datereceived && $samples->where('receivedstatus', null)->first())
                            ) )
                                <div class="col-md-4">
                                    <a href="{{ url('viralbatch/site_approval_group/' . $batch->id) }} ">
                                        <button class="btn btn-primary">Approve Site Entry</button>
                                    </a>
                                </div>
                            @endif


                            @if($batch->site_entry == 2)
                                <div class="col-md-4">
                                    <a href="{{ url('viralbatch/convert_from_poc/' . $batch->id) }} ">
                                        <button class="btn btn-primary">Convert to Site Entry</button>
                                    </a>
                                </div>
                            @endif

                            <div class="col-md-4 pull-right">
                                <a href="{{ url('viralbatch/transfer/' . $batch->id) }} ">
                                    <button class="btn btn-primary">Transfer Samples To Another Batch</button>
                                </a>
                            </div>
                        </div>
                        <br />
                    @endif
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th colspan="16"><center> Sample Log</center></th>
                                </tr>
                                <tr>
                                    <th colspan="6">Patient Information</th>
                                    <th colspan="4">Sample Information</th>
                                    <th colspan="6">History Information</th>
                                </tr>
                                <tr>
                                    <th>#</th>
                                    <th>Lab ID</th>
                                    <th>Patient CCC No</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>DOB</th>

                                    <th>Sample Type</th>
                                    <th>Collection Date</th>
                                    <th>Received Status</th>
                                    <th>Worksheet</th>

                                    <th>Current Regimen</th>
                                    <th>ART Initiation Date</th>
                                    <th>Justification</th>
                                    <th>Viral Load</th>
                                    <th>Task</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <?php $i=1; ?>
                                @foreach($samples as $key => $sample)
                                    @continue($sample->repeatt == 1)
                                    <tr>
                                        <td> {{ $i++ }} </td>
                                        <td> {{ $sample->id }} </td>
                                        <td> {!! $sample->patient->hyperlink !!} </td>
                                        <td> {{ $sample->patient->gender }} </td>
                                        <td> {{ $sample->age }} </td>
                                        <td> {{ $sample->patient->my_date_format('dob') }} </td>
                                        <td> {{ $sample->get_prop_name($sample_types, 'sampletype') }} </td>
                                        <td> {{ $sample->datecollected }} </td>
                                        <td> 
                                            {{ $sample->get_prop_name($received_statuses, 'receivedstatus') }} 
                                            @if($sample->receivedstatus == 2)
                                               <b>({{ $sample->get_prop_name($viral_rejected_reasons, 'rejectedreason') }})</b>
                                            @endif
                                        </td>
                                        <td> {!! $sample->get_link('worksheet_id') !!} </td>
                                        <td> {{ $sample->get_prop_name($prophylaxis, 'prophylaxis') }} </td>
                                        <td> {{ $sample->patient->my_date_format('initiation_date') }} </td>
                                        <td> {{ $sample->get_prop_name($justifications, 'justification') }} </td>
                                        <td> {{ $sample->result }}  
                                            @if(is_numeric($sample->result))
                                                {{ $sample->units }}
                                            @endif
                                        </td>
                                        <td>
                                            @if(auth()->user()->user_type_id != 5 && $batch->batch_complete == 0 && env('APP_LAB') == 3 && !$sample->worksheet_id && $sample->receivedstatus == 1 && (($sample->sample_received_by && $sample->sample_received_by != auth()->user()->id) || 
                                            (!$sample->sample_received_by && $batch->received_by != auth()->user()->id) ))
                                                <a href="{{ url('/sample/transfer/' . $sample->id ) }}">Transfer To My Account</a> |
                                            @endif
                                            
                                            @if($batch->batch_complete == 1)
                                                <a href="{{ url('/viralsample/print/' . $sample->id ) }} " target='_blank'>Print</a> |
                                            @endif
                                            <a href="{{ url('/viralsample/' . $sample->id ) }} ">View</a> |
                                            <a href="{{ url('/viralsample/' . $sample->id . '/edit') }} ">Edit</a> |

                                            @if(auth()->user()->is_lab_user())
                                                @if($batch->batch_complete == 0 && $sample->receivedstatus == 1 && !$sample->worksheet_id && !$sample->result && $sample->run > 1)
                                                    | <a href="{{ url('/viralsample/release/' . $sample->id ) }} ">Release As Redraw</a> 
                                                @endif
                                                @if($sample->result == 'Collect New Sample' && $sample->age_in_months < 4)
                                                    | <a href="{{ url('/viralsample/return_for_testing/' . $sample->id ) }}">Return for Testing</a> 
                                                @endif
                                                @if($batch->batch_complete == 0 && $sample->receivedstatus)
                                                    | <a href="{{ url('/viralsample/unreceive/' . $sample->id ) }}">Unreceive Sample</a> 
                                                @endif
                                            @endif
                                        </td>

                                        <td>
                                            @if($batch->batch_complete == 0 && $sample->result == null && $sample->worksheet_id == null && $sample->run < 2 && $sample->receivedstatus != 2)
                                                <form action="{{ url('viralsample/' . $sample->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following sample?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-primary">Delete</button>
                                                </form>
                                            @endif                                            
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>

                    @if($batch->batch_complete == 1)
                        <br />
                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{ url('viralbatch/summary/' . $batch->id) }}">
                                    <button class="btn btn-primary">Download Batch Summary</button>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="{{ url('viralbatch/individual/' . $batch->id) }}" target="_blank">
                                    <button class="btn btn-primary">Print Individual Results</button>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection