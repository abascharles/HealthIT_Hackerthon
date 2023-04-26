@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('custom_css')
    <style type="text/css">
        .hpanel {
            margin-bottom: 4px;
        }
        .hpanel.panel-heading {
            padding-bottom: 2px;
            padding-top: 4px;
        }
    </style>
@endsection

@section('content')

    <div class="content">
        <div>
            @if(isset($sample))
            <form action="{{ url('/cancersample/' . $sample->id) }}" class="form-horizontal" method="POST" id='samples_form'>
                @method('PUT')
            @else
            <form action="{{ url('/cancersample') }}" class="form-horizontal" method="POST" id='samples_form'>
            @endif
            @csrf()

            <input type="hidden" value=0 name="new_patient" id="new_patient">

            @if ($errors->any())
                <div class="row">
                    <div class="col-lg-12">
                        <div class="hpanel">
                            <div class="panel-body" style="padding-bottom: 6px;">
                                <div class="alert alert-danger">
                                    <center>
                                        The sample was not saved due to the following errors: <br />
                                        @foreach ($errors->all() as $error)
                                            {{ $error }} <br />
                                        @endforeach
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>                
                </div>
            @endif
            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-body" style="padding-bottom: 6px;">

                            <div class="alert alert-warning">
                                <center>
                                    Please fill the form correctly. <br />
                                    Fields with an asterisk(*) are mandatory.
                                </center>
                            </div>
                            <br />
  
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Facility 
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control requirable" required name="facility_id" id="facility_id">
                                        @isset($sample)
                                            <option value="{{ $sample->facility->id }}" selected>{{ $sample->facility->facilitycode }} {{ $sample->facility->name }}</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center>Patient Information</center>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Patient / Sample ID
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control requirable" required name="patient" type="text" value="{{ $sample->patient->patient ?? '' }}" id="patient">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Patient Names</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="patient_name" type="text" value="{{ $sample->patient->patient_name ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Date Of Birth
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-3">
                                    <div class="input-group date date-dob">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="dob" class="form-control lockable"
                                        @if(auth()->user()->user_type_id == 5)
                                            required
                                        @endif
                                         value="{{ $sample->patient->dob ?? '' }}" name="dob">
                                    </div>
                                </div>
                                <label class="col-sm-1 control-label">
                                    <strong> OR </strong>
                                </label>
                                <label class="col-sm-2 control-label" for="age">Age (In Years)
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-3">
                                    <input class="form-control requirable" required name="age" type="number" value="{{ $sample->age ?? '' }}" id="age" min="0" max="120">
                                </div>                            
                            </div>

                            {{--
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sex
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control lockable requirable" required name="sex" id="sex">

                                        <option></option>
                                        @foreach ($genders as $gender)
                                            <option value="{{ $gender->id }}"

                                            @if (isset($sample) && $sample->patient->sex == $gender->id)
                                                selected
                                            @endif

                                            > {{ $gender->gender_description }}
                                            </option>
                                        @endforeach


                                    </select>
                                </div>
                            </div>
                            --}}
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Entry Point</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="entry_point" type="text" value="{{ $sample->patient->entry_point ?? '' }}">
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sample Type
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control requirable" required name="sampletype" id="sampletype">
                                        <option></option>
                                        @foreach ($sampletypes as $sampletype)
                                            <option value="{{ $sampletype->id }}"

                                            @if (isset($sample) && $sample->sample_type == $sampletype->id)
                                                selected
                                            @endif

                                            >{{ $sampletype->id }} &nbsp; {{ $sampletype->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">HIV Status
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control lockable requirable" required name="hiv_status" id="hiv_status">

                                        <option></option>
                                        @foreach ($hivstatuses as $status)
                                            <option value="{{ $status->id }}"

                                            @if (isset($sample) && $sample->patient->hiv_status == $status->id)
                                                selected
                                            @endif

                                            > {{ $status->name }}
                                            </option>
                                        @endforeach


                                    </select>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>                      

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date of Collection
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date date-normal">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="datecollected" required class="form-control requirable" value="{{ $sample->datecollected ?? '' }}" name="datecollected">
                                    </div>
                                </div>                            
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date Received
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date date-normal">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="datereceived" required class="form-control requirable" value="{{ $sample->datereceived ?? '' }}" name="datereceived">
                                    </div>
                                </div>                            
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Received Status
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                        <select class="form-control requirable" required name="receivedstatus" id="receivedstatus">

                                        <option></option>
                                        @foreach ($receivedstatuses as $receivedstatus)
                                            <option value="{{ $receivedstatus->id }}"

                                            @if (isset($sample) && $sample->receivedstatus == $receivedstatus->id)
                                                selected
                                            @endif

                                            > {{ $receivedstatus->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="rejection" >
                                <label class="col-sm-4 control-label">Rejected Reason</label>
                                <div class="col-sm-8">
                                        <select class="form-control" required name="rejectedreason" id="rejectedreason" disabled>

                                        <option></option>
                                        @foreach ($rejectedreasons as $rejectedreason)
                                            <option value="{{ $rejectedreason->id }}"

                                            @if (isset($sample) && $sample->rejectedreason == $rejectedreason->id)
                                                selected
                                            @endif

                                            > {{ $rejectedreason->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            
                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Justification
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control requirable" required name="justification" id="justification">
                                        <option></option>
                                        @foreach ($justifications as $justification)
                                            @continue($justification->id == 8 && auth()->user()->user_type_id == 5)
                                            <option value="{{ $justification->id }}"

                                            @if (isset($sample) && $sample->justification == $justification->id)
                                                selected
                                            @endif

                                            > {{ $justification->displaylabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            
                            <div class="hr-line-dashed"></div> 

                            <div class="form-group">
                                <center>
                                @isset($sample)
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <button class="btn btn-success" type="submit" name="submit_type" value="update">Update sample</button>
                                    </div>
                                @else
                                    <div class="col-sm-10 col-sm-offset-1">
                                        <button class="btn btn-success" type="submit" name="submit_type" value="release">Save & Release sample</button>
                                        <button class="btn btn-primary" type="submit" name="submit_type" value="add">Save & Add sample</button>
                                    </div>
                                @endisset
                                </center>
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
                    lessThan: ["#datecollected", "Date of Birth", "Date Collected"]
                },
                datecollected: {
                    lessThan: ["#datedispatched", "Date Collected", "Date Dispatched From Facility"],
                    @if(auth()->user()->user_type_id != 5)
                        lessThanTwo: ["#datereceived", "Date Collected", "Date Received"]
                    @endif
                },
                datedispatched: {
                    lessThan: ["#datereceived", "Date Dispatched From Facility", "Date Received"]
                } 
                               
            }
        @endslot

        // $(".date :not(.date-dob, .date-dispatched)").datepicker({
        $(".date-normal").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: "-6m",
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        $(".date-dob").datepicker({
            startView: 1,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: "-1w",
            format: "yyyy-mm-dd"
        });

        $(".date-dispatched").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: "-6m",
            endDate: "+7d",
            format: "yyyy-mm-dd"
        });

        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
        set_select_facility("lab_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

    @endcomponent


    <script type="text/javascript">
        $(document).ready(function(){
            $("#rejection").hide();

            @if(env('APP_LAB') == 8 && auth()->user()->is_lab_user() && !isset($sample))
                $("#samples_form input,select").change(function(){
                    var frm = $('#samples_form');
                    // var data = JSON.stringify(frm.serializeObject());
                    var data = frm.serializeObject();
                    console.log(data);
                });  
            @endif

            @if(isset($sample))                
                @if($sample->receivedstatus == 2)
                    $("#rejection").show();
                    $("#rejectedreason").removeAttr("disabled");
                    $('.requirable').removeAttr("required");
                @endif
            @else
                $("#patient").blur(function(){
                    var patient = $(this).val();
                    var facility = $("#facility_id").val();
                    check_new_patient(patient, facility);
                });
            @endif

            $("#facility_id").change(function(){
                var val = $(this).val();

                if(val == 7148 || val == '7148'){
                    $('.requirable').removeAttr("required");
                }
                else{
                    $('.requirable').attr("required", "required");
                }
            }); 



            $("#receivedstatus").change(function(){
                var val = $(this).val();
                if(val == 2){
                    $("#rejection").show();
                    $("#rejectedreason").removeAttr("disabled");
                    $('.requirable').removeAttr("required");
                    // $("#rejectedreason").prop('disabled', false);
                }
                else{
                    $("#rejection").hide();
                    $("#rejectedreason").attr("disabled", "disabled");
                    $('.requirable').attr("required", "required");
                    // $("#enrollment_ccc_no").attr("disabled", "disabled");
                    // $("#rejectedreason").prop('disabled', true);

                }
            }); 

            $("#pcrtype").change(function(){
                var val = $(this).val();
                if(val == 4){
                    $("#enrollment_ccc_no").removeAttr("disabled");
                }
                else{
                    $("#enrollment_ccc_no").attr("disabled", "disabled");
                }
            }); 

        });

        $("#dob").change(function(){
            const dob = new Date($(this).val());
            const today = new Date();
            let yearsDiff =  today.getFullYear() - dob.getFullYear();
            $("#age").val(yearsDiff);
        });

        $("#age").change(function() {
            const age = $(this).val();
            const today = new Date;
            let dobYear = today.getFullYear() - age;
            // let dob = new Date(dobYear,today.getMonth(), today.getDate());
            $("#dob").val([dobYear, ("0" + (today.getMonth() + 1)).slice(-2), ("0" + today.getDate()).slice(-2)].join('-'));
        });


        function check_new_patient(patient, facility_id){
            $.ajax({
               type: "POST",
               data: {
                patient : patient,
                facility_id : facility_id
               },
               url: "{{ url('/sample/new_patient') }}",


               success: function(data){

                    console.log(data);

                    $("#new_patient").val(data[0]);

                    if(data[0] == 0){
                        localStorage.setItem("new_patient", 0);
                        var patient = data[1];
                        var mother = data[2];
                        var prev = data[3];

                        console.log(patient.dob);

                        $("#dob").val(patient.dob);
                        
                        $("#patient_name").val(patient.patient_name);
                        $("#patient_phone_no").val(patient.patient_phone_no);
                        $("#sex").val(patient.sex).change();
                        $("#entry_point").val(patient.entry_point).change();
                        $("#mother_age").val(mother.age);
                        // $("#hiv_status").val(mother.hiv_status).change();
                        $("#ccc_no").val(mother.ccc_no).change();
                        $("#pcrtype").val(prev.recommended_pcr).change();

                        $('<input>').attr({
                            type: 'hidden',
                            name: 'patient_id',
                            value: patient.id,
                            id: 'hidden_patient',
                            class: 'patient_details'
                        }).appendTo("#samples_form");

                        if(data[4] != 0)
                        {
                            set_message(data[4]);
                        }
                    }
                    else{
                        localStorage.setItem("new_patient", 1);
                        $('#pcrtype option[value=1]').attr('selected','selected').change();
                        $('.patient_details').remove();
                    }

                }
            });

        }
    </script>



@endsection
