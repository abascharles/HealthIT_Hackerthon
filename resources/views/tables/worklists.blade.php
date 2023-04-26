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
                    Worklists
                </div>
                <div class="panel-body">

                    <table class="table table-striped table-bordered table-hover data-table" >
                        <thead>
                            <tr>
                                <th> Lab ID </th>
                                <th> Date Created </th>
                                <th> Test Type </th>
                                <th> Status  </th>
                                <th> Facility </th>
                                <th> # Samples </th>
                                <th> Task </th>             
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($worklists as $key => $worklist)
                                <tr>

                                    <td> {{ $worklist->id }} </td>
                                    <td> {{ $worklist->my_date_format('created_at') }} </td>
                                    <td> {{ $worklist->type }} </td>
                                    <td> {{ $worklist->status }} </td>
                                    <td> {{ $worklist->facility->name }} </td>
                                    <td> {{ $worklist->sample_count }} </td>
                                    <td> 
                                        <a href="{{ url('worklist/print/' . $worklist->id) }}" title="Click to Download Worklist" target='_blank'>
                                            Print
                                        </a> | 
                                        <a href="{{ url('worklist/' . $worklist->id) }}" title="Click to Download Worklist" target='_blank'>
                                            View
                                        </a>   
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


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection