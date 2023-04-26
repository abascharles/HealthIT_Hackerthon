@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data-table" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Patient</th>
                                    <th>Facility</th>
                                    <th>Batch No</th>
                                    <th>Received Status</th>
                                    <th>Spots</th>
                                    <th>Date Collected</th>
                                    <th>Date Received</th>
                                    <th>Worksheet</th>
                                    <th>Date Tested</th>
                                    <th>Date Modified</th>
                                    <th>Date Dispatched</th>
                                    <th>Run</th>
                                    <th>Result</th>
                                    <th>Task</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($samples as $key => $sample)
                                    @continue($sample->repeatt == 1)
                                    <?php
                                        if(!$sample->batch) unset($sample->batch);
                                    ?>
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $patient->patient ?? '' }} </td>
                                        <td> {{ $patient->facility->name ?? '' }} </td>
                                        <td>  {!! $sample->get_link('batch_id') !!} </td>
                                        <td>
                                            @foreach($received_statuses as $received_status)
                                                @if($sample->receivedstatus == $received_status->id)
                                                    {{ $received_status->name ?? '' }}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td> {{ $sample->spots ?? '' }} </td>
                                        <td> {{ $sample->my_date_format('datecollected') ?? '' }} </td>
                                        <td> {{ $sample->batch->my_date_format('datereceived') ?? '' }} </td>
                                        <td> {!! $sample->get_link('worksheet_id') !!} </td>
                                        <td> {{ $sample->my_date_format('datetested') ?? '' }} </td>
                                        <td> {{ $sample->my_date_format('datemodified') ?? '' }} </td>
                                        <td> {{ $sample->batch->my_date_format('datedispatched') ?? '' }} </td>
                                        <td> {{ $sample->run ?? '' }} </td>
                                        <td>
                                            @foreach($results as $result)
                                                @if($sample->result == $result->id)
                                                    {{ $result->name ?? '' }}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>
                                            {{--@if($sample->batch->batch_complete == 1)--}}
                                            @if($sample->is_ready)
                                                <a href="{{ url('/sample/print/' . $sample->id ) }}" target='_blank'>Print</a> |
                                            @endif

                                            {!! $sample->hyper_link !!}

                                            <a href="{{ url('/sample/' . $sample->id ) }}" target='_blank'>View</a> |
                                            <a href="{{ url('/sample/' . $sample->id . '/edit' ) }}" target='_blank'>Edit</a>
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection