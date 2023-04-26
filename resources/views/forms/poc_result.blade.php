@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Update Result
                </h2>
            </div>
        </div>
    </div>



   <div class="content">
        <div>

        <form action="{{ url($pre . 'sample/' . $sample->id . '/edit_result') }}" class="form-horizontal" method="POST" >
            @csrf
            @method('PUT')

            <input type="hidden" value="{{ auth()->user()->id }}" name="approvedby">
            <input type="hidden" value="{{ auth()->user()->id }}" name="approvedby2">
            <input type="hidden" value="{{ date('Y-m-d') }}" name="dateapproved">
            <input type="hidden" value="{{ date('Y-m-d') }}" name="dateapproved2">
            <input type="hidden" value="{{ date('Y-m-d') }}" name="datemodified">
            <input type="hidden" value="{{ $sample->batch->datereceived }}" id="datereceived">

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
                                    <input class="form-control" disabled type="text" value="{{ $sample->batch->facility->name ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Patient</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled type="text" value="{{ $sample->patient->patient ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date Collected</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled type="text" value="{{ $sample->datecollected ?? '' }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date Received</label>
                                <div class="col-sm-8">
                                    <input class="form-control" disabled type="text" value="{{ $sample->batch->datereceived ?? '' }}">
                                </div>
                            </div>   

                            @if($sample->batch->site_entry == 2)

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">POC Site Sample Tested at
                                        <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control" required name="lab_id" id="lab_id">
                                            @if($sample->batch->facility_lab)
                                                <option value="{{ $sample->batch->facility_lab->id }}" selected>{{ $sample->batch->facility_lab->facilitycode }} {{ $sample->batch->facility_lab->name }}</option>
                                            @else
                                                <option value="{{ $sample->batch->facility->id }}" selected>{{ $sample->batch->facility->facilitycode }} {{ $sample->batch->facility->name }}</option>
                                            @endif

                                        </select>
                                    </div>
                                </div>

                            @endif

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Date Tested
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="datetested" @if(env('APP_LAB') != 7) required @endif class="form-control" value="{{ $sample->datetested ?? '' }}" name="datetested">
                                    </div>
                                </div>                            
                            </div>

                            @if($pre == '')

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Result
                                        <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                    </label>
                                    <div class="col-sm-8">
                                        <select class="form-control" required name="result">

                                            <option value=""> Select One </option>
                                            @foreach ($results as $result)
                                                @continue($result->id == 3 || $result->id == 4)
                                                <option value="{{ $result->id }}"

                                                @if (isset($sample) && $sample->result == $result->id)
                                                    selected
                                                @endif

                                                > {{ $result->name }}
                                                </option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>

                            @else

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Result (Check if result is Target Not Detected. If the result is numerical, fill the field below.)
                                        <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                    </label>

                                    <div class="col-sm-3">
                                        <label> <input type="radio" class="i-checks" name="result" value="< LDL copies/ml"/> &lt; LDL copies/ml </label>
                                    </div>

                                    <div class="col-sm-3">
                                        <label> <input type="radio" class="i-checks" name="result" value="Collect New Sample"/> Collect New Sample </label>
                                    </div>

                                    <div class="col-sm-2">
                                        <label> <input type="radio" class="i-checks" name="result" value="< 40 Copies/ mL"/> &lt;40 </label>
                                    </div>
                                </div> 

                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Result</label>
                                    <div class="col-sm-8"><input class="form-control" name="result_2" type="text"  number="number"

                                            @if(isset($sample) && is_numeric($sample->result))
                                                value="{{ $sample->result ?? '' }}"
                                            @endif

                                        ></div>
                                </div>

                            @endif

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Update Result</button>
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
                datetested: {
                    greaterThan: ["#datereceived", "Date Tested", "Date Received"]
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

        set_select_facility("lab_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

    @endcomponent


@endsection
