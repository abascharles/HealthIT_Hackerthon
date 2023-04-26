@extends('layouts.master')

    @component('/forms/css')
    @endcomponent

@section('content')

 
<div class="content">
    <div class="row">

        <div class="alert alert-success">
            <center>
                All samples that are not selected as passed and are not marked as collect new sample will be sent for rerun as a VL sample.
            </center>
        </div>        
        <br />
        
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Drug Resistance Gel Documentation
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <form action="{{ url('dr_extraction_worksheet/gel_documentation/' . ($worksheet->id ?? '')) }}" class="form-horizontal" method="POST" target="_blank">
                            @csrf
                            @method('PUT')
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th id="check_all">Passed (Check All)</th>
                                        <th>Collect New Sample</th>
                                        <th>Sample Type</th>
                                        <th>Sample Code / Patient ID</th>
                                        <th>Nat No</th>
                                        <th>Facility</th>
                                        <th>Lab ID</th>
                                        <th>Date Received</th>
                                        <th>Reason</th>
                                        <th>Patient History</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @foreach($samples as $key => $sample)
                                        <tr>
                                            <td> {{ $key+1 }} </td>
                                            <td>
                                                <div align='center'>
                                                    <input name='samples[]' type='checkbox' class='checks' value='{{ $sample->id }}' 
                                                        @if($sample->passed_gel_documentation)
                                                            checked='checked'
                                                        @endif
                                                    />
                                                </div>
                                            </td>
                                            <td>
                                                <div align='center'>
                                                    <input name='cns[]' type='checkbox' class='other_checks' value='{{ $sample->id }}' />
                                                </div>
                                            </td>
                                            <td> {{ $sample->control_type }} </td>
                                            <td> {{ $sample->patient }} </td>
                                            <td> {{ $sample->nat }} </td>
                                            <td> {{ $sample->facilityname ?? '' }} </td>
                                            <td> {{ $sample->id }} </td>
                                            <td> {{ $sample->my_date_format('datereceived') }} </td>
                                            <td> {{ $drug_resistance_reasons->where('id', $sample->dr_reason_id)->first()->name ?? '' }} </td>
                                            <td>
                                                <a href="{{ url('viralpatient/' . $sample->patient_id) }}" target="_blank">
                                                    View History 
                                                </a> 
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button class="btn btn-success" type="submit">Proceed to Submit Gel Documentation</button>
                        </form>
                    </div>
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
            if(str == "Passed (Check All)"){
                $(this).html("Uncheck All");
                $(".checks").prop('checked', true);
            }
            else{
                $(this).html("Passed (Check All)");
                $(".checks").prop('checked', false);           
            }
        });

    @endcomponent

@endsection