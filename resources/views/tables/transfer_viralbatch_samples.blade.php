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
                                        {{ $batch->creator->facility->name ?? '' }}
                                    @endif
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
                            <div class="col-md-8">
                                <p><strong>Received By:</strong> {{ $batch->receiver->full_name ?? '' }}</p>
                            </div>
                        @endif                        
                    </div>


                    
                    <form  method="post" action="{{ url('viralbatch/transfer/' . $batch->id) }}"  class="confirmSubmit" confirm_message="Are you sure you would like to transfer the selected samples?">
                        @csrf

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th colspan="14"><center> Sample Log</center></th>
                                    </tr>
                                    <tr>
                                        <th colspan="6">Patient Information</th>
                                        <th colspan="3">Sample Information</th>
                                        <th colspan="5">History Information</th>
                                    </tr>
                                    <tr>
                                        <th>Check</th>
                                        <th>#</th>
                                        <th>Patient CCC No</th>
                                        <th>Sex</th>
                                        <th>Age</th>
                                        <th>DOB</th>

                                        <th>Sample Type</th>
                                        <th>Collection Date</th>
                                        <th>Received Status</th>

                                        <th>Current Regimen</th>
                                        <th>ART Initiation Date</th>
                                        <th>Justification</th>
                                        <th>Viral Load</th>
                                        <th>Task</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                <?php $i=1; ?>
                                    @foreach($samples as $key => $sample)
                                        @continue($sample->repeatt == 1)
                                        <tr>                                           
                                            <td>
                                                <div align='center'>
                                                    <input name='samples[]' type='checkbox' class='checks' value='{{ $sample->id }}' />
                                                </div>
                                            </td>
                                            <td> {{ $i++ }} </td>
                                            <td> {{ $sample->patient->patient }} </td>
                                            <td> {{ $sample->patient->gender }} </td>
                                            <td> {{ $sample->age }} </td>
                                            <td> {{ $sample->patient->my_date_format('dob') }} </td>
                                            <td>
                                                @foreach($sample_types as $sample_type)
                                                    @if($sample->sampletype == $sample_type->id)
                                                        {{ $sample_type->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td> {{ $sample->datecollected }} </td>
                                            <td>
                                                @foreach($received_statuses as $received_status)
                                                    @if($sample->receivedstatus == $received_status->id)
                                                        {{ $received_status->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach($prophylaxis as $proph)
                                                    @if($sample->prophylaxis == $proph->id)
                                                        {{ $proph->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td> {{ $sample->patient->initiation_date }} </td>
                                            <td>
                                                @foreach($justifications as $justification)
                                                    @if($sample->justification == $justification->id)
                                                        {{ $justification->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td> {{ $sample->result }} </td>
                                            <td>
                                                <a href="{{ url('/viralsample/' . $sample->id . '/edit') }} ">View</a> |
                                                <a href="{{ url('/viralsample/' . $sample->id . '/edit') }} ">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>

                        <button class="btn btn-primary" type="submit" name="submit_type" value="new_facility">Transfer Selected Samples to a New Facility </button>

                        <button class="btn btn-primary" type="submit" name="submit_type" value="new_batch">Transfer Selected Samples to a New Batch (To enable dispatch) </button>
                    </form>
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