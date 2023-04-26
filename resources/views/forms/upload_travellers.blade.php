@extends('layouts.master')

@component('/forms/css')
        <link href="{{ asset('css/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Upload Travellers
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>


        <form method="POST" action="{{ url('traveller') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
            @csrf

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="alert alert-warning">
                            <center>
                                The file must be a csv or excel file. <br />
                                The first row serves as the column header and is necessary for a successful upload. The columns must be named as below, spaces included. <br />

                                NAME (3 NAMES)<br />
                                GEN <br />
                                AGE(in Years) <br />
                                STATUS <br />
                                ID/PASSPORT <br />
                                MOBILE NO <br />
                                CITIZENSHIP <br />
                                COUNTY <br />
                                ESTATE <br />
                                PCR Result <br />
                                IgM Test result <br />
                                IgM Index Test result <br />
                                IgG/IgM Result <br />
                                Antigen Result <br />
                                 <br />
                                DOB<br />
                                Date Collected<br />
                                Date Received<br />
                                Date Tested<br />
                                Date Dispatched<br />
                                (By default any date missing date will be filled with the current day. Date must be filled in the YYYY-MM-DD 00:00:00 format e.g. {{ date('Y-m-d H:i:s') }}) DOB which is date of birth should be filled in the YYYY-MM-DD format

                            </center>
                        </div>


                        <div class="alert alert-warning">
                            <center>
                                The file must be a csv or excel file.
                            </center>
                        </div>


                        <br />


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
