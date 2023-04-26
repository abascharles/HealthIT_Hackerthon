@extends('layouts.tasks')

@section('css_scripts')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endsection

@section('custom_css')
	<style type="text/css">
		.input-edit {
            background-color: #FFFFCC;
        }
        .input-edit-danger {
            background-color: #f2dede;
        }
	</style>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
    	<div class="alert alert-success">
            <center>
            	<strong> BELOW ARE THE COVID ALLOCATION MADE BY KEMSA, PLEASE CONFIRM WHICH HAVE BEEN RECEVIED AT THE LAB </strong>
            </center>
        </div>
        <div class="hpanel" style="margin-top: 1em;margin-right: 2%;">
            <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                <div class="text-center m-b-md">
                    <h3>Accordion</h3>
                    <p>Click on the allocations listed below to reveal the details of the allocation.</p>
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                @foreach($allocations as $allocation_key => $allocation)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="heading{{ $allocation_key }}">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $allocation_key }}" aria-expanded="true" aria-controls="collapse{{ $allocation_key }}">
                                   <h5>Allocation made on date {{ date('M d, Y', strtotime($allocation_key)) }}</h5>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{ $allocation_key }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $allocation_key }}">
                        {{-- @include('tasks.covid.allocation', ['allocation' => $allocation]) --}}
                        <form action="/covidkits/allocation" method="POST" class="form-horizontal" id="covid_allocation{{ $allocation_key }}" >
                        @csrf
                        @foreach($allocation as $allocation_data)
                            @php
                                $machine = null;
                                if (null !== $allocation_data->platform){
                                   $machine = $allocation_data->platform;
                                   $machinename = $machine->machine . ' Kits';
                                } else {
                                   $machinename = 'Consumables'; 
                                }
                            @endphp
                            <div class="panel-heading">
                                <i class="fa fa-bolt"></i> Please enter <strong>{{ ucfirst($machinename) }}</strong> allocations received from KEMSA.
                                @if($machine)
                                    <input type="hidden" name="machine[]" value="{{ $machine->id }}">   
                                @endif
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;width: 100%">
                                        <thead>               
                                            <tr>
                                                <th>Material Number</th>
                                                <th>Product Description</th>
                                                <th>{{ ucfirst($machinename) }} Allocated by KEMSA</th>
                                                <th>{{ ucfirst($machinename) }} Received From KEMSA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($allocation_data->details as $kitkey => $kit)
                                            <tr>
                                                <td>{{ $kit->kit->material_no }}</td>
                                                <td>{{ $kit->kit->product_description }}</td>
                                                <td>{{ $kit->allocated_kits }}</td>
                                                <td>
                                                    <input class="form-control received" type="number" name="received[{{$kit->id}}]" id="received[{{$kit->id}}]" value="{{ $kit->allocated_kits ?? o }}" min="0" required>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                        <div class="hpanel" style="margin-top: 1em;margin-right: 6%;">
                            <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                                <div class="col-sm-12">
                                    <center>
                                        <div class="form-group">
                                            <label class="col-md-6"> Date Received</label>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    <input type="date"  class="form-control" name="datereceived[{{ $allocation_key }}]" id="datereceived-{{ $allocation_key }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-6"> Have you received this allocation </label>
                                            <div class="col-md-6">
                                                <button class="btn btn-success" type="submit" name="response" value="YES" onclick="">YES</button>
                                                <button class="btn btn-primary" type="submit" name="response" value="NO">NO</button>
                                            </div>
                                        </div>
                                    </center>
                                </div>
                            </div>
                        </div> 
                        </form>
                        </div>
                    </div>
                @endforeach
                </div>
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
           ,
            rules: {
                dob: {
                    lessThan: ["#datecollected", "Date of Birth", "Date Collected"]
                },
                datecollected: {
                    lessThan: ["#datedispatched", "Date Collected", "Date Dispatched From Facility"],
                    lessThanTwo: ["#datereceived", "Date Collected", "Date Received"]
                },
                datedispatched: {
                    lessThan: ["#datereceived", "Date Dispatched From Facility", "Date Received"]
                } 
                               
            }
        @endslot

        $(".date:not(#datedispatched)").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        $("#datedispatched").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: "+7d",
            format: "yyyy-mm-dd"
        });


    @endcomponent
    <script type="text/javascript">
    	clickYes = date => {
            let formdatereceived = $("#datereceived-" + date).val();
            console.log(formdatereceived);
        };

        $(function(){
        	
        });
    </script>
@endsection