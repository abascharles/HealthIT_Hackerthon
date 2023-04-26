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
            <?php $m = $sample ?? null; $model = $sample ?? null; ?>

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

        <div id="cif_covid_samples"></div>

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body" style="padding-bottom: 6px;">

                        <div class="alert alert-warning">
                            <center>
                                Please fill the form correctly. <br />
                                Fields with an asterisk(*) are mandatory. <br />
                                For samples for Air Travel, indicate it on the justification. <br />
                                The system will automatically pick the county and subcounty of the facility. If the patient does not hail from the subcounty or county of the facility, select a subcounty and county. 
                                @if(!auth()->user()->is_covid_lab_user())
                                    <br />
                                    Kindly indicate on the form or sample that the data has already been filled. This will help the people at the lab know that the data has been entered.
                                @endif
                            </center>
                        </div>
                        <br />

                        @if(session('last_covid_sample'))
                            <div class="alert alert-success">
                                <center>
                                    The sample added has been assigned lab id {{ session('last_covid_sample') }}.
                                </center>
                            </div>
                            <br />
                        @endif

                        <div class="alert alert-info" id="new_patient_div">
                            <center id="new_patient_info">

                            </center>
                        </div>

                        @include('partial.input', ['model' => $m, 'required' => true, 'prop' => 'patient_name', 'default_val' => $sample->patient->patient_name ?? null, 'label' => 'Patient Name'])
  
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Facility</label>
                            <div class="col-sm-8">
                                <select class="form-control" @if((env('APP_LAB') == 4 && !$m) || auth()->user()->facility_user) required @endif name="facility_id" id="facility_id">
                                    @if(isset($sample) && $sample->patient->facility)
                                        <option value="{{ $sample->patient->facility->id }}" selected>{{ $sample->patient->facility->facilitycode }} {{ $sample->patient->facility->name }}</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        @if(auth()->user()->lab_id == 1 && auth()->user()->user_type_id)
                            @include('partial.input', ['model' => $m, 'prop' => 'kemri_id', 'label' => 'KEMRI ID', 'required' => true])
                        @endif

                        @if(auth()->user()->lab_id == 25 || (auth()->user()->lab_id == 1 && !auth()->user()->user_type_id))
                            @include('partial.input', ['model' => $m, 'prop' => 'kemri_id', 'label' => 'AMREF ID'])
                        @endif


                        @if(auth()->user()->is_covid_lab_user())
                            <div class="form-group">
                                <label class="col-sm-4 control-label">High Priority</label>
                                <div class="col-sm-8">
                                <input type="checkbox" class="i-checks" name="highpriority" value="1"
                                    @if(isset($sample) && $sample->highpriority)
                                        checked
                                    @endif
                                 />
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
                    <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                        <center>Patient Information</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">

                        @if(auth()->user()->quarantine_site)
                            <input type="hidden" name="quarantine_site_id" value="{{ auth()->user()->facility_id }}">
                        @elseif(auth()->user()->facility_user)

                        @else
                            @if(auth()->user()->lab_id == 1 && auth()->user()->is_covid_lab_user())
                                @include('partial.select', ['model' => $m, 'required' => true, 'default_val' => $sample->patient->quarantine_site_id ?? null, 'prop' => 'quarantine_site_id', 'label' => 'Quarantine Site', 'items' => $quarantine_sites])
                            @else
                                @include('partial.select', ['model' => $m, 'default_val' => $sample->patient->quarantine_site_id ?? null, 'prop' => 'quarantine_site_id', 'label' => 'Quarantine Site', 'items' => $quarantine_sites])
                            @endif
                        @endif

                        @include('partial.select', ['model' => $m, 'default_val' => $sample->patient->nationality ?? null, 'prop' => 'nationality', 'label' => 'Nationality', 'items' => $nationalities, 'facility_required' => true])

                        @include('partial.input', ['model' => $m, 'prop' => 'identifier', 'default_val' => $sample->patient->identifier ?? null, 'required' => true, 'label' => 'Patient Identifier'])

                        @if(env('APP_LAB') == 2)
                            @include('partial.input', ['model' => $m, 'prop' => 'national_id', 'default_val' => $sample->patient->national_id ?? null, 'label' => 'National ID / Passport Number (Enter No Data if absent)', 'required' => true])
                        @else
                            @include('partial.input', ['model' => $m, 'prop' => 'national_id', 'default_val' => $sample->patient->national_id ?? null, 'label' => 'National ID / Passport Number (If any)'])
                        @endif

                        @include('partial.select', ['model' => $m, 'required' => true, 'default_val' => $sample->patient->county_id ?? null, 'prop' => 'county_id', 'label' => 'County', 'items' => $countys])

                        @include('partial.select', ['model' => $m, 'default_val' => $sample->patient->subcounty_id ?? null, 'prop' => 'subcounty_id', 'label' => 'Subcounty', 'items' => $districts])


                        @include('partial.input', ['model' => $m, 'prop' => 'email_address', 'default_val' => $sample->patient->email_address ?? null, 'label' => 'Email Address'])


                        @if(env('APP_LAB') == 2)
                            @include('partial.input', ['model' => $m, 'prop' => 'phone_no', 'default_val' => $sample->patient->phone_no ?? null, 'label' => 'Phone Number', 'required' => true])
                        @else
                            @include('partial.input', ['model' => $m, 'prop' => 'phone_no', 'default_val' => $sample->patient->phone_no ?? null, 'label' => 'Phone Number', 'facility_required' => true])
                        @endif


                        @include('partial.date', ['model' => $m, 'prop' => 'dob', 'label' => 'Date of Birth', 'default_val' => $sample->patient->dob ?? null, 'class' => 'date-dob'])

                        @include('partial.input', ['model' => $m, 'prop' => 'age', 'is_number' => true, 'label' => 'Age'])

                        @include('partial.select', ['model' => $m, 'prop' => 'sex', 'default_val' => $sample->patient->sex ?? null, 'required' => true, 'label' => 'Sex', 'items' => $gender, 'prop2' => 'gender_description'])

                        @include('partial.input', ['model' => $m, 'prop' => 'residence', 'default_val' => $sample->patient->residence ?? null, 'label' => 'Area of Residence', 'facility_required' => true])

                        @include('partial.select', ['model' => $m, 'prop' => 'vaccination_status', 'label' => 'Vaccination Status', 'items' => $covid_vaccination_statuses, ])

                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                        <center>Clinical Information</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">

                        @include('partial.select', ['model' => $m, 'required' => true, 'prop' => 'justification', 'label' => 'Justification', 'items' => $covid_justifications, 'default_val' => $sample->patient->justification ?? null])

                        @include('partial.select', ['model' => $m, 'required' => true, 'prop' => 'test_type', 'label' => 'Test Type', 'items' => $covid_test_types, ])

                        <div @if(env('APP_LAB') == 3) style="display: none;"  @endif >

                        @include('partial.date', ['model' => $m, 'prop' => 'date_symptoms', 'label' => 'Date Symptoms Began Showing', 'default_val' => $sample->patient->date_symptoms ?? null,])

                        @include('partial.date', ['model' => $m, 'prop' => 'date_admission', 'label' => 'Date of Admission to Hospital', 'default_val' => $sample->patient->date_admission ?? null,])

                        @include('partial.input', ['model' => $m, 'prop' => 'hospital_admitted', 'default_val' => $sample->patient->hospital_admitted ?? null, 'label' => 'Hospital Admitted'])

                        @include('partial.date', ['model' => $m, 'prop' => 'date_isolation', 'label' => 'Date of Isolation', 'default_val' => $sample->patient->date_isolation ?? null,])

                        @include('partial.select', ['model' => $m, 'prop' => 'health_status', 'label' => 'Health Status', 'items' => $health_statuses])

                        @include('partial.date', ['model' => $m, 'prop' => 'date_death', 'label' => 'Date of Death', 'default_val' => $sample->patient->date_death ?? null,])

                        @include('partial.select_multiple', ['model' => $m, 'prop' => 'symptoms', 'label' => 'Symptoms', 'items' => $covid_symptoms])

                        @include('partial.input', ['model' => $m, 'prop' => 'temperature', 'is_number' => true, 'label' => 'Temperature (Celcius)'])

                        @include('partial.select_multiple', ['model' => $m, 'prop' => 'observed_signs', 'label' => 'Observed Signs', 'items' => $observed_signs])

                        @include('partial.select_multiple', ['model' => $m, 'prop' => 'underlying_conditions', 'label' => 'Underlying Conditions', 'items' => $underlying_conditions])

                        </div>



                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                        <center>Exposure and travel information in the 14 days prior to symptom onset (prior to reporting if asymptomatic)</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">

                        @include('partial.input', ['model' => $m, 'prop' => 'occupation', 'default_val' => $sample->patient->occupation ?? null, 'label' => 'Occupation', 'facility_required' => true])

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

                        @if(isset($sample) && $sample->patient->travel)
                            @foreach($sample->patient->travel as $key => $travel)
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
                                        @if($travel->town)
                                            <option value="{{ $travel->city_id }}"> {{ $travel->town->name }} </option>
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

                        @include('partial.select', ['model' => $m, 'required' => true, 'prop' => 'sample_type', 'label' => 'Sample Type', 'items' => $covid_sample_types, ])

                        @include('partial.date', ['model' => $m, 'required' => true, 'prop' => 'datecollected', 'label' => 'Date of Collection',])

                        @if(auth()->user()->lab_user || auth()->user()->other_lab)

                            @include('partial.date', ['model' => $m, 'required' => true, 'prop' => 'datereceived', 'label' => 'Date of Received',])

                            @include('partial.select', ['model' => $m, 'required' => true, 'prop' => 'receivedstatus', 'label' => 'Received Status', 'items' => $receivedstatus, ])

                            @include('partial.select', ['model' => $m, 'row_attr' => "id='rejection'", 'prop' => 'rejectedreason', 'label' => 'Rejected Reason', 'items' => $viralrejectedreasons, ])

                            @if(auth()->user()->other_lab || env('APP_LAB') == 7)

                                @include('partial.date', ['model' => $m, 'prop' => 'datetested', 'label' => 'Date Tested',])

                                @include('partial.select', ['model' => $m, 'prop' => 'result', 'label' => 'Result', 'items' => $results, ])

                            @endif

                            @if(Str::contains(url()->current(), 'antigen') || ($m && $m->antigen))

                                <input type="hidden" name="antigen" value="1">

                                @include('partial.input', ['prop' => 'assay_kit_name', 'label' => 'Assay Kit Name'])
                                @include('partial.input', ['prop' => 'lot_no', 'label' => 'Lot Number'])

                                @include('partial.date', ['prop' => 'kit_expiry', 'label' => 'Kit Expiry',])

                                @include('partial.date', ['prop' => 'datetested', 'label' => 'Date Tested', 'required' => true])

                                @include('partial.select', ['prop' => 'result', 'label' => 'Result', 'items' => $results, 'required' => true])

                            @endif

                        @endif
                        
                        @if(!auth()->user()->is_covid_lab_user())

                            @include('partial.input', ['model' => $m, 'prop' => 'entered_by', 'label' => 'Entered By'])

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
                        @if(auth()->user()->is_covid_lab_user())
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
                county_id: {
                    // required: "#facility_id:blank"
                    /*required: function(element){
                        return $("#facility_id").val().length == 0;
                    }*/
                },
                subcounty_id: {
                    /*required: function(element){
                        return $("#facility_id").val().length == 0;
                    }*/
                },
                age: {
                    required: '#dob:blank'
                },
                dob: {
                    lessThan: ["#datecollected", "Date of Birth", "Date Collected"]
                },
                @if(auth()->user()->lab_user || auth()->user()->other_lab)
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
            $("#new_patient_div").hide();

            @if(isset($sample))                
                @if($sample->receivedstatus == 2)
                    $("#rejection").show();
                    $("#rejectedreason").removeAttr("disabled");
                    $('.requirable').removeAttr("required");
                @endif
            @else
                $("#identifier").blur(function(){
                    check_new_patient();
                    @if(in_array(env('APP_LAB'), [1,2,3,6]))
                        check_cif_patient();
                    @endif
                });
                $("#national_id").blur(function(){
                    check_new_patient();
                    @if(in_array(env('APP_LAB'), [1,2,3,6]))
                        check_cif_patient();
                    @endif
                });

                @if(in_array(env('APP_LAB'), [1,2,3,6]))
                    $("#patient_name").blur(function(){
                        check_cif_patient();
                    });
                @endif
            @endif


            @if(auth()->user()->user_type_id == 5)
                $("#age").change(function(){
                    var val = $(this).val();

                    // $('#county_id').removeAttr("required");

                    if(val > 17){
                        $('#national_id').attr("required", "required");
                    }
                    else{
                        $('#national_id').removeAttr("required");
                    }
                });
            @endif

            $("#justification").change(function(){
                var val = $(this).val();
                if(val == 16){
                    $("#national_id").attr("required", "required");
                    $("#age").attr("required", "required");
                    $("#phone_no").attr("required", "required");
                    $("#occupation").attr("required", "required");
                    $("#email_address").attr("required", "required");
                    $("#residence").attr("required", "required");
                }
                else{
                    $("#national_id").removeAttr("required");
                    $("#age").removeAttr("required");
                    $("#phone_no").removeAttr("required");
                    $("#occupation").removeAttr("required");
                    $("#email_address").removeAttr("required");
                    $("#residence").removeAttr("required");
                }
            });


            $("#facility_id").change(function(){
                var val = $(this).val();

                $('#county_id').removeAttr("required");

                if(val == 7148 || val == '7148'){
                    $('.requirable').removeAttr("required");
                }
                else{
                    $('.requirable').attr("required", "required");
                }

            });

            $("#quarantine_site_id").change(function(){
                $('#facility_id').removeAttr("required");
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


        function check_new_patient(){
            var national_id = $("#national_id").val();
            var identifier = $("#identifier").val();
            var facility_id = $("#facility_id").val();
            var quarantine_site_id = $("#quarantine_site_id").val();
            var patient_name = $("#patient_name").val();

            $.ajax({
                type: "POST",
                data: {
                    national_id : national_id,
                    identifier : identifier,
                    facility_id : facility_id,
                    quarantine_site_id : quarantine_site_id,
                    patient_name : patient_name,
                },
                url: "{{ url('/covid_sample/new_patient') }}",

                success: function(data){

                    // console.log(data);

                    if(data['message']){
                        set_message(data['message']);
                        $("#new_patient_info").html(data['message']);
                        $("#new_patient_div").show();

                        if(data['patient']){

                            var patient = data['patient'];

                            var national_id = $("#national_id").val();
                            var identifier = $("#identifier").val();
                            var facility_id = $("#facility_id").val();
                            var quarantine_site_id = $("#quarantine_site_id").val();
                            var patient_name = $("#patient_name").val();

                            if(!national_id.length) $("#national_id").val(patient.national_id);
                            if(!identifier.length) $("#identifier").val(patient.identifier);


                            $("#sex").val(patient.sex).change();
                            $("#facility_id").val(patient.facility_id).change();
                            $("#quarantine_site_id").val(patient.quarantine_site_id).change();
                            $("#dob").val(patient.dob);
                            $("#age").val(patient.most_recent.age);
                            
                            $("#patient_name").val(patient.patient_name);
                            $("#county_id").val(patient.county_id);
                            $("#subcounty_id").val(patient.subcounty_id);
                            $("#email_address").val(patient.email_address);
                            $("#phone_no").val(patient.phone_no);
                            $("#residence").val(patient.residence);
                        }
                    }
                    else{
                        $("#new_patient_div").hide();
                    }
                }
            });
        }

        function check_cif_patient(){
            // console.log('Here');
            // return;
            var patient_name = $("#patient_name").val();
            var national_id = $("#national_id").val();
            var identifier = $("#identifier").val();

            $.ajax({
                type: "POST",
                data: {
                    patient_name : patient_name,
                    national_id : national_id,
                    identifier : identifier,
                },
                url: "{{ url('/covid_sample/cif_patient') }}",

                success: function(data){
                    $("#cif_covid_samples").html(data);
                }
            });
        }

    </script>



@endsection
