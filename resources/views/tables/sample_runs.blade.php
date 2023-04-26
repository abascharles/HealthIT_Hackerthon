@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')

<!-- <div class="normalheader ">
    <div class="hpanel">
        <div class="panel-body">
            <a class="small-header-action" href="#">
                <div class="clip-header">
                    <i class="fa fa-arrow-up"></i>
                </div>
            </a>

            <div id="hbreadcrumb" class="pull-right m-t-lg">
                <ol class="hbreadcrumb breadcrumb">
                    <li><a href="index-2.html">Dashboard</a></li>
                    <li>
                        <span>Tables</span>
                    </li>
                    <li class="active">
                        <span>DataTables</span>
                    </li>
                </ol>
            </div>
            <h2 class="font-light m-b-xs">
                DataTables
            </h2>
            <small>Advanced interaction controls to any HTML table</small>
        </div>
    </div>
</div> -->
 
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Sample Runs
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data-table" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Sample Code / Patient ID</th>
                                    <th>Lab ID</th>
                                    <th>Original Lab ID</th>
                                    <th>Run</th>
                                    <th>Date Sample Drawn</th>
                                    <th>Date Tested</th>
                                    <th>Worksheet</th>
                                    <th>Interpretation</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($samples as $key => $samp)
                                    <tr>
                                        <td> {{ $key+1 }} </td>
                                        <td> {{ $patient->patient }} </td>
                                        <td> {{ $samp->id }} </td>
                                        <td> {{ $samp->parentid }} </td>
                                        <td> {{ $samp->run }} </td>
                                        <td> {{ $samp->datecollected }} </td>
                                        <td> {{ $samp->datetested }} </td>
                                        <td> {!! $samp->get_link('worksheet_id') !!} </td>
                                        <td> {{ $samp->interpretation }} </td>
                                        <td> {{ $samp->result }} </td>
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