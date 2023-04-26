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
                    Confirm Results
                </div>
                <div class="panel-body">
                    <form  method="post" action="{{ url('dr_worksheet/approve/' . $worksheet->id) }}  " name="worksheetform"  onSubmit="return confirm('Are you sure you want to approve the below test results as final results?');" >
                        {{ method_field('PUT') }} @csrf

                        <div class="table-responsive">

                            <table class="table table-striped table-bordered table-hover" >
                                <thead>
                                    <tr>
                                        <th>Lab ID</th>
                                        <th>Approve</th>
                                        <th>Rerun</th>
                                        <th>Collect New Sample</th>
                                        <th>Sample ID</th>
                                        @if(env('APP_LAB') == 7)
                                            <th>Nat No</th>
                                        @endif
                                        <th>Exatype Status</th>
                                        <th>Facility</th>
                                        <th>Control</th>
                                        <th>Has Errors</th>
                                        <th>Has Warnings</th>
                                        <th>Has Mutations</th>
                                        <th>Requires Manual Intervention</th>    
                                        <th>View Chromatogram</th>         
                                        <th>Results</th>                
                                        <th>Print</th>               
                                    </tr>
                                </thead>
                                <tbody>

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
                                        <tr>
                                            <td> {{ $sample->id }} </td>
                                            <td>
                                                @if(in_array($sample->status_id, [1]) && !$sample->dateapproved)                                                
                                                    <div align='center'>
                                                        <input name='approved[]' type='checkbox' class='checks' value='{{ $sample->id }}' />
                                                    </div>
                                                @elseif($sample->dateapproved)
                                                    {{ $sample->my_date_format('dateapproved') }}
                                                @endif
                                            </td>
                                            <td>
                                                @if($sample->has_rerun)
                                                    <span style='color: #FF0000;'> Has Rerun </span>
                                                @elseif(in_array($sample->status_id, [2, 3]) && !$sample->collect_new_sample) 
                                                    <div align='center'>
                                                        <input name='rerun[]' type='checkbox' class='checks_rerun' value='{{ $sample->id }}' />
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($sample->collect_new_sample)
                                                    <span style='color: #FF0000;'> Dispatched as CNS </span>
                                                @elseif(in_array($sample->status_id, [2, 3]) && !$sample->has_rerun)
                                                    <div align='center'>
                                                        <input name='cns[]' type='checkbox' class='checks_cns' value='{{ $sample->id }}' />
                                                    </div>
                                                @endif
                                            </td>
                                            <td> {{ $sample->patient }} </td>
                                            @if(env('APP_LAB') == 7)
                                                <td> {{ $sample->nat }} </td>
                                            @endif
                                            <td> {!! $dr_sample_statuses->where('id', $sample->status_id)->first()->output ?? '' !!} </td>
                                            <!-- <td> {!! $dr_sample_statuses->first()->output ?? '' !!} </td> -->
                                            <td> {{ $sample->facilityname }} </td>
                                            <td> {{ $sample->control_type }} </td>
                                            <td> {{ $sample->my_boolean_format('has_errors') }} </td>
                                            <td> {{ $sample->my_boolean_format('has_warnings') }} </td>
                                            <td> {{ $sample->my_boolean_format('has_mutations') }} </td>
                                            @if($sample->pending_manual_intervention && !$sample->had_manual_intervention)
                                                <td> Yes </td>
                                            @else
                                                <td>  </td>
                                            @endif                                        
                                            <td> {!! $sample->view_chromatogram !!} </td>
                                            <td> 
                                                @if(in_array($sample->status_id, [1,7]))
                                                    <a href="{{ url('dr_sample/' . $sample->id) }}" target="_blank">View Results</a>
                                                @endif 
                                            </td>
                                            <td> 
                                                <!-- <a href="{{ url('dr_sample/results/' . $sample->id) }}" target="_blank">Results</a> | -->
                                                <!-- <a href="{{ url('dr_sample/results/' . $sample->id ) }}" target="_blank">View Results</a> | -->
                                                <a href="{{ url('dr_sample/download_results/' . $sample->id) }}">Download</a> 
                                            </td>
                                        </tr>

                                    @endforeach

                                </tbody>
                            </table>

                            @if($worksheet->status_id == 6)
                                <button class="btn btn-success" type="submit">Confirm Approval</button>
                            @endif

                        </div>

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