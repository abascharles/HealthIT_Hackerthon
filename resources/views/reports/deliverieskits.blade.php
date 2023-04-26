@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<style type="text/css">
    .spacing-div-form {
        margin-top: 15px;
    }
</style>
@php
    $viraltestingSys = '';
    $eidtestingSys = '';
    if(Session('testingSystem') == 'Viralload') {
        $viraltestingSys = 'checked';
    } else {
        $eidtestingSys = 'checked';
    }
@endphp
<div class="content">
    <div class="row">
    @foreach($deliveries as $key => $delivery)
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading" class="alert alert-warning">
                    <h5> {{ $delivery->testtype->name }} {{ $delivery->platform->machine }} Kits delivered on {{ date('F', mktime(0, 0, 0, $delivery->month, 1, Date('Y'))) }}, {{ $delivery->year }} </h5>
                </div>
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    <table class="table table-striped table-bordered table-hover" style="font-size: 10px;margin-top: 1em;">
                        <thead>               
                            <tr>
                                <th>Product Name</th>
                                <th>Product Unit</th>
                                <th>Received</th>
                                <th>Damaged</th>
                                <th>To Be Used</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($delivery->details as $detail)
                            <tr>
                                <td>{{ str_replace("REPLACE", $delivery->testtype->type, $detail->kit->name) }}</td>
                                <td>{{ $detail->kit->unit ?? '' }}</td>
                                <td>{{ number_format($detail->received) }}</td>
                                <td>{{ number_format($detail->damaged) }}</td>
                                <td>{{ number_format(($detail->received - $detail->damaged)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent
@endsection