@extends('layouts.master')

@component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

<div class="content">    
    
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    CIF Covid-19 Samples
                </div>
                <div class="panel-body">
                    <div class="alert alert-success">
                        Select samples that have been entered in CIF and have arrived at the lab. Select those samples, then submit and those samples will be sent to the LIMS.
                    </div>
                    <div class="table-responsive">
                        <form  method="post" action="{{ url('covid_sample/cif/') }}" onsubmit="return confirm('Are you sure you want to import the selected samples?');">
                            @csrf

                            <table class="table table-striped table-bordered table-hover multi-filter" >
                                <thead>
                                    <tr class="colhead">
                                        <th> # </th>
                                        <th> Border Point </th>
                                        <th> CIF ID </th>
                                        <th> Identifier </th>
                                        <th> National ID </th>
                                        <th> County </th>
                                        <th> Name </th>
                                        <th> DOB </th>
                                        <th> Age </th>
                                        <th> Date Collected </th>
                                        <th> Select Sample </th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach($samples as $key => $sample)
                                        <tr>
                                            <td> {{ $key+1 }} </td>
                                            <td> {{ $sample->border_point ?? '' }} </td>
                                            <td> {{ $sample->cif_sample_id ?? '' }} </td>
                                            <td> {{ $sample->patient->identifier ?? '' }} </td>
                                            <td> {{ $sample->patient->national_id ?? '' }} </td>
                                            <td> {{ $sample->patient->county ?? '' }} </td>
                                            <td> {{ $sample->patient->patient_name ?? '' }} </td>
                                            <td> {{ $sample->patient->dob ?? '' }} </td>
                                            <td> {{ $sample->age ?? '' }} </td>
                                            <td> {{ $sample->datecollected ?? '' }} </td>
                                            <td> 
                                                <div align="center">
                                                    <input name="sample_ids[]" type="checkbox" class="checks" value="{{ $sample->id }}"  />
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th> # </th>
                                        <th> Border Point </th>
                                        <th> CIF ID </th>
                                        <th> Identifier </th>
                                        <th> National ID </th>
                                        <th> County </th>
                                        <th> Name </th>
                                        <th> DOB </th>
                                        <th> Age </th>
                                        <th> Date Collected </th>
                                        <th> Select Sample </th>
                                    </tr>
                                    
                                </tfoot>
                            </table>

                            <button type="submit" class="btn btn-primary">Pull Samples</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot

    @endcomponent

    <script type="text/javascript">
        $(document).ready(function(){
            

            // $("#check_all").on('click', function(){
            //     var str = $(this).html();
            //     if(str == "Check All"){
            //         $(this).html("Uncheck All");
            //         $(".checks").prop('checked', true);
            //     }
            //     else{
            //         $(this).html("Check All");
            //         $(".checks").prop('checked', false);           
            //     }
            // });

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

        });
        
    </script>

@endsection