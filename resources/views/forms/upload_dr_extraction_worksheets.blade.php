@extends('layouts.master')

@component('/forms/css')
        <link href="{{ asset('css/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Upload DR Extraction Worksheets
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>


        <form method="POST" action="{{ url('dr_extraction_worksheet/upload_worksheet_excel') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
            @csrf

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div class="hpanel">
                    <div class="panel-body">


                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">Select Excel/CSV</span>
                                <span class="fileinput-exists">Change</span>
                                <input id="upload" type="file" required accept=".xlsx, .xls, .csv" name="upload" >
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                        </div>



                        <div class="form-group">
                            <div class="col-sm-8 col-sm-offset-4">
                                <button class="btn btn-success" type="submit">Submit</button>
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
