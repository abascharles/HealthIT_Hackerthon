@extends('layouts.master')

    @component('/forms/css')
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
                    Drug Resistance Bulk Registration
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sample Code / Patient ID</th>
                                    <th>NAT ID</th>
                                    <th>Facility</th>
                                    <th>Lab ID</th>
                                    <th>Date Received</th>
                                    <th>Reason</th>
                                    <th>Patient History</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($samples as $key => $dr_sample)
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $dr_sample->patient ?? '' }} </td>
                                        <td> {{ $dr_sample->nat ?? '' }} </td>
                                        <td> {{ $dr_sample->facilityname ?? '' }} </td>
                                        <td> {{ $dr_sample->id }} </td>
                                        <td> {{ $dr_sample->my_date_format('datereceived') }} </td>
                                        <td> {{ $drug_resistance_reasons->where('id', $dr_sample->dr_reason_id)->first()->name ?? '' }} </td>
                                        <td>
                                            <a href="{{ url('viralpatient/' . $dr_sample->patient_id) }}" target="_blank">
                                                View History 
                                            </a> 
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(isset($create) && $create)

                        @if (isset($drBulkRegistration))
                            {{ Form::open(['url' => '/dr_bulk_registration/' . $drBulkRegistration->id, 'method' => 'put', 'class'=>'form-horizontal', 'target' => '_blank']) }}
                        @else
                            {{ Form::open(['url'=>'/dr_bulk_registration', 'method' => 'post', 'class'=>'form-horizontal', 'id' => 'worksheets_form', 'target' => '_blank']) }}
                            <input type="hidden" value="{{ env('APP_LAB') }}" name="lab_id">
                            <input type="hidden" value="{{ auth()->user()->id }}" name="createdby">
                        @endif


                                <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-4">
                                        <button class="btn btn-success" type="submit"

                                        >Create Bulk Registration</button>
                                    </div>
                                </div>

                        {{ Form::close() }}

                    @else

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="hpanel">
                                    <div class="panel-body"> 
                                        <div class="alert alert-warning">
                                            <center>
                                                There are only {{ $samples->count() }} samples that qualify to be in a worksheet.
                                            </center>
                                        </div>
                                    <br />
                                    </div>
                                </div>
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

    @component('/forms/scripts')

    @endcomponent

@endsection