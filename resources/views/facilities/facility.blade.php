@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')


<div class="p-lg">
    <div class="content animate-panel" data-child="hpanel">
        <form action="{{ url('/facility/' . $facility->id) }}" class="form-horizontal" method="POST">
            @csrf
            @method('PUT')


            <input type="hidden" name="id" value="{{ $facility->id }}">
            <div class="row">
                <div class="col-lg-5">
                    <div class="hpanel">
                        <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                            <center>Facility Information</center>
                        </div>
                        <div class="panel-body" style="padding-bottom: 6px;">

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Code</label>
                                <div class="col-sm-8">
                                    <input class="form-control editable" required name="facilitycode" type="text" value="{{ $facility->facilitycode }}" id="facilitycode" @if(Auth::user()->user_type_id == 5) disabled @else {{ $disabled }} @endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control editable" required name="name" type="text" value="{{ $facility->facility }}" id="name" @if(Auth::user()->user_type_id == 5) disabled @else {{ $disabled }} @endif>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sub County</label>
                                <div class="col-sm-8">
                                    <input class="form-control" required name="subconty" type="text" value="{{ $facility->subcounty }}" id="subconty" disabled="true">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">County</label>
                                <div class="col-sm-8">
                                    <input class="form-control" required name="county" type="text" value="{{ $facility->county }}" id="county" disabled="true">
                                </div>
                            </div>
                            {{--<div class="form-group">
                                <label class="col-sm-4 control-label">Laboratory</label>
                                <div class="col-sm-8">
                                    <input class="form-control" required name="lab" type="text" value="{{ $facility->lab }}" id="lab" disabled="true">
                                </div>
                            </div>--}}
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="hpanel">
                        <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                            <center>Facility Contact Details</center>
                        </div>
                        <div class="panel-body" style="padding-bottom: 6px;">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Postal Address</label>
                                <div class="col-sm-8">
                                    <input class="form-control editable" name="PostalAddress" type="text" value="{{ $facility->PostalAddress ?? '' }}" id="PostalAddress" {{ $disabled }}>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">Physical Address</label>
                                <div class="col-sm-8">
                                    <input class="form-control editable" name="physicaladdress" type="text" value="{{ $facility->physicaladdress ?? '' }}" id="physicaladdress" {{ $disabled }}>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Telephone 1</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="telephone" type="text" value="{{ $facility->telephone ?? '' }}" id="telephone" {{ $disabled }}>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Fax</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="fax" type="text" value="{{ $facility->fax ?? '' }}" id="fax" {{ $disabled }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Telephone 2</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="telephone2" type="text" value="{{ $facility->telephone2 ?? '' }}" id="telephone2" {{ $disabled }}>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Email</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="email" type="email" value="{{ $facility->email ?? '' }}" id="email" {{ $disabled }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">SMS Printer No.</label>
                                <div class="col-sm-8">
                                    <input class="form-control editable" name="smsprinterphoneno" type="text" value="{{ $facility->smsprinterphoneno ?? '' }}" id="smsprinterphoneno" {{ $disabled }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>    
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                            <center>Facility Contact Person</center>
                        </div>
                        <div class="panel-body" style="padding-bottom: 6px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Full Name(s)</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="contactperson" type="text" value="{{ $facility->contactperson ?? '' }}" id="contactperson" {{ $disabled }}>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Contact Tel. No. 1</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="contacttelephone" type="text" value="{{ $facility->contacttelephone ?? '' }}" id="contacttelephone" {{ $disabled }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Contact Email Address</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="ContactEmail" type="text" value="{{ $facility->ContactEmail ?? '' }}" id="ContactEmail" {{ $disabled }}>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Contact Tel No. 2</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="contacttelephone2" type="text" value="{{ $facility->contacttelephone2 ?? '' }}" id="contacttelephone2" {{ $disabled }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @if(auth()->user()->lab_user)
                <div class="col-lg-12">
                    <div class="hpanel">
                        <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                            <center>Facility Covid-19 Contact Person</center>
                        </div>
                        <div class="panel-body" style="padding-bottom: 6px;">
                            <div class="alert alert-warning">
                                Comma separated email addresses. Please ensure that there are no spaces between the email addresses.
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">Covid Email Address</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="covid_email" type="text" value="{{ $facility->covid_email ?? '' }}" id="covid_email" {{ $disabled }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif

            <div class="row">
            <div class="col-lg-12" 
                @empty ($edit)
                    style="display: none;"
                @endempty
                 id="g4s">
                <div class="hpanel">
                    <div class="panel-heading" style="padding-bottom: 2px;padding-top: 4px;">
                        <center>Facility G4S Details</center>
                    </div>
                    <div class="panel-body" style="padding-bottom: 6px;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">G4S Branch Name</label>
                                    <div class="col-sm-8">
                                        <input class="form-control editable" name="G4Sbranchname" type="text" value="{{ $facility->G4Sbranchname ?? '' }}" id="G4Sbranchname" {{ $disabled }}>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">G4S Phone No. 1</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="G4Sphone1" type="text" value="{{ $facility->G4Sphone1 ?? '' }}" id="G4Sphone1" {{ $disabled }}>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">G4S Phone No. 3</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="G4Sphone3" type="text" value="{{ $facility->G4Sphone3 ?? '' }}" id="G4Sphone3" {{ $disabled }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">G4S Location</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="G4Slocation" type="text" value="{{ $facility->G4Slocation ?? '' }}" id="G4Slocation" {{ $disabled }}>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">G4S Phone No. 2</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="G4Sphone2" type="text" value="{{ $facility->G4Sphone2 ?? '' }}" id="G4Sphone2" {{ $disabled }}>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-4 control-label">G4S Fax</label>
                                        <div class="col-sm-8">
                                            <input class="form-control editable" name="G4Sfax" type="text" value="{{ $facility->G4Sfax ?? '' }}" id="G4Sfax" {{ $disabled }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="hr-line-dashed"></div>
                <div class="form-group">
                    <div class="col-sm-8 col-sm-offset-5">
                        <button class="btn btn-success" type="submit" id="update" 
                            @empty ($edit)
                                style="display: none;"
                            @endempty
                            >Update</button>
                        <button class="btn btn-success" 
                            @empty ($edit)
                                style="display: none;"
                            @endempty
                             id="edit">Edit</button>
                        <a href="{{ route('facility.index') }}" class="btn btn-primary">Go Back</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent
    <script type="text/javascript">
        $("#edit").click(function(e){
            e.preventDefault();
            $("#g4s").show();
            $("#edit").hide();
            $(".editable").prop("disabled", false);
            $("#update").show();
        });
    </script>
@endsection