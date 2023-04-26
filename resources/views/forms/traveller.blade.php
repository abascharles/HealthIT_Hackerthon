@extends('layouts.master')

@component('/forms/css')
    <link href="{{ asset('css/datapicker/datepicker3.css') }}" rel="stylesheet" type="text/css">
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Update Patient
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        @if(isset($traveller))
            <form method="POST" class="form-horizontal" action='{{ url("/traveller/{$traveller->id}") }}' >
            @method('PUT')
        @else
            <form method="POST" class="form-horizontal" action='{{ url("/traveller/") }}'>
        @endif
            <?php $m = $traveller ?? null; ?>

        @csrf

            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center> </center>
                        </div>
                        <div class="panel-body">

                            @include('partial.input', ['model' => $m, 'prop' => 'id_passport', 'label' => 'ID / Passport'])
                            @include('partial.input', ['model' => $m, 'prop' => 'patient_name', 'label' => 'Patient Name'])
                            @include('partial.input', ['model' => $m, 'prop' => 'marriage_status',  'label' => 'Marriage Status'])
                            @include('partial.input', ['model' => $m, 'prop' => 'phone_no', 'label' => 'Phone Number'])
                            @include('partial.input', ['model' => $m, 'prop' => 'county', 'label' => 'County'])
                            @include('partial.input', ['model' => $m, 'prop' => 'residence', 'label' => 'Residence'])
                            @include('partial.input', ['model' => $m, 'prop' => 'citizenship', 'label' => 'Citizenship'])
                            @include('partial.input', ['model' => $m, 'prop' => 'age', 'label' => 'Age', 'is_number' => true])
                            @include('partial.input', ['model' => $m, 'prop' => 'county', 'label' => 'County'])

                            @include('partial.date', ['model' => $m, 'required' => true, 'prop' => 'datecollected', 'label' => 'Date of Collection',])
                            @include('partial.date', ['model' => $m, 'required' => true, 'prop' => 'datereceived', 'label' => 'Date of Receipt',])

                            @include('partial.date', ['model' => $m, 'required' => true, 'prop' => 'datetested', 'label' => 'Date of Testing',])
                            @include('partial.date', ['model' => $m, 'required' => true, 'prop' => 'datedispatched', 'label' => 'Date of Dispatch',])


                            @include('partial.date', ['model' => $m, 'required' => true, 'prop' => 'datetested', 'label' => 'Date of Testing',])
                            @include('partial.date', ['model' => $m, 'required' => true, 'prop' => 'datedispatched', 'label' => 'Date of Dispatch',])
                            <div class="hr-line-dashed"></div>




                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Update Traveller</button>
                                </div>
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
        @slot('js_scripts')
            <script src="{{ asset('js/datapicker/bootstrap-datepicker.js') }}"></script>
        @endslot

        $(".date").datepicker({
            startView: 0,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            endDate: new Date(),
            format: "yyyy-mm-dd"
        });

    @endcomponent

@endsection
