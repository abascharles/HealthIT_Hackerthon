@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
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
                                        {{ $batch->creator->full_name ?? '' }}
                                    @else
                                        {{ $batch->creator->facility->name ?? '' }}
                                    @endif
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Date Received:</strong> {{ $batch->my_date_format('datereceived')  ?? '' }}</p>
                        </div>
                        <div class="col-md-8">
                            <p><strong>Received By:</strong> {{ $batch->receiver->full_name ?? '' }}</p>
                        </div>                       
                    </div>


                    
                    <form  method="post" action="{{ url('batch/transfer/' . $batch->id) }}"  class="confirmSubmit" confirm_message="Are you sure you would like to transfer the selected samples?">
                        @csrf


                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th colspan="18"><center> Sample Log</center></th>
                                    </tr>
                                    <tr>
                                        <th colspan="7">Patient Information</th>
                                        <th colspan="3">Sample Information</th>
                                        <th colspan="8">Mother Information</th>
                                    </tr>
                                    <tr> 
                                        <th>Check</th>
                                        <th>No</th>
                                        <th>Patient ID</th>
                                        <th>Sex</th>
                                        <th>DOB</th>
                                        <th>Age (Months)</th>
                                        <th>Infant Prophylaxis</th>

                                        <th>Date Collected</th>
                                        <th>Status</th>
                                        <th>Spots</th>

                                        <th>CCC #</th>
                                        <th>Age</th>
                                        <th>Last Vl</th>
                                        <th>PMTCT Intervention</th>
                                        <th>Feeding Type</th>
                                        <th>Entry Point</th>
                                        <th>Result</th>
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
                                                @foreach($received_statuses as $received_status)
                                                    @if($sample->receivedstatus == $received_status->id)
                                                        {{ $received_status->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td> {{ $sample->spots }} </td>

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
                                                @foreach($results as $result)
                                                    @if($sample->result == $result->id)
                                                        {{ $result->name }}
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <a href="{{ url('/sample/' . $sample->id . '/edit') }} ">View</a> |
                                                <a href="{{ url('/sample/' . $sample->id . '/edit') }} ">Edit</a>
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