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
                    <form action="{{ url('/kitsdeliveries') }}" method="POST" class='form-horizontal'>
                        @csrf
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><center>Received for the Quarter</center></label>
                            <div class="col-sm-8">
                                <select class="form-control input-sm" required name="quarter" id="quarter">
                                    <option value="" selected disabled>Select Quarter</option>
                                    <option value="1">JAN - MAR</option>
                                    <option value="2">APR - JUN</option>
                                    <option value="3">JUL - SEP</option>
                                    <option value="4">OCT - DEC</option>
                                </select>
                            </div>
                        </div>
                        <!-- <div class="form-group">
                            <label class="col-sm-4 control-label"><center>Kit Source</center></label>
                            <div class="col-sm-8">
                                <select class="form-control input-sm" required name="source" id="source">
                                    <option value="" selected>Select Source</option>
                                    <option value="3">KEMSA</option>
                                    <option value="2">Other Lab</option>
                                </select>
                            </div>
                        </div> -->

                        <div class="form-group" style="/*display: none;" id="platformDiv">
                            <label class="col-sm-4 control-label"><center>Platform</center></label>
                            <div class="col-sm-8">
                                <select class="form-control input-sm" required name="platform" id="platform">
                                    <option value="" selected>Select Platform</option>
                                    {{-- @if($data->taqmandeliveries == 0) --}}
                                    <option value="1">COBAS/TAQMAN @if($data->taqmandeliveries > 0) <i>(Entry made for current quarter)</i> @endif</option>
                                    {{-- @endif
                                    @if($data->abbottdeliveries == 0) --}}
                                    <option value="2">ABBOTT @if($data->abbottdeliveries > 0) <i>(Entry made for current quarter)</i> @endif</option>
                                    {{-- @endif --}}
                                </select>
                            </div>
                        </div>

                        <!-- TAQMAN kits -->
                        <div id="taqman" style="display: none; margin-top: 1em;">
                            <!-- EID Section -->
                            <div class="alert alert-warning" style="margin-bottom: 1em;">
                                <center><i class="fa fa-bolt"></i> Please enter EID values in the yellow boxes.</center>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><center>Test Kit Lot No</center></label>
                                <div class="col-sm-4">
                                    <input class="form-control input-sm" id="kitlotno" name="kitlotno" type="text" value="">
                                </div>
                                <label class="col-sm-2 control-label"><center>Expiry Date</center></label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="expirydate" required class="form-control input-sm" value="" name="expirydate">
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                                <thead>               
                                    <tr>
                                        <th></th>
                                        <th>HIV Qualitative Test Kits</th>
                                        <th>SPEX Agent</th>
                                        <th>Ampliprep Input s-tube</th>
                                        <th>Ampliprep flapless SPU</th>
                                        <th>Ampliprep K-tips</th>
                                        <th>Ampliprep Wash Reagent</th>
                                        <th>TAQMAN K-tubes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Received</th>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="rqualkit" name="rqualkit" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="rspexagent" name="rspexagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="rampinput" name="rampinput" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="rampflapless" name="rampflapless" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="rampktips" name="rampktips" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="rampwash" name="rampwash" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="rktubes" name="rktubes" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Damaged</th>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="dqualkit" name="dqualkit" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="dspexagent" name="dspexagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="dampinput" name="dampinput" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="dampflapless" name="dampflapless" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="dampktips" name="dampktips" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="dampwash" name="dampwash" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="dktubes" name="dktubes" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>To be Used</th>
                                        <td>
                                            <input class="form-control input-sm" id="uqualkit" name="uqualkit" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="uspexagent" name="uspexagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="uampinput" name="uampinput" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="uampflapless" name="uampflapless" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="uampktips" name="uampktips" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="uampwash" name="uampwash" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="uktubes" name="uktubes" type="text" value="">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><center>Received By:</center></label>
                                <div class="col-sm-4">
                                    <select class="form-control input-sm" required name="receivedby" id="receivedby">
                                        <option value="" selected disabled>Select a User</option>
                                    @forelse ($data->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->oname }} {{ $user->surname }}</option>
                                    @empty
                                        <option value="" disabled>No User</option>
                                    @endforelse
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label"><center>Date Received</center></label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="datereceived" required class="form-control input-sm" value="" name="datereceived">
                                    </div>
                                </div>
                            </div>
                            <!-- EID Section -->

                            <!-- VL Section -->
                            <div class="alert alert-warning" style="margin-bottom: 1em;">
                                <center><i class="fa fa-bolt"></i> Please enter VIRAL LOAD values in the yellow boxes.</center>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><center>Test Kit Lot No</center></label>
                                <div class="col-sm-4">
                                    <input class="form-control input-sm" id="vkitlotno" name="vkitlotno" type="text" value="">
                                </div>
                                <label class="col-sm-2 control-label"><center>Expiry Date</center></label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="vexpirydate" required class="form-control input-sm" value="" name="vexpirydate">
                                    </div>
                                </div>
                            </div>
                            <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                                <thead>               
                                    <tr>
                                        <th></th>
                                        <th>HIV Quantitative Test Kits</th>
                                        <th>SPEX Agent</th>
                                        <th>Ampliprep Input s-tube</th>
                                        <th>Ampliprep flapless SPU</th>
                                        <th>Ampliprep K-tips</th>
                                        <th>Ampliprep Wash Reagent</th>
                                        <th>TAQMAN K-tubes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>Received</th>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="vrqualkit" name="vrqualkit" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vrspexagent" name="vrspexagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vrampinput" name="vrampinput" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vrampflapless" name="vrampflapless" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vrampktips" name="vrampktips" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vrampwash" name="vrampwash" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vrktubes" name="vrktubes" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Damaged</th>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="vdqualkit" name="vdqualkit" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="vdspexagent" name="vdspexagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="vdampinput" name="vdampinput" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="vdampflapless" name="vdampflapless" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="vdampktips" name="vdampktips" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="vdampwash" name="vdampwash" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="vdktubes" name="vdktubes" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>To be Used</th>
                                        <td>
                                            <input class="form-control input-sm" id="vuqualkit" name="vuqualkit" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vuspexagent" name="vuspexagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vuampinput" name="vuampinput" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vuampflapless" name="vuampflapless" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vuampktips" name="vuampktips" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vuampwash" name="vuampwash" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="vuktubes" name="vuktubes" type="text" value="">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><center>Received By:</center></label>
                                <div class="col-sm-4">
                                    <select class="form-control input-sm" required name="vreceivedby" id="vreceivedby">
                                        <option value="" selected disabled>Select a User</option>
                                    @forelse ($data->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->oname }} {{ $user->surname }}</option>
                                    @empty
                                        <option value="" disabled>No User</option>
                                    @endforelse
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label"><center>Date Received</center></label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="vdatereceived" required class="form-control input-sm" value="" name="vdatereceived">
                                    </div>
                                </div>
                            </div>
                            <!-- VL Section -->
                            <div class="col-sm-12">
                                <center>
                                <button class="btn btn-success" type="submit" id="saveTaqman" name="saveTaqman" value="saveTaqman">Save Kit Delivery</button>
                                <button class="btn btn-primary" type="reset" name="discard" value="add">Discard Changes</button>
                                </center>
                            </div>
                        </div>
                        <!-- TAQMAN kits -->


                        <!-- ABBOTT kits -->
                        <div id="abbott" style="display: none;">
                            <!-- EID Section -->
                            <div class="alert alert-warning">
                                <center><i class="fa fa-bolt"></i> Please enter EID values in the yellow boxes.</center>
                            </div>
                            <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                                <thead>               
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Description of Goods</th>
                                        <th rowspan="2">Lot No</th>
                                        <th rowspan="2">Expiry Date</th>
                                        <th colspan="3"><center>Quantity</center></th>
                                    </tr>
                                    <tr>
                                        <th>Received</th>
                                        <th>Damaged</th>
                                        <th>To be Used</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><strong>ABBOTT RealTime HIV-1 Qualitative Amplification Reagent Kit</strong></td>
                                        <td>
                                            <input class="form-control input-sm input-edit-empty" id="aqualkitlotno" required name="aqualkitlotno" type="text" value="">
                                        </td>
                                        <td>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="aqualkitexpiry" required class="form-control input-sm input-edit-empty" value="" name="aqualkitexpiry">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="arqualkit" name="arqualkit" required type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="adqualkit" name="adqualkit" required type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="auqualkit" name="auqualkit" required type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><strong>ABBOTT RealTime HIV-1 Qualitative Control Kit </strong></td>
                                        <td>
                                            <input class="form-control input-sm input-edit-empty" id="acontrollotno" required name="acontrollotno" type="text" value="">
                                        </td>
                                        <td>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="acontrolexpiry" required class="form-control input-sm input-edit-empty" value="" name="acontrolexpiry">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="arcontrol" name="arcontrol" required type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" id="adcontrol" name="adcontrol" required type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="aucontrol" name="aucontrol" required type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><strong>Bulk mLysisDNA Buffer (for DBS processing only)</strong></td>
                                        <td>
                                            <input class="form-control input-sm input-edit-empty" id="abufferlotno" required name="abufferlotno" type="text" value="">
                                        </td>
                                        <td>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="abufferexpiry" required class="form-control input-sm input-edit-empty" value="" name="abufferexpiry">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="arbuffer" required name="arbuffer" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="adbuffer" name="adbuffer" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="aubuffer" required name="aubuffer" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><strong>ABBOTT mSample Preparation System DNA</strong></td>
                                        <td>
                                            <input class="form-control input-sm input-edit-empty" required id="apreparationlotno" name="apreparationlotno" type="text" value="">
                                        </td>
                                        <td>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="apreparationexpiry" required class="form-control input-sm input-edit-empty" value="" name="apreparationexpiry">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="arpreparation" required name="arpreparation" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="adpreparation" name="adpreparation" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="aupreparation" name="aupreparation" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>ABBOTT Optical Adhesive Covers</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="aradhesive" name="aradhesive" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="adadhesive" name="adadhesive" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="auadhesive" name="auadhesive" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>ABBOTT 96-Deep-Well Plate</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="ardeepplate" name="ardeepplate" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="addeepplate" name="addeepplate" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="audeepplate" name="audeepplate" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>Saarstedt Master Mix Tube</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="armixtube" name="armixtube" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="admixtube" name="admixtube" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="aumixtube" name="aumixtube" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>Saarstdet 5mL Reaction Vessels</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="arreactionvessels" name="arreactionvessels" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="adreactionvessels" name="adreactionvessels" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="aureactionvessels" name="aureactionvessels" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td>200mL Reagent Vessels</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="arreagent" name="arreagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="adreagent" name="adreagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="aureagent" name="aureagent" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>10</td>
                                        <td>ABBOTT 96-Well Optical Reaction Plate</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="arreactionplate" name="arreactionplate" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="adreactionplate" name="adreactionplate" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="aureactionplate" name="aureactionplate" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>11</td>
                                        <td>1000 μL Eppendorf (Tecan) Disposable Tips (for 1000 tests)</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="ar1000disposable" name="ar1000disposable" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="ad1000disposable" name="ad1000disposable" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="au1000disposable" name="au1000disposable" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>12</td>
                                        <td>200 ΜL Eppendorf (Tecan) Disposable Tips</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="ar200disposable" name="ar200disposable" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="ad200disposable" name="ad200disposable" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="au200disposable" name="au200disposable" type="text" value="">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><center>Received By:</center></label>
                                <div class="col-sm-4">
                                    <select class="form-control input-sm" required name="areceivedby" id="areceivedby">
                                        <option value="" selected disabled>Select a User</option>
                                    @forelse ($data->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->oname }} {{ $user->surname }}</option>
                                    @empty
                                        <option value="" disabled>No User</option>
                                    @endforelse
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label"><center>Date Received</center></label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="adatereceived" required class="form-control input-sm" value="" name="adatereceived">
                                    </div>
                                </div>
                            </div>
                            <!-- EID Section -->
                            
                            <!-- VL Section -->
                            <div class="alert alert-warning">
                                <center><i class="fa fa-bolt"></i> Please enter VIRAL LOAD values in the yellow boxes.</center>
                            </div>
                            <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;">
                                <thead>               
                                    <tr>
                                        <th rowspan="2">No</th>
                                        <th rowspan="2">Description of Goods</th>
                                        <th rowspan="2">Lot No</th>
                                        <th rowspan="2">Expiry Date</th>
                                        <th colspan="3"><center>Quantity</center></th>
                                    </tr>
                                    <tr>
                                        <th>Received</th>
                                        <th>Damaged</th>
                                        <th>To be Used</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><strong>ABBOTT RealTime HIV-1 Quantitative Amplification Reagent Kit</strong></td>
                                        <td>
                                            <input class="form-control input-sm input-edit-empty" required id="vaqualkitlotno" name="vaqualkitlotno" type="text" value="">
                                        </td>
                                        <td>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="vaqualkitexpiry" required class="form-control input-sm input-edit-empty" value="" name="vaqualkitexpiry">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="varqualkit" name="varqualkit" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadqualkit" name="vadqualkit" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vauqualkit" name="vauqualkit" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><strong>ABBOTT RealTime HIV-1 Quantitative Control Kit </strong></td>
                                        <td>
                                            <input class="form-control input-sm input-edit-empty" required id="vacontrollotno" name="vacontrollotno" type="text" value="">
                                        </td>
                                        <td>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="vacontrolexpiry" required class="form-control input-sm input-edit-empty" value="" name="vacontrolexpiry">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="varcontrol" name="varcontrol" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadcontrol" name="vadcontrol" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vaucontrol" name="vaucontrol" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><strong>Bulk mLysisDNA Buffer (for DBS processing only)</strong></td>
                                        <td>
                                            <input class="form-control input-sm input-edit-empty" required id="vabufferlotno" name="vabufferlotno" type="text" value="">
                                        </td>
                                        <td>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="vabufferexpiry" required class="form-control input-sm input-edit-empty" value="" name="vabufferexpiry">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" id="varbuffer" required name="varbuffer" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadbuffer" name="vadbuffer" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vaubuffer" name="vaubuffer" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>4</td>
                                        <td><strong>ABBOTT mSample Preparation System DNA</strong></td>
                                        <td>
                                            <input class="form-control input-sm input-edit-empty" required id="vapreparationlotno" name="vapreparationlotno" type="text" value="">
                                        </td>
                                        <td>
                                            <div class="input-group date">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="text" id="vapreparationexpiry" required class="form-control input-sm input-edit-empty" value="" name="vapreparationexpiry">
                                            </div>
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="varpreparation" name="varpreparation" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadpreparation" name="vadpreparation" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vaupreparation" name="vaupreparation" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>ABBOTT Optical Adhesive Covers</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="varadhesive" name="varadhesive" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadadhesive" name="vadadhesive" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vauadhesive" name="vauadhesive" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6</td>
                                        <td>ABBOTT 96-Deep-Well Plate</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="vardeepplate" name="vardeepplate" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vaddeepplate" name="vaddeepplate" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vaudeepplate" name="vaudeepplate" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>7</td>
                                        <td>Saarstedt Master Mix Tube</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="varmixtube" name="varmixtube" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadmixtube" name="vadmixtube" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vaumixtube" name="vaumixtube" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>8</td>
                                        <td>Saarstdet 5mL Reaction Vessels</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="varreactionvessels" name="varreactionvessels" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadreactionvessels" name="vadreactionvessels" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vaureactionvessels" name="vaureactionvessels" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>9</td>
                                        <td>200mL Reagent Vessels</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="varreagent" name="varreagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadreagent" name="vadreagent" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vaureagent" name="vaureagent" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>10</td>
                                        <td>ABBOTT 96-Well Optical Reaction Plate</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="varreactionplate" name="varreactionplate" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vadreactionplate" name="vadreactionplate" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vaureactionplate" name="vaureactionplate" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>11</td>
                                        <td>1000 μL Eppendorf (Tecan) Disposable Tips (for 1000 tests)</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="var1000disposable" name="var1000disposable" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vad1000disposable" name="vad1000disposable" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vau1000disposable" name="vau1000disposable" type="text" value="">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>12</td>
                                        <td>200 ΜL Eppendorf (Tecan) Disposable Tips</td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <input class="form-control input-sm" required id="var200disposable" name="var200disposable" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm input-edit" required id="vad200disposable" name="vad200disposable" type="text" value="">
                                        </td>
                                        <td>
                                            <input class="form-control input-sm" required id="vau200disposable" name="vau200disposable" type="text" value="">
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><center>Received By:</center></label>
                                <div class="col-sm-4">
                                    <select class="form-control input-sm" required name="vareceivedby" id="vareceivedby">
                                        <option value="" selected disabled>Select a User</option>
                                    @forelse ($data->users as $user)
                                        <option value="{{ $user->id }}">{{ $user->oname }} {{ $user->surname }}</option>
                                    @empty
                                        <option value="" disabled>No User</option>
                                    @endforelse
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label"><center>Date Received</center></label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" id="vadatereceived" required class="form-control input-sm" value="" name="vadatereceived">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <center>
                                <button class="btn btn-success" type="submit" name="saveAbbott" value="saveAbbott">Save Kit Delivery</button>
                                <button class="btn btn-primary" type="submit" name="discard" value="add">Discard Changes</button>
                                </center>
                            </div>
                            <!-- VL Section -->

                        </div>
                        <!-- ABBOTT kits -->
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
        $('.input-edit').val('0');
        $("#platform").change(function(){
            if ($(this).val() == 1) {
                $("#taqman").show();
                $("#abbott").hide();
                $("#kitlotno").attr("required", "true");$("#vkitlotno").attr("required", "true");
                $("#rqualkit").attr("required", "true");$("#rspexagent").attr("required", "true");    
                $("#rampinput").attr("required", "true");$("#rampflapless").attr("required", "true");    
                $("#rampktips").attr("required", "true");$("#rampwash").attr("required", "true");    
                $("#rktubes").attr("required", "true");$("#dqualkit").attr("required", "true");    
                $("#dspexagent").attr("required", "true");$("#dampinput").attr("required", "true");    
                $("#dampflapless").attr("required", "true");$("#dampktips").attr("required", "true");    
                $("#dampwash").attr("required", "true");$("#dktubes").attr("required", "true");    
                $("#uqualkit").attr("required", "true");$("#uspexagent").attr("required", "true");    
                $("#uampinput").attr("required", "true");$("#uampflapless").attr("required", "true");    
                $("#uampktips").attr("required", "true");$("#uampwash").attr("required", "true");    
                $("#uktubes").attr("required", "true");$("#vrqualkit").attr("required", "true");    
                $("#vrspexagent").attr("required", "true");$("#vrampinput").attr("required", "true");    
                $("#vrampflapless").attr("required", "true");$("#vrampktips").attr("required", "true");
                $("#vrampwash").attr("required", "true");$("#vrktubes").attr("required", "true");    
                $("#vdqualkit").attr("required", "true");$("#vdspexagent").attr("required", "true");    
                $("#vdampinput").attr("required", "true");$("#vdampflapless").attr("required", "true");
                $("#vdampktips").attr("required", "true");$("#vdampwash").attr("required", "true");    
                $("#vdktubes").attr("required", "true");$("#vuqualkit").attr("required", "true");    
                $("#vuspexagent").attr("required", "true");$("#vuampinput").attr("required", "true");
                $("#vuampflapless").attr("required", "true");$("#vuampktips").attr("required", "true");
                $("#vuampwash").attr("required", "true");$("#vuktubes").attr("required", "true");
            } else if ($(this).val() == 2) {      
                $("#taqman").hide();
                $("#abbott").show();
            } else {
                $("#taqman").hide();
                $("#abbott").hide();
            }
        });

        $("#saveTaqman").submit(function(e){
            
        });
        

        /********************************
        *** TAQMAN FORM MANIPULATIONS ***
        ********************************/ 
        $("#rqualkit").keyup(function(){
            val = $(this).val();
            if (val == 0) {
                $("#kitlotno").removeAttr('required');
                $("#expirydate").removeAttr('required');
                $("#receivedby").removeAttr('required');
                $("#datereceived").removeAttr('required');
            }
            $("#rspexagent").val(Math.round(val* 0.15)); 
            $("#rampinput").val(Math.round(val * 0.2)); 
            $("#rampflapless").val(Math.round(val * 0.2)); 
            $("#rampktips").val(Math.round(val * 0.15));
            $("#rampwash").val(Math.round(val * 0.5)); 
            $("#rktubes").val(Math.round(val * 0.05));

            $("#uqualkit").val($("#rqualkit").val()-$("#dqualkit").val());
            $("#uspexagent").val($("#rspexagent").val()-$("#dspexagent").val());
            $("#uampinput").val($("#rampinput").val()-$("#dampinput").val());
            $("#uampflapless").val($("#rampflapless").val()-$("#dampflapless").val());
            $("#uampktips").val($("#rampktips").val()-$("#dampktips").val());
            $("#uampwash").val($("#rampwash").val()-$("#dampwash").val());
            $("#uktubes").val($("#rktubes").val()-$("#dktubes").val()); 
        });

        $("#dqualkit").keyup(function(){
            $("#uqualkit").val($("#rqualkit").val()-$(this).val());
        });
        $("#dspexagent").keyup(function(){
            $("#uspexagent").val($("#rspexagent").val()-$(this).val());
        });
        $("#dampinput").keyup(function(){
            $("#uampinput").val($("#rampinput").val()-$(this).val());
        });
        $("#dampflapless").keyup(function(){
            $("#uampflapless").val($("#rampflapless").val()-$(this).val());
        });
        $("#dampktips").keyup(function(){
            $("#uampktips").val($("#rampktips").val()-$(this).val());
        });
        $("#dampwash").keyup(function(){
            $("#uampwash").val($("#rampwash").val()-$(this).val());
        });
        $("#dktubes").keyup(function(){
            $("#uktubes").val($("#rktubes").val()-$(this).val());
        });

        $("#vrqualkit").keyup(function(){
            val = $(this).val();
            if (val == 0) {
                $("#vkitlotno").removeAttr('required');
                $("#vexpirydate").removeAttr('required');
                $("#vreceivedby").removeAttr('required');
                $("#vdatereceived").removeAttr('required');
            }
            $("#vrspexagent").val(Math.round(val* 0.15)); 
            $("#vrampinput").val(Math.round(val * 0.2)); 
            $("#vrampflapless").val(Math.round(val * 0.2)); 
            $("#vrampktips").val(Math.round(val * 0.15));
            $("#vrampwash").val(Math.round(val * 0.5)); 
            $("#vrktubes").val(Math.round(val * 0.05));

            $("#vuqualkit").val($("#vrqualkit").val()-$("#vdqualkit").val());
            $("#vuspexagent").val($("#vrspexagent").val()-$("#vdspexagent").val());
            $("#vuampinput").val($("#vrampinput").val()-$("#vdampinput").val());
            $("#vuampflapless").val($("#vrampflapless").val()-$("#vdampflapless").val());
            $("#vuampktips").val($("#vrampktips").val()-$("#vdampktips").val());
            $("#vuampwash").val($("#vrampwash").val()-$("#vdampwash").val());
            $("#vuktubes").val($("#vrktubes").val()-$("#vdktubes").val()); 
        });

        $("#vdqualkit").keyup(function(){
            $("#vuqualkit").val($("#vrqualkit").val()-$(this).val());
        });
        $("#vdspexagent").keyup(function(){
            $("#vuspexagent").val($("#vrspexagent").val()-$(this).val());
        });
        $("#vdampinput").keyup(function(){
            $("#vuampinput").val($("#vrampinput").val()-$(this).val());
        });
        $("#vdampflapless").keyup(function(){
            $("#vuampflapless").val($("#vrampflapless").val()-$(this).val());
        });
        $("#vdampktips").keyup(function(){
            $("#vuampktips").val($("#vrampktips").val()-$(this).val());
        });
        $("#vdampwash").keyup(function(){
            $("#vuampwash").val($("#vrampwash").val()-$(this).val());
        });
        $("#vdktubes").keyup(function(){
            $("#vuktubes").val($("#vrktubes").val()-$(this).val());
        });

        /********************************
        *** ABBOTT FORM MANIPULATIONS ***
        ********************************/ 
        $("#arqualkit").keyup(function(){
            val = $(this).val();
            if (val == 0) {
                $("#aqualkitlotno").removeAttr('required');
                $("#aqualkitexpiry").removeAttr('required');
                $("#acontrollotno").removeAttr('required');
                $("#acontrolexpiry").removeAttr('required');
                $("#abufferlotno").removeAttr('required');
                $("#abufferexpiry").removeAttr('required');
                $("#apreparationlotno").removeAttr('required');
                $("#apreparationexpiry").removeAttr('required');
                $("#areceivedby").removeAttr('required');
                $("#adatereceived").removeAttr('required');
            }
            $("#arcontrol").val(Math.round((val * 2)*(2/24)));
            $("#arbuffer").val(Math.round(val * 1));
            $("#arpreparation").val(Math.round(val * 1));
            $("#aradhesive").val(Math.round((val * 2)/100));
            $("#ardeepplate").val(Math.round((val * 2)*(2/4)));
            $("#armixtube").val(Math.round((val * 2)*(1/25)));
            $("#arreactionvessels").val(Math.round((val * 192)/500));
            $("#arreagent").val(Math.round((val * 2)*(5/6)));
            $("#arreactionplate").val(Math.round((val * 2)/20));
            $("#ar1000disposable").val(Math.round((val * 2)*(421/192)));
            $("#ar200disposable").val(Math.round((val * 2)/100));

            $("#auqualkit").val($("#arqualkit").val()-$("#adqualkit").val());
            $("#aucontrol").val($("#arcontrol").val()-$("#adcontrol").val());
            $("#aubuffer").val($("#arbuffer").val()-$("#adbuffer").val());
            $("#aupreparation").val($("#arpreparation").val()-$("#adpreparation").val());
            $("#auadhesive").val($("#aradhesive").val()-$("#adadhesive").val());
            $("#audeepplate").val($("#ardeepplate").val()-$("#addeepplate").val());
            $("#aumixtube").val($("#armixtube").val()-$("#admixtube").val());
            $("#aureactionvessels").val($("#arreactionvessels").val()-$("#adreactionvessels").val());
            $("#aureagent").val($("#arreagent").val()-$("#adreagent").val());
            $("#aureactionplate").val($("#arreactionplate").val()-$("#adreactionplate").val());
            $("#au1000disposable").val($("#ar1000disposable").val()-$("#ad1000disposable").val());
            $("#au200disposable").val($("#ar200disposable").val()-$("#ad200disposable").val());
        });

        $("#adqualkit").keyup(function(){
            $("#auqualkit").val($("#arqualkit").val()-$("#adqualkit").val());
        });
        $("#adcontrol").keyup(function(){
            $("#aucontrol").val($("#arcontrol").val()-$("#adcontrol").val());
        });
        $("#adbuffer").keyup(function(){
            $("#aubuffer").val($("#arbuffer").val()-$("#adbuffer").val());
        });
        $("#adpreparation").keyup(function(){
            $("#aupreparation").val($("#arpreparation").val()-$("#adpreparation").val());
        });
        $("#adadhesive").keyup(function(){
            $("#auadhesive").val($("#aradhesive").val()-$("#adadhesive").val());
        });
        $("#addeepplate").keyup(function(){
            $("#audeepplate").val($("#ardeepplate").val()-$("#addeepplate").val());
        });
        $("#admixtube").keyup(function(){
            $("#aumixtube").val($("#armixtube").val()-$("#admixtube").val());
        });
        $("#adreactionvessels").keyup(function(){
            $("#aureactionvessels").val($("#arreactionvessels").val()-$("#adreactionvessels").val());
        });
        $("#adreagent").keyup(function(){
            $("#aureagent").val($("#arreagent").val()-$("#adreagent").val());
        });
        $("#adreactionplate").keyup(function(){
            $("#aureactionplate").val($("#arreactionplate").val()-$("#adreactionplate").val());
        });
        $("#ad1000disposable").keyup(function(){
            $("#au1000disposable").val($("#ar1000disposable").val()-$("#ad1000disposable").val());
        });
        $("#ad200disposable").keyup(function(){
            $("#au200disposable").val($("#ar200disposable").val()-$("#ad200disposable").val());
        });

        $("#varqualkit").keyup(function(){
            val = $(this).val();
            if (val == 0) {
                $("#vaqualkitlotno").removeAttr('required');
                $("#vaqualkitexpiry").removeAttr('required');
                $("#vacontrollotno").removeAttr('required');
                $("#vacontrolexpiry").removeAttr('required');
                $("#vabufferlotno").removeAttr('required');
                $("#vabufferexpiry").removeAttr('required');
                $("#vapreparationlotno").removeAttr('required');
                $("#vapreparationexpiry").removeAttr('required');
                $("#vareceivedby").removeAttr('required');
                $("#vadatereceived").removeAttr('required');
            }
            $("#varcontrol").val(Math.round((val * 2)*(2/24)));
            $("#varbuffer").val(Math.round(val * 1));
            $("#varpreparation").val(Math.round(val * 1));
            $("#varadhesive").val(Math.round((val * 2)/100));
            $("#vardeepplate").val(Math.round((val * 2)*(2/4)));
            $("#varmixtube").val(Math.round((val * 2)*(1/25)));
            $("#varreactionvessels").val(Math.round((val * 192)/500));
            $("#varreagent").val(Math.round((val * 2)*(5/6)));
            $("#varreactionplate").val(Math.round((val * 2)/20));
            $("#var1000disposable").val(Math.round((val * 2)*(421/192)));
            $("#var200disposable").val(Math.round((val * 2)/100));

            $("#vauqualkit").val($("#varqualkit").val()-$("#vadqualkit").val());
            $("#vaucontrol").val($("#varcontrol").val()-$("#vadcontrol").val());
            $("#vaubuffer").val($("#varbuffer").val()-$("#vadbuffer").val());
            $("#vaupreparation").val($("#varpreparation").val()-$("#vadpreparation").val());
            $("#vauadhesive").val($("#varadhesive").val()-$("#vadadhesive").val());
            $("#vaudeepplate").val($("#vardeepplate").val()-$("#vaddeepplate").val());
            $("#vaumixtube").val($("#varmixtube").val()-$("#vadmixtube").val());
            $("#vaureactionvessels").val($("#varreactionvessels").val()-$("#vadreactionvessels").val());
            $("#vaureagent").val($("#varreagent").val()-$("#vadreagent").val());
            $("#vaureactionplate").val($("#varreactionplate").val()-$("#vadreactionplate").val());
            $("#vau1000disposable").val($("#var1000disposable").val()-$("#vad1000disposable").val());
            $("#vau200disposable").val($("#var200disposable").val()-$("#vad200disposable").val());
        });

        $("#vadqualkit").keyup(function(){
            $("#vauqualkit").val($("#varqualkit").val()-$("#vadqualkit").val());
        });
        $("#vadcontrol").keyup(function(){
            $("#vaucontrol").val($("#varcontrol").val()-$("#vadcontrol").val());
        });
        $("#vadbuffer").keyup(function(){
            $("#vaubuffer").val($("#varbuffer").val()-$("#vadbuffer").val());
        });
        $("#vadpreparation").keyup(function(){
            $("#vaupreparation").val($("#varpreparation").val()-$("#vadpreparation").val());
        });
        $("#vadadhesive").keyup(function(){
            $("#vauadhesive").val($("#varadhesive").val()-$("#vadadhesive").val());
        });
        $("#vaddeepplate").keyup(function(){
            $("#vaudeepplate").val($("#vardeepplate").val()-$("#vaddeepplate").val());
        });
        $("#vadmixtube").keyup(function(){
            $("#vaumixtube").val($("#varmixtube").val()-$("#vadmixtube").val());
        });
        $("#vadreactionvessels").keyup(function(){
            $("#vaureactionvessels").val($("#varreactionvessels").val()-$("#vadreactionvessels").val());
        });
        $("#vadreagent").keyup(function(){
            $("#vaureagent").val($("#varreagent").val()-$("#vadreagent").val());
        });
        $("#vadreactionplate").keyup(function(){
            $("#vaureactionplate").val($("#varreactionplate").val()-$("#vadreactionplate").val());
        });
        $("#vad1000disposable").keyup(function(){
            $("#vau1000disposable").val($("#var1000disposable").val()-$("#vad1000disposable").val());
        });
        $("#vad200disposable").keyup(function(){
            $("#vau200disposable").val($("#var200disposable").val()-$("#vad200disposable").val());
        });
    });
</script>
@endsection