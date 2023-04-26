@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data-table" >
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Machine</th>
                                   <!--  <th>ID Column Name</th>
                                    <th>Target Column Name</th>
                                    <th>CT Column Name</th> -->
                                    <th>Target1 Name</th>
                                    <th>Target2 Name</th>
                                    <th>Control Name</th>
                                    <th>Threshhold</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody> 
                                @foreach($covid_kit_types as $key => $covid_kit_type)
                                    <tr>
                                        <td> {{ $covid_kit_type->covid_kit_type }} </td>
                                        <td> {{ $covid_kit_type->machine->machine }} </td>
                                        <!-- <td> {{ $covid_kit_type->id_column }} </td>
                                        <td> {{ $covid_kit_type->target_column }} </td>
                                        <td> {{ $covid_kit_type->ct_column }} </td> -->
                                        <td> {{ $covid_kit_type->target1 }} </td>
                                        <td> {{ $covid_kit_type->target2 }} </td>
                                        <td> {{ $covid_kit_type->control_gene }} </td>
                                        <td> {{ $covid_kit_type->threshhold }} </td>
                                        <td>
                                            <a href="{{ url('/covid_kit_type/' . $covid_kit_type->id . '/edit' ) }} " target='_blank'>Edit</a>
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
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')

    @endcomponent

@endsection