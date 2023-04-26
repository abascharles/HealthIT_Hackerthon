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
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    <!-- ABBOTT kits -->
                    @forelse($data->kits as $kits)
                        <table class="table table-striped table-bordered table-hover" style="font-size: 10px;margin-top: 1em;">
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
                                @foreach($data->abbottdata as $key => $abbottdata)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    @if($key < 5)
                                        <td><strong>{{ $abbottdata['name'] ?? '' }}</strong></td>
                                    @else
                                        <td>{{ $abbottdata['name'] ?? '' }}</td>
                                    @endif
                                    
                                    @if($key < 5)
                                        @php
                                            $suffix = 'lotno';
                                            $prefix = $abbottdata['alias'].$suffix;
                                        @endphp
                                        <td><strong>{{ $kits->$prefix ?? '' }}</strong></td>
                                    @else
                                        <td> - </td>
                                    @endif

                                    @if($key < 5)
                                        @php
                                            $suffix = 'expiry';
                                            $prefix = $abbottdata['alias'].$suffix;
                                        @endphp
                                        <td><strong>{{ $kits->$prefix ?? '' }}</strong></td>
                                    @else
                                        <td> - </td>
                                    @endif
                                    @php
                                        $receivedsuffix = 'received';
                                        $receivedprefix = $abbottdata['alias'].$receivedsuffix;
                                        $damagedsuffix = 'damaged';
                                        $damagedprefix = $abbottdata['alias'].$damagedsuffix;
                                    @endphp
                                    <td>{{ $kits->$receivedprefix ?? 0 }}</td>
                                    <td>{{ $kits->$damagedprefix  ?? 0 }}</td>
                                    <td>{{ ($kits->$receivedprefix-$kits->$damagedprefix) ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @empty

                    @endforelse
                    <!-- ABBOTT kits -->
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent
@endsection