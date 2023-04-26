@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div>
                        <b>Facility: {{ $sample->facility->name ?? '' }} </b> <br />
                        <b>Date Received: {{ date('d-M-Y', strtotime($sample->datereceived)) }} </b> <br />
                        <b>Date Entered: {{ date('d-M-Y', strtotime($sample->created_at)) }} </b> <br />
                        <br />
                        <br />                        
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th colspan="10"><center> Sample Log</center></th>
                                </tr>
                                <tr>
                                    <th colspan="4">Patient Information</th>
                                    <th colspan="4">Sample Information</th>
                                    <th rowspan="2">Task</th>
                                </tr>
                                <tr>
                                    <th>Lab ID</th>
                                    <th>Patient ID</th>
                                    <th>Age (Months)</th>
                                    <th>Entry Point</th>

                                    <th>Date Collected</th>
                                    <th>Status</th>
                                    <th>Worksheet</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <!-- {{ $sample->patient }} -->
                            <tbody> 
                                <tr>
                                    <td> {{ $sample->id }} </td>
                                    <td> {!! $sample->get_link('patient_id') !!} </td>
                                    <td> {{ $sample->age }} </td>
                                    <td> {{ $sample->entry_point }} </td>
                                    <td> {{ $sample->datecollected }} </td>
                                    <td>
                                        @foreach($receivedstatuses as $received_status)
                                            @if($sample->receivedstatus == $received_status->id)
                                                {{ $received_status->name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td> {!! $sample->get_link('worksheet_id') !!} </td>
                                    <td>
                                        @foreach($results as $result)
                                            @if($sample->result == $result->id)
                                                {{ $result->name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($sample->result)
                                            <a href="{{ url('/cancersample/print/' . $sample->id ) }} " target='_blank'>Print</a> |
                                        @endif
                                        <a href="{{ url('/cancersample/' . $sample->id . '/edit') }} ">View</a> |
                                        <a href="{{ url('/cancersample/' . $sample->id . '/edit') }} ">Edit</a> |

                                        <form action="{{ url('cancersample/' . $sample->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following sample?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-primary">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->user_type_id != 5)
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading">
                        <div class="panel-tools">
                            <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                            <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                        </div>
                        Sample Runs
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover data-table" >
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sample Code / Patient ID</th>
                                        <th>Lab ID</th>
                                        <th>Original Lab ID</th>
                                        <th>Run</th>
                                        <th>Date Sample Drawn</th>
                                        <th>Date Tested</th>
                                        <th>Worksheet</th>
                                        <th>Interpretation</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @foreach($samples as $key => $samp)
                                        <tr>
                                            <td> {{ $key+1 }} </td>
                                            <td> {{ $patient->patient }} </td>
                                            <td> {{ $samp->id }} </td>
                                            <td> {{ $samp->parentid }} </td>
                                            <td> {{ $samp->run }} </td>
                                            <td> {{ $samp->datecollected }} </td>
                                            <td> {{ $samp->datetested }} </td>
                                            <td> {{ $samp->worksheet_id }} </td>
                                            <td> {{ $samp->interpretation }} </td>
                                            <td> {{ $samp->result_name }} </td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection