@extends('layouts.tasks')

@section('css_scripts')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('custom_css')
	<style type="text/css">
		.hpanel .panel-body .spacing {
			margin-bottom: 1em;
		}
        .input-edit {
            background-color: #FFFFCC;
        }
        .input-edit-empty {
            background-color: #FFFFCC;   
        }
	</style>
@endsection

@section('content')
<div class="row">
        <div class="col-md-12">
            <div class="hpanel" style="margin-top: 1em;margin-right: 18%;">
            	<div class="alert alert-default">
		                <center><i class="fa fa-bolt"></i> Please enter the Kit Delivery details to keep track of deliveries and consumption.</center>
	            </div>
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    <form action="/consumption" method="POST" class="form-horizontal" >
                        @csrf
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><center>Received for the month</center></label>
                            <div class="col-sm-8">
                                <label class="col-sm-4 control-label">
                                    {{ date("F", mktime(0, 0, 0, $period->month, 1, Date('Y'))) }}, {{ $period->year }}
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="/*display: none;" id="platformDiv">
                            <label class="col-sm-4 control-label">
                                <center> 
                                    Platforms Available
                                    <br>
                                    <small style="color:red;">These are the platforms found in your system automatically selected. Please proceed </small>
                                </center>
                            </label>
                            <div class="col-sm-7">
                                <select class="form-control input-sm" required name="machine[]" id="machine" multiple="true">
                                @foreach($machines as $machine)
                                    <option value="{{ $machine->id }}" selected="true">{{ $machine->machine }}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <center>
                                <button class="btn btn-success" type="submit" name="platform" value="platform">Submit Platform Values</button>
                                <button class="btn btn-primary" type="submit" name="discard" value="add">Discard Changes</button>
                                </center>
                            </div>
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
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot


        @slot('val_rules')
           
        @endslot
        $("select").select2();

        $(".date").datepicker({
            todayBtn: "linked",
            forceParse: true,
            autoclose: true,
            format: "yyyy-mm-dd"
        });

    @endcomponent
<script type="text/javascript">
    $().ready(function() {
        
    });
</script>
@endsection