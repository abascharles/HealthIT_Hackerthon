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
                        <b>Date Received: {{ date('d-M-Y', strtotime($sample->datereceived)) ?? '' }} </b> <br />
                        <b>Date Entered: {{ date('d-M-Y', strtotime($sample->created_at)) }} </b> <br />
                        @if($sample->high_priority)
                            <b>High Priority sample </b> <br />
                        @endif
                        <br />
                        <br />                        
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th colspan="15"><center> Sample Log</center></th>
                                </tr>
                                <tr>
                                    <th colspan="5">Patient Information</th>
                                    <th colspan="5">Sample Information</th>
                                    <th colspan="5">History Information</th>
                                </tr>
                                <tr>
                                	<th>Lab ID</th>
                                    <th>Patient CCC No</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>DOB</th>

                                    <th>Sample Type</th>
                                    <th>Collection Date</th>
                                    <th>Received Status</th>
                                    <th>Batch</th>
                                    <th>Worksheet</th>

                                    <th>Current Regimen</th>
                                    <th>ART Initiation Date</th>
                                    <th>Justification</th>
                                    <th>Viral Load</th>
                                    <th>Task</th>
                                </tr>
                            </thead>
                            <tbody> 
                                <tr>
                                	<td> {{ $sample->id }} </td>
                                    <td> {!! $sample->get_link('patient_id') !!} </td>
                                    <td>
                                        @foreach($genders as $gender)
                                            @if($sample->sex == $gender->id)
                                                {{ $gender->gender_description }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td> {{ $sample->age }} </td>
                                    <td> {{ $sample->dob }} </td>
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
                                    <td> {!! $sample->get_link('batch_id') !!} </td>
                                    <td> {!! $sample->get_link('worksheet_id') !!} </td>
                                    <td>
                                        @foreach($prophylaxis as $proph)
                                            @if($sample->prophylaxis == $proph->id)
                                                {{ $proph->name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td> {{ $sample->initiation_date }} </td>
                                    <td>
                                        @foreach($justifications as $justification)
                                            @if($sample->justification == $justification->id)
                                                {{ $justification->name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td> {{ $sample->result }} </td>
                                    <td>
                                        @if($sample->batch_complete == 1)
                                            <a href="{{ url('/viralsample/print/' . $sample->id ) }} " target='_blank'>Print</a> |
                                        @endif
                                        <a href="{{ url('/viralsample/' . $sample->id . '/edit') }} ">View</a> |
                                        <a href="{{ url('/viralsample/' . $sample->id . '/edit') }} ">Edit</a> |

                                        <form action="{{ url('viralsample/' . $sample->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following sample?');">
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
                                            <td> {{ $samp->result }} </td>
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