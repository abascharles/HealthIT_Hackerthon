@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')
 
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Transfer Samples to Another Lab
                </div>
                <div class="panel-body">

                    <form action="{{ url($pre . 'sample/transfer_samples') }}" class="form-horizontal" method="POST" id='approve_batch_form'>
                        @csrf


                        <!-- <div class="alert alert-warning">
                            <center>
                                Please fill the date received before proceeding. <br />
                                For every rejected sample, please fill the rejected reason.
                            </center>
                        </div>
                        <br /> -->

                        <div class="row">

                          <div class="form-group">
                              <label class="col-sm-4 control-label">Facility 
                                <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                              </label>
                              <div class="col-sm-8">
                                <select class="form-control requirable" @if($pre != 'covid_') required @endif name="facility_id" id="facility_id">
                                    @if($facility)
                                        <option value="{{ $facility->id }}" selected>{{ $facility->facilitycode }} {{ $facility->name }}</option>
                                    @endif

                                </select>
                              </div>
                          </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Labs
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <select class="form-control requirable" required name="lab">
                                        <option></option>
                                        @foreach ($labs as $lab)
                                            @continue(in_array($lab->id, [env('APP_LAB'), 7, 10, 8, 9]))
                                            <option value="{{ $lab->id }}"> {{ $lab->name }}  </option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            
                        </div>



                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr> 

                                        <th>No</th>
                                        <th id="check_all">Check</th>

                                        <th>Lab ID</th>
                                        @if($pre == 'covid_')
                                            <th>Patient ID</th>
                                            <th>Patient Name</th>
                                        @else
                                            <th>Batch ID</th>
                                            <th>Patient ID</th>
                                        @endif
                                        <th>Facility</th>
                                        <th>Entered By</th>
                                        <th>Entry Type</th>
                                        <th>Sex</th>
                                        <th>DOB</th>
                                        <th>Age</th>
                                        <th>Date Collected</th>
                                        <th>Date Entered</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @foreach($samples as $key => $sample)
                                        <tr>
                                            <td> {{ $key+1 }} </td>
                                            <td>
                                                <div align='center'>
                                                    <input name='samples[]' type='checkbox' class='checks sample_ids' value='{{ $sample->id }}' />
                                                </div>
                                            </td>

                                            <td> {{ $sample->id }} </td>
                                            @if($pre == 'covid_')
                                                <td> {{ $sample->identifier }} </td>
                                                <td> {{ $sample->patient_name }} </td>
                                            @else
                                                <td> {!! $sample->get_link('batch_id') !!} </td>
                                                <td> {!! $sample->get_link('patient_id') !!} </td>
                                            @endif
                                            <td> {{ $sample->facilitycode . ' ' . $sample->facilityname }} </td>
                                            @if($sample->site_entry)
                                                <td> {{ $sample->creator->facility->name ?? '' }} </td>
                                                <td>Site Entry</td>
                                            @else
                                                <td> {{ $sample->creator->full_name ?? '' }} </td>
                                                <td>Lab Entry</td>
                                            @endif
                                            <td> {{ $sample->gender }} </td>
                                            <td> {{ $sample->my_date_format('dob') }} </td>
                                            <td> {{ $sample->age }} </td>
                                            <td> {{ $sample->my_date_format('datecollected') }} </td>
                                            <td> {{ $sample->my_date_format('created_at') }} </td>

                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>

                        {{ $samples->links() }}

                        <div class="row">
                            <div class="col-sm-10 col-sm-offset-1">
                                <button class="btn btn-success" type="submit" id="accept_samples" name="submit_type" value="accepted">Transfer Samples</button>
                            </div>                        
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

        set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

    @endcomponent

    <script type="text/javascript">
        $(document).ready(function(){
            $("#facility_id").change(function(){
                var url = "{{ url($pre . 'sample/transfer_samples') }}";
                var val = $(this).val();
                window.location.href = url + '/' + val;
            });
        });
        
    </script>

@endsection