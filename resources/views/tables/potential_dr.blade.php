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
                    Potential Drug Resistance Patients
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sample Code / Patient ID</th>
                                    <th>Facility</th>
                                    <th>Justification</th>
                                    <th>Date Collected</th>
                                    <th>Date Received</th>
                                    <th>Date Tested</th>
                                    <th>Worksheet</th>
                                    <th>Result</th>
                                    <th>Patient History</th>
                                    <th>Create DR Sample</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($samples as $key => $sample)
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $sample->patient ?? '' }} </td>
                                        <td> {{ $sample->facilityname ?? '' }} </td>
                                        <td> {{ $sample->get_prop_name($justifications, 'justification') }} </td>
                                        <td> {{ $sample->datecollected }} </td>
                                        <td> {{ $sample->datereceived }} </td>
                                        <td> {{ $sample->datetested }} </td>
                                        <td> {{ $sample->worksheet_id }} </td>
                                        <td> {{ $sample->result }} </td>
                                        <td>
                                            <a href="{{ url('viralpatient/' . $sample->patient_id) }}" target="_blank">
                                                View History 
                                            </a> 
                                        </td>
                                        <td>
                                            <a href="{{ url('dr_sample/create_remnant/' . $sample->id) }}" title="Click to select sample for DR testing">
                                                Create Sample 
                                            </a>                                            
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

    @endcomponent

@endsection