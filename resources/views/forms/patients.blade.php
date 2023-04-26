@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Update Patient
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        <form action="{{ url('/patient/' . $patient->id) }}" class="form-horizontal" method="POST" confirm_message="Are you sure you would like to update this patient?">
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
                                <label class="col-sm-4 control-label">Patient HEI number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" value="{{ $patient->patient ?? '' }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date of Birth</label>
                                <div class="col-sm-8">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="dob" required class="form-control lockable" value="{{ $patient->dob ?? '' }}" name="dob">
                                    </div>
                                </div>                            
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sex</label>
                                <div class="col-sm-8">
                                    <select class="form-control lockable" required name="sex" id="sex">
                                        <option value=""> Select One </option>
                                        @foreach ($genders as $gender)
                                            <option value="{{ $gender->id }}"

                                            @if (isset($patient) && $patient->sex == $gender->id)
                                                selected
                                            @endif

                                            > {{ $gender->gender_description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Entry Point</label>
                                <div class="col-sm-8">
                                    <select class="form-control lockable" required name="entry_point" id="entry_point">

                                        <option value=""> Select One </option>
                                        @foreach ($entry_points as $entry_point)
                                            <option value="{{ $entry_point->id }}"

                                            @if (isset($patient) && $patient->entry_point == $entry_point->id)
                                                selected
                                            @endif

                                            > {{ $entry_point->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Enrollment CCC No</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="enrollment_ccc_no" type="text" value="{{ $patient->ccc_no ?? '' }}">
                                </div>
                            </div>


                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <label class="col-sm-4 control-label">Mother's Age</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="mother_age" name="mother_age" type="text" value="{{ $patient->mother->age ?? '' }}" number="number" min=10 max=70>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Mother's CCC No</label>
                                <div class="col-sm-8">
                                    <input class="form-control" id="ccc_no" name="ccc_no" type="text" value="{{ $patient->mother->ccc_no ?? '' }}">
                                </div>
                            </div>


                            <div class="hr-line-dashed"></div>




                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Update Patient</button>
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
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot

        $(".date").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

    @endcomponent

@endsection
