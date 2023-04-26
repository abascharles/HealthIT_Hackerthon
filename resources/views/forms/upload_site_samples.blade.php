@extends('layouts.master')

@component('/forms/css')
        <link href="{{ asset('css/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Upload Facility Samples
                </h2>
            </div>
        </div>
    </div>



   <div class="content">
        <div>


        <form method="POST" action="{{ url($url . '/upload') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
            @csrf



        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div class="hpanel">
                    <div class="panel-body">

                        @if(Str::contains($url, ['covid']) && in_array(auth()->user()->lab_id, [2,4,5,6]))

                        <div class="alert alert-warning">
                            <center>
                                The file must be a csv or excel file. <br />
                                The first row serves as the column header and is necessary for a successful upload. The columns must be named as below, spaces included. <br />
                                <b> Required Columns </b> <br />
                                MFL Code <br />
                                (Quarantine Site ID) Can be used when MFL Code is not present. Add as a separate column. <br />
                                Identifier<br />
                                Patient Name<br />
                                Gender<br />
                                Age<br />
                                <b> Optional Columns </b> <br />
                                Justification (default is 3 for surveillance)<br />
                                &nbsp;&nbsp;&nbsp; 1 => Contact with confirmed case  <br />
                                &nbsp;&nbsp;&nbsp; 2 => Presented at health facility  <br />
                                &nbsp;&nbsp;&nbsp; 3 => Surveillance  <br />
                                &nbsp;&nbsp;&nbsp; 4 => Point of entry detection  <br />
                                &nbsp;&nbsp;&nbsp; 5 => Repatriation  <br />
                                &nbsp;&nbsp;&nbsp; 6 => Other  <br />
                                &nbsp;&nbsp;&nbsp; 7 => Surveillance and Quarantine  <br />
                                &nbsp;&nbsp;&nbsp; 8 => Recent travel  <br />
                                &nbsp;&nbsp;&nbsp; 9 => Health Care Worker  <br />
                                &nbsp;&nbsp;&nbsp; 10 => Truck Driver  <br />
                                &nbsp;&nbsp;&nbsp; 11 => Food Handlers  <br />
                                &nbsp;&nbsp;&nbsp; 12 => Before medical/surgical procedure  <br />
                                &nbsp;&nbsp;&nbsp; 13 => Travel to Country/County with presumed widespread COVID-19  <br />
                                &nbsp;&nbsp;&nbsp; 14 => Meets case definition  <br />
                                &nbsp;&nbsp;&nbsp; 15 => Acute severe respiratory illness  <br />
                                &nbsp;&nbsp;&nbsp; 16 => Air Travel  <br />
                                Test Type (1 for Initial, 2 for 1st repeat, 3 for 2nd repeatt ...)<br />
                                Symptoms (A comma separated list of all symptom codes)
                                &nbsp;&nbsp;&nbsp; 1 => Fever / Chills  <br />
                                &nbsp;&nbsp;&nbsp; 2 => Shortness of Breath  <br />
                                &nbsp;&nbsp;&nbsp; 3 => Muscular Pain  <br />
                                &nbsp;&nbsp;&nbsp; 4 => General Weakness  <br />
                                &nbsp;&nbsp;&nbsp; 5 => Diarrhoea  <br />
                                &nbsp;&nbsp;&nbsp; 6 => Chest Pain  <br />
                                &nbsp;&nbsp;&nbsp; 7 => Cough  <br />
                                &nbsp;&nbsp;&nbsp; 8 => Nausea / vomiting  <br />
                                &nbsp;&nbsp;&nbsp; 9 => Abdominal Pain  <br />
                                &nbsp;&nbsp;&nbsp; 10 => Sore Throat  <br />
                                &nbsp;&nbsp;&nbsp; 11 => Headache  <br />
                                &nbsp;&nbsp;&nbsp; 12 => Joint Pain  <br />
                                &nbsp;&nbsp;&nbsp; 13 => Runny Nose  <br />
                                &nbsp;&nbsp;&nbsp; 14 => Irritability / Confusion  <br />

                                Vaccination Status <br />
                                &nbsp;&nbsp;&nbsp; 1 => Not Vaccinated  <br />
                                &nbsp;&nbsp;&nbsp; 2 => Partially Vaccinated (One Dose  <br />
                                &nbsp;&nbsp;&nbsp; 3 => Fully Vaccinated  <br />
                                Email Address<br />
                                Phone Number<br />
                                National ID<br />
                                Occupation<br />
                                County<br />
                                Subcounty<br />
                                Residence<br />
                                Date Collected<br />
                                Date Received<br />
                                (By default any date missing date will be filled with the current day. Date must be filled in the YYYY-MM-DD format e.g. {{ date('Y-m-d') }})

                            </center>
                        </div>

                        @elseif(Str::contains($url, ['covid']) && auth()->user()->lab_id == 1)

                        <div class="alert alert-warning">
                            <center>
                                The file must be a csv or excel file. <br />
                                The first row serves as the column header and is necessary for a successful upload. The columns must be named as below, spaces included. <br />
                                <b> Required Columns </b> <br />
                                MFL Code <br />
                                Quarantine Site ID <br />
                                Patient ID (This is the identifier. If it is blank, the system will fill the identifier with the patient's name)<br />
                                Name<br />
                                Sex<br />
                                Age<br />
                                <b> Optional Columns </b> <br />
                                National ID<br />
                                Justification (default is 3 for surveillance)<br />
                                &nbsp;&nbsp;&nbsp; 1 => Contact with confirmed case  <br />
                                &nbsp;&nbsp;&nbsp; 2 => Presented at health facility  <br />
                                &nbsp;&nbsp;&nbsp; 3 => Surveillance  <br />
                                &nbsp;&nbsp;&nbsp; 4 => Point of entry detection  <br />
                                &nbsp;&nbsp;&nbsp; 5 => Repatriation  <br />
                                &nbsp;&nbsp;&nbsp; 6 => Other  <br />
                                &nbsp;&nbsp;&nbsp; 7 => Surveillance and Quarantine  <br />
                                &nbsp;&nbsp;&nbsp; 8 => Recent travel  <br />
                                &nbsp;&nbsp;&nbsp; 9 => Health Care Worker  <br />
                                &nbsp;&nbsp;&nbsp; 10 => Truck Driver  <br />
                                &nbsp;&nbsp;&nbsp; 11 => Food Handlers  <br />
                                &nbsp;&nbsp;&nbsp; 12 => Before medical/surgical procedure  <br />
                                &nbsp;&nbsp;&nbsp; 13 => Travel to Country/County with presumed widespread COVID-19  <br />
                                &nbsp;&nbsp;&nbsp; 14 => Meets case definition  <br />
                                &nbsp;&nbsp;&nbsp; 15 => Acute severe respiratory illness  <br />
                                &nbsp;&nbsp;&nbsp; 16 => Air Travel  <br />

                                Vaccination Status <br />
                                &nbsp;&nbsp;&nbsp; 1 => Not Vaccinated  <br />
                                &nbsp;&nbsp;&nbsp; 2 => Partially Vaccinated (One Dose  <br />
                                &nbsp;&nbsp;&nbsp; 3 => Fully Vaccinated  <br />
                                Nationality<br />
                                Telehone Number<br />
                                Email Address<br />
                                Occupation<br />
                                County of Residence<br />
                                Sub-county<br />
                                Residence<br />
                                Date Collected<br />
                                Time Collected<br />
                                Date Received<br />
                                Time Received<br />
                                Date Tested<br />
                                Time Tested<br />
                                (By default any date missing date will be filled with the current day. Date must be filled in the YYYY-MM-DD format e.g. {{ date('Y-m-d') }})

                            </center>
                        </div>

                        @elseif(Str::contains($url, ['covid']) && env('APP_LAB') == 9)

                        <div class="alert alert-warning">
                            <center>
                                The file must be a csv or excel file. <br />
                                The first row serves as the column header and is necessary for a successful upload. The columns must be named as below, spaces included. <br />
                                <b> Required Columns </b> <br />
                                MFL Code <br />
                                Identifier<br />
                                Patient Name<br />
                                Gender<br />
                                Age<br />
                                <b> Optional Columns </b> <br />
                                Justification (default is 3 for surveillance)<br />
                                &nbsp;&nbsp;&nbsp; 1 => Contact with confirmed case  <br />
                                &nbsp;&nbsp;&nbsp; 2 => Presented at health facility  <br />
                                &nbsp;&nbsp;&nbsp; 3 => Surveillance  <br />
                                &nbsp;&nbsp;&nbsp; 4 => Point of entry detection  <br />
                                &nbsp;&nbsp;&nbsp; 5 => Repatriation  <br />
                                &nbsp;&nbsp;&nbsp; 6 => Other  <br />
                                &nbsp;&nbsp;&nbsp; 7 => Surveillance and Quarantine  <br />
                                &nbsp;&nbsp;&nbsp; 8 => Recent travel  <br />
                                &nbsp;&nbsp;&nbsp; 9 => Health Care Worker  <br />
                                &nbsp;&nbsp;&nbsp; 10 => Truck Driver  <br />
                                &nbsp;&nbsp;&nbsp; 11 => Food Handlers  <br />
                                &nbsp;&nbsp;&nbsp; 12 => Before medical/surgical procedure  <br />
                                &nbsp;&nbsp;&nbsp; 13 => Travel to Country/County with presumed widespread COVID-19  <br />
                                &nbsp;&nbsp;&nbsp; 14 => Meets case definition  <br />
                                &nbsp;&nbsp;&nbsp; 15 => Acute severe respiratory illness  <br />
                                &nbsp;&nbsp;&nbsp; 16 => Air Travel  <br />
                                Phone Number<br />
                                Type of Case &nbsp; <i> (Whether it's initial or repeat. Defaults to Initial) </i> <br />
                                National ID<br />
                                Occupation<br />
                                County Of Residence<br />
                                Subcounty Of Residence<br />
                                Village/Estate<br />
                                Date Collected<br />
                                Date Received<br />
                                (By default any date missing date will be filled with the current day. Date must be filled in the YYYY-MM-DD format e.g. 2020-07-15)

                            </center>
                        </div>

                        @elseif(Str::contains($url, ['covid']))

                        <div class="alert alert-warning">
                            <center>
                                The file must be a csv or excel file.
                            </center>
                        </div>

                        @else

                        <div class="alert alert-warning">
                            <center>
                                The file must be a csv file.
                            </center>
                        </div>

                        @endif
                        <br />

                        @if(in_array(env('APP_LAB'), [3]))   
                            @include('partial.select', ['model' => null, 'default_val' => null, 'prop' => 'quarantine_site_id', 'label' => 'Quarantine Site', 'items' => $quarantine_sites])
                          <div class="form-group">
                              <label class="col-sm-4 control-label">Facility</label>
                              <div class="col-sm-8">
                                <select class="form-control requirable" name="facility_id" id="facility_id">
                                    @isset($sample)
                                        <option value="{{ $sample->batch->facility->id }}" selected>{{ $sample->batch->facility->facilitycode }} {{ $sample->batch->facility->name }}</option>
                                    @endisset

                                </select>
                              </div>
                          </div>
                        @endif
                        <br />


                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                <span class="fileinput-filename"></span>
                            </div>
                            @if(Str::contains($url, ['covid']))
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">Select Excel/CSV</span>
                                <span class="fileinput-exists">Change</span>
                                <input id="upload" type="file" required accept=".xlsx, .xls, .csv" name="upload" >
                            </span>
                            @else
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">Select CSV</span>
                                <span class="fileinput-exists">Change</span>
                                <input id="upload" type="file" required accept=".csv" name="upload" >
                            </span>
                            @endif
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                        </div>



                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-4">
                                <button class="btn btn-success" type="submit">Submit</button>
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
            <script src="{{ asset('js/jasny/jasny-bootstrap.min.js') }}"></script>
        @endslot
        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);
    @endcomponent


@endsection
