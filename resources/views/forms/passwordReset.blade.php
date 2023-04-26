@extends('layouts.master')

@component('/forms/css')
    
@endcomponent

@section('custom_css')
    <style type="text/css">
        .hpanel {
            margin-bottom: 4px;
        }
        .hpanel.panel-heading {
            padding-bottom: 2px;
            padding-top: 4px;
        }
    </style>
@endsection

@section('content')
    <div class="content">
        <div>
            <div class="row">
                <div class="col-lg-12">
                @if($user)
                    <form action="{{ url('/user/password_reset/'.md5(($user == 'personal') ? Auth()->user()->id : $user->id)) }}" class="form-horizontal" method="POST">
                        @csrf
                        @method('PUT')
                    @if($user == 'personal')
                    <input type="hidden" name="user" value="1">
                    @endif
                    <div class="hpanel">
                        <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                            <center>User Information</center>
                        </div>
                        <div class="panel-body" style="padding-bottom: 6px;">
                            <!-- <div class="form-group">
                                <label class="col-sm-4 control-label">Account Type</label>
                                <div class="col-sm-8">
                                    <select class="form-control" required name="user_type" id="user_type">
                                        <option value="" selected disabled>Select Account Type</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Email</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="email" id="email" type="email" value="{{ $user->email ?? Auth()->user()->email }}" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Full Names</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="name" id="name" type="text" value="@if($user == 'personal') {{ Auth()->user()->surname.' '.Auth()->user()->oname }} @else {{ $user->getFullNameAttribute() }} @endif" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hpanel">
                        <div class="panel-body" style="padding-bottom: 6px;">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Password</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="password" id="password" type="password" value="" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Confirm Password</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="confirm-password" id="confirm-password" type="password" value="" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <center>
                            <div class="col-sm-10 col-sm-offset-1">
                                <button class="btn btn-success submit" type="submit" name="submit_type" value="paswordreset">Save User</button>
                                <button class="btn btn-danger" type="reset" formnovalidate name="submit_type" value="cancel">Reset</button>
                            </div>
                        </center>
                    </div>
                </form>
                @else
                    <div class="hpanel">
                        <div class="panel-body" style="padding-bottom: 6px;">
                            <div class="alert alert-warning"><center>User Not found.</center></div>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')

    @component('/forms/scripts')
        @slot('js_scripts')
            
        @endslot

        @slot('val_rules')
           
        @endslot

    @endcomponent


    <script type="text/javascript">
        $(document).ready(function(){
            $(".submit").click(function(e){
                password = $("#password").val();
                confirm = $("#confirm-password").val();
                if (password !== confirm) {
                    e.preventDefault();
                    set_warning("Passwords do not match");
                    $("#confirm-password").val("");
                    $("#confirm-password").focus();
                }
            });
        });
    </script>
@endsection
