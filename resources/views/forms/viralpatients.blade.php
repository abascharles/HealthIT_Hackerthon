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

        <form action="{{ url('viralpatient/' . $patient->id) }}" class="form-horizontal" method="POST" confirm_message='Are you sure you would like to update this patient?'>
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
                                <label class="col-sm-4 control-label">Patient CCC number</label>
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
                                <label class="col-sm-4 control-label">ART Inititation Date</label>
                                <div class="col-sm-8">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="initiation_date" class="form-control lockable" value="{{ $patient->initiation_date ?? '' }}" name="initiation_date">
                                    </div>
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


        @slot('val_rules')
           ,
            rules: {
                dob: {
                    lessThan: ["#initiation_date", "Date of Birth", "ART Inititation Date"]
                }               
            }
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
