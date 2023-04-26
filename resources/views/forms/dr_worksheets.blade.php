@extends('layouts.master')

    @component('/forms/css')
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
                    Drug Resistance Worksheet
                </div>
                <div class="panel-body">


                    @if($create)
                        @if(isset($worksheet))
                        <form action="{{ url('/dr_worksheet/' . $worksheet->id) }}" class="form-horizontal" method="POST" target="_blank">
                            @method('PUT')
                        @else
                        <form action="{{ url('/dr_worksheet') }}" class="form-horizontal" method="POST" target="_blank">

                            <input type="hidden" value="{{ env('APP_LAB') }}" name="lab_id">
                            <input type="hidden" value="{{ auth()->user()->id }}" name="createdby">
                            <input type="hidden" value="{{ $extraction_worksheet_id }}" name="extraction_worksheet_id">
                        @endif

                            @csrf

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th id="check_all">Check All </th>
                                        <th>Sample Type</th>
                                        <th>Sample Code / Patient ID</th>
                                        <th>NAT ID</th>
                                        <th>Facility</th>
                                        <th>Lab ID</th>
                                        <th>Original Lab ID</th>
                                        <th>Run</th>
                                        <th>Date Received</th>
                                        <th>Reason</th>
                                        <th>Patient History</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @foreach($samples as $key => $dr_sample)
                                        <tr>
                                            <td> {{ $key+1 }} </td>
                                            <td>
                                                <div align='center'>
                                                    <input name='samples[]' type='checkbox' class='checks' value='{{ $dr_sample->id }}' />
                                                </div>
                                            </td>
                                            <td> {{ $sampletypes->where('id', $dr_sample->sampletype)->first()->name ?? '' }} </td>
                                            <td> {{ $dr_sample->patient ?? '' }} </td>
                                            <td> {{ $dr_sample->nat ?? '' }} </td>
                                            <td> {{ $dr_sample->facilityname ?? '' }} </td>
                                            <td> {{ $dr_sample->id }} </td>
                                            <td> {{ $dr_sample->parentid }} </td>
                                            <td> {{ $dr_sample->run }} </td>
                                            <td> {{ $dr_sample->my_date_format('datereceived') }} </td>
                                            <td> {{ $drug_resistance_reasons->where('id', $dr_sample->dr_reason_id)->first()->name ?? '' }} </td>
                                            <td>
                                                <a href="{{ url('viralpatient/' . $dr_sample->patient_id) }}" target="_blank">
                                                    View History 
                                                </a> 
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(env('APP_LAB') != 7)

                            <div class="form-group"  >
                                <label class="col-sm-3 control-label">Create Control Samples </label>
                                <div class="col-sm-9">
                                    <div>
                                        <label> 
                                            <input name="control_samples" type="checkbox" class="i-checks" value="1" checked="checked" /> 
                                            Yes 
                                        </label>
                                    </div>
                                </div>
                            </div>

                        @endif

                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-4">
                                <button class="btn btn-success" type="submit">Save & Download Worksheet</button>
                            </div>
                        </div>
                        </form>

                    @else

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="hpanel">
                                    <div class="panel-body"> 
                                        <div class="alert alert-warning">
                                            <center>
                                                You cannot create a worksheet now. Only {{ $samples->count() }} samples eligible for testing.
                                            </center>
                                        </div>
                                    <br />
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/forms/scripts')

        $("#check_all").on('click', function(){
            var str = $(this).html();
            if(str == "Check All"){
                $(this).html("Uncheck All");
                $(".checks").prop('checked', true);
            }
            else{
                $(this).html("Check All");
                $(".checks").prop('checked', false);           
            }
        });

    @endcomponent

@endsection