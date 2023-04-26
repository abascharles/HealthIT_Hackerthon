@extends('layouts.master')

@component('/forms/css')
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Merge Patient Records
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        <form action="{{ url($submit_url) }}" class="form-horizontal confirmSubmit" method="POST" confirm_message='Are you sure you would like to merge these patient records?'>
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center> </center>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Facility</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled type="text" value="{{ $patient->facility->name ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Patient</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled type="text" value="{{ $patient->patient ?? '' }}">
                                </div>
                            </div>  

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Patients to be merged</label>
                                <div class="col-sm-8">
                                    <select class="form-control" required name="patients[]" id="patients" multiple="multiple">
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Merge Patient Records</button>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

        </form>
      </div>
    </div>

@endsection

@section('scripts')

    @component('/forms/scripts')
        set_select_patient("patients", "{{ $url }}", 2, "Search for patient", false);
    @endcomponent

@endsection
