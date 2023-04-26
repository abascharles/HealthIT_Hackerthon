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
    @php
        $disabled = '';
        if(isset($view))
            $disabled = "disabled";
    @endphp
    <div class="content">
        <div>
        @if(!isset($view))

            @if(isset($sample))
            <form class="form-horizontal" method="POST" id='samples_form' action="{{ url('/cd4/sample/' . $sample->id) }}">
                @method('PUT')
            @else
            <form class="form-horizontal" method="POST" id='samples_form' action="{{ url('/cd4/sample/') }}">
            @endif

                @csrf

            <input type="hidden" value=0 name="new_patient" id="new_patient">
        @endif

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body" style="padding-bottom: 6px;">
                    @if(!isset($view))
                        <div class="alert alert-warning">
                            <center>
                                Please fill the form correctly. <br />
                                Fields with an asterisk(*) are mandatory.
                            </center>
                        </div>
                        <br />

                        @isset($sample)
                            <div class="alert alert-warning">
                                <center>
                                    NB: If you edit the facility name, date received or date dispatched from the facility this will be reflected on the other samples in this batch.
                                </center>
                            </div>
                            <br />
                        @endisset
                    @else
                        <div class="alert alert-warning">
                            <center>
                            @foreach($samplestatus as $samplestatus)
                                @if($samplestatus->id == $sample->status_id)
                                    Sample Status: <strong>{{ $samplestatus->name }}</strong>
                                @endif
                            @endforeach
                            </center>
                        </div>
                    @endif
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Facility 
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control requirable" required name="facility_id" id="facility_id" {{ $disabled }} required>
                                    @isset($sample)
                                        <option value="{{ $sample->facility->id }}" selected>{{ $sample->facility->facilitycode }} - {{ $sample->facility->name }}</option>
                                    @endisset
                                </select>
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <label class="col-sm-4 control-label">AMRS Location</label>
                            <div class="col-sm-8">
                                <select class="form-control ampath-only" name="amrs_location" {{ $disabled }}>

                                  <option></option>
                                  @foreach ($amrs_locations as $amrs_location)
                                      <option value="{{ $amrs_location->id }}"

                                      @if (isset($sample) && $sample->amrs_location == $amrs_location->id)
                                          selected
                                      @endif

                                      > {{ $amrs_location->name }}
                                      </option>
                                  @endforeach

                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group ampath-div">
                            <label class="col-sm-4 control-label">AMRS Provider Identifier</label>
                            <div class="col-sm-8">
                                <input class="form-control ampath-only" name="provider_identifier" type="text" value="{{ $sample->provider_identifier ?? '' }}" {{ $disabled }}>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                        <center>Patinet Information</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Medical Record No. (Ampath #)
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control requirable" required name="medicalrecordno" type="text" value="{{ $sample->patient->medicalrecordno ?? '' }}" id="medicalrecordno" {{ $disabled }}>
                            </div>
                        </div>

                        <div class="form-group ampath-div">
                            <label class="col-sm-4 control-label">Patient Name</label>
                            <div class="col-sm-8">
                                <input class="form-control" name="patient_name" id="patient_name" type="text" value="{{ $sample->patient->patient_name ?? '' }}" {{ $disabled }}>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date of Birth
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="dob" required class="form-control lockable requirable" value="{{ $sample->patient->dob ?? '' }}" name="dob" {{ $disabled }}>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Gender
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control lockable requirable" required name="sex" id="sex" {{ $disabled }}>
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
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                        <center>Sample Information</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date of Collection
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="datecollected" required class="form-control requirable" value="{{ $sample->datecollected ?? '' }}" name="datecollected" {{ $disabled }}>
                                </div>
                            </div>                            
                        </div> 
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Received
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="datereceived" required class="form-control requirable" value="{{ $sample->datereceived ?? '' }}" name="datereceived" {{ $disabled }}>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Received Status
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                    <select class="form-control requirable" required name="receivedstatus" id="receivedstatus" {{ $disabled }}>

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
                                    <select class="form-control" required name="rejectedreason" id="rejectedreason" {{ $disabled }} disabled>

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
                    </div>
                </div>
            </div>
        </div>

                
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body" style="padding-bottom: 6px;">
                        <div class="form-group"><label class="col-sm-4 control-label">Lab Comments</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="labcomment" {{ $disabled }}>{{ $sample->labcomment ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                @if(!isset($view))
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <center>
                    @if(isset($sample))
                        <div class="col-sm-10 col-sm-offset-1">
                            <button class="btn btn-success" type="submit" name="submit_type" value="release">Update sample</button>
                        </div>
                    @else
                        <div class="col-sm-10 col-sm-offset-1">
                            <button class="btn btn-success" type="submit" name="submit_type" value="release">Save & Release sample</button>
                            <button class="btn btn-primary" type="submit" name="submit_type" value="add">Save & Add sample</button>
                            <button class="btn btn-danger" type="submit" formnovalidate name="submit_type" value="cancel">Cancel & Release</button>
                        </div>
                    @endif
                    </center>
                </div>
                @endif
            </div>
        </div>

        @isset($view)
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                        <center>Test Information</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Worksheet
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control requirable" value="{{ $sample->worksheet_id ?? '' }}" name="worksheet_id" {{ $disabled }}>
                            </div>                            
                        </div> 
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Tested
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text"  class="form-control requirable" value="{{ $sample->datetested ?? '' }}" name="datetested" {{ $disabled }}>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">CD3 %
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input type="text"  class="form-control requirable" value="{{ $sample->AVGCD3percentLymph ?? '' }}%" name="AVGCD3percentLymph" {{ $disabled }}>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">CD3 Abs
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input type="text"  class="form-control requirable" value="{{ $sample->AVGCD3AbsCnt ?? '' }}cells/ul" name="AVGCD3AbsCnt" {{ $disabled }}>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">CD4 %
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input type="text"  class="form-control requirable" value="{{ $sample->AVGCD3CD4percentLymph ?? '' }}%" name="AVGCD3CD4percentLymph" {{ $disabled }}>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">CD4 Abs
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input type="text"  class="form-control requirable" value="{{ $sample->AVGCD3CD4AbsCnt ?? '' }}cells/ul" name="AVGCD3CD4AbsCnt" {{ $disabled }}>
                            </div>                            
                        </div>


                        <div class="form-group">
                            <label class="col-sm-4 control-label">Total Lymphocytes
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input type="text"  class="form-control requirable" value="{{ $sample->CD45AbsCnt ?? '' }}%" name="CD45AbsCnt" {{ $disabled }}>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">T HELPER/SUPPRESSOR RATIO
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input type="text"  class="form-control requirable" value="{{ $sample->THelperSuppressorRatio ?? '' }}cells/ul" name="THelperSuppressorRatio" {{ $disabled }}>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Approved (1st)
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text"  class="form-control requirable" value="{{ $sample->dateapproved ?? '' }}" name="dateapproved" {{ $disabled }}>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Approved (2nd)
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text"  class="form-control requirable" value="{{ $sample->dateapproved2 ?? '' }}" name="dateapproved2" {{ $disabled }}>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Result Printed
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text"  class="form-control requirable" value="{{ $sample->dateprinted ?? '' }}" name="dateprinted" {{ $disabled }}>
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Printed By
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input type="text"  class="form-control requirable" value="{{ $sample->printer->full_name ?? '' }}" name="printedby" {{ $disabled }}>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endisset

        @if(!isset($view))
        </form>
        @endif
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
                    lessThanTwo: ["#datereceived", "Date Collected", "Date Received"]
                },
                datedispatched: {
                    lessThan: ["#datereceived", "Date Dispatched From Facility", "Date Received"]
                } 
                               
            }
        @endslot

        $(".date:not(#datedispatched)").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        $("#datedispatched").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: "+7d",
            format: "yyyy-mm-dd"
        });

        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
        set_select_facility("lab_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

    @endcomponent


    <script type="text/javascript">
        $(document).ready(function(){
            $("#rejection").hide();

            @if(isset($sample))                
                @if($sample->receivedstatus == 2)
                    $("#rejection").show();
                    $("#rejectedreason").removeAttr("disabled");
                    $('.requirable').removeAttr("required");
                @endif
            @else
                $("#medicalrecordno").change(function(){
                    var patient = $(this).val();
                    // console.log(patient);
                    // var facility = $("#facility_id").val();
                    check_new_patient(patient);
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

        function check_new_patient(patient){
            $.ajax({
               type: "POST",
               data: {
                patient : patient
               },
               url: "{{ url('/cd4/patient/new') }}",


                success: function(data){
                    data = JSON.parse(data);
                    if(data == null) {
                        ("#patient_name").val();
                        ("#dob").val();
                        ("#sex").val();
                        ("#hidden_patient").val();
                    } else {
                        $("#patient_name").val(data.patient_name);
                        $("#dob").val(data.dob);
                        $("#sex").val(data.sex).change();
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'patient_id',
                            value: patient.id,
                            id: 'hidden_patient',
                            class: 'patient_details'
                        }).appendTo("#samples_form");
                    }
                }
            });

        }
    </script>



@endsection
