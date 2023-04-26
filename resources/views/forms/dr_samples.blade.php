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
        <form action="{{ url('/dr_sample/' . $sample->id) }}" class="form-horizontal" method="POST" id='samples_form'>
            @method('PUT')
        @else
        <form action="{{ url('/dr_sample') }}" class="form-horizontal" method="POST" id='samples_form'>
        @endif

            @csrf

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


                            <input disabled="disabled" type="hidden" value="{{ $sample->patient_id ?? 0 }}" name="patient_id" id="patient_id">

       
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Facility </label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="facility_id" id="facility_id">
                                        @isset($sample)
                                        <option value="{{ $sample->patient->facility->id ?? '' }}" selected>{{ $sample->patient->facility->facilitycode ?? '' }} {{ $sample->patient->facility->name ?? '' }}</option>
                                        @endisset
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Patient / Sample ID
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    <input class="form-control requirable" required name="patient" type="text" value="{{ $sample->patient->patient ?? '' }}" id="patient">
                                </div>
                            </div>

                            @if(env('APP_LAB') == 7)

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Nat ID</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="nat" type="text" value="{{ $sample->patient->nat ?? '' }}" id="nat" placeholder="NAT ...">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">CNT</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="cnt" type="text" value="{{ $sample->patient->cnt ?? '' }}" id="cnt" >
                                    </div>
                                </div>

                            @endif


                            <div class="hr-line-dashed"></div>



                            <div class="form-group">
                                <label class="col-sm-4 control-label">Patient Names</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="patient_name" type="text" value="{{ $sample->patient->patient_name ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date Of Birth
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date date-dob">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="dob" class="form-control lockable" value="{{ $sample->patient->dob ?? '' }}" name="dob">
                                    </div>
                                </div>                            
                            </div>


                            <div class="form-group">
                                <label class="col-sm-4 control-label">Age (In Years)</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="age" id='age' number='number' placeholder="Fill this or set the DOB." value="{{ $sample->age ?? '' }}">
                                </div>
                            </div>

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


                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date Started on ART
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date date-art">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="initiation_date" 
                                        @if(!isset($sample) || ($sample && $sample->patient->initiation_date))
                                            required 
                                        @endif
                                        class="form-control lockable requirable" value="{{ $sample->patient->initiation_date ?? '' }}" name="initiation_date">
                                    </div>
                                </div>                            
                            </div>



                            <div class="hr-line-dashed"></div>

                            <!-- Clinician -->                        

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Clinician Name</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="clinician_name" type="text" value="{{ $sample->clinician_name ?? '' }}" id="clinician_name">
                                </div>
                            </div>

                            @include('shared.dropdown', ['model' => $sample ?? null, 'attr' => 'dr_reason_id', 'drops' => $drug_resistance_reasons, 'label' => 'Reason for DR test', 'required' => true])

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Previous Regimen</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="prev_prophylaxis" id="prev_prophylaxis">
                                        <option></option>
                                        @foreach ($prophylaxis as $key => $proph)
                                            @continue($proph->code == '' && auth()->user()->user_type_id == 5)

                                            @if(!$key || $prophylaxis[$key-1]->age != $proph->age || $prophylaxis[$key-1]->line != $proph->line)
                                                <optgroup label="{{ $regimen_age[$proph->age] . ' ' . $regimen_line[$proph->line] }} ">
                                            @endif
                                            <option value="{{ $proph->id }}"

                                            @if (isset($sample) && $sample->prophylaxis == $proph->id)
                                                selected
                                            @endif

                                            > {{ $proph->code . ' ' . $proph->name }}
                                            </option>


                                            @if(!isset($prophylaxis[$key+1]) || $prophylaxis[$key+1]->age != $proph->age || $prophylaxis[$key+1]->line != $proph->line)
                                                </optgroup>
                                            @endif

                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Current Regimen
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    <select class="form-control requirable" required name="prophylaxis" id="prophylaxis">
                                        <option></option>
                                        @foreach ($prophylaxis as $key => $proph)
                                            @continue($proph->code == '' && auth()->user()->user_type_id == 5)

                                            @if(!$key || $prophylaxis[$key-1]->age != $proph->age || $prophylaxis[$key-1]->line != $proph->line)
                                                <optgroup label="{{ $regimen_age[$proph->age] . ' ' . $regimen_line[$proph->line] }} ">
                                            @endif
                                            <option value="{{ $proph->id }}"

                                            @if (isset($sample) && $sample->prophylaxis == $proph->id)
                                                selected
                                            @endif

                                            > {{ $proph->code . ' ' . $proph->name }}
                                            </option>


                                            @if(!isset($prophylaxis[$key+1]) || $prophylaxis[$key+1]->age != $proph->age || $prophylaxis[$key+1]->line != $proph->line)
                                                </optgroup>
                                            @endif


                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>                        

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Date Started on Previous Regimen</label>
                                <div class="col-sm-9">
                                    <div class="input-group date date-art">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="date_prev_regimen" class="form-control" value="{{ $sample->date_prev_regimen ?? '' }}" name="date_prev_regimen">
                                    </div>
                                </div>                            
                            </div>                      

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Date Started on Current Regimen</label>
                                <div class="col-sm-9">
                                    <div class="input-group date date-art">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="date_current_regimen" class="form-control requirable" value="{{ $sample->date_current_regimen ?? '' }}" name="date_current_regimen">
                                    </div>
                                </div>                            
                            </div> 

                            <div class="hr-line-dashed"></div>   

                            @foreach([1, 2, 3] as $v)                 

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">VL Result
                                        @if($v == 1)
                                            <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                        @endif
                                    </label>
                                    <div class="col-sm-4">
                                        <input class="form-control  @if($v == 1) requirable  @endif" name="{{ 'vl_result' . $v }}" type="text" value="{{ $sample->{'vl_result' . $v} ?? '' }}" id="{{ 'vl_result' . $v }}" @if($v == 1) required  @endif>
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="input-group date date-art">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" id="{{ 'vl_date_result' . $v }}" class="form-control  @if($v == 1) requirable  @endif" value="{{ $sample->{'vl_date_result' . $v} ?? '' }}" name="{{ 'vl_date_result' . $v }}" @if($v == 1) required  @endif>
                                        </div>
                                    </div>                            
                                </div>

                            @endforeach 


                            <div class="hr-line-dashed"></div>

                            @include('shared.dropdown', ['model' => $sample ?? null, 'attr' => 'sampletype', 'drops' => $sampletypes, 'label' => 'Sample Type', 'required' => true])

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Clinical Indication
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    @foreach ($clinical_indications as $clinical_indication)
                                        <div>
                                            <label> 
                                                <input name="clinical_indications[]" type="checkbox" class="i-checks requirable" required
                                                    value="{{ $clinical_indication->id }}" 

                                                    @if(isset($sample) && is_array($sample->clinical_indications_array) &&
                                                    in_array($clinical_indication->id, $sample->clinical_indications_array) )
                                                        checked="checked"
                                                    @endif
                                                /> 
                                                {{ $clinical_indication->name }} 
                                            </label>
                                        </div>

                                    @endforeach
                                </div>
                            </div>

                            <!-- Opportunistic Infections -->

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Has Opportunistic Infections
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    @component('shared/boolean_dropdown', ['obj' => $sample ?? null, 'field' => 'has_opportunistic_infections'])

                                    @endcomponent 
                                </div>
                            </div>

                            <div class="form-group" id="has_opportunistic_infections_div" 
                                @if(isset($sample) && $sample->has_opportunistic_infections)
                                @else
                                    style="display: none;" 
                                @endif
                                >
                                <label class="col-sm-3 control-label">Opportunistic Infections</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="opportunistic_infections" type="text" value="{{ $sample->opportunistic_infections ?? '' }}" id="opportunistic_infections">
                                </div>
                            </div>


                            <!-- TB -->

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Has TB
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    @component('shared/boolean_dropdown', ['obj' => $sample ?? null, 'field' => 'has_tb'])

                                    @endcomponent
                                </div>
                            </div>

                            <div class="form-group" id="has_tb_div" 
                                @if(isset($sample) && $sample->has_tb)
                                @else
                                    style="display: none;" 
                                @endif
                                >
                                <label class="col-sm-3 control-label">TB treatment phase</label>
                                <div class="col-sm-9">

                                    <select class="form-control" name="tb_treatment_phase_id" id="tb_treatment_phase_id">
                                        <option></option>
                                        @foreach ($tb_treatment_phases as $tb_treatment_phase)
                                            <option value="{{ $tb_treatment_phase->id }}"

                                            @if (isset($sample) && $sample->tb_treatment_phase_id == $tb_treatment_phase->id)
                                                selected
                                            @endif

                                            > {{ $tb_treatment_phase->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>



                            <!-- Arv Toxicity -->

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Has ARV Toxicity
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    @component('shared/boolean_dropdown', ['obj' => $sample ?? null, 'field' => 'has_arv_toxicity'])

                                    @endcomponent   
                                </div>
                            </div>

                            <div class="form-group" id="has_arv_toxicity_div" 
                                @if(isset($sample) && $sample->has_arv_toxicity)
                                @else
                                    style="display: none;" 
                                @endif

                                >
                                <label class="col-sm-3 control-label">ARV Toxicities </label>
                                <div class="col-sm-9">
                                    @foreach ($arv_toxicities as $arv_toxicity)
                                        <div>
                                            <label> 
                                                <input name="arv_toxicities[]" id="arv_toxicities" type="checkbox" class="i-checks"
                                                    value="{{ $arv_toxicity->id }}" 

                                                    @if(isset($sample) && is_array($sample->arv_toxicities_array) &&
                                                    in_array($arv_toxicity->id, $sample->arv_toxicities_array) )
                                                        checked="checked"
                                                    @endif
                                                /> 
                                                {{ $arv_toxicity->name }} 
                                            </label>
                                        </div>

                                    @endforeach
                                </div>
                            </div>

                            <!-- CD4 result -->                        

                            <div class="form-group">
                                <label class="col-sm-3 control-label">CD4 result with the last 6 Months</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="cd4_result" type="text" value="{{ $sample->cd4_result ?? '' }}" id="cd4_result">
                                </div>
                            </div>


                            <!-- Missed Pills -->

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Has Missed Pills
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    @component('shared/boolean_dropdown', ['obj' => $sample ?? null, 'field' => 'has_missed_pills'])

                                    @endcomponent   
                                </div>
                            </div>                        

                            <div class="form-group" id="has_missed_pills_div"
                                @if(isset($sample) && $sample->has_missed_pills)
                                @else
                                    style="display: none;" 
                                @endif

                                >
                                <label class="col-sm-3 control-label">No of Missed Pills</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="missed_pills" type="text" number="number" value="{{ $sample->missed_pills ?? '' }}" id="missed_pills">
                                </div>
                            </div>


                            <!-- Missed Visits -->

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Has Missed Visits
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    @component('shared/boolean_dropdown', ['obj' => $sample ?? null, 'field' => 'has_missed_visits'])

                                    @endcomponent   
                                </div>
                            </div>                        

                            <div class="form-group" id="has_missed_visits_div"
                                @if(isset($sample) && $sample->has_missed_visits)
                                @else
                                    style="display: none;" 
                                @endif

                                >
                                <label class="col-sm-3 control-label">No of Missed Visits</label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="missed_visits" type="text" number="number" value="{{ $sample->missed_visits ?? '' }}" id="missed_visits">
                                </div>
                            </div>


                            <!-- Missed Pills because of Missed Visits -->

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Has Missed Pills Because of Missed Visits
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    @component('shared/boolean_dropdown', ['obj' => $sample ?? null, 'field' => 'has_missed_pills_because_missed_visits'])

                                    @endcomponent   
                                </div>
                            </div> 

                            <div class="hr-line-dashed"></div>  

                            <!-- Other Medications -->

                            <div class="form-group"  >
                                <label class="col-sm-3 control-label">Other Medications </label>
                                <div class="col-sm-9">
                                    @foreach ($other_medications as $other_medication)
                                        <div>
                                            <label> 
                                                <input name="other_medications[]" type="checkbox" class="i-checks"
                                                    value="{{ $other_medication->id }}" 

                                                    @if(isset($sample) && is_array($sample->other_medications_array) &&
                                                    in_array($other_medication->id, $sample->other_medications_array) )
                                                        checked="checked"
                                                    @endif
                                                /> 
                                                {{ $other_medication->name }} 
                                            </label>
                                        </div>

                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">Other Medications (Separate using commas) </label>
                                <div class="col-sm-9">
                                    <input class="form-control" name="other_medications_text" type="text" value="{{ $sample->other_medications_string ?? '' }}" id="other_medications_text">
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
                                <label class="col-sm-3 control-label">Date of Collection
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-9">
                                    <div class="input-group date date-normal">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="datecollected" required class="form-control requirable" value="{{ $sample->datecollected ?? '' }}" name="datecollected">
                                    </div>
                                </div>                            
                            </div> 

                            @if(auth()->user()->user_type_id <= 1)

                                @include('shared.dropdown', ['model' => $sample ?? null, 'attr' => 'project', 'drops' => $dr_projects, 'label' => 'Project Name', 'required' => true])

                                @include('shared.dropdown', ['model' => $sample ?? null, 'attr' => 'container_type', 'drops' => $container_types, 'label' => 'Container Type', 'required' => false])

                                @include('shared.dropdown', ['model' => $sample ?? null, 'attr' => 'amount_unit', 'drops' => $amount_units, 'label' => 'Amount Unit', 'required' => false])    


                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Sample Amount</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" name="sample_amount" type="text" number='number' value="{{ $sample->sample_amount ?? '' }}" id="sample_amount">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Date Received
                                        <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="input-group date date-normal">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" id="datereceived" required class="form-control requirable" value="{{ $sample->datereceived ?? '' }}" name="datereceived">
                                        </div>
                                    </div>                            
                                </div>

                                @include('shared.dropdown', ['model' => $sample ?? null, 'attr' => 'receivedstatus', 'drops' => $received_statuses, 'label' => 'Received Status', 'required' => true])

                                @include('shared.dropdown', ['model' => $sample ?? null, 'attr' => 'rejectedreason', 'drops' => $dr_rejected_reasons, 'label' => 'Rejected Reason', 'required' => true, 'disabled' => true, 'form_div' => 'rejection'])

                            @endif

                        </div>
                    </div>
                </div>
            </div>

                    
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <center>
                            <div class="col-sm-10 col-sm-offset-1">
                                <button class="btn btn-success" type="submit" name="submit_type" value="release" id='submit_form_button'>Save</button>

                                <button class="btn btn-danger" type="submit" formnovalidate name="submit_type" value="cancel">Cancel</button>
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
                @if(env('APP_LAB') == 7)
                    dob: {
                        lessThan: ["#datecollected", "Date of Birth", "Date Collected"],
                        lessThanTwo: ["#initiation_date", "Date of Birth", "ART Inititation Date"]
                    },
                    initiation_date:{
                        GreaterThanSpecific: ["1990-01-01", "Date of Initiating ART"]
                    },
                    age: {
                        required: '#dob:blank'
                    },
                    nat: {
                        regex: '^[N][A][T]'
                    },
                @endif
                date_prev_regimen: {
                    lessThanTwo: ["#date_current_regimen", "Date of Previous Regimen", "Date of Current Regimen"]
                },
                datecollected: {
                    lessThanTwo: ["#datereceived", "Date Collected", "Date Received"]
                }                               
            }
        @endslot

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

        // $("#dateinitiatedontreatment").datepicker({
        $(".date-art").datepicker({
            startView: 2,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            // startDate: '-24y',
            startDate: '1990-01-01',
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });


        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
        set_select_facility("lab_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

    @endcomponent


    <script type="text/javascript">
        $(document).ready(function(){
            $("#rejection").hide();
            $('.requirable').removeAttr("required");

            @if(env('APP_LAB') != 7)
                $("#patient").blur(function(){
                    var patient = $(this).val();
                    var facility = $("#facility_id").val();
                    check_new_patient(patient, facility);
                });
            @else
                // $('.requirable').removeAttr("required");
            @endif

            $("#facility_id").change(function(){
                var val = $(this).val();

                if(val == 7148 || val == '7148'){
                    $('.requirable').removeAttr("required");
                }
                else{
                    @if(env('APP_LAB') != 7)
                        // $('.requirable').attr("required", "required");
                    @endif
                }
            }); 

            $("#receivedstatus").change(function(){
                var val = $(this).val();
                if(val == 2){
                    $("#rejection").show();
                    $("#rejectedreason").removeAttr("disabled");
                    $('.requirable').removeAttr("required");
                }
                else{
                    $("#rejection").hide();
                    $("#rejectedreason").attr("disabled", "disabled");
                    @if(env('APP_LAB') != 7)
                        // $('.requirable').attr("required", "required");
                    @endif
                }
            });   

            

            $("#has_opportunistic_infections").change(function(){
                var val = $(this).val();

                if(val == 1){
                    $("#has_opportunistic_infections_div").show();
                    $("#opportunistic_infections").removeAttr("disabled");                    
                }
                else{
                    $("#has_opportunistic_infections_div").hide();
                    $("#opportunistic_infections").attr("disabled", "disabled");
                }
            });   

            $("#has_tb").change(function(){
                var val = $(this).val();

                if(val == 1){
                    $("#has_tb_div").show();
                    $("#tb_treatment_phase_id").removeAttr("disabled");                    
                }
                else{
                    $("#has_tb_div").hide();
                    $("#tb_treatment_phase_id").attr("disabled", "disabled");
                }
            });  

            $("#has_arv_toxicity").change(function(){
                var val = $(this).val();

                if(val == 1){
                    $("#has_arv_toxicity_div").show();
                    $("#arv_toxicities").removeAttr("disabled");                    
                }
                else{
                    $("#has_arv_toxicity_div").hide();
                    $("#arv_toxicities").attr("disabled", "disabled");
                }
            });    

            $("#has_missed_pills").change(function(){
                var val = $(this).val();

                if(val == 1){
                    $("#has_missed_pills_div").show();
                    $("#missed_pills").removeAttr("disabled");                    
                }
                else{
                    $("#has_missed_pills_div").hide();
                    $("#missed_pills").attr("disabled", "disabled");
                }
            });    

            $("#has_missed_visits").change(function(){
                var val = $(this).val();

                if(val == 1){
                    $("#has_missed_visits_div").show();
                    $("#missed_visits").removeAttr("disabled");                    
                }
                else{
                    $("#has_missed_visits_div").hide();
                    $("#missed_visits").attr("disabled", "disabled");
                }
            });    


        });

        function check_new_patient(patient, facility_id){
            $.ajax({
               type: "POST",
               data: {
                _token : "{{ csrf_token() }}",
                patient : patient,
                facility_id : facility_id
               },
               url: "{{ url('/viralsample/new_patient') }}",

               success: function(data){

                    console.log(data);

                    // $("#new_patient").val(data[0]);
                    var patient = data[1];

                    if(data[0] == 0){
                        // $("#submit_form_button").removeAttr("disabled");
                        $("#patient_id").val(patient.id);
                        $("#dob").val(patient.dob);
                        $("#initiation_date").val(patient.initiation_date);
                        $("#sex").val(patient.sex).change();
                    }
                    else{
                        // $("#submit_form_button").attr("disabled", "disabled");
                        set_warning("This patient was not found.")
                    }

                }
            });
        }

    </script>



@endsection
