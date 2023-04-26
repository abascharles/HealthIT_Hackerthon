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
            <form method="POST" class="form-horizontal" action='{{ url("/covid_sample/{$sample->id}") }}' >
            @method('PUT')
        @else
            <form method="POST" class="form-horizontal" action='{{ url("/covid_sample/") }}'>
        @endif

        @csrf

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

                        
                        <div class="form-group ampath-div">
                            <label class="col-sm-4 control-label">(*for Ampath Sites only) AMRS Location</label>
                            <div class="col-sm-8">
                                <select class="form-control ampath-only" name="amrs_location">

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
                            <label class="col-sm-4 control-label">(*for Ampath Sites only) AMRS Provider Identifier</label>
                            <div class="col-sm-8">
                                <input class="form-control ampath-only" name="provider_identifier" type="text" value="{{ $sample->provider_identifier ?? '' }}">
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
                        <center>Patient Details</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Patient/Sample Identifier
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control requirable" required name="patient" type="text" value="{{ $sample->patient ?? '' }}" id="patient">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date of Birth
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date date-dob">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="dob" required class="form-control requirable" value="{{ $sample->dob ?? '' }}" name="dob">
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Sex
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control requirable" required name="sex" id="sex">
                                    <option></option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}"

                                        @if (isset($sample) && $sample->sex == $gender->id)
                                            selected
                                        @endif

                                        > {{ $gender->gender_description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Area of Residence
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control requirable" required name="residence" type="text" value="{{ $sample->residence ?? '' }}" id="residence">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Symptoms Began Showing
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date date-normal">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="symptoms_date" required class="form-control requirable" value="{{ $sample->symptoms_date ?? '' }}" name="symptoms_date">
                                </div>
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
                        <center>History of Travel</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">

                        <div class="travel_item" id="first_travel_item">
                            <div class="col-sm-4">
                                <div class="input-group date date-normal">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" placeholder="Date of Travel" class="form-control requirable" disabled name="travel[travel_date][]">
                                </div>
                            </div> 
                            <div class="col-sm-4">
                                <!-- <input class="form-control requirable" disabled placeholder="City Visited" name="travel[city_visited][]" type="text" value="{{ $sample->city_visited ?? '' }}"> -->
                                <select class="form-control requirable city_select" disabled name="travel[city_id][]" ></select>
                            </div>  
                            <div class="col-sm-4">
                                <input class="form-control requirable" disabled placeholder="Duration Visited (In Days)" number="number" name="travel[duration_visited][]" type="text" >
                            </div>
                            <div class="col-sm-12"><br/><br/></div>                      
                        </div>

                        @if(isset($sample))
                            @foreach($sample->travel as $key => $travel)
                                <input type="hidden" name="travel[travel_id][]" value="{{ $travel->id }}">
                                <div class="col-sm-4">
                                    <div class="input-group date date-normal">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" required placeholder="Date of Travel" class="form-control requirable" value="{{ $travel->travel_date ?? '' }}" name="travel[travel_date][]">
                                    </div>
                                </div> 
                                <div class="col-sm-4">
                                    <!-- <input class="form-control requirable" required placeholder="City Visited" name="travel[city_visited][]" type="text" value="{{ $travel->city_visited ?? '' }}"> -->
                                    <select class="form-control requirable saved_city_select" id="saved_city_select_{{ $key }}" name="travel[city_id][]" >
                                        @if($travel->city)
                                            <option value="{{ $travel->city_id }}"> {{ $travel->city->name }} </option>
                                        @endif
                                    </select>

                                </div>  
                                <div class="col-sm-4">
                                    <input class="form-control requirable" required placeholder="Duration Visited (In Days)" number="number" name="travel[duration_visited][]" type="text" value="{{ $travel->duration_visited ?? '' }}">
                                </div>
                                <div class="col-sm-12"><br/><br/></div>    
                            @endforeach
                        @endif

                        <div id="travel_container"></div>

                        <button class="btn btn-success btn-lg" id="add_travel"> Add Travel Detail </button>
                        <button class="btn btn-warning btn-lg" id="remove_travel"> Remove Travel Detail </button>

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
                                <div class="input-group date date-normal">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="datecollected" required class="form-control requirable" value="{{ $sample->datecollected ?? '' }}" name="datecollected">
                                </div>
                            </div>                            
                        </div> 

                        @if(auth()->user()->user_type_id != 5)
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date Received
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date date-normal">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="datereceived" required class="form-control requirable" value="{{ $sample->datereceived ?? $batch->datereceived ?? '' }}" name="datereceived">
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
                        @endif
                        
                        @if(auth()->user()->user_type_id == 5)
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Entered By
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control requirable" required name="entered_by"  type="text" value="{{ $sample->entered_by ?? '' }}">
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
                
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body" style="padding-bottom: 6px;">
                        <div class="form-group"><label class="col-sm-4 control-label">Comments (from facility)</label>
                            <div class="col-sm-8">
                                <textarea  class="form-control" name="comments">{{ $sample->comments ?? '' }}</textarea>
                            </div>
                        </div>
                        @if(auth()->user()->user_type_id != 5)
                            <div class="form-group"><label class="col-sm-4 control-label">Lab Comments</label>
                                <div class="col-sm-8"><textarea  class="form-control" name="labcomment">
                                    {{ $sample->labcomment ?? '' }}
                                </textarea></div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <center>
                        <div class="col-sm-4 col-sm-offset-4">
                            <button class="btn btn-primary" type="submit">
                                @if(isset($sample))
                                    Update Sample
                                @else
                                    Save Sample
                                @endif
                            </button>
                        </div>
                    </center>
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
                @if(auth()->user()->user_type_id != 5)
                    datecollected: {
                        lessThanTwo: ["#datereceived", "Date Collected", "Date Received"]
                    },
                @endif                               
            }
        @endslot

        // $(".date :not(.date-dob, .date-dispatched)").datepicker({

        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
        set_select_facility('.saved_city_select', "{{ url('/city/search') }}", 3, "Search for city", false);

    @endcomponent


    <script type="text/javascript">
        $(document).ready(function(){
            $("#rejection").hide();
            $("#first_travel_item").hide();

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
                startView: 2,
                keyboardNavigation: false,
                forceParse: true,
                autoclose: true,
                startDate: '-100y',
                endDate: "-1m",
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

            @if(!in_array(env('APP_LAB'), $amrs))
                $(".ampath-div").hide();
            @endif 

            @if(env('APP_LAB', 3))
                $(".alupe-div").hide();
            @endif  

            $('#add_travel').click(function(event){
                event.preventDefault();
                // $(".travel_item :first").clone().appendTo("#travel_container");
                $("#first_travel_item").clone().appendTo("#travel_container");

                var rand = Math.floor(Math.random() * 1000);
                var id = 'select_city_'+rand;

                $('#travel_container select').last().attr('id', id);

                $('#travel_container .travel_item').last().show().removeAttr('id');
                $('#travel_container input').attr("required", "required");
                $('#travel_container input').removeAttr("disabled");
                // $('#travel_container select').removeAttr("disabled");

                $('#travel_container select').attr("required", "required");
                $('#travel_container select').removeAttr("disabled");


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

                set_select_facility(id, "{{ url('/city/search') }}", 3, "Search for city", false);
                set_select_facility(id, "{{ url('/city/search') }}", 3, "Search for city", false);
                // set_select_facility(id, "{{ url('/city/search') }}", 3, "Search for city", false);
            });

            $('#remove_travel').click(function(event){
                event.preventDefault();
                $('#travel_container .travel_item').last().remove();
            });


        });


    </script>



@endsection
