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
                    Emails
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        @if(isset($email))
        <form action="{{ url('/email/' . $email->id) }}" class="form-horizontal" method="POST">
            @method('PUT')
        @else
        <form action="{{ url('/email') }}" class="form-horizontal" method="POST">
            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
        @endif

            @csrf

            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center> </center>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">County </label>
                                <div class="col-sm-8">

                                    <select class="form-control" name="county_id">

                                      <option></option>
                                      <option value="0">All Counties</option>
                                      @foreach ($counties as $county)
                                          <option value="{{ $county->id }}"

                                          @if (isset($email) && $email->county_id == $county->id)
                                              selected
                                          @endif

                                          > {{ $county->name }}
                                          </option>
                                      @endforeach

                                    </select>
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Name
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" required type="text" name="name" value="{{ $email->name ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Subject
                                    <strong><div style='color: #ff0000; display: inline;'>*</div></strong>
                                </label>
                                <div class="col-sm-8">
                                    <input class="form-control" required type="text" name="subject" value="{{ $email->subject ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">From Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="from_name" value="{{ $email->from_name ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Use Lab Signature</label>
                                <div class="col-sm-8">
                                    <input type="checkbox" class="i-checks" name="lab_signature" value=1
                                    @if(isset($email) && $email->lab_signature)
                                        checked
                                    @endif
                                    > Tick if yes
                                </div>
                            </div> 

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Day to Send Email</label>
                                <div class="col-sm-8">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control" value="{{ $email->sending_day ?? null }}" name="sending_day">
                                    </div>
                                </div>                            
                            </div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Time to Send Email</label>
                                <div class="col-sm-8">
                                    <select class="form-control requirable" name="sending_hour">

                                        <option></option>
                                        @for($i=1; $i<13; $i++)
                                            <option value="{{ $i }}"

                                            @if (isset($email) && $email->sending_hour == $i)
                                                selected
                                            @endif

                                            > {{ $i }} A.M.
                                            </option>
                                        @endfor

                                        @for($i=1; $i<13; $i++)
                                            <option value="{{ $i+12 }}"

                                            @if (isset($email) && $email->sending_hour == ($i + 12))
                                                selected
                                            @endif

                                            > {{ $i }} P.M.
                                            </option>
                                        @endfor

                                    </select>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">CC List (Comma Separated Email Addresses)</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="cc_list" value="{{ $email->cc_list ?? null }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">BCC List (Comma Separated Email Addresses)</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" name="bcc_list" value="{{ $email->bcc_list ?? null }}">
                                </div>
                            </div> 

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Email Content</label><div class="col-sm-8"></div>
                            </div> 

                            <p>
                                If you want to have the facility name in the email content, add <b>:facilityname</b>. It will be replaced by the name of the facility in the email.
                            </p>

                            <div class="col-sm-12">
                                <textarea name="email_content" id="email_content">
                                    {{ $email->content ?? null }}
                                </textarea>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">

                                    @if (isset($email))
                                        <button class="btn btn-success" type="submit">Update Email</button>
                                    @else
                                        <button class="btn btn-success" type="submit">Save Email</button>
                                    @endif     
                                    
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
