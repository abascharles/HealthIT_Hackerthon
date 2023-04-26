@extends('layouts.master')

    @component('/tables/css')
    @endcomponent

@section('content')
@php
	$disabled = "";
@endphp
@isset($data->view)
	@php
		$disabled = "disabled";
	@endphp
@endisset
<div class="content">

    <div class="row">
        <div class="col-lg-12">
            <div class="hpanel">
                <div class="panel-heading" style="margin-bottom: 0.5em;">
                    Worksheet Summary 
                    
                    <div class="panel-tools">
                        <a href="{{ url('/cd4/worksheet/cancel_upload/' . $data->worksheet->id) }} ">
                            <button class="btn btn-danger">Cancel Upload</button>
                        </a>
                    </div>
                    
                </div>
                <div class="panel-body">
                    <div class="row">
                                              
                    </div>
                    <div class="table-responsive">
                    @if(!isset($data->view))
                        <form action="{{ url('/cd4/worksheet/save/'.$data->worksheet->id) }}" class="form-horizontal" method="POST" id='worksheet_form'>
                            @csrf
                            @method('PUT')
                    @endif
                   	@if(!isset($data->view) && $data->samples->count() == 0)
                   		<center><div class="alert alert-warning">No samples availabe to run a worksheet</div></center>
                   	@else
                    	<table class="table table-striped table-bordered table-hover">
                    		<tr>
                    			<th rowspan="2"><br>Worksheet No</th>
                    			<td rowspan="2"><br>{{ $data->worksheet->id }}</td>
                    			<th>Created By</th>
                    			<td>{{ $data->worksheet->creator->full_name ?? '' }}</td>
                    			<th>Tru Count Lot #</th>
                    			<td>{{ $data->worksheet->TruCountLotno ?? '' }}</td>
                    			<th>Multicheck Normal Lot #	</th>
                    			<td>{{ $data->worksheet->MulticheckNormalLotno ?? '' }}</td>
                    		</tr>
                    		<tr>
                    			<th>Date Created</th>
                    			<td>{{ date('d-M-Y', strtotime($data->worksheet->created_at)) }}</td>
                    			<th>Antibody Lot #</th>
                    			<td>{{ $data->worksheet->AntibodyLotno ?? '' }}</td>
                    			<th>Multicheck Low Lot #</th>
                    			<td>{{ $data->worksheet->MulticheckLowLotno ?? '' }}</td>
                    		</tr>
                            <tr>
                                <th>Date Run</th>
                                <td>
                                    @isset($data->worksheet->daterun)
                                        {{ date('d-M-Y', strtotime($data->worksheet->daterun)) }}
                                    @endisset
                                </td>
                                <th>Date Updated</th>
                                <td>
                                    @isset($data->worksheet->dateuploaded)
                                        {{ date('d-M-Y', strtotime($data->worksheet->dateuploaded)) }}
                                    @endisset
                                </td>
                                <th>Date Reviewed (1st)</th>
                                <td>
                                    @isset($data->worksheet->datereviewed)
                                        {{ date('d-M-Y', strtotime($data->worksheet->datereviewed)) }}
                                    @endisset
                                </td>
                                <th>Date Reviewed (2nd)</th>
                                <td>
                                    @isset($data->worksheet->datereviewed2)
                                        {{ date('d-M-Y', strtotime($data->worksheet->datereviewed2)) }}
                                    @endisset
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @foreach($data->worksheet_statuses as $worksheetstatus)
                                        @if($worksheetstatus->id == $data->worksheet->status_id)
                                            @php
                                                echo $worksheetstatus->output;
                                            @endphp
                                        @endif
                                    @endforeach
                                </td>
                                <th>Updated By</th>
                                <td>{{ $data->worksheet->uploader->full_name ?? '' }}</td>
                                <th>Reveiwed By (1st)</th>
                                <td>{{ $data->worksheet->first_reviewer->full_name ?? '' }}</td>
                                <th>Reviewed By (2nd)</th>
                                <td>{{ $data->worksheet->second_reviewer->full_name ?? '' }}</td>
                            </tr>
                    	</table>
                    	<table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr> 
                                   <th> Serial #</th>
                                   <th> Lab ID </th>
                                   <th> Ampath #</th>
                                   <th> Patient Names </th>
                                   <th> Run </th>
                                   <th> CD3+ %Lymph </th>
                                   <th> CD3+ Abs Cnt </th>
                                   <th> CD3+CD4+ %Lymph </th>
                                   <th> CD3+CD4+ Abs Cnt </th>                                   
                                   <th> Lymphocytes </th>
                                   <th> Action </th>
                                   <th> Reviewed (1st) </th>
                                   <th> Date Reviewed (1st) </th>
                                   <th> Reviewed By (1st) </th>
                                   <th> Reviewed (2nd) </th>
                                   <th> Date Reviewed (2nd) </th>
                                   <th> Reviewed By (2nd) </th>
                                   <th> Task </th>
                                </tr>
                            </thead>
                            <tbody> 
                            @forelse($data->samples as $key => $sample)
                                <tr>
                                    <td>{{ $sample->serial_no ?? '' }}</td>
                                    <td>{{ $sample->id ?? '' }}</td>
                                    <td>{{ $sample->medicalrecordno ?? '' }}</td>
                                    <td>{{ $sample->patient_name ?? '' }}</td>
                                    <td>{{ $sample->run ?? '' }}</td>
                                    <input type="hidden" name="id[]" value="{{ $sample->id }}">
                                    <td>
                                        <input type="text" name="AVGCD3percentLymph[]" class="form-control" value="{{ $sample->AVGCD3percentLymph ?? '' }}" style="min-width: 60px;">
                                    </td>
                                    <td>
                                        <input type="text" name="AVGCD3AbsCnt[]" class="form-control" value="{{ $sample->AVGCD3AbsCnt ?? '' }}" style="min-width: 60px;">
                                    </td>
                                    <td>
                                        <input type="text" name="AVGCD3CD4percentLymph[]" class="form-control" value="{{ $sample->AVGCD3CD4percentLymph ?? '' }}" style="min-width: 60px;">
                                    </td>
                                    <td>
                                        <input type="text" name="AVGCD3CD4AbsCnt[]" class="form-control" value="{{ $sample->AVGCD3CD4AbsCnt ?? '' }}" style="min-width: 60px;">
                                    </td>
                                    <td>
                                        <input type="text" name="CD45AbsCnt[]" class="form-control" value="{{ $sample->CD45AbsCnt ?? '' }}" style="min-width: 60px;">
                                    </td>
                                    <td>
                                    @if(!isset($sample->dateapproved) || ($data->worksheet->reviewedby != Auth::user()->id))
                                        <select name="repeatt[]" class="form-control" style="width: 104px;">
                                            <option value='0' selected style='color:#339900'>Dispatch</option>
                                            <option value='1' style='color:#FFD324'>Rerun</option>
                                            <option value='2' style='color:#ee5253'>Collect New Sample</option>
                                        </select>
                                    @else
                                        @if($sample->repeatt == 0)
                                            <strong><font color='#339900'> Dispatch </font></strong>
                                        @elseif($sample->repeatt == 1)
                                            <strong><font color='#FFD324'> Rerun </font></strong>
                                        @elseif($sample->repeatt == 2)
                                            <strong><font color='#ee5253'> Collect New Sample </font></strong>
                                        @endif
                                    @endif
                                    </td>
                                    <td>
                                    @if($sample->dateapproved)
                                        <center><input class="form-control input-sm" name="checkbox[]" type="checkbox" id="checkbox[]" checked disabled style="width: 16px;height: 16px;" /></center>
                                    @else
                                        <center><input class="form-control input-sm" name="checkbox[]" type="checkbox" id="checkbox[]" value="{{ $key }}" checked style="width: 16px;height: 16px;" /></center>
                                    @endif
                                    </td>
                                    <td>
                                    @isset($sample->dateapproved)
                                        {{ date('d-M-Y', strtotime($sample->dateapproved)) }}
                                    @endisset
                                    </td>
                                    <td>{{ $sample->first_approver->full_name ?? '' }}</td>
                                    <td>
                                    @if(null !== $sample->dateapproved && $sample->dateapproved2 == null)
                                        <center><input class="form-control input-sm" name="checkbox[]" type="checkbox" id="checkbox[]" value="{{ $key }}" checked style="width: 16px;height: 16px;" /></center>
                                    @else
                                        <center><input class="form-control input-sm" name="checkbox[]" type="checkbox" id="checkbox[]" checked disabled style="width: 16px;height: 16px;" /></center>
                                    @endif
                                    </td>
                                    <td>
                                    @isset($sample->dateapproved2)
                                        {{ date('d-M-Y', strtotime($sample->dateapproved2)) }}
                                    @endisset
                                    </td>
                                    <td>{{ $sample->second_approver->full_name ?? '' }}</td>
                                    <td>
                                        <a href="{{ URL::to('cd4/sample/'.$sample->id) }}" title='Click to view Details' target='_blank'>Details</a> | 
                                        <a href="#">Runs</a> 
                                    @if($sample->status_id != 5)
                                        | <a href="#">Release as Redraw</a>
                                    @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="18">
                                    	<center>
                                    		<div class="alert alert-warning">
                                    			All the samples that were in this worksheet have been released back into the queue for selection in next worksheet.
                                    		</div>
                                    	</center>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <div class="hr-line-dashed"></div>
		                <div class="form-group">
		                    <center>
                            @if($data->worksheet->reviewedby != Auth::user()->id)
		                        <div class="col-sm-10 col-sm-offset-1">
		                          	<button class="btn btn-success" type="submit"> Confirm & Approve Results</button>
		                        </div>
                            @endif
		                    </center>
		                </div>
		            @endif
		            @if(!isset($data->view))
                        </form>
	                @endif
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