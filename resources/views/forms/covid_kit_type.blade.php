@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('custom_css')
    <style type="text/css">
        .hpanel {
            margin-bottom: 4px;
        }
        .hpanel.panel-heading {
            padding-bottom: 2px;
            padding-top: 4px;
        }
    </style>
@endsection

@section('content')

    <div class="content">
        <div>

        @if(isset($covidKitType))
            <form method="POST" class="form-horizontal" action='{{ url("/covid_kit_type/{$covidKitType->id}") }}' >
            @method('PUT')
        @else
            <form method="POST" class="form-horizontal" action='{{ url("/covid_kit_type/") }}'>
        @endif
            <?php $model = $covidKitType ?? null; ?>
        @csrf

                
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body" style="padding-bottom: 6px;">


                        @include('partial.select', ['required' => true, 'prop' => 'machine_id', 'prop2' => 'machine', 'label' => 'Machine Type', 'items' => $machines,])

                        @include('partial.input', ['required' => true, 'prop' => 'covid_kit_type', 'label' => 'Kit Name'])
                        @include('partial.input', ['required' => true, 'prop' => 'target1', 'label' => 'Target 1'])
                        @include('partial.input', ['required' => true, 'prop' => 'target2', 'label' => 'Target 2'])
                        @include('partial.input', ['required' => true, 'prop' => 'control_gene', 'label' => 'Control Gene'])
                        @include('partial.input', ['prop' => 'threshhold', 'label' => 'Threshhold', 'is_number' => true])

                        <div class="form-group">
                            <center>
                                <div class="col-sm-4 col-sm-offset-4">
                                    <button class="btn btn-primary" type="submit"> Save </button>
                                </div>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>

      </div>
    </div>

@endsection

@section('scripts')

    @component('/forms/scripts')
    @endcomponent

@endsection
