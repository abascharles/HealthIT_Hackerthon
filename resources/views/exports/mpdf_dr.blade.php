<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page title -->
    <title>EID/VL | LAB</title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- Vendor styles -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/font-awesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/animate.css/animate.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.css') }}" />

    <style type="text/css">
        body.light-skin #menu {
            width: 240px;
        }
        #wrapper {
            margin: 0px 0px 0px 230px;
        }
        #toast-container > div {
            color: black;
        }
        .navbar-nav>li>a {
            padding: 15px 15px;
            font-size: 13px;
            color: black;
        }
        .btn {
            padding: 4px 8px;
            font-size: 12px;
        }
        .hpanel {
            margin-bottom: 4px;
        }
        .hpanel.panel-heading {
            padding-bottom: 2px;
            padding-top: 4px;
        }
    </style>

</head>
<!-- <body class="light-skin fixed-navbar sidebar-scroll"> -->
<body>

<!-- Main Wrapper -->
<!-- <div id="wrapper"> -->

    <!-- <div class="content"> -->

        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body" style="padding-bottom: 6px;">

                        <div class="alert alert-warning">
                            <center>
                                Please fill the form correctly. <br />
                                Please fill the date fields in the form "YYYY-MM-DD" <br />
                                Fields with an asterisk(*) are mandatory.
                            </center>
                        </div>
                        <br />

                        <input type="hidden" value=0 name="patient_id" id="patient_id">

   
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Facility 
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>

                            <div class="col-sm-9">
                                <input class="form-control requirable" required name="facility" type="text" value="
                                {{ $sample->patient->facility->facilitycode }} {{ $sample->patient->facility->name }}
                                " id="facility">
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

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Reason for DR test
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control requirable" required name="dr_reasons" type="text" value="{{ $drug_resistance_reasons->where('id', $sample->dr_reason_id)->first()->name ?? '' }}" id="dr_reasons">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Previous Regimen</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="prev_prophylaxis" type="text" value="" id="prev_prophylaxis">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Current Regimen
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control requirable" required name="prophylaxis" type="text" value="" id="prophylaxis">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>                        

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Date Started on Previous Regimen</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="date_prev_regimen" type="text" value="" id="date_prev_regimen">
                            </div>                          
                        </div>                      

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Date Started on Current Regimen</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="date_current_regimen" type="text" value="" id="date_current_regimen">
                            </div>                          
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Clinical Indication
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-9">
                                @foreach ($clinical_indications as $clinical_indication)
                                    <div>
                                        <label> 
                                            <input name="clinical_indications[]" type="checkbox" class="i-checks" required
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

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Opportunistic Infections (If Any)</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="date_current_regimen" type="text" value="" id="date_current_regimen">
                            </div>                          
                        </div>

                        <div class="form-group">
                            <div class="col-sm-3">
                                <label><input type="checkbox" class="i-checks" name="has_opportunistic_infections"  

                                    @if(isset($sample) && $sample->has_tb)
                                        checked="checked"
                                    @endif
                                    > 
                                    Has TB
                                </label>
                            </div>
                            <label class="col-sm-3 control-label">Treatment Phase</label>
                            <div class="col-sm-6">
                                @foreach ($tb_treatment_phases as $tb_treatment_phase)
                                    <div>
                                        <label> 
                                            <input name="clinical_indications[]" type="checkbox" class="i-checks" required /> 
                                            {{ $tb_treatment_phase->name }} 
                                        </label>
                                    </div>

                                @endforeach                                
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-3">
                                <label><input type="checkbox" class="i-checks" name="has_opportunistic_infections"  

                                    @if(isset($sample) && $sample->has_tb)
                                        checked="checked"
                                    @endif
                                    > 
                                    Has ARV Toxicity
                                </label>
                            </div>
                            <label class="col-sm-3 control-label">ARV Toxicities</label>
                            <div class="col-sm-6">
                                @foreach ($arv_toxicities as $arv_toxicity)
                                    <div>
                                        <label> 
                                            <input name="clinical_indications[]" type="checkbox" class="i-checks" required /> 
                                            {{ $arv_toxicity->name }} 
                                        </label>
                                    </div>

                                @endforeach                                
                            </div>
                        </div>                     

                        <div class="form-group">
                            <label class="col-sm-3 control-label">CD4 result with the last 6 Months</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="cd4_result" type="text" value="{{ $sample->cd4_result ?? '' }}" id="cd4_result">
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-3">
                                <label><input type="checkbox" class="i-checks" name="has_opportunistic_infections"  

                                    @if(isset($sample) && $sample->has_tb)
                                        checked="checked"
                                    @endif
                                    > 
                                    Has Missed Pills
                                </label>
                            </div>
                            <label class="col-sm-3 control-label">No of Missed Pills</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="datecollected" type="text" value="" id="datecollected">          
                            </div>
                        </div> 


                        <div class="form-group">
                            <div class="col-sm-3">
                                <label><input type="checkbox" class="i-checks" name="has_opportunistic_infections"  

                                    @if(isset($sample) && $sample->has_tb)
                                        checked="checked"
                                    @endif
                                    > 
                                    Has Missed Visits
                                </label>
                            </div>
                            <label class="col-sm-3 control-label">No of Missed Visits</label>
                            <div class="col-sm-6">
                                <input class="form-control" name="datecollected" type="text" value="" id="datecollected">          
                            </div>
                        </div> 


                        <div class="form-group">
                            <div class="col-sm-6">
                                <label><input type="checkbox" class="i-checks" name="has_opportunistic_infections"  

                                    @if(isset($sample) && $sample->has_tb)
                                        checked="checked"
                                    @endif
                                    > 
                                    Has Missed Pills Because of Missed Visits
                                </label>
                            </div>
                        </div> 

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




                        <div class="form-group">
                            <label class="col-sm-3 control-label">Date of Collection
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                            </label>
                            <div class="col-sm-9">
                                <input class="form-control" name="datecollected" type="text" value="" id="datecollected">
                            </div>                           
                        </div>

                    </div>
                </div>
            </div>
        </div>

    <!-- </div> -->

    <!-- Footer-->
    <footer class="footer">
        <center>&copy; NASCOP 2010 - {{ @Date('Y') }} | All Rights Reserved</center>
    </footer>

<!-- </div> -->

<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/iCheck/icheck.min.js') }}"></script>

</body>
</html>
