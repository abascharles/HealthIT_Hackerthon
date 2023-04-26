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

        @if(isset($quarantine_site))
            <form method="POST" class="form-horizontal" action='{{ url("/quarantine_site/{$quarantine_site->id}") }}' >
            @method('PUT')
        @else
            <form method="POST" class="form-horizontal" action='{{ url("/quarantine_site/") }}'>
        @endif
            <?php $m = $quarantine_site ?? null; ?>

        @csrf

                
        <div class="row">
            <div class="col-lg-12">
                <div class="hpanel">
                    <div class="panel-body" style="padding-bottom: 6px;">

                        <div class="alert alert-warning">
                            <center>
                                For email address, enter a comma separated list of email addresses with no spaces.
                            </center>
                        </div>
                        <br />

                        @include('partial.input', ['model' => $m, 'required' => true, 'prop' => 'name', 'label' => 'Quarantine Site'])
                        @include('partial.input', ['model' => $m, 'prop' => 'email', 'label' => 'Email'])


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
