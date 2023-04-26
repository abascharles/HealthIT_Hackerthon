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
                    Drug Resistance Patients
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sample Code / Patient ID</th>
                                    <th>Facility</th>
                                    <th>Lab ID</th>
                                    <th>Date Received</th>
                                    <th>Result</th>
                                    <th>Reason</th>
                                    <th>DR Worksheet</th>
                                    <th>Patient History</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($dr_patients as $key => $dr_patient)
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $dr_patient->patient->patient ?? '' }} </td>
                                        <td> {{ $dr_patient->patient->facility->name ?? '' }} </td>
                                        <td> {{ $dr_patient->id }} </td>
                                        <td> {{ $dr_patient->datereceived }} </td>
                                        <td> {{ $dr_patient->result }} </td>
                                        <td> {{ $drug_resistance_reasons->where('id', $dr_patient->dr_reason_id)->first()->name ?? '' }} </td>
                                        <td></td>
                                        <td>
                                            <a href="{{ url('viralpatient/' . $dr_patient->patient->id) }}" target="_blank">
                                                View History 
                                            </a>  |
                                            @if($dr_patient->status_id == 1)
                                                <a href="{{ url('dr_sample/create/' . $dr_patient->id) }}">
                                                    Create Sample 
                                                </a>  |
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>

                    {{ $dr_patients->links() }}
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