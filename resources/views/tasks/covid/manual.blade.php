@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="alert alert-danger">
                    <center><i class="fa fa-bolt"></i> Please note that you CANNOT access the main system until the below pending tasks have been completed.</center>
                </div>
                
                <div class="panel-body" style="padding: 20px;box-shadow: none; border-radius: 0px;">
                    @if(sizeof($time) > 0)
                        <div class="alert alert-danger spacing bottom">
                            <strong><a href="{{ url('covidkits') }}">Click to Submit Last Week`s COVID Consumptions</a></strong>
                            <strong><p style="margin-left: 1.5em;"><font color="#CCCCCC" style="color: black;">Your lab needs to update consumptions for the last {{ sizeof($time) }} weeks. Click on the link above to update them</font></p></strong>
                            @foreach($time as $key => $week)
                            <p style="margin-left: 3em;"><font color="#CCCCCC" style="color: black;">Update consumptions for week {{ $week->week }} ({{ $week->week_start }} - {{ $week->week_end }})</font></p>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-success spacing bottom">
                            <strong><a href="#" style="color: black;">Pevious weeks COVID consumptions Submitted</a></strong>
                        </div>
                    @endif
                    <!-- Kit and kits consumption -->
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
