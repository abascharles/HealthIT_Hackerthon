@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="row" style="margin-bottom: 1em;">
                <!-- Year -->
                <div class="col-md-6">
                    <center><h5>Year Filter</h5></center>
                    @for ($i = 0; $i <= 9; $i++)
                        @php
                            $year=Date('Y')-$i
                        @endphp
                        <a href='{{ url("users/activity/null/$year") }}'>{{ Date('Y')-$i }}</a> |
                    @endfor
                </div>
                <!-- Year -->
                <!-- Month -->
                <div class="col-md-6">
                    <center><h5>Month Filter</h5></center>
                    @for ($i = 1; $i <= 12; $i++)
                        <a href='{{ url("users/activity/null/null/$i") }}'>{{ date("F", strtotime(date("Y") ."-". $i ."-01")) }}</a> |
                    @endfor
                </div>
                <!-- Month -->
            </div>
            <div class="hpanel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th rowspan="2">#</th>
                                    <th rowspan="2">Full Names</th>
                                    <th rowspan="2">Email</th>
                                    <th colspan="2"><center>Samples Entered ({{ $data->year }} {{ $data->monthName }})</center></th>
                                    <th colspan="2"><center>Site Samples Approved ({{ $data->year }} {{ $data->monthName }})</center></th>
                                    <th rowspan="2">Action</th>
                                </tr>
                                <tr>
                                    <th><center>EID</center></th>
                                    <th><center>VL</center></th>
                                    <th><center>EID</center></th>
                                    <th><center>VL</center></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $key => $user)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->email ?? '' }}</td>
                                    <td>{{ $user->samples_entered('EID', $data->year, $data->month) }}</td>
                                    <td>{{ $user->samples_entered('VL', $data->year, $data->month) }}</td>
                                    <td>{{ $user->sitesamplesapproved('EID', $data->year, $data->month) }}</td>
                                    <td>{{ $user->sitesamplesapproved('VL', $data->year, $data->month) }}</td>
                                    <td><a class='btn btn-success btn-xs' href='{{ url("users/activity/$user->id") }}'>View Log</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {{-- <div class="hpanel">
                <div class="panel-head">
                    Daily Individual Performance <small>(This part is not affected by the filters)</small>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>Full Names</th>
                                    <th>Email</th>
                                    <th>Samples Logged/Approved</th>
                                    <th>Worksheets Sorted</th>
                                    <th>Worksheets Aliquoted</th>
                                    <th>Worksheets Run</th>
                                    <th>Samples Dispatched</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $key => $user)
                                <tr>
                                    <td>{{ $user->full_name }}</td>
                                    <td>
                                        {{ $user->samplesLoggedToday() }}
                                        &nbsp;/&nbsp;
                                        {{ $user->samplesApprovedToday() }}
                                    </td>
                                    <td>{{ $user->worksheetsSortedToday() }}</td>
                                    <td>{{ $user->worksheetsAliquotedToday() }}</td>
                                    <td>{{ $user->worksheetsRunToday() }}</td>
                                    <td>{{ $user->samplesDispatchedToday() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection