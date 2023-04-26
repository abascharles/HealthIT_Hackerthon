@extends('layouts.master')

    
@section('content')

    <div class="content">
        <div>
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="hpanel" style="margin-top: 1em;margin-right: 2%;">
                        <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                            <div class="text-center m-b-md">
                                <div class="alert alert-success" style="margin-bottom: .5em;">
                                    <center>
                                        <strong> BELOW ARE THE COVID ALLOCATION MADE BY KEMSA, PLEASE CONFIRM WHICH HAVE BEEN RECEVIED AT THE LAB </strong>
                                        <p>Click on the allocations listed below to reveal the details of the allocation.</p>
                                    </center>
                                </div>
                                <hr />
                                <a href="{{ url('covidkits/allocation/refresh') }}" class="btn btn-primary btn-lg" style="margin-top: .5em;margin-bottom: .5em;"> Check Latest Allocations From KEMSA </a>
                                <hr>
                            </div>
                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                            @foreach($allocations as $allocation_key => $allocation)
                                @php
                                    $allocation = (object)$allocation;
                                @endphp
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="heading{{ $allocation_key }}">
                                        <div class="row">
                                            <h4 class="panel-title">
                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse{{ $allocation_key }}" aria-expanded="true" aria-controls="collapse{{ $allocation_key }}">
                                                   <h5 class="col-md-4">Allocation made on date {{ date('M d, Y', strtotime($allocation_key)) }}</h5>
                                                    <div class="col-md-2">
                                                    @foreach($allocation->types as $type)
                                                        <span class="label label-primary">{{ $type }}</span>
                                                    @endforeach                                                
                                                    </div>
                                                    <div class="col-md-2">
                                                    @if($allocation->received == "YES")
                                                        <span class="label label-success">{{ $allocation->received }}</span>
                                                    @else
                                                        <span class="label label-danger">{{ $allocation->received }}</span>
                                                    @endif
                                                    </div>
                                                    @isset($allocation->date_received)
                                                    <h5 class="col-md-4">Date Received {{ date('M d, Y', strtotime($allocation->date_received)) }}</h5>
                                                    @endisset
                                                </a>
                                            </h4>
                                        </div>
                                        
                                    </div>
                                    <div id="collapse{{ $allocation_key }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $allocation_key }}">
                                    <form action="/covidkits/allocation" method="POST" class="form-horizontal" id="covid_allocation{{ $allocation_key }}" >
                                    @csrf
                                    @foreach($allocation->data as $allocation_data)
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
                                                                @if($allocation->received == "YES")
                                                                    {{ $kit->received_kits }}
                                                                @else
                                                                    <input class="form-control received" type="number" name="received[{{$kit->id}}]" id="received[{{$kit->id}}]" value="{{ $kit->allocated_kits ?? 0 }}" min="0" required>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($allocation->received != "YES")
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
                                    @endif                                    
                                    </form>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts') 

    
    <script type="text/javascript">
        $(document).ready(function(){
            
        });
    </script>
@endsection