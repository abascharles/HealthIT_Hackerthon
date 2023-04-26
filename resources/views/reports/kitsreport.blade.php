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
    $rejected = 0;
    $viraltestingSys = '';
    $eidtestingSys = '';
    if(Session('testingSystem') == 'Viralload') {
        $viraltestingSys = 'checked';
    } else {
        $eidtestingSys = 'checked';
    }
    foreach ($data['allocations'] as $allocation) {
        $rejected += $allocation->rejected;
    }
@endphp
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#kits-deliveries"><strong>A.) KITS DELIVERIES (RECEIVED KITS ) REPORT</strong></a></li>
                    <li class=""><a data-toggle="tab" href="#kits-consumption"><strong>B.) SUBMITTED MONTHLY KITS CONSUMPTION REPORTS</strong></a></li>
                    <li class=""><a data-toggle="tab" href="#kits-allocation"><strong>C.) KITS ALLOCATIONS </strong></a></li>
                </ul>
                <div class="tab-content">
                    <div id="kits-deliveries" class="tab-pane active">
                        @include('reports.kitsreport-deliveries')
                    </div>
                    <div id="kits-consumption" class="tab-pane">
                        @include('reports.kitsreport-consumption')
                    </div>
                    <div id="kits-allocation" class="tab-pane">
                        @include('reports.kitsreport-allocation')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent
    <script type="text/javascript">
        $(document).ready(function(){
            $('input[name="period"]').change(function(){
                period = $(this).val();
                $('#periodSelection').show();
                $('#monthSelection').hide();
                $('#quarterSelection').hide();
                $('#yearSelection').hide();
                if (period == 'monthly') {
                    $('#monthSelection').show();
                } else if (period == 'quarterly') {
                    $('#quarterSelection').show();
                } else if (period == 'yearly') {
                    $('#yearSelection').show();
                }
            });

        });
    </script>
@endsection