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
                    Batches Awaiting Dispatch
                </div>
                <div class="panel-body">
                    <form  method="post" action="{{ url('batch/complete_dispatch') }}  " name="worksheetform"  onSubmit="return confirm('Are you sure you want to dispatch the selected batches?');" >
                        @csrf

                        <table class="table table-striped table-bordered table-hover data-table" >
                            <thead>
                                <tr>
                                    <th> # </th>
                                    <th> Date Created </th>
                                    <th> Created By </th>
                                    <th> Type </th>
                                    <th> Status </th>
                                    <th> # Samples </th>
                                    <th> Date Run </th>
                                    <th> Date Updated </th>
                                    <th> Date Reviewed </th>
                                    <th> Task </th>             
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($worksheets as $key => $worksheet)
                                    <td> {{ $worksheet->id }} </td>
                                    <td> {{ $worksheet->created_at }} </td>
                                    <td> {{ $worksheet->surname . ' ' . $worksheet->oname }} </td>

                                    {{-- @switch($worksheet->machine_type)
                                        @case(2)
                                            <td> <strong><font color='#0000FF'> Abbott </font></strong> </td>
                                            @break
                                        @case(3)
                                            <td> <strong> C8800 </strong> </td>
                                            @break
                                        @case(4)
                                            <td><strong><font color='#FF00FB'> Panther </font></strong> </td>
                                            @break
                                        @default
                                            <td><strong> TaqMan </strong></td>
                                            @break
                                    @endswitch --}}

                                    <td> {{ $machines->where('machine', $worksheet->machine_type)->first()->string }} </td>
                                    <td> {{ $statuses->where('status', $worksheet->status)->first()->string }} </td>

                                    <td> {{ $worksheet->samples_no }} </td>
                                    <td> {{ $worksheet->daterun }} </td>
                                    <td> {{ $worksheet->dateuploaded }} </td>
                                    <td> {{ $worksheet->datereviewed }} </td>
                                    <td>  </td>

                                @endforeach

                            </tbody>
                        </table>

                        <input type="submit" name="Proceed to Confirm Selected Dispatch ">
                    </form>
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