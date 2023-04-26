@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data-table" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Facility</th>
                                    <th>Patient Name</th>
                                    <th>Sex</th>
                                    <th>DOB</th>
                                    <th>Initiation Date</th>
                                    <th># Samples</th>
                                    <th>Edit Patient</th>
                                    <th>Merge With Another Patient</th>
                                    <th>Transfer to Another Facility</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($patients as $key => $patient)
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $patient->patient ?? '' }} </td>
                                        <td> {{ $facility->name ?? $patient->facility->name ?? '' }} </td>
                                        <td> {{ $patient->patient_name ?? '' }} </td>
                                        <td> {{ $patient->gender }} </td>
                                        <td> {{ $patient->my_date_format('dob') ?? '' }} </td>
                                        <td> {{ $patient->my_date_format('initiation_date') ?? '' }} </td>
                                        <td> {{ $patient->sample_count ?? '' }} </td>

                                        <td>
                                            <a href="{{ url('/viralpatient/' . $patient->id . '/edit' ) }} " target='_blank'>Edit</a>
                                        </td>

                                        <td>
                                            <a href="{{ url('/viralpatient/' . $patient->id . '/merge' ) }} " target='_blank'>Merge</a>
                                        </td>

                                        <td>
                                            <a href="{{ url('/viralpatient/' . $patient->id . '/transfer' ) }} " target='_blank'>Transfer</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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