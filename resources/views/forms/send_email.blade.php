@extends('layouts.master')

@component('/forms/css')
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Preview Email
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        <form action="{{ url('/email/preview/' . $email->id) }}" class="form-horizontal" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center>Preview Email </center>
                        </div>
                        <div class="panel-body">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Recepient Email Address</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="email" name="recepient">
                                </div>
                            </div> 

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Preview Email</button>
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
    @endcomponent

@endsection
