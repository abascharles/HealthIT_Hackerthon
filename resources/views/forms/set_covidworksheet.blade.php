@extends('layouts.master')

@component('/forms/css')
@endcomponent

@section('content')

    <div class="small-header">
        <div class="hpanel">
            <div class="panel-body">
                <h2 class="font-light m-b-xs">
                    Select Covid-19 Worksheet Details
                </h2>
            </div>
        </div>
    </div>

   <div class="content">
        <div>


        <form method="POST" action="{{ url('covid_worksheet/create') }}" class="form-horizontal">
            @csrf
            <div class="row">
                <div class="col-lg-8 col-lg-offset-2">
                    <div class="hpanel">
                        <div class="panel-heading">
                            <center> </center>
                        </div>
                        <div class="panel-body">

                            @include('partial.select', ['model' => null, 'prop' => 'machine_type', 'prop2' => 'machine', 'required' => true, 'label' => 'Machine', 'items' => $machines, ])

                            <div class="form-group">
                                <label class="col-sm-4 control-label">Sample Number</label>
                                <div class="col-sm-8">
                                    <select class="form-control" id="limit" name="limit" id="limit">
                                        <option></option>
                                        <option value="22">24</option>
                                        <option value="46">48</option>
                                        <option value="70">72</option>
                                        <option value="94">96</option>
                                    </select>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-4 control-label">Set Sample Number</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="text" number="number" id="soft_limit"  name="soft_limit" min="1" max="94">
                                </div>
                            </div>



                            <!-- <div class="form-group">
                                <label class="col-sm-4 control-label">Combined</label>
                                <div class="col-sm-8">
                                    <select class="form-control" required name="combined" id="combined">
                                        <option></option>
                                        <option value="0">No</option>
                                        <option value="1">Eid</option>
                                        <option value="2">VL</option>
                                    </select>
                                </div>
                            </div> -->

                            {{--@include('partial.select', ['model' => null, 'prop' => 'sampletype', 'label' => 'Worksheet Sample Type (Required for VL)', 'items' => $worksheet_sampletypes, ])--}}


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
                                    <button class="btn btn-success" type="submit">Set Worksheet Details</button>
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
