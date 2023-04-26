@extends('layouts.master')

@component('/tables/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

<div class="content">

    <br />
        
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                    </div>
                    Imported Worksheets
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover" >
                        <thead>
                            <tr>
                                <th> W No </th>
                                <th> Date Created </th>
                                <th> Created By </th>
                                <th> Type </th>
                                <th> Status </th>
                                <th> Sample Type </th>
                                <th> Date Run </th>
                                <th> Date Updated </th>
                                <th> Date Reviewed </th>
                                <th> Task </th>                 
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($worksheets as $key => $worksheet)
                            <tr>
                                <td>{{ $worksheet->id }} </td>
                                <td> {{ $worksheet->my_date_format('created_at') }} </td>
                                <td> {{ $worksheet->creator->full_name ?? '' }} </td>

                                <td> {!! $worksheet->get_prop_name($machines, 'machine_type', 'output') !!} </td>
                                <td> {!! $worksheet->get_prop_name($worksheet_statuses, 'status_id', 'output') !!} </td>
                                <td>{{ $worksheet->sample_type_name }} </td>

                                <td> {{ $worksheet->my_date_format('daterun') }} </td>
                                <td> {{ $worksheet->my_date_format('dateuploaded') }} </td>
                                <td> {{ $worksheet->my_date_format('datereviewed') }} </td>
                                <td> 
                                    @include('shared.worksheet_links', ['worksheet' => $worksheet])
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
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot
    @endcomponent

    <script type="text/javascript">
        $(document).ready(function(){

        });
        
    </script>

@endsection