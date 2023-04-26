@extends('layouts.master')

@component('/forms/css')
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Select Received By
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>

        <form action="{{ url('worksheet/set_sampletype') }}" class="form-horizontal" method="POST">
            @csrf

            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center> </center>
                        </div>
                        <div class="panel-body">
                            <input type="hidden" name="machine_type" value="{{ $machine_type }}">
                            @if($limit)
                                <input type="hidden" name="limit" value="{{ $limit }}">
                            @endif



                            <div class="form-group">
                                <label class="col-sm-4 control-label">Samples Entered By/Received By</label>
                                <div class="col-sm-8">

                                    @foreach ($users as $user)
                                        <div>
                                            <label> 
                                                <input name="entered_by[]" type="checkbox" class="i-checks" value="{{ $user->id }}" />
                                                {{ $user->full_name }}
                                            </label>
                                        </div>
                                    @endforeach

                                </div>
                            </div>


                            <div class="hr-line-dashed"></div>




                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Set Entered By</button>
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

    @endcomponent

@endsection
