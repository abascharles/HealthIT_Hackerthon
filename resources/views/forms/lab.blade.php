@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('css/summernote/summernote.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Lab
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        <form action="{{ url('/lab') }}" class="form-horizontal" method="POST">
            @csrf
            @method("PUT")

            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center> </center>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Lab Full Name
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" required type="text" name="name" value="{{ $lab->name ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Lab Name
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" required type="text" name="labname" value="{{ $lab->labname ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Lab Location
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" required type="text" name="lablocation" value="{{ $lab->lablocation ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Lab Email
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" required type="text" name="email" value="{{ $lab->email ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Lab Telephone 1
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" required type="text" name="labtel1" value="{{ $lab->labtel1 ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Lab Telephone 2
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" required type="text" name="labtel2" value="{{ $lab->labtel2 ?? null }}">
                                </div>
                            </div> 

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Update Lab Contacts</button>                                
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
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
            <script src="{{ asset('js/summernote/summernote.js') }}"></script>
        @endslot

        $('#email_content').summernote();

        $(".date").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            startDate: new Date(),
            format: "yyyy-mm-dd"
        });

    @endcomponent

@endsection
