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
                    Cervical Cancer Samples List
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>Lab #</th>
                                    <th>Facility</th>
                                    <th>Patient #</th>
                                    <th>Patient Name</th>
                                    <th>Age </th>
                                    <th>Gender</th>
                                    <th>Date Drawn</th>
                                    <th>Received Status</th>
                                    <th>Tested At</th>
                                    <th>Run</th>
                                    <th>Date Tested</th>
                                    <th>Worksheet</th>
                                    <th>Date Dispatched</th>
                                    <th>Result</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($samples as $key => $sample)
                                    <tr>
                                        <td> {{ $sample->id }} </td>
                                        <td> {{ $sample->facility->name ?? '' }} </td>
                                        <td> {{ $sample->patient }} </td>
                                        <td> {{ $sample->patient_name }} </td>
                                        <td> {{ $sample->age }} </td>
                                        <td> {{ $sample->gender ?? '' }} </td>
                                        <td> {{ $sample->my_date_format('datecollected') }} </td>
                                        <td> {{ $sample->received }} </td>
                                        <td> {{ $sample->tested_at }} </td>
                                        <td> {{ $sample->run }} </td>
                                        <td> {{ $sample->my_date_format('datetested') }} </td>
                                        <td> {{ $sample->worksheet->id ?? '' }} </td>
                                        <td> {{ $sample->my_date_format('datedispatched') }} </td>
                                        <td> 
                                            {{ $sample->resultname ?? '' }}
                                        </td>
                                        <td> 
                                            {{--
                                            @if($sample->datedispatched)
                                                <a href="{{ url($pre . 'batch/summary/' . $sample->batch_id) }}" target="_blank"><i class='fa fa-print'></i> Summary</a> |

                                                <a href="{{ url($pre . 'sample/print/' . $sample->id) }}" target="_blank"><i class='fa fa-print'></i> Print</a> |
                                            @endif
                                            --}}

                                            @if(!$sample->result && $sample->receivedstatus == 1)
                                                @if($sample->age_in_days < 30)
                                                    @if(in_array($sample->site_entry, [0, 1]) && !$facility_user)
                                                        <a href="{{ url('cancersample/' . $sample->id . '/edit/') }}" target="_blank" class="btn btn-warning btn-xs" style="margin-bottom: 0.2em;">Edit</a>
                                                        <br/>
                                                        @if(!($sample->result || $param))
                                                            <form action="{{ url('cancersample/' . $sample->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following sample?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-xs btn-danger">Delete</button>
                                                            </form>
                                                        @endif
                                                    @else
                                                        @if(in_array($sample->site_entry, [2]) && $facility_user)
                                                            <a href="{{ url('cancersample/' . $sample->id . '/edit_result/') }}" target="_blank" class="btn btn-success btn-xs" style="margin-bottom: 0.2em;">Update Result</a>
                                                            <br/>
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                @if(in_array($sample->site_entry, [0, 1]) && !$facility_user)
                                                    @if(!$param)
                                                    <a href="{{ url('cancersample/' . $sample->id . '/edit/') }}" target="_blank" class="btn btn-warning btn-xs" style="margin-bottom: 0.2em;">Edit</a>
                                                    <br />
                                                    @endif
                                                @endif
                                                <a href="{{ url('cancersample/' . $sample->id . '/print/') }}" target="_blank" class="btn btn-default btn-xs" style="margin-bottom: 0.2em;">Print</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                        {{ $samples->links() }}
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