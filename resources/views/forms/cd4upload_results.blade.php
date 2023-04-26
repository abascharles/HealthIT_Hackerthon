@extends('layouts.master')

@component('/forms/css')
        <link href="{{ asset('css/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Update Results for Worksheet No {{ $worksheet->id }}
                </h2>
            </div>
        </div>
    </div>



   <div class="content">
        <div>

        <form action="{{ url('/cd4/worksheet/upload/' . $worksheet->id) }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" value="{{ auth()->user()->id }}" name="uploadedby">
            <input type="hidden" value="{{ date('Y-m-d') }}" name="dateuploaded">
            <input type="hidden" value="2" name="status_id">


            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center>Worksheet Information</center>
                        </div>
                        <div class="panel-body">

                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <th rowspan="2"><br>Worksheet No</th>
                                    <td rowspan="2"><br>{{ $worksheet->id }}</td>
                                    <th>Created By</th>
                                    <td>{{ $worksheet->creator->full_name ?? '' }}</td>
                                    <th>Tru Count Lot #</th>
                                    <td>{{ $worksheet->TruCountLotno ?? '' }}</td>
                                    <th>Multicheck Normal Lot # </th>
                                    <td>{{ $worksheet->MulticheckNormalLotno ?? '' }}></td>
                                </tr>
                                <tr>
                                    <th>Date Created</th>
                                    <td>{{ gmdate('d-M-Y') }}</td>
                                    <th>Antibody Lot #</th>
                                    <td>{{ $worksheet->AntibodyLotno ?? '' }}</td>
                                    <th>Multicheck Low Lot #</th>
                                    <td>{{ $worksheet->MulticheckLowLotno ?? '' }}</td>
                                </tr>
                            </table>

                            <div class="hr-line-dashed"></div>

                            <p>Locate Excel file name to import. The file must be an excel file eg {{ $worksheet->id }}.xlsx </p>


                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput">
                                    <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new">Select Excel</span>
                                    <span class="fileinput-exists">Change</span>
                                    <input id="upload" type="file" required accept=".xlsx, .xls, .csv" name="upload" >
                                </span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                            </div>


                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <center><button class="btn btn-success" type="submit">Submit</button></center>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>

        </form>

      </div>
    </div>

@endsection

@section('scripts')

    @component('/forms/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/jasny/jasny-bootstrap.min.js') }}"></script>
        @endslot
    @endcomponent


@endsection
