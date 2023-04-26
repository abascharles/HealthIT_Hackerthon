@extends('layouts.master')

@component('/forms/css')
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Subcounty
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        <form action="{{ url('/district/' . $district->id) }}" class="form-horizontal" method="POST">
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
                                <label class="col-sm-4 control-label">Subcounty</label>
                                <div class="col-sm-8">
                                    <input class="form-control"  type="text" disabled value="{{ $district->name }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Contact Person 1 Name </label>
                                <div class="col-sm-8">
                                    <input class="form-control"  type="text" name="subcounty_person1" value="{{ $district->subcounty_person1 }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Contact Person 1 Position </label>
                                <div class="col-sm-8">
                                    <input class="form-control"  type="text" name="subcounty_position1" value="{{ $district->subcounty_position1 }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Contact Person 1 Email </label>
                                <div class="col-sm-8">
                                    <input class="form-control"  type="email" name="subcounty_email1" value="{{ $district->subcounty_email1 }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Contact Person 2 Name </label>
                                <div class="col-sm-8">
                                    <input class="form-control"  type="text" name="subcounty_person2" value="{{ $district->subcounty_person2 }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Contact Person 2 Position </label>
                                <div class="col-sm-8">
                                    <input class="form-control"  type="text" name="subcounty_position2" value="{{ $district->subcounty_position2 }}">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Contact Person 2 Email </label>
                                <div class="col-sm-8">
                                    <input class="form-control"  type="email" name="subcounty_email2" value="{{ $district->subcounty_email2 }}">
                                </div>
                            </div> 

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Update Subcounty Contacts</button>                                
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
