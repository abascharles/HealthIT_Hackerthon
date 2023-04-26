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
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    Create Worklist of {{ $worklist_type }} Samples To Be Run {{ date('d-M-Y') }}
                </div>
                <div class="panel-body">
                    <form action="{{ url('/worklist') }}" class="form-horizontal" method="POST">
                        @csrf

                        @if($worklist_type == "Eid")
                            <input type="hidden" value=1 name="testtype">
                        @else
                            <input type="hidden" value=2 name="testtype">
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr> 
                                        <th>No</th>
                                        <th id="check_all">Check All</th>

                                        <th>Lab ID</th>
                                        <th>Patient CCC No</th>
                                        <th>Sex</th>
                                        <th>Age
                                            @if($worklist_type == "Eid")
                                                (Months)
                                            @else
                                                (Years)
                                            @endif
                                        </th>
                                        <th>DOB</th>
                                        <th>Collection Date</th>
                                        <th>Received Status</th>
                                        <th>Date Entered</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    {{--  These samples are coming from views hence they have everything --}}
                                    @foreach($samples as $key => $sample)
                                        <tr>
                                            <td> {{ $key+1 }} </td>
                                            <td>
                                                <div align='center'>
                                                    <input name='samples[]' type='checkbox' class='checks' value='{{ $sample->id }}' />
                                                </div>
                                            </td>

                                            <td> {{ $sample->id }} </td>
                                            <td> {{ $sample->patient }} </td>
                                            <td> {{ $sample->gender }} </td>
                                            <td> {{ $sample->age }} </td>
                                            <td> {{ $sample->my_date_format('dob') }} </td>
                                            <td> {{ $sample->my_date_format('datecollected') }} </td>
                                            <td> {{ $sample->received }} </td>
                                            <td> {{ $sample->my_date_format('created_at') }} </td>
                                        </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>

                        <div class="row">

                            <div class="col-sm-10 col-sm-offset-1">
                                <button class="btn btn-success" type="submit" name="submit_type" value="accepted">Generate Worklist For Selected Samples</button>
                                <button class="btn btn-danger" type="submit" name="submit_type" value="rejected">Cancel Worklist Creation</button>
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

        $("#check_all").on('click', function(){
            var str = $(this).html();
            if(str == "Check All"){
                $(".checks").prop('checked', true);
                $(this).html("Uncheck All");
            }
            else{
                $(".checks").prop('checked', false); 
                $(this).html("Check All");           
            }
        });

    @endcomponent

@endsection