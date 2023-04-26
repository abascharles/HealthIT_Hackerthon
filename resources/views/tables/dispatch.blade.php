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
                    Batches Awaiting Dispatch
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                    
                        <form  method="post" action="{{ url('batch/complete_dispatch') }}" name="worksheetform"

                            @if($batch_list)
                                onSubmit="return confirm('Are you sure you want to dispatch the selected batches?');"
                            @endif

                          >
                            @csrf

                            @if($batch_list)
                                <input type="hidden" name="final_dispatch" value=1>
                            @endif
                            

                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th id="check_all">Check All</th>
                                        <th> Batch No </th>
                                        <th> Facility </th>
                                        <th> Email Address </th>
                                        <th> Date Received </th>
                                        <th> No. of Samples </th>
                                        <th> Rejected </th>
                                        <th> Date Tested </th>
                                        <th> Date Updated </th>
                                        <th> Positive </th>
                                        <th> Negative </th>
                                        <th> Redraw </th>
                                        <th> Failed </th>
                                        <th> Delay(days) </th>              
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($batches as $batch)
                                        <tr>
                                            <td>
                                                <div align='center'>
                                                    <input name='batches[]' type='checkbox' class='checks' value='{{ $batch->id }}' />
                                                </div>
                                            </td>
                                            <td> {{ $batch->id }} </td>
                                            <td> {{ $batch->name }} </td>
                                            <td> {{ $batch->email }} </td>
                                            <td> {{ $batch->my_date_format('datereceived') }} </td>
                                            <td> {{ $batch->total }} </td>
                                            <td> {{ $batch->rejected }} </td>
                                            <td> {{ $batch->my_date_format('date_tested') }} </td>
                                            <td> {{ $batch->my_date_format('date_modified') }} </td>
                                            <td> {{ $batch->positives }} </td>
                                            <td> {{ $batch->negatives }} </td>
                                            <td> {{ $batch->redraw }} </td>
                                            <td> {{ $batch->failed }} </td>
                                            <td> {{ $batch->tat() }} </td>
                                        </tr>
                                    @endforeach



                                    @php
                                        // echo $rows;
                                    @endphp 
                                </tbody>
                            </table>

                            <button class="btn btn-success" type="submit">Proceed to Confirm Selected Dispatch</button>
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