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
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Facility</th>
                                    <th>Patient</th>
                                    <th>Full Name</th>
                                    <th>Age</th>
                                    <th>Phone #</th>
                                    <th>Date Collected</th>
                                    <th>Date Tested</th>
                                    <th>Result</th>
                                    <th>Date SMS Sent</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($samples as $key => $sample)
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $sample->facilityname }} </td>
                                        <td> {{ $sample->patient }} </td>
                                        <td> {{ $sample->full_name }} </td>
                                        <td> {{ $sample->age }} </td>
                                        <td> {{ $sample->patient_phone_no }} </td>
                                        <td> {{ $sample->my_date_format('datecollected') }} </td>
                                        <td> {{ $sample->my_date_format('datetested') }} </td>

                                        <td> 
                                            @if($pre == '')
                                                {{ $results->where('id', $sample->result)->first()->name ?? '' }}
                                            @else
                                                {{ $sample->result }}
                                            @endif 
                                        </td>
                                        <td> {{ $sample->my_time_format('time_result_sms_sent') }} </td>
                                        <td>
                                            <a href="{{ url($pre . 'sample/sms/' . $sample->id) }}">Resend SMS</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    {{ $samples->links() }}
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