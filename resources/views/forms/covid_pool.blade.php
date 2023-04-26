@extends('layouts.master')

@component('/forms/css')
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Select Covid-19 Worksheet Pool Details
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>


        <form method="POST" action="{{ url('covid_pool') }}" class="form-horizontal">
            @csrf
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center> </center>
                        </div>
                        <div class="panel-body">


                            <div class="form-group">
                                <label class="col-sm-4 control-label">Worksheet No</label>
                                <div class="col-sm-8">
                                    @foreach ($worksheets as $worksheet)
                                        <div>
                                            <label> 
                                                <input name="worksheet_ids[]" type="checkbox" class="i-checks" value="{{ $worksheet->id }}" />
                                                {{ $worksheet->id }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>


                            <div class="hr-line-dashed"></div>


                            <div class="form-group">
                                <div class="col-sm-8 col-sm-offset-4">
                                    <button class="btn btn-success" type="submit">Create Pool</button>
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


        @slot('val_rules')
           ,
            rules: {
                limit: {
                    required: '#soft_limit:blank'
                },                             
            }
        @endslot

    @endcomponent

@endsection
