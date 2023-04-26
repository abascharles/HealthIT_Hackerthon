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
                                    <th>ID</th>
                                    <th>Date Created</th>
                                    <th>Samples Count</th>
                                    <th>Download</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($templates as $key => $template)
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $template->id }} </td>
                                        <td> {{ $template->created_at }} </td>
                                        <td> {{ $template->sample_count }} </td>
                                        <td>
                                            <a href="{{ url('/dr_bulk_registration/' . $template->id) }}">Download</a>
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