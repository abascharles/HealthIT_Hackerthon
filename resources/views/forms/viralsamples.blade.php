@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

   <div class="content">
        <div>

        @if(isset($viralsample))
        <form action="{{ url('/viralsample/' . $viralsample->id) }}" class="form-horizontal" method="POST" id='samples_form'>
            @method('PUT')
        @else
        <form action="{{ url('/viralsample') }}" class="form-horizontal" method="POST" id='samples_form'>
        @endif

            @csrf

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
            <div id="similar_samples"></div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="alert alert-info">
                            <center>
                                Recency testing is now available. For a recency test, under justifications option 7 listed as <b>Recency Testing</b> <br />
                                Recency testing is for those aged over 15. If the system detects the patient's age is 15 or under, the system will automatically reject the sample.
                            </center>
                        </div>
                        <br />

                        <div class="alert alert-warning">
                            <center>
                                Please fill the form correctly. <br />
                                Fields with an asterisk(*) are mandatory.
                            </center>
                        </div>
                        <br />

                        @if(env('APP_LAB') == 2)

                            <div class="alert alert-warning">
                                <center>
                                    Please fill the ccc number by starting with the facility mfl code 
                                </center>
                            </div>
                            <br />

                        @endif

                        @isset($viralsample)
                            <div class="alert alert-warning">
                                <center>
                                    NB: If you edit the facility name, date received or date dispatched from the facility this will be reflected on the other samples in this batch.
                                </center>
                            </div>
                            <br />
                        @endisset

                        @if(isset($form_sample_type) && $form_sample_type)
                            <input type="hidden" name="form_sample_type" value="{{$form_sample_type}}">
                            <input type="hidden" name="sampletype" value="{{$form_sample_type}}">
                        @endif


                        @if(!$batch || isset($viralsample))    
                          <div class="form-group">
                              <label class="col-sm-4 control-label">Facility
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                              <div class="col-sm-8">
                                <select class="form-control requirable" required name="facility_id" id="facility_id">
                                    @isset($viralsample)
                                        <option value="{{ $viralsample->batch->facility->id }}" selected>{{ $viralsample->batch->facility->facilitycode }} {{ $viralsample->batch->facility->name }}</option>
                                    @endisset
                                </select>
                              </div>
                          </div>
                        @else

                            <div class="alert alert-success">
                                <center> <b>Facility</b> - {{ $facility_name }}<br />  <b>Batch</b> - {{ $batch->id }} </center>
                            </div>
                            <br />

                            @if(session('viral_last_patient'))

                                <div class="alert alert-success">
                                    <center> <b>Last Patient Entered</b> - {{ session('viral_last_patient') }} </center>
                                </div>
                                <br />

                            @endif
                            
                            <input type="hidden" name="facility_id" value="{{$batch->facility_id}}" id="facility_id">
                        @endif
                        
                        @if(auth()->user()->user_type_id != 5)
                            <div class="form-group">
                                <label class="col-sm-4 control-label">High Priority</label>
                                <div class="col-sm-8">
                                <input type="checkbox" class="i-checks" name="highpriority" value="1"
                                    @if(isset($viralsample) && $viralsample->batch->highpriority)
                                        checked
                                    @endif

                                 />
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Recency Test ?</label>
                            <div class="col-sm-8">
                                <input type="checkbox" class="i-checks" name="isRecencyCheck" id="isRecencyCheck" value="=1"
                                       @if(isset($viralsample) && $viralsample->justification ==12)
                                checked
                                @endif
                                />
                                <small>&nbsp; Select this option if sample is for recency</small>
                            </div>
                        </div>

                        <div class="form-group ampath-div">
                            <label class="col-sm-4 control-label">(*for Ampath Sites only) AMRS Location</label>
                            <div class="col-sm-8">
                                <select class="form-control ampath-only" name="amrs_location">

                                    <option></option>
                                    @foreach ($amrs_locations as $amrs_location)
                                        <option value="{{ $amrs_location->id }}"

                                        @if (isset($viralsample) && $viralsample->amrs_location == $amrs_location->id)
                                          selected
                                        @endif

                                        > {{ $amrs_location->name }}
                                        </option>
                                    @endforeach

                              </select>
                            </div>
                        </div>

                      @if(env('APP_LAB') == 8)

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Specimen Label ID </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="label_id" type="text" value="{{ $viralsample->label_id ?? '' }}" id="label_id">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Area Name </label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="areaname" type="text" value="{{ $viralsample->areaname ?? '' }}" id="areaname">
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
                    <div class="panel-heading">
                        <center>Patient Information</center>
                    </div>
                    <div class="panel-body">

                    <div class="form-group" id="patient-ccc-div">
                          <fieldset class="panel-info">
                              <label class="col-sm-1 control-label">CCC  MFL
                              </label>
                              {{-- <div class="col-sm-8"> --}}
                                  <div class="col-sm-3">
                                      <select class="form-control "  name="patient_facility_id" onChange="showFacilityCode(this.value)" id="patient_facility_id">
                                          @isset($viralsample)
                                          <option value="{{ $viralsample->batch->facility->id }}" selected></option>
                                          @endisset
                                      </select>
                                  </div>
                                  {{-- <div class="col-sm-8"> --}}
                                      <label class="col-sm-1 control-label">Patient serial No.
                                      </label>
                                      <div class="col-sm-3">
                                          <input class="form-control " id="patient_serial"   name="patient_serial" onChange="showSerial(this.value)" type="text"            maxlength="5" value="" id="patient_serial">
                                      </div>
                                      {{-- </div> --}}
                                  {{-- <div class="col-sm-3 "> --}}
                                      <label class="col-sm-1 control-label">CCC No.
                                      </label>  <strong><div class="non-recency-field" style='color: #ff0000; display: inline;'>*</div></strong>
                                      <div class="col-sm-3">
                                          <input class="form-control " id="patient" onKeyUp="fetchPatientDetails(this.value)"  name="patient" type="text" value="{{ $viralsample->patient->patient ?? '' }}" id="patient" readonly required>
                                      </div>
                                      {{-- </div> --}}
                              </div>

                              <div class="form-group center recency-field" id="patient-rec-div">
                                  <label class="col-sm-1 control-label">REC MFL
                                  </label>
                                  {{--
                                  <div class="col-sm-8"> --}}
                                      <div class="col-sm-3">
                                          <select class="form-control " id="rec_facility_id" name="rec_facility_id"
                                                  onChange="showFacilCode(this.value)" id="rec_facility_id">
                                              @isset($viralsample)
                                              <option value="{{ $viralsample->batch->facility->id }}" selected></option>
                                              @endisset
                                          </select>
                                      </div>
                                      {{--
                                      <div class="col-sm-8"> --}}
                                          <label class="col-sm-1 control-label">Patient REC serial No.
                                          </label>
                                          <div class="col-sm-3">
                                              <input class="form-control " id="rec_serial" name="rec_serial"
                                                     onChange="showRecSerial(this.value)" type="text" maxlength="5" value=""
                                                     id="patient_serial">
                                          </div>
                                          {{--
                                      </div>
                                      --}}
                                      <div class="form-group">
                                          <label class="col-sm-1 control-label">Recency Number
                                          </label> <strong><div class="recency-field" style='color: #ff0000; display: inline;'>*</div></strong>
                                          <div class="col-sm-3">
                                              <input class="form-control" id="recency_number" onKeyUp="fetchPatientDetail(this.value)" name="recency_number" type="text"
                                                     value="{{ $viralsample->recency_number ?? '' }}" id="recency_number" readonly >
                                          </div>
                                      </div>
                                  </div>
                              </div>
                              <br>
                          </fieldset>



                        @if( in_array(env('APP_LAB'), $sms))
                    <div class="panel">
                            <div class="form-group non-recency-field" id="phone-no-div">
                                <label class="col-sm-4 control-label">Phone No (format 254725******)</strong>
                                    <strong><div style='color: #ff0000; display: inline;'></div></strong>
                                </label>
                                <div class="col-sm-3">
                                    <input class="form-control" name="patient_phone_no" id="patient_phone_no" type="text" maxlength="12" value="{{ $viralsample->patient->patient_phone_no ?? '' }}" >
                                </div>

                                <div class="col-sm-1">Patient's Preferred Language</div>

                                <div class="col-sm-4">
                                    @foreach($languages as $key => $value)
                                        <label><input type="radio" class="i-chszecks" id="preferred_language" name="preferred_language" value="{{ $key }}" 

                                            @if(isset($viralsample) && $viralsample->patient->preferred_language == $key)
                                                checked="checked"
                                            @endif
                                            >
                                            {{ $value }}
                                        </label>

                                    @endforeach
                                </div>
                            </div>

                        @endif

                       
                        @if(env('APP_LAB') == 4)

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Automatically Add MFL Code to CCC Number</label>
                                <div class="col-sm-8">
                                <input type="checkbox" class="i-checks" name="automatic_mfl" value="1" checked="checked" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Automatically Add Slash to CCC Number</label>
                                <div class="col-sm-8">
                                <input type="checkbox" class="i-checks" name="automatic_slash" value="1" checked="checked" />
                                </div>
                            </div>

                        @endif

                        @if(!isset($viralsample) && auth()->user()->user_type_id != 5)

                            {{-- <div class="form-group">
                                <label class="col-sm-4 control-label">Confirm Re-Entry (Sample Exists but should not be flagged as a double-entry)</label>
                                <div class="col-sm-8">
                                <input type="checkbox" class="i-checks" name="reentry" value="1" />
                                </div>
                            </div> --}}

                        @endif

                        <div class="form-group ampath-div">
                            <label class="col-sm-4 control-label">(*for Ampath Sites only) AMRS Provider Identifier</label>
                            <div class="col-sm-8">
                                <input class="form-control ampath-only" name="provider_identifier" type="text" value="{{ $viralsample->provider_identifier ?? '' }}">
                            </div>
                        </div>

                        <div class="form-group">
                            @include("forms.partials.upi")
                            @include("forms.partials.patient_verified")
                        </div>
                        <div class="form-group non-recency-field">
                            <label class="col-sm-4 control-label">Patient Names
                            </label>
                            <div class="col-sm-8">
                                <input class="form-control" id="name" name="patient_name" type="text" value="{{ $viralsample->patient->patient_name ?? '' }}" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Of Birth
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date date-dob">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="dob" class="form-control lockable"
                                    @if(auth()->user()->user_type_id == 5)
                                        required
                                    @endif
                                     value="{{ $viralsample->patient->dob ?? '' }}" name="dob" required>
                                </div>
                            </div>                            
                        </div>

                        @if(auth()->user()->user_type_id != 5)
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Age (In Years)
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="age" id='age' number='number' placeholder="Fill this or set the DOB." value="{{ $viralsample->age ?? '' }}" required>
                                </div>
                            </div>
                        @endif



                        <div class="form-group">
                            <label class="col-sm-4 control-label">Sex
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control lockable requirable" required name="sex" id="sex">

                                    <option value="3">- Select Option -</option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->id }}"

                                        @if (isset($viralsample) && $viralsample->patient->sex == $gender->id)
                                            selected
                                        @endif

                                        > {{ $gender->gender_description }}
                                        </option>
                                    @endforeach


                                </select>
                            </div>
                        </div>

                        <div class="form-group non-recency-field">
                            <label class="col-sm-4 control-label">PMTCT(If Female)
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control requirable" name="pmtct" id="pmtct" required>

                                    <option></option>
                                    @foreach ($pmtct_types as $pmtct)
                                        <option value="{{ $pmtct->id }}"

                                        @if (isset($viralsample) && $viralsample->pmtct == $pmtct->id)
                                            selected
                                        @endif

                                        > {{ $pmtct->name }}
                                        </option>
                                    @endforeach


                                </select>
                            </div>
                        </div>
                    </div>
                        <div class="hr-line-dashed"></div>
                    <div class="hpanel">
                        <!-- <div class="panel-heading">
                            <center>Sample Information</center>
                        </div> -->
                        <div class="panel-body">

                        @if(isset($form_sample_type) && $form_sample_type)

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sample Type
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled type="text" value="{{ $sampletypes->where('id', $form_sample_type)->first()->name ?? '' }}">
                                </div>
                            </div>

                        @else

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sample Type
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control requirable" required name="sampletype" id="sampletype">
                                        <option></option>
                                        @foreach ($sampletypes as $sampletype)
                                            @continue($sampletype->flag == 0)
                                            <option value="{{ $sampletype->id }}"

                                            @if (isset($viralsample) && $viralsample->sampletype == $sampletype->id)
                                                selected
                                            @endif

                                            >{{ $sampletype->id }} &nbsp; {{ $sampletype->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        @endif

                        <div class="hr-line-dashed"></div>                      

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date of Collection
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date date-normal">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="datecollected" required class="form-control requirable" value="{{ $viralsample->datecollected ?? '' }}" name="datecollected">
                                </div>
                            </div>                            
                        </div>                      

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date of Separation / Centrifugation
                                {{-- <strong><div style='color: #ff0000; display: inline;'>*</div></strong> --}}
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date date-normal">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="dateseparated" class="form-control"

                                        @if (isset($viralsample))
                                            value="{{ $viralsample->my_date_format('dateseparated', 'Y-m-d') }}"
                                        @endif

                                      name="dateseparated" >
                                </div>
                            </div>                            
                        </div> 

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Time of Separation / Centrifugation</label>
                            <div class="col-sm-8">
                                <select class="form-control" id="separating_hour" name="separating_hour" >

                                    <option></option>
                                    @for($i=1; $i<13; $i++)
                                        <option value="{{ $i }}"

                                        @if (isset($viralsample) && $viralsample->my_date_format('dateseparated', 'H') == $i)
                                            selected
                                        @endif

                                        > {{ $i }} A.M.
                                        </option>
                                    @endfor

                                    @for($i=1; $i<13; $i++)
                                        <option value="{{ $i+12 }}"

                                        @if (isset($viralsample) && $viralsample->my_date_format('dateseparated', 'H') == ($i + 12))
                                            selected
                                        @endif

                                        > {{ $i }} P.M.
                                        </option>
                                    @endfor

                                </select>
                            </div>
                        </div> 

                        <div class="form-group non-recency-field" >
                            <label class="col-sm-4 control-label">Date Started on ART
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date date-art">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="initiation_date" 
                                    @if(!isset($viralsample) || ($viralsample && $viralsample->patient->initiation_date))
                                        required 
                                    @endif
                                    class="form-control lockable requirable" value="{{ $viralsample->patient->initiation_date ?? '' }}" name="initiation_date">
                                </div>
                            </div>                            
                        </div>

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Date Dispatched from Facility
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date date-dispatched">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="datedispatched" class="form-control" value="{{ $viralsample->batch->datedispatchedfromfacility ?? $batch->datedispatchedfromfacility ?? '' }}" name="datedispatchedfromfacility" required>
                                </div>
                            </div>                            
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group non-recency-field">
                            <label class="col-sm-4 control-label">Current Regimen
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8" id="prophylaxis-div">
                                <select class="form-control requirable" required name="prophylaxis" id="prophylaxis">
                                    <option></option>
                                    @foreach ($prophylaxis as $key => $proph)
                                        @continue($proph->code == '' && auth()->user()->user_type_id == 5)

                                        @if(!$key || $prophylaxis[$key-1]->age != $proph->age || $prophylaxis[$key-1]->line != $proph->line)
                                            <optgroup class="regimen_age_{{ $proph->age }}" label="{{ $regimen_age[$proph->age] . ' ' . $regimen_line[$proph->line] }} ">
                                        @endif
                                        <option value="{{ $proph->id }}" class="regimen_age_{{ $proph->age }}"

                                        @if (isset($viralsample) && $viralsample->prophylaxis == $proph->id)
                                            selected
                                        @endif

                                        > {{ $proph->code . ' - ' . $proph->name }}
                                        </option>


                                        @if(!isset($prophylaxis[$key+1]) || $prophylaxis[$key+1]->age != $proph->age || $prophylaxis[$key+1]->line != $proph->line)
                                            </optgroup>
                                        @endif

                                    @endforeach
                                    
                                </select>
                            </div>
                        </div>

                        <div class="form-group non-recency-field">
                            <label class="col-sm-4 control-label">Date Initiated on Current Regimen
                                <strong><div class="non-recency-field" style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group date date-art">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    <input type="text" id="dateinitiatedonregimen" class="form-control" value="{{ $viralsample->dateinitiatedonregimen ?? '' }}" name="dateinitiatedonregimen" required>
                                </div>
                            </div>                            
                        </div>

                        <div class="hr-line-dashed"></div>  

                        <div class="form-group">
                            <label class="col-sm-4 control-label">Justification
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-8" id="justification-div">
                                <select class="form-control requirable" required name="justification" id="justification">
                                    <option></option>
                                    @foreach ($justifications as $justification)
                                    @if($justification->id != 7)
                                        @continue($justification->flag == 0)
                                        @continue($justification->id == 8 && auth()->user()->user_type_id == 5)
                                        <option value="{{ $justification->id }}"

                                        @if (isset($viralsample) && $viralsample->justification == $justification->id)
                                            selected
                                        @endif

                                        > {{ $justification->rank_id . ' ' . $justification->name }}
                                        </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    @if(isset($poc))
                                    <input type="hidden" value=2 name="site_entry">

                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">POC Site Sample Tested at
                                            <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                        </label>
                                        <div class="col-sm-8">
                                            <select class="form-control requirable" required name="lab_id" id="lab_id">
                                                @isset($viralsample)
                                                <option value="{{ $viralsample->batch->facility_lab->id }}" selected>{{ $viralsample->batch->facility_lab->facilitycode }} {{ $viralsample->batch->facility_lab->name }}</option>
                                                @endisset
                                            </select>
                                        </div>
                                    </div>

                                    @endif

                                    <div class="form-group alupe-div">
                                        <label class="col-sm-4 control-label">VL Test Request Number</label>
                                        <div class="col-sm-8">
                                            <input class="form-control" name="vl_test_request_no" number="number" min=0 max=10 type="text" value="{{ $viralsample->vl_test_request_no ?? '' }}" required>
                                        </div>
                                    </div>


                        <div class="hr-line-dashed"></div> 

                        <div></div>

                        @if(auth()->user()->user_type_id != 5 || isset($poc) || (isset($viralsample) && $viralsample->batch->site_entry == 2))

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date Received
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date date-normal">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="datereceived" required class="form-control requirable" value="{{ $viralsample->batch->datereceived ?? $batch->datereceived ?? '' }}" name="datereceived">
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

                                            @if (isset($viralsample) && $viralsample->receivedstatus == $receivedstatus->id)
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
                                                @continue(!in_array($rejectedreason->id, [3,12,17,20,24,25,26,27,28]))
                                        @if($rejectedreason->name != "Other" )
                                            <option value="{{ $rejectedreason->id }}"

                                            @if (isset($viralsample) && $viralsample->rejectedreason == $rejectedreason->id)
                                                selected
                                            @endif

                                            > {{ $rejectedreason->name }}
                                            </option>
                                        @endif
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
                                    <input class="form-control requirable" required name="entered_by" id="entered_by"  type="text" value="{{ $viralsample->batch->entered_by ?? '' }}">
                                </div>
                            </div>
                        @endif

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- <div class="row">
            <div class="col-lg-7 col-lg-offset-2">
                <div class="hpanel">
                    <div class="panel-heading">
                        <center>Infant Information</center>
                    </div>
                    <div class="panel-body">


                    </div>
                </div>
            </div>
        </div> -->


                
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body">
                        <div class="form-group"><label class="col-sm-4 control-label">Comments (from facility)</label>
                            <div class="col-sm-8"><textarea  class="form-control" name="comments"> {{ $viralsample->comments ?? '' }} </textarea></div>
                        </div>
                        @if(auth()->user()->user_type_id != 5)
                            <div class="form-group"><label class="col-sm-4 control-label">Lab Comments</label>
                                <div class="col-sm-8"><textarea  class="form-control" name="labcomment"> {{ $viralsample->labcomment ?? '' }} </textarea></div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <center>
                        @if(isset($viralsample))
                            <div class="col-sm-4 col-sm-offset-4">
                                <button class="btn btn-primary" type="submit" name="submit_type" value="add">
                                        @if (isset($site_entry_approval))
                                            Save & Load Next Sample in Batch for Approval
                                        @else
                                            Update Sample
                                        @endif
                                </button>
                            </div>
                        @else
                            <div class="col-sm-8 col-sm-offset-2">
                                <button class="btn btn-success" type="submit" name="submit_type" value="release">Save & Release sample</button>
                                <button class="btn btn-primary" type="submit" name="submit_type" value="add">Save & Add sample</button>
                                    
                                @isset($batch)
                                    <button class="btn btn-danger" type="submit" formnovalidate name="submit_type" value="cancel">Cancel & Release</button>
                                @endisset
                            </div>
                        @endif
                    </center>
                </div>
            </div>
        </div>
        </form>
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
                    lessThan: ["#datecollected", "Date Collected", "Date of Birth"],
                    lessThan: ["#initiation_date","ART Inititation Date","Date of Birth"],
                    lessThan: ["#dateseparated", "Date of Separation / Centrifugation", "Date of Birth"],
                    lessThan: ["#datedispatched", "Date Dispatched from Facility", "Date of Birth"],
                    lessThan: ["#dateinitiatedonregimen", "Date Initiated on Current Regimen", "Date of Birth"],
                    lessThan: ["#datereceived", "Date Received","Date of Birth"]
                },
           datecollected: {
           greaterThan: ["#dob","Date Collected","Date of Birth"],
           },
           initiation_date:{
           GreaterThanSpecific: ["1990-01-01", "Date of Initiating ART"],
           lessThan: ["#datecollected","ART Inititation Date","Date Collected"],
           },
{{--                initiation_date:{
                    GreaterThanSpecific: ["1990-01-01", "Date of Initiating ART"]
                },
                datecollected: {
                    greaterThan: ["#initiation_date", "Date of Initiating ART", "Date Collected"],
                    lessThan: ["#datedispatched", "Date Collected", "Date Dispatched From Facility"],
                    @if(auth()->user()->user_type_id != 5)
                        lessThanTwo: ["#datereceived", "Date Collected", "Date Received"]
                    @endif
                },
                --}}
                datereceived: {
                    greaterThan: ["#datedispatched","Date Received", "Date Dispatched From Facility"],
                },
                @if(auth()->user()->user_type_id != 5)
                    age: {
                        required: '#dob:blank'
                    }
                @endif
            }
        @endslot


        //$(".date :not(.date-dob, .date-art, .date-dispatched)").datepicker({
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
            endDate: "-3m",
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
            startDate: '-24y',
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
        set_select_facility_mfl("patient_facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
        set_select_facility("lab_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
        set_select_facility_mfl("rec_facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

    @endcomponent


    <script type="text/javascript">
        $(document).ready(function(){

            @if(in_array(env('APP_LAB'), [3, 1]) && auth()->user()->is_lab_user() && !isset($viralsample))
                $("#samples_form input,select").change(function(){
                    var frm = $('#samples_form');
                    var data = frm.serializeObject();
                    check_similar_samples(data);
                });  
            @endif


            $("#rejection").hide();
            $("#patient-rec-div").hide();
            if($("#isRecencyCheck").is(':checked')){
                enable_recency_field();
            }


            @if(isset($viralsample))                
                @if($viralsample->receivedstatus == 2)
                    $("#rejection").show();
                    $("#rejectedreason").removeAttr("disabled");
                    $('.requirable').removeAttr("required");
                @endif

                @if($viralsample->patient->sex == 1)
                    $("#pmtct").attr("disabled", "disabled");
                @endif
            @else
                // $("#patient").change(function(){
                //     var patient = $(this).val();
                //     var facility = $("#facility_id").val();
                //     concat_patient_id = $("#patient_facility_id").val() + "-" + patient;
                //     document.getElementById("patient").value = concat_patient_id;
                //     // document.getElementById("patient_facility_id").value = concat_patient_id.slice(0,4); 
                //     // console.log(concat_patient_id)
                //     check_new_patient(concat_patient_id, facility);
                // });
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
            $("#patient_facility_id").change(function(){
                
                document.getElementById("patient").value = "";
            });
            $("#rec_facility_id").change(function(){

                document.getElementById("recency_number").value = "";
            });
            // $("#patient").blur(function(){
            //         var patient = $(this).val();
            //         var facility = $("#facility_id").val();
            //         if (patient == null){
            //         concat_patient_id = $("#patient_facility_id").val() + "-" + patient;
            //         document.getElementById("patient").value = concat_patient_id;
            //         // document.getElementById("patient_facility_id").value = concat_patient_id.slice(0,4); 
            //         console.log(concat_patient_id)
            //         check_new_patient(concat_patient_id, facility);
            //     }
            // }); 

            $("#sampletype").change(function(){
                var val = $(this).val();
                if(val == 3 || val == 4){
                    $("#dateseparated").attr("disabled", "disabled");
                }
                else{
                    $("#dateseparated").removeAttr("disabled");
                }
            });

            $("#sex").change(function(){
                var val = $(this).val();
                if(val == 2){
                    // $("#pmtct").empty();
                    $("#pmtct").removeAttr("disabled");
                    $("#pmtct").attr("required", "required");
                    
                }
                else{
                    // $("#pmtct")[0].selectedIndex = 0;
                    document.getElementById("pmtct").value = '';
                    $("#pmtct").attr("disabled", "disabled");
                    $("#pmtct").removeAttr("required");
                    
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
                    $('.requirable').attr("required", "required");
                }
            });


            $('#isRecencyCheck').on('ifChanged', function (event) {
                //Check if checkbox is checked or not
                var checkboxChecked = $(this).is(':checked');
                if (checkboxChecked) {
                    $(".non-recency-field").hide();
                    $("div#justification-div select").val(12).trigger("change");
                    $("div#prophylaxis-div select").val(45).trigger("change");
                } else {
                    $("div#justification-div select").val('').trigger("change");
                    $("div#prophylaxis-div select").val('').trigger("change");
                }
            });

            $("#justification").change(function () {
                var val = $("#justification").val();

                if (val == 12) {
                    enable_recency_field();
                } else {
                    disable_recency_field();
                }
            });

            function enable_recency_field() {
                $(".recency-field").show();
                $("#recency_number").attr("required", "required");
                $("#recency_number").removeAttr("disabled");
                $("#patient").removeAttr("required");
                $("#patient_facility_id").removeAttr("required");
                $("#initiation_date").removeAttr("required");
                $("#dateinitiatedonregimen").removeAttr("required");
                $("#phone-no-div").hide();
                $(".non-recency-field").hide();
                $("#name").removeAttr("required");
                $("#pmtct").removeAttr("required");
            }

            function disable_recency_field() {
                $(".recency-field").hide();
                $("#phone-no-div").show()
                $(".non-recency-field").show();
                $("#recency_number").removeAttr("required");
                $("#recency_number").attr("disabled", "disabled");
                $("#patient").attr("required", "required");
                $("#patient_facility_id").attr("required", "required");
                $("#initiation_date").attr("required", "required");
                $("#dateinitiatedonregimen").attr("required", "required");
                $("#name").attr("required", "required");
                $("#pmtct").attr("required", "required");
            }

            /*$("#dob").change(function(){
                var val = $(this).val();
                var dt1 = new Date();
                var dt2 = new Date(val);
                var age = diff_in_years(dt2, dt1);
                if(age > 18){
                    set_message('Age is ' + age);
                    $('.regimen_age_2').hide();
                    $('.regimen_age_2').attr("disabled", "disabled");
                }
            });*/


            @if(!in_array(env('APP_LAB'), $amrs))
                $(".ampath-div").hide();
            @endif 

            @if(env('APP_LAB') != 2)
                $(".alupe-div").hide();
            @endif  

            
        });

        // function check_new_patient(patient, facility_id){
        //     $.ajax({
        //        type: "POST",
        //        data: {
        //         _token : "{{ csrf_token() }}",
        //         patient : patient,
        //         facility_id : facility_id
        //        },
        //        url: "{{ url('/viralsample/new_patient') }}",

        //        success: function(data){

        //             // console.log(data);

        //             $("#new_patient").val(data[0]);

        //             if(data[0] == 0){
        //                 localStorage.setItem("new_patient", 0);
        //                 var patient = data[1];
        //                 var prev = data[2];

        //                 // console.log(patient.dob);

        //                 $("#dob").val(patient.dob);
        //                 $("#initiation_date").val(patient.initiation_date);
        //                 $("#patient_phone_no").val(patient.patient_phone_no);
        //                 // $('#sex option[value='+ patient.sex + ']').attr('selected','selected').change();

        //                 $("#sex").val(patient.sex).change();

        //                 $('<input>').attr({
        //                     type: 'hidden',
        //                     name: 'patient_id',
        //                     value: patient.id,
        //                     id: 'hidden_patient',
        //                     class: 'patient_details'
        //                 }).appendTo("#samples_form");

        //                 if(data[3] != 0)
        //                 {
        //                     set_message(data[3]);
        //                 }

        //                 // $(".lockable").attr("disabled", "disabled");
        //             }
        //             else{
        //                 localStorage.setItem("new_patient", 1);
        //                 // $(".lockable").removeAttr("disabled");
        //                 // $(".lockable").val('').change();

        //                 $('.patient_details').remove();
        //             }

        //         }
        //     });
        // }

        function check_similar_samples(json_data){
            json_data['_token'] = "{{ csrf_token() }}";
            $.ajax({
               type: "POST",
               data: json_data,
               url: "{{ url('/viralsample/similar') }}",
               success: function(data){
                    $("#similar_samples").html(data);
                    // console.log(data);
                }
            });
        }
    </script>



@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){

        $("#dob").change(function(){
            var value = $("#dob").val();
            var dob = new Date(value);
            var today = new Date();
            var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
            if(isNaN(age)) {

                // will set 0 when value will be NaN
                age=0;

            }
            else{
                age=age;
            }
            $('#age').val(age);

        });

    });


    function fetchPatientDetails(ccc){
            $.ajax({
               type: "POST",
               data: ccc,
               url: "{{ url('/viralsample/getPatientDetails') }}",
               success: function(data){
                    
                    console.log(data);
                    let dataArray = JSON.parse(data);

                    if(dataArray.status == 'success'){
                        document.getElementById('name').value = dataArray.data.patient_name;
                        document.getElementById('dob').value = dataArray.data.dob;
                        document.getElementById('age').value = dataArray.data.age;
                        document.getElementById('initiation_date').value = dataArray.data.dateinitiatedontreatment;
                        document.getElementById('patient_phone_no').value = dataArray.data.patient_phone_no;
                        
                        // document.getElementById('age').value = dataArray.data.age;
                        // document.getElementById('sex').options.selectedIndex = dataArray.data.sex-1;

                        // var sex = document.getElementById('sex');
                        // var option;
                        
                        // for (var i=0; i<sex.options.length; i++) {
                        // option = sex.options[i];
                        
                        // if (option.value == ''+dataArray.data.sex) {
                        // // or
                        // // if (option.text == 'Malaysia') {
                        //     option.setAttribute('selected', true);
                        
                        //     // For a single select, the job's done
                        //     return; 
                        //     } 
                        // }
                        // alert(dataArray.data.sex);
                        $('#sex option[value='+ dataArray.data.sex + ']').attr('selected','selected').change();
                        
                        $("input[name=preferred_language][value=" + dataArray.data.preferred_language + "]").prop('checked', true);
                    }
                    if(dataArray.status == 'error'){
                        document.getElementById('name').value = '';
                        document.getElementById('dob').value = '';
                        document.getElementById('age').value = '';
                        document.getElementById('initiation_date').value = '';
                        $('#sex option[value=""]').attr('selected','selected').change();
                        document.getElementById('patient_phone_no').value = '';
                        $("input[name=preferred_language][value=1]").prop('unchecked', true);
                        $("input[name=preferred_language][value=2]").prop('unchecked', true);

                    }
                }
            });



    }

    function showFacilityCode(facilityCode){
        document.getElementById('patient').value = facilityCode+'-';
        let serial =  document.getElementById('patient_serial')
        serial.setAttribute('required', '');
        if (facilityCode == 99999999 || facilityCode == 999999){
            $("#patient_serial").attr("maxlength", "10");
        }else {
            $("#patient_serial").attr("maxlength", "5");
        }
        }
    function showSerial(serialCode){
        let facilityCode =  document.getElementById('patient_facility_id').value
        var ccc_no = facilityCode+serialCode;
        fetchPatientDetails(ccc_no)
    }
    function showFacilCode(facilityCode){
        document.getElementById('recency_number').value = facilityCode;
    }
    function showRecSerial(serialCode){
        let facilityCode =  document.getElementById('rec_facility_id').value
        var rec_no = 'REC'+facilityCode+serialCode;
        $.ajax({
            type: "POST",
            data: rec_no,
            url: "{{ url('/viralsample/getRecPatient') }}",
            success: function(data){
                let dataArray = JSON.parse(data);
                if(dataArray.status === 'success'){
                    alert("Same rec number exist on Batch:" + dataArray.data.batch_id + " . Please checkout.")
                    document.getElementById('recency_number').value = "";
                    document.getElementById('rec_serial').value = "";
                }
                if(dataArray.status === 'error'){
                    document.getElementById('recency_number').value = rec_no
                }
            }
        });
    }
</script>