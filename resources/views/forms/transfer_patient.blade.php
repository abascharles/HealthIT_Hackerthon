@extends('layouts.master')

@component('/forms/css')
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Transfer Patient to Another Facility
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        <form action="{{ url($submit_url) }}" class="form-horizontal confirmSubmit" method="POST" confirm_message='Are you sure you would like to transfer this patient to another facility?'>
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
                                <label class="col-sm-4 control-label">Current Facility</label>
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
                                <label class="col-sm-4 control-label">Facility</label>
                                <div class="col-sm-8">
                                    <select class="form-control" required name="facility_id" id="facility_id">
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Transfer Patient</button>
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
        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
    @endcomponent

@endsection
