@extends('layouts.master')

    @component('/tables/css')
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
                    VL EDARP Samples for Approval to go to Nascop 
                </div>
                <div class="panel-body">

                    <div class="table-responsive">
                        <form  method="post" action="{{ url('viralsample/nhrl') }}"
                            class="confirmSubmit">
                            @csrf

                            <table class="table table-striped table-bordered table-hover data-table" >
                                <thead>
                                    <tr>
                                        <th id="check_all">Check All</th>
                                        <th> Facility Code </th>
                                        <th> Specimen Label ID </th>
                                        <th> Client Code </th>
                                        <th> Age </th>
                                        <th> Gender </th>
                                        <th> Regimen Code </th>
                                        <th> Justification </th>
                                        <th> Sample Type </th>
                                        <th> ART Initiation </th>
                                        <th> Date Collected </th>
                                        <th> Date Received </th>
                                        <th> Date Tested </th>
                                        <th> Result </th>
                                        <th> Date Dispatched </th>
                                        <th> Date Uploaded On Nascop </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($samples as $sample)
                                        <tr>
                                            <td>
                                                <div align='center'>
                                                    <input name='samples[]' type='checkbox' class="checks" value='{{ $sample->id }}' />
                                                </div>
                                            </td>

                                            <td> {{ $sample->batch->facility->facilitycode }} </td>
                                            <td> {{ $sample->comment }} </td>
                                            <td> {{ $sample->patient->patient }} </td>
                                            <td> {{ $sample->age }} </td>
                                            <td> {{ $sample->patient->gender }} </td>
                                            <td> {{ $sample->prophylaxis }} </td>
                                            <td> {{ $sample->justification }} </td>
                                            <td> {{ $sample->sampletype }} </td>
                                            <td> {{ $sample->patient->initiation_date }} </td>
                                            <td> {{ $sample->datecollected }} </td>
                                            <td> {{ $sample->datereceived }} </td>
                                            <td> {{ $sample->datetested }} </td>
                                            <td> {{ $sample->result }} </td>
                                            <td> {{ $sample->datedispatched }} </td>
                                            <td> {{ $sample->created_at->toDateString() }} </td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            <button class="btn btn-success" type="submit" name="submit_type" value="release">Approve Selected Samples as OK to go to NASCOP</button>
                            <button class="btn btn-success" type="submit" name="submit_type" value="delete">Delete Selected Samples to be Resent Afresh from EDARP</button>
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