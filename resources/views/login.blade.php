@extends('layouts.auth')

@section('content')

<div class="row">
        <div class="col-md-12">
            <!-- <div class="text-center m-b-md">
                <img src="{{ asset('img/nascoplogo.png') }}">
            </div> -->
            <div class="hpanel" style="width: 430px;">
                <div class="panel-body">
                    <form action="#" id="loginForm">
                        <div class="form-group">
                            <label class="control-label" for="username" style="color: black;">Username:</label>
                            <div class="input-group m-b"><span class="input-group-addon"><span class="fa fa-user-o"></span></span> <input  type="text" placeholder="Username" title="Please enter you username" required="" value="" name="username" id="username" class="form-control"></div>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="password" style="color: black;">Password:</label>
                            <div class="input-group m-b"><span class="input-group-addon"><span class="pe-7s-unlock"></span></span> <input type="password" title="Please enter your password" placeholder="Password" required="" value="" name="password" id="password" class="form-control"></div>
                        </div>
                        <button type="submit" class="btn btn-danger btn-block" style="background-color: #16A085;border-color: #16A085;margin-top: 2em;">Login</button>
                    </form>
                    <div class="text-center m-b-md">
                        <a href="#" style="color: white;"><button class="btn btn-primary btn-block" style="margin-top: 2em;">Click <strong class="font-extra-bold font-uppercase">here</strong> for facility login</button></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection