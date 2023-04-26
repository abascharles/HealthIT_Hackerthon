@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')

<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Errors & Warnings
                </div>
                <div class="panel-body">   
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>System</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($sample->warning as $warning)
                                    <tr>
                                        @if($warning->warning_code && $warning->warning_code->error)
                                            <td> Error </td>
                                        @else
                                            <td> Warning </td>
                                        @endif
                                        <td> {{ $warning->warning_code->name ?? '' }} </td>
                                        <td> {{ $warning->warning_code->description ?? '' }} </td>
                                        <td> {{ $warning->system }} </td>
                                        <td> {{ $warning->detail }} </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <!-- <a class="closebox"><i class="fa fa-times"></i></a> -->
                    </div>
                    Mutations
                </div>
                <div class="panel-body">   
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>Drug Class</th>
                                    <th>Mutations</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($sample->dr_call as $call)

                                    <tr>
                                        <td> {{ $call->drug_class }} </td>
                                        <td>
                                            @if($call->mutations)
                                                @foreach($call->mutations as $key => $value)
                                                    {{ ($key+1) }}. {{ $value }} <br />
                                                @endforeach
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--<div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading">
                    <div class="panel-tools">
                        <a class="showhide"><i class="fa fa-chevron-up"></i></a>
                        <a class="closebox"><i class="fa fa-times"></i></a>
                    </div>
                    Mutations
                </div>
                <div class="panel-body">   
                    <div class="table-responsive">
                        <table class="table data-table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>Drug Class</th>
                                    <th>Major Mutations</th>
                                    <th>Minor Mutations</th>
                                    <th>Short Name</th>
                                    <th>Call</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($sample->dr_call as $call)
                                    @foreach($call->call_drug as $drug)
                                        <tr>
                                            <td> {{ $call->drug_class }} </td>
                                            <td>
                                                @if($call->major_mutations_array)
                                                    @foreach($call->major_mutations_array as $key => $value)
                                                        {{ ($key+1) }}. {{ $value }} <br />
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td>
                                                @if($call->other_mutations_array)
                                                    @foreach($call->other_mutations_array as $key => $value)
                                                        {{ ($key+1) }}. {{ $value }} <br />
                                                    @endforeach
                                                @endif
                                            </td>
                                            <td> {{ $drug->short_name }} </td>
                                            <td> {{ $drug->call }} </td>
                                        </tr>
                                    @endforeach
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>--}}

</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection