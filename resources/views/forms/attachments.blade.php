@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Add Attachment
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        <form action="{{ url('/email/attachment/' . $email->id) }}" class="form-horizontal" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center> </center>
                        </div>
                        <div class="panel-body">



                            <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                <div class="form-control" data-trigger="fileinput">
                                    <i class="glyphicon glyphicon-file fileinput-exists"></i> 
                                    <span class="fileinput-filename"></span>
                                </div>
                                <span class="input-group-addon btn btn-default btn-file">
                                    <span class="fileinput-new">Select File</span>
                                    <span class="fileinput-exists">Change</span>
                                    <input id="upload" type="file"  name="upload" required >
                                </span>
                                <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                            </div>




                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Add Attachment</button>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-lg-8 col-lg-offset-2">
                <div class="hpanel">
                    <div class="panel-heading">
                        <center>Attachments </center>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover data-table" >
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Attachment</th>
                                        <th>Download Attachment</th>
                                        <th>Delete Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($email->attachment as $key => $attachment)
                                        <tr>
                                            <td> {{ $key+1 }} </td>
                                            <td> {{ $attachment->download_name }} </td>

                                            <td>
                                                <a href="{{ url('/email/download_attachment/' . $attachment->id ) }}">Download</a>
                                            </td>
                                            <td> 
                                                <form action="{{ url('/email/attachment/' . $attachment->id) }}" method="POST" onSubmit="return confirm('Are you sure you want to delete the following attachment?')" >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-primary">Delete</button> 
                                                </form>
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
    </div>

@endsection

@section('scripts')

    @component('/forms/scripts')
        @slot('js_scripts')
            <script src="{{ asset('js/jasny/jasny-bootstrap.min.js') }}"></script>
        @endslot

    @endcomponent

@endsection
