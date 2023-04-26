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
                        <a href="{{ url('/viralworksheet/cancel_upload/' . $worksheet->id) }} ">
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
                        @include('shared/viral-other-header-partial')
                    @else
                        @include('shared/viral-abbot-header-partial')
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
                    <form  method="post" action="{{ url('viralworksheet/approve/' . $worksheet->id) }}  " name="worksheetform"  onSubmit="return confirm('Are you sure you want to approve the below test results as final results?');" >
                        {{ method_field('PUT') }} @csrf

                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr class="colhead">
                                    <th>Sample ID</th>
                                    <th>Lab ID</th>
                                    <th>Run</th>
                                    <th>Result</th>                
                                    <th>Dilution Factor</th>                
                                    <th>Interpretation</th>                
                                    <th></th>                 
                                    <th>Action</th>               
                                    <th>Approved Date</th>                
                                    <th>Approved By</th>                
                                    <th>Task</th>                
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td >HPC</td>
                                    <td >-</td>
                                    <td >-</td>
                                    <td ><small><strong><font color='#FF0000'> {{ $worksheet->highpos_control_interpretation }} </font></strong></small> </td>
                                    <td >&nbsp; </td>
                                    <td ><small><strong>
                                        <font color='#FF0000'> {{ $worksheet->highpos_control_result }} </font>
                                         </strong></small>
                                     </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>  
                                    <td >&nbsp; </td>   
                                    <td >&nbsp; </td>   
                                </tr>
                                <tr>
                                    <td >LPC</td>
                                    <td >-</td>
                                    <td >-</td>
                                    <td ><small><strong><font color='#FF0000'> {{ $worksheet->lowpos_control_interpretation }} </font></strong></small> </td>
                                    <td >&nbsp; </td>
                                    <td ><small><strong>
                                        <font color='#FF0000'> {{ $worksheet->lowpos_control_result }} </font>
                                         </strong></small>
                                     </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>  
                                    <td >&nbsp; </td>   
                                    <td >&nbsp; </td>   
                                </tr>

                                <tr>
                                    <td >NC</td>
                                    <td >-</td>
                                    <td >-</td>
                                    <td ><small><strong><font color='#339900'> {{ $worksheet->neg_control_interpretation }} </font></strong></small> </td>
                                    <td >&nbsp; </td>
                                    <td ><small><strong>
                                        <font color='#339900'> {{ $worksheet->neg_control_result }} </font>
                                         </strong></small>
                                     </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td>
                                    <td >&nbsp; </td> 
                                    <td >&nbsp; </td>   
                                    <td >&nbsp; </td>   
                                </tr>

                                @php
                                    if($worksheet->status_id == 3){
                                        $class = 'noneditable';
                                        $editable = false;                                  
                                    }
                                    else{
                                        $class = 'editable';
                                        $editable = true;  
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


                                            
                                            /*if($sample->repeatt == 1){
                                                // $class = 'noneditable';
                                                $class = 'editable';
                                            }
                                            else{
                                                $class = 'editable';
                                            }*/
                                        }

                                    @endphp

                                    <tr>
                                        <td> 
                                            {!! $sample->patient->hyperlink !!}  
                                        </td>
                                        <td>{{ $sample->id }}  </td>
                                        <td> {{ $sample->run }} </td>
                                        <td> {{ $sample->interpretation }} </td>


                                        <td> 
                                            <select class="dilutionfactor {{ $class }} dilution-{{ $sample->dilutionfactor }}" name="dilutionfactors[]">
                                                @foreach($dilutions as $dilution)
                                                    <option value="{{$dilution->dilutionfactor }}"
                                                        @if($sample->dilutionfactor == $dilution->dilutionfactor    )
                                                            selected="selected"
                                                        @endif
                                                        > {{ $dilution->dilutiontype }} </option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td> 
                                            {!! $sample->coloured_result !!}
                                        </td>

                                        <td>
                                            @if( $class == 'editable' )
                                                <div><label> <input type="checkbox" class="i-checks {{ $class }}"  name="redraws[]" value="{{ $sample->id }}"> Collect New Sample </label></div>
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
                                            <input type="hidden" name="samples[]" value="{{ $sample->id }}" class="{{ $class }}">
                                            <input type="hidden" name="batches[]" value="{{ $sample->batch_id }}" class="{{ $class }}">
                                            <input type="hidden" name="results[]" value="{{ $sample->result }}" class="{{ $class }}">
                                            <input type="hidden" name="interpretations[]" value="{{ $sample->interpretation }}" class="{{ $class }}">
                                            
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
                                            <a href="{{ url('viralsample/' . $sample->id) }}" title='Click to view Details' target='_blank'> Details</a> | 
                                            <a href="{{ url('viralsample/runs/' . $sample->id) }}" title='Click to View Runs' target='_blank'>Runs </a>  
                                        </td>
                                    </tr>

                                @endforeach

                                {{--@foreach($samples->where('parentid', '=', 0) as $key => $sample)
                                    @include('shared/confirm_viral_result_row', ['sample' => $sample])
                                @endforeach--}}

                                @if($worksheet->status_id == 2)

                                    @if((!in_array(env('APP_LAB'), $double_approval) && $worksheet->uploadedby != auth()->user()->id) || 
                                     (in_array(env('APP_LAB'), $double_approval) && ($worksheet->reviewedby != auth()->user()->id || !$worksheet->reviewedby)) )

                                        <tr bgcolor="#999999">
                                            <td  colspan="12" bgcolor="#00526C" >
                                                <center>
                                                    <!-- <input type="submit" name="approve" value="Confirm & Approve Results" class="button"  /> -->
                                                    <button class="btn btn-success" type="submit">Confirm & Approve Results</button>
                                                </center>
                                            </td>
                                        </tr>

                                    @else

                                        <tr>
                                            <td  colspan="12">
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
        $(".editable.dilutionfactor").val(1).change();
        $(".editable.dilution-2").val(2).change();
        $(".editable.dilution-4").val(4).change();
        $(".editable.dilution-8").val(8).change();
        $('.noneditable').attr("disabled", "disabled");     
    @endcomponent

@endsection