@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')

<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading" style="margin-bottom: 0.5em;">
                    Worksheet Summary 
                    
                    <div class="panel-tools">
                        @if($worksheet->failed)
                            <a href="{{ url($worksheet->route_name . '/rerun_worksheet/' . $worksheet->id) }} ">
                                <button class="btn btn-danger">Rerun Worksheet</button>
                            </a>
                        @endif
                        <a href="{{ url($worksheet->route_name . '/cancel_upload/' . $worksheet->id) }} ">
                            <button class="btn btn-danger">Cancel Upload</button>
                        </a>
                        @if($worksheet->reversible)
                            <a href="{{ url($worksheet->route_name . '/reverse_upload/' . $worksheet->id) }} ">
                                <button class="btn btn-danger">Reverse Upload</button>
                            </a>
                        @endif
                    </div>
                    
                </div>
                <div class="panel-body">

                    @if($worksheet->machine_type == 1)
                        @include('shared/other-header-partial')
                    @else
                        @include('shared/abbot-header-partial')
                    @endif

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
                    Confirm Results
                </div>
                <div class="panel-body">

                    <form  method="post" action="{{ url($worksheet->route_name . '/approve/' . $worksheet->id) }}  " name="worksheetform"  onSubmit="return confirm('Are you sure you want to approve the below test results as final results?');" >
                        {{ method_field('PUT') }} {{ csrf_field() }}

                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr>
                                    <th>Sample ID</th>
                                    <th>Lab ID</th>
                                    @if(in_array(env('APP_LAB'), [1,25]) && isset($covid))
                                        <th> KEMRI ID </th>
                                        <th> Patient Name </th>
                                    @endif
                                    <th>Run</th>
                                    @if(session('testingSystem') == 'HPV')
                                        <th>Target 1</th>
                                        <th>Target 2</th>
                                        <th>Target 3</th>
                                    @else
                                    <th>Result</th>                
                                    @endif
                                    <th>Interpretation</th>                
                                    <th>Action</th>                
                                    <th>Approved Date</th>                
                                    <th>Approved By</th>                
                                    <th>Task</th>                
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td >PC</td>
                                    <td >-</td>
                                    @if(in_array(env('APP_LAB'), [1,25]) && isset($covid))
                                        <td>-</td>
                                    @endif
                                    <td >-</td>
                                    @if(session('testingSystem') == 'HPV')
                                        @php
                                            $result = 'Invalid';
                                            if($worksheet->pos_control_result == 6 || $worksheet->pos_control_result == 2)
                                                $result = 'Valid';
                                        @endphp
                                        <td><small><strong><font color='#FF0000'>{{ $result }}</font></strong></small></td>
                                        <td><small><strong><font color='#FF0000'>{{ $result }}</font></strong></small></td>
                                        <td><small><strong><font color='#FF0000'>{{ $result }}</font></strong></small></td>
                                    @else
                                    <td ><small><strong>
                                        <font color='#FF0000'>
                                            @if($worksheet->pos_control_result == 6 || $worksheet->pos_control_result == 2)
                                                Valid
                                            @else
                                                Invalid
                                            @endif
                                        </font>
                                         </strong></small>
                                    </td>
                                    @endif
                                    <td ><small><strong><font color='#FF0000'> {{ $worksheet->pos_control_interpretation }} </font></strong></small> </td>
                                    <td >Control </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>   
                                </tr>

                                <tr>
                                    <td >NC</td>
                                    <td >-</td>
                                    @if(in_array(env('APP_LAB'), [1,25]) && isset($covid))
                                        <td>-</td>
                                    @endif
                                    <td >-</td>
                                    @if(session('testingSystem') == 'HPV')
                                        @php
                                            $result = 'Invalid';
                                            if($worksheet->neg_control_result == 6 || $worksheet->neg_control_result == 1)
                                                $result = 'Valid';
                                        @endphp
                                        <td><small><strong><font color='#339900'>{{ $result }}</font></strong></small></td>
                                        <td><small><strong><font color='#339900'>{{ $result }}</font></strong></small></td>
                                        <td><small><strong><font color='#339900'>{{ $result }}</font></strong></small></td>
                                    @else
                                    <td ><small><strong>
                                        <font color='#339900'> 
                                            @if($worksheet->neg_control_result == 6 || $worksheet->neg_control_result == 1)
                                                Valid
                                            @else
                                                Invalid
                                            @endif
                                        </font>
                                         </strong></small>
                                     </td>
                                    @endif
                                    <td ><small><strong><font color='#339900'> {{ $worksheet->neg_control_interpretation }} </font></strong></small> </td>
                                    <td >Control </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>   
                                </tr>

                                @php
                                    $class = '';
                                    /*if(in_array(env('APP_LAB'), $double_approval) && $worksheet->reviewedby && !$worksheet->reviewedby2){

                                        $class = 'editable';
                                        $editable = true;
                                    }
                                    else if(!in_array(env('APP_LAB'), $double_approval) && $worksheet->reviewedby){
                                        $class = 'editable';
                                        $editable = true;
                                    }*/
                                    if($worksheet->status_id != 3){
                                        $class = 'editable';
                                        $editable = true;                                    
                                    }
                                    else{
                                        $class = 'noneditable';
                                        $editable = false;
                                    }
                                @endphp

                                @foreach($samples as $key => $sample)                                    

                                    @php

                                        if(in_array(env('APP_LAB'), $double_approval) && $worksheet->status_id == 2){

                                            if((in_array(env('APP_LAB'), $double_approval) && $sample->approvedby && $sample->approvedby2) || (!in_array(env('APP_LAB'), $double_approval) && $sample->approvedby) || $sample->has_rerun){
                                                $class = 'noneditable';
                                            }
                                            else{
                                                $class = 'editable';
                                            }
                                        }

                                        /*if(in_array(env('APP_LAB'), $double_approval) && $editable){
                                            
                                            if($sample->repeatt == 1){
                                                $class = 'noneditable';
                                            }
                                            else{
                                                $class = 'editable';
                                            }
                                        }*/
                                    @endphp

                                    <tr>
                                        <td> 
                                            {!! $sample->patient->hyperlink !!} 
                                        </td>
                                        <td> {{ $sample->id }}  </td>
                                        @if(in_array(env('APP_LAB'), [1,25]) && isset($covid))
                                            <td> {{ $sample->kemri_id }} </td>
                                            <td> {{ $sample->patient->patient_name }} </td>
                                        @endif
                                        <td> {{ $sample->run }} </td>
                                        @if(session('testingSystem') == 'HPV')
                                            <th>{{ $sample->target_1 }}</th>
                                            <th>{{ $sample->target_2 }}</th>
                                            <th>{{ $sample->target_3 }}</th>
                                        @else
                                        <td> {{ $sample->interpretation }} </td>
                                        @endif
                                        <td>  
                                            @if( $class == 'noneditable' || isset($covid))
                                                {!! $sample->get_prop_name($results, 'result', 'name_colour') !!}
                                            @else
                                            <input type="hidden" name="samples[]" value="{{ $sample->id }}" class="{{ $class }}">
                                            <input type="hidden" name="batches[]" value="{{ $sample->batch_id }}" class="{{ $class }}">
                                            
                                                <select name="results[]" class="{{ $class }}">
                                                    @foreach($results as $result)
                                                        <option value="{{$result->id}}"
                                                            @if(($sample->result == $result->id) || (!$sample->result && $result->id == 5))
                                                                selected
                                                            @endif
                                                            > {!! $result->name !!} </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </td>

                                        <td> 
                                            @if( $class == 'noneditable' )
                                                @foreach($actions as $action)
                                                    @if($sample->repeatt == $action->id)
                                                        {!! $action->name_colour !!}
                                                    @endif
                                                @endforeach

                                            @else
                                                @if(isset($covid))
                                                    <input type="hidden" name="samples[]" value="{{ $sample->id }}" class="{{ $class }}">
                                                @endif
                                                <select name="actions[]" class="{{ $class }}">
                                                    @foreach($actions as $action)
                                                        <option value="{{$action->id}}"
                                                            @if($sample->repeatt == $action->id)
                                                                selected
                                                            @endif
                                                            > {{ $action->name }} </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </td>
                                        @if(in_array(env('APP_LAB'), $double_approval))
                                            <td> {{ $sample->dateapproved2 }} </td>
                                            <td> {{ $sample->final_approver->full_name ?? '' }} </td>
                                        @else
                                            <td> {{ $sample->dateapproved }} </td>
                                            <td> {{ $sample->approver->full_name ?? '' }} </td>
                                        @endif
                                        <td> 
                                            <a href="{{ url($sample->route_name . '/' . $sample->id) }}" title='Click to view Details' target='_blank'> Details</a> | 
                                            @if($sample->route_name != 'covid_sample')
                                            <a href="{{ url($sample->route_name . '/runs/' . $sample->id) }}" title='Click to View Runs' target='_blank'>Runs </a>  
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach


                                {{--@foreach($samples->where('parentid', '=', 0) as $key => $sample)
                                    @include('shared/confirm_result_row', ['sample' => $sample])
                                @endforeach--}}

                                @if($worksheet->status_id == 2)

                                    @if((!in_array(env('APP_LAB'), $double_approval) && $worksheet->uploadedby != auth()->user()->id) 
                                    || 
                                     (in_array(env('APP_LAB'), $double_approval) && ($worksheet->reviewedby != auth()->user()->id || !$worksheet->reviewedby))
                                     || auth()->user()->user_type_id == 12 )

                                        <tr bgcolor="#999999">
                                            <td  colspan="10" bgcolor="#00526C" >
                                                <center>
                                                    <!-- <input type="submit" name="approve" value="Confirm & Approve Results" class="button"  /> -->
                                                    <button class="btn btn-success" type="submit">Confirm & Approve Results</button>
                                                </center>
                                            </td>
                                        </tr>

                                    @else

                                        <tr>
                                            <td  colspan="10">
                                                <center>
                                                    You are not permitted to complete the approval. Another user should be the one to complete the approval process.
                                                </center>
                                            </td>
                                        </tr>

                                    @endif

                                @endif

                            </tbody>
                        </table>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts') 

    @component('/tables/scripts')
        $('.noneditable').attr("disabled", "disabled");
        // $('.noneditable').prop("disabled", true);
    @endcomponent

@endsection