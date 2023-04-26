@extends('layouts.tasks')

@section('css_scripts')
    
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
    <form action="/covidkits/consumption" method="POST" class="form-horizontal" id="covid_consumption" >
    @csrf
    <input type="hidden" name="week_start" value="{{ $time->week_start }}">
    <input type="hidden" name="week_end" value="{{ $time->week_end }}">
    <input type="hidden" name="tests" value="{{ $tests }}">
    @foreach($covidkits as $machinekey => $kits)
        <div class="hpanel" style="margin-top: 1em;margin-right: 2%;">
            <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                <div class="alert alert-danger">
                    <center><strong>Please enter the Kits received from KEMSA</strong></center>
                </div>
                <div class="alert alert-info">
                    <center><i class="fa fa-bolt"></i> Please enter <strong>{{ ucfirst($machinekey) }}</strong> consumption values below for the week starting {{ $time->week_start }} and ending {{ $time->week_end }}.
                    @if($machinekey == "Manual")
                        <strong>(Week`s Tests:{{ number_format($tests) }})</strong>
                    @endif
                    </center>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover data-table" style="font-size: 10px;margin-top: 1em;width: 100%">
                        <thead>               
                            <tr>
                                <th>Material Number</th>
                                <th>Product Description</th>
                                @if($machinekey == "Manual")
                                <th>Pack Size</th>
                                <th>Calculated Pack Size by Number of Tests</th>
                                @endif
                                <th>{{ ucfirst($machinekey) }} Used</th>
                                <th>Begining Balance</th>
                                <th>{{ ucfirst($machinekey) }} Received From KEMSA</th>
                                <th>Positive Adjustments</th>
                                <th>Negative Adjustments</th>
                                <th>Losses/Wastage</th>
                                <th>Ending Balance</th>
                                <th>Requested {{ ucfirst($machinekey) }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($kits as $kitkey => $kit)
                            <tr>
                                <td>{{ $kit->material_no }}</td>
                                <td>{{ $kit->product_description }}</td>                            
                                @if($machinekey == "Manual")
                                <td>{{ $kit->pack_size }}</td>
                                <td>{{ $kit->calculated_pack_size }}</td>
                                @endif
                                @php
                                    if($machinekey == "Manual")
                                        $kitsused = $kit->computekitsUsed($tests);
                                @endphp
                                <td>                            
                                    <input class="form-control kits_used" type="number" name="kits_used[{{$kit->material_no}}]" id="kits_used[{{$kit->material_no}}]" value="{{$kitsused}}" min="0" required="true">
                                </td>
                                <td>
                                    <input class="form-control begining_balance" type="text" name="begining_balance[{{$kit->material_no}}]" id="begining_balance[{{$kit->material_no}}]" value="{{$kit->beginingbalance($time->week_start) ?? 0}}" required="true">
                                </td>
                                <td>
                                    <input class="form-control received" type="number" name="received[{{$kit->material_no}}]" id="received[{{$kit->material_no}}]" value="0" min="0" required>
                                </td>
                                <td>
                                    <input class="form-control positive" type="number" name="positive[{{$kit->material_no}}]" id="positive[{{$kit->material_no}}]" value="0" min="0" required>
                                </td>
                                <td>
                                    <input class="form-control negative" type="number" name="negative[{{$kit->material_no}}]" id="negative[{{$kit->material_no}}]" value="0" min="0" required>
                                </td>
                                <td>
                                    <input class="form-control wastage" type="number" name="wastage[{{$kit->material_no}}]" id="wastage[{{$kit->material_no}}]" value="0" min="0" required>
                                </td>
                                <td>
                                    <input class="form-control ending" type="number" name="ending[{{$kit->material_no}}]" id="ending[{{$kit->material_no}}]" value="{{@($kit->beginingbalance($time->week_start)-$kitsused)}}" min="0" disabled="true">
                                    <input type="hidden" name="ending[{{$kit->material_no}}]" id="ending[{{$kit->material_no}}]" value="{{@($kit->beginingbalance($time->week_start)-$kitsused)}}">
                                </td>
                                <td>
                                    <input class="form-control" type="number" name="requested[{{$kit->material_no}}]" id="requested[{{$kit->material_no}}]" value="0" min="0" required>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
    <div class="hpanel" style="margin-top: 1em;margin-right: 6%;">
        <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
            <div class="col-sm-12">
                <center>
                <button class="btn btn-success" type="submit">Submit Covid Consumption</button>
                <button class="btn btn-primary" type="reset" name="discard" value="add">Discard Changes</button>
                </center>
            </div>
        </div>
    </div>    
    </form>
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
        $(function(){
            // Observe changes on received kits
            $(".kits_used").change(function(){
                var kits_used = $(this).get(0).id;
                var kits_usedval = $(this).val();
                if (kits_usedval == '')
                    kits_usedval = 0;
                // console.log(kits_usedval);
                updateendingbalance("kits_used", kits_used, (parseInt(kits_usedval)*-1));
            });

            // Observe changes on received kits
            $(".begining_balance").change(function(){
                var begining_balance = $(this).get(0).id;
                var begining_balanceval = $(this).val();
                if (begining_balanceval == '')
                    begining_balanceval = 0;
                // console.log(begining_balanceval);
                updateendingbalance("begining_balance", begining_balance, begining_balanceval);
            });
            
            // Observe changes on received kits
            $(".received").change(function(){
                var received = $(this).get(0).id;
                var receivedval = $(this).val();
                // console.log(receivedval);
                updateendingbalance("received", received, receivedval);
            });

            // Observe changes on the positive kits
            $(".positive").change(function(){
                var positive = $(this).get(0).id;
                var positiveval = $(this).val();
                updateendingbalance("positive", positive, positiveval);
            });

            // Observe changes on the negatiive kits
            $(".negative").change(function(){
                var negative = $(this).get(0).id;
                var negativeval = $(this).val();
                if (negativeval == '')
                    negativeval = 0;
                updateendingbalance("negative", negative, (parseInt(negativeval)*-1));
            });

            // Observe changes on the wastage kits
            $(".wastage").change(function(){
                var wastage = $(this).get(0).id;
                var wastageval = $(this).val();
                if (wastageval == '')
                    wastageval = 0;
                updateendingbalance("wastage", wastage, (parseInt(wastageval)*-1));
            });

            $('#covid_consumption').submit(function(e){
                console.log('Reading inputs');
                var inputs = $(".ending");
                for(var i = 0; i < inputs.length; i++){
                    if ($(inputs[i]).val() < 0) {
                        e.preventDefault();
                        setTimeout(function(){
                            toastr.options = {
                                closeButton: false,
                                progressBar: false,
                                showMethod: 'slideDown',
                                timeOut: 10000
                            };
                            toastr.error("No negative ending balances are allowed. Please fill in the respective kits received to proceed with this submission", "Warning!");
                        });
                        break;
                    }
                }
                // console.log(inputs);
            });
        });

        function updateendingbalance(elementname, elementid, value) {
            if (value == '')
                value = 0;

            var kits_used = elementid.replace(elementname, "kits_used");
            var kits_usedval = $('input[name="' + kits_used + '"').val();
            var begining_balance = elementid.replace(elementname, "begining_balance");
            var begining_balanceval = $('input[name="' + begining_balance + '"').val();
            var received = elementid.replace(elementname, "received");
            var receivedval = $('input[name="' + received + '"').val();
            var positive = elementid.replace(elementname, "positive");
            var positiveval = $('input[name="' + positive + '"').val();
            var negative = elementid.replace(elementname, "negative");
            var negativeval = $('input[name="' + negative + '"').val();
            var wastage = elementid.replace(elementname, "wastage");
            var wastageval = $('input[name="' + wastage + '"').val();

            var additinalvalues = (parseInt(begining_balanceval)+parseInt(receivedval)+parseInt(positiveval));
            var subtractivevalues = (parseInt(kits_usedval)+parseInt(negativeval)+parseInt(wastageval));

            var ending = elementid.replace(elementname, "ending");
            // var endingval = $('input[name="' + ending + '"').val();
            $('input[name="' + ending + '"').val((parseInt(additinalvalues)-parseInt(subtractivevalues)));
        }
    </script>
@endsection