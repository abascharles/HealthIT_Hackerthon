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
                                              
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr> 
                                    <th>Serial #</th>
                                    <th>Lab #</th>
                                    <th>Medical Record #</th>
                                    <th>Patient Names</th>
                                    <th>Age</th>
                                    <th>Facility Name</th>
                                    <th>Date Collected</th>
                                    <th>Date Received</th>
                                    <th>Viability</th>
                                    <th>Status</th>
                                    <th>Worksheet</th>
                                    <th>Date Tested</th>
                                    <th>CD4 abs</th>
                                    <th>Date Printed</th>
                                    <th>Task</th>
                                </tr>
                            </thead>
                            <tbody> 
                            @forelse($data->samples as $key => $sample)
                                <tr>
                                    <td>{{ $sample->serial_no ?? '' }}</td>
                                    <td>{{ $sample->id ?? '' }}</td>
                                    <td>{{ $sample->patient->medicalrecordno ?? '' }}</td>
                                    <td>{{ $sample->patient->patient_name ?? '' }}</td>
                                    <td>{{ $sample->patient->age ?? '' }}</td>
                                    <td>{{ $sample->facility->name ?? '' }}</td>
                                    <td>{{ date('d-M-Y', strtotime($sample->datecollected)) }}</td>
                                    <td>@if($sample->datereceived) 
                                            {{ date('d-M-Y', strtotime($sample->datereceived)) }} 
                                        @endif</td>
                                    <td>
                                    @foreach($data->received_statuses as $received_status)
                                        @if($sample->receivedstatus == $received_status->id)
                                            @if($received_status->id == 1)
                                                <label class="label label-success">{{ $received_status->name }}</label>
                                            @elseif($received_status->id == 2)
                                                <label class="label label-danger">{{ $received_status->name }}</label>
                                            @endif
                                        @endif
                                    @endforeach
                                    </td>
                                    <td>
                                        @foreach($data->sample_statuses as $sample_status)
                                            @if($sample->status_id == $sample_status->id)
                                                {{ $sample_status->name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>{{ $sample->worksheet_id ?? '' }}</td>
                                    <td>
                                        @if($sample->datetested) 
                                            {{ date('d-M-Y', strtotime($sample->datetested)) }} 
                                        @endif
                                    </td>
                                    <td>{{ $sample->AVGCD3CD4AbsCnt ?? '' }}</td>
                                    <td>
                                        @if($sample->dateresultprinted) 
                                            {{ date('d-M-Y', strtotime($sample->dateresultprinted)) }} 
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ URL::to('cd4/sample/'.$sample->id) }}" target="blank">Details</a> |
                                        @if($sample->status_id > 1)
                                            <a href="{{ URL::to('cd4/sample/print/'.$sample->id) }}" target="blank">Print</a> |
                                        @endif
                                        @if($sample->status_id == 1 ||$sample->status_id == 2)
                                            <a href="{{ url('cd4/sample/'.$sample->id.'/edit') }}" target="blank">Edit</a> | <a href="{{ url('cd4/sample/delete/'.$sample->id) }}">Delete</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15">No Samples available yet</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        @if(!isset($data->search))
                        <center>
                            {{ $data->samples->links() }}
                        </center>
                        @endif
                    </div>

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