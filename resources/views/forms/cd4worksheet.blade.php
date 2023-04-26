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
                <div class="panel-body">
                    <div class="row">
                                              
                    </div>
                    <div class="table-responsive">
                    @if(!isset($data->view))
                        <form action="{{ url('/cd4/worksheet') }}" class="form-horizontal" method="POST" id='worksheet_form'>
                            @csrf
                    @endif
                   	@if(!isset($data->view) && $data->samples->count() == 0)
                   		<center><div class="alert alert-warning">No samples availabe to run a worksheet</div></center>
                   	@else
                    	<table class="table table-striped table-bordered table-hover">
                    		<tr>
                    			<th rowspan="2"><br>Worksheet No</th>
                    			<td rowspan="2"><br>
                    				@if(!isset($data->view))
                    					{{ $data->worksheet }}
                    				@else
                    					{{ $data->worksheet->id }}
                    				@endif
                    			</td>
                    			<th>Created By</th>
                    			<td>
                                    @if(isset($data->view))
                                        {{ $data->worksheet->creator->full_name ?? '' }}
                                    @else
                                        {{ Auth::user()->full_name }}
                                    @endif
                                </td>
                    			<th>Tru Count Lot #</th>
                    			<td><input type="text" name="TruCountLotno" class="form-control" value="{{ $data->worksheet->TruCountLotno ?? '' }}" required {{ $disabled }}></td>
                    			<th>Multicheck Normal Lot #	</th>
                    			<td><input type="text" name="MulticheckNormalLotno" class="form-control" value="{{ $data->worksheet->MulticheckNormalLotno ?? '' }}" required {{ $disabled }}></td>
                    		</tr>
                    		<tr>
                    			<th>Date Created</th>
                    			<td>{{ gmdate('d-M-Y') }}</td>
                    			<th>Antibody Lot #</th>
                    			<td><input type="text" name="AntibodyLotno" class="form-control" value="{{ $data->worksheet->AntibodyLotno ?? '' }}" required {{ $disabled }}></td>
                    			<th>Multicheck Low Lot #</th>
                    			<td><input type="text" name="MulticheckLowLotno" class="form-control" value="{{ $data->worksheet->MulticheckLowLotno ?? '' }}" required {{ $disabled }}></td>
                    		</tr>
                    	</table>
                    	<center><h5>{{ $data->samples->count() }} WORKSHEET SAMPLES [2 Controls]</h5></center>
                        <table class="table table-striped table-bordered table-hover" >
                            <thead>
                                <tr> 
                                   <th> SR.No</th>
                                   <th> Acc.No </th>
                                   <th> Acc.No Bar Code</th>
                                   <th> Ampath No </th>
                                   <th> Study No </th>
                                   <th> Patient Names </th>
                                   <th> Received Dt. </th>
                                   <th> Reg Dt. </th>
                                   <th> Sampl Dt. </th>
                                   <th> (Average) CD3+CD4+ %Lymph </th>
                                   <th> (Average) CD3+CD4+ Abs Cnt </th>
                                   <th> Action </th>
                                   <th> Tests </th>
                                </tr>
                            </thead>
                            <tbody> 
                            @if(!isset($data->view))
                            <input type="hidden" name="limit" value="{{ $data->limit }}">
                            @endif
                            @forelse($data->samples as $key => $sample)
                                <tr>
                                    <td>{{ $sample->serial_no ?? '' }}</td>
                                    <td>
                                        {{ $sample->id ?? '' }}
                                        @if($sample->parentid > 0)
                                            <div align='right'>
                                                <table>
                                                    <tr>
                                                        <td style='background-color:#FAF156'><small> R </small></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                    	<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($sample->id, 'C128') }}" alt="barcode" height="30" width="80"  />
                                    </td>
                                    <td>{{ $sample->medicalrecordno ?? $sample->patient->medicalrecordno ?? '' }}</td>
                                    <td>{{ __(' ') }}</td>
                                    <td>
                                    	{{ $sample->patient_name ?? $sample->patient->patient_name ?? '' }} 
                                    	/ {{ $sample->age ?? $sample->patient->age ?? '' }} 
                                    	/ {{ $sample->gender ?? $sample->patient->gender ?? '' }}
                                    </td>
                                    <td>
                                        @if($sample->datereceived) 
                                            {{ gmdate('d-M-Y', strtotime($sample->datereceived)) }} 
                                        @endif
                                    </td>
                                    <td>{{ gmdate('d-M-Y', strtotime($sample->created_at)) }}</td>
                                    <td>
                                        @if($sample->datetested) 
                                            {{ gmdate('d-M-Y', strtotime($sample->datetested)) }} 
                                        @endif
                                    </td>
                                    <td>{{ $sample->AVGCD3CD4percentLymph ?? '' }}</td>
                                    <td>{{ $sample->AVGCD3CD4AbsCnt ?? '' }}</td>
                                    <td>
                                        @if($sample->repeatt == 0)
                                            <strong><font color='#339900'> Dispatch </font></strong>
                                        @elseif($sample->repeatt == 1)
                                            <strong><font color='#FFD324'> Rerun </font></strong>
                                        @elseif($sample->repeatt == 2)
                                            <strong><font color='#ee5253'> Collect New Sample </font></strong>
                                        @endif
                                    </td>
                                    <td>{{ __('CD3/CD4') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10">
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
		                        <div class="col-sm-10 col-sm-offset-1">
                                @if(isset($data->worksheet->status_id))
    		                        @if($data->worksheet->status_id != 4)
    		                        	@if(isset($data->view))
    		                            	<a href="{{ url('cd4/worksheet/print/'.$data->worksheet->id) }}"><button class="btn btn-success">Print Worksheet</button></a>
    		                            @else
    		                            	<button class="btn btn-success" type="submit"> Save & Print Worksheet</button>
    		                            @endif
    		                        @endif
                                @else
                                    <button class="btn btn-success" type="submit"> Save & Print Worksheet</button>
                                @endif
		                        </div>
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