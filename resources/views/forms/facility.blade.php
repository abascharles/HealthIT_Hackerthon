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
                <form action="{{ url('/facility') }}" class="form-horizontal" method="POST">
                    @csrf
                    <div class="hpanel">
                        <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                            <center>Facility Information</center>
                        </div>
                        <div class="panel-body" style="padding-bottom: 6px;">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Facility Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="name" id="name" type="text" value="{{ $facility->name ?? '' }}" required >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">MFL Code</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="facilitycode" id="facilitycode" type="text" value="{{ $facility->facilitycode ?? '' }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">DHIS Code</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="DHIScode" id="DHIScode" type="text" value="{{ $facility->DHIScode ?? '' }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Facility Type</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="ftype" id="ftype">
                                        <option value="" selected disabled>Select Facility Type</option>
                                    @foreach ($data->facilitytype as $ftype)
                                        <option value="{{ $ftype->name }}">{{ $ftype->name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Ward</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="ward_id" id="ward_id">
                                        <option value="" selected disabled>Select Ward</option>
                                    @foreach ($data->wards as $ward)
                                        <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <i>Optional</i>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sub County</label>
                                <div class="col-sm-8">
                                    <select class="form-control" required name="district" id="district">
                                        <option value="" selected disabled>Select Sub County</option>
                                    @foreach ($data->districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Partners</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="partner" id="partner">
                                        <option value="" selected disabled>Select Partner</option>
                                    @foreach ($data->partners as $partner)
                                        <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Telephone</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="telephone" id="telephone" type="text" value="{{ $facility->telephone ?? '' }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Telephone 2</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="telephone2" id="telephone2" type="text" value="{{ $facility->telephone2 ?? '' }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Email</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="email" id="email" type="email" value="{{ $facility->email ?? '' }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Postal Address</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="PostalAddress" id="PostalAddress" type="text" value="{{ $facility->PostalAddress ?? '' }}" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Contact Person</label>
                                <div class="col-sm-8">
                                    <input class="form-control" name="contactperson" id="contactperson" type="text" value="{{ $facility->contactperson ?? '' }}" >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <center>
                            <div class="col-sm-10 col-sm-offset-1">
                                <button class="btn btn-success submit" type="submit" name="submit_type" value="paswordreset">Save Facility</button>
                                <button class="btn btn-danger" type="reset" formnovalidate name="submit_type" value="cancel">Reset</button>
                            </div>
                        </center>
                    </div>
                </form>
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
