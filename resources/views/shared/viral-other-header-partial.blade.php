<table  class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>&lt;LDL </th>
			<th>Detected</th>
			<th>Failed</th>
			<th>No Result</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td> {{ $subtotals['undetected'] }} </td>
			<td> {{ $subtotals['detected'] }} </td>
			<td> {{ $subtotals['failed'] }} </td>
			<td> {{ $subtotals['noresult'] }} </td>
			<td> {{ $subtotals['total'] }} </td>
		</tr>

	</tbody>

</table>

<br />
@php
	if(in_array(env('APP_LAB'), $double_approval)){
		$dual_approval = true;
	}
	else{
		$dual_approval = false;	
	}
@endphp

<table class="table table-striped table-bordered table-hover">
	<tr>
		<td><strong>Worksheet No</strong> </td>
		<td>{{ $worksheet->id }} </td>
		<td></td>
		<td></td>
		<td><strong>KIT EXP</strong></td>
		<td>{{ $worksheet->my_date_format('kitexpirydate')  }}</td>
		<td><strong>Date Reviewed @if($dual_approval) (1<sup>st</sup>) @endif</strong></td>
		<td>{{ $worksheet->my_date_format('datereviewed')  }}</td>
	</tr>
	<tr>
		<td><strong>Status</strong> </td>
		<td>{!! $worksheet_statuses->where('id', $worksheet->status_id)->first()->output ?? '' !!} </td>
		<td><strong>Lot No</strong> </td>
		<td>{{ $worksheet->lot_no }}</td>
		<td><strong>Date Cut</strong> </td>
		<td>{{ $worksheet->my_date_format('datecut')  }}</td>
		<td><strong>Reviewed By @if($dual_approval) (1<sup>st</sup>) @endif</strong></td>
		<td>{{ $worksheet->reviewer->full_name ?? ''  }}</td>
	</tr>
	<tr>
		<td><strong>Date Created</strong> </td>
		<td>{{ $worksheet->my_date_format('created_at')  }}</td>
		<td><strong>HIQCAP Kit No</strong> </td>
		<td>{{ $worksheet->hiqcap_no }}</td>
		<td><strong>Date Run</strong> </td>
		<td>{{ $worksheet->my_date_format('daterun')  }}</td>

		@if($dual_approval)
			<td><strong>Date Reviewed (2<sup>nd</sup>) </strong></td>
			<td>{{ $worksheet->my_date_format('datereviewed2')  }}</td>
		@else
			<td></td>
			<td></td>
		@endif
	</tr>
	<tr>
		<td><strong>Created By</strong> </td>
		<td>{{ $worksheet->creator->full_name ?? ''  }}</td>
		<td><strong>Rack No</strong> </td>
		<td>{{ $worksheet->rack_no }}</td>
		<td><strong>Date Updated</strong> </td>
		<td>{{ $worksheet->my_date_format('dateuploaded')  }}</td>

		@if($dual_approval)
			<td><strong>Reviewed By (2<sup>nd</sup>) </strong></td>
			<td>{{ $worksheet->reviewer2->full_name ?? ''  }}</td>
		@else
			<td></td>
			<td></td>
		@endif
	</tr>
	<tr>
		<td><strong>Type</strong> </td>
		<td>{!! $machines->where('id', $worksheet->machine_type)->first()->output ?? '' !!} </td>
		<td><strong>Spek Kit No</strong> </td>
		<td>{{ $worksheet->spekkit_no }}</td>
		<td><strong>Updated By</strong></td>
		<td>{{ $worksheet->uploader->full_name ?? '' }}</td>
		<td>Alli Quoted By</td>
		<td>{{ $worksheet->quoter->full_name ?? '' }}</td>
	</tr>


</table>


{{--
<table  class="table table-striped table-bordered table-hover">

	<tr >
		<td class="comment style1 style4"> Worksheet No		</td>
		<td class="comment"> <span class="style5"> {{ $worksheet->id }} </span></td>
		<td class="comment style1 style4">Lot No </td>
		<td><span class="comment style1 style4"> {{ $worksheet->lot_no }}78 </span></td>
		<td><span class="style5">Date Cut </span></td>
		<td colspan="2">{{ $worksheet->my_date_format('datecut')  }}</td>
	</tr>
	<tr >
		<td class="comment style1 style4"> Date Created		</td>
		<td class="comment" ><span class="style5">{{ $worksheet->my_date_format('created_at') }}</span></td>
		<td class="comment style1 style4">HIQCAP Kit No</td>	
		<td><span class="comment style1 style4">{{ $worksheet->hiqcap_no }}</span></td>	
		<td><span class="style5">Reviewed By  </span></td>
		<td colspan="2">N/A</td>
	</tr>
	<tr >
		<td class="comment style1 style4"> Created By	    </td>
		<td class="comment"  ><span class="style5"> {{ $worksheet->creator->full_name ?? '' }}</span></td>
		<td class="comment style1 style4"> Rack <strong>#</strong></td>
		<td><span class="comment style1 style4">{{ $worksheet->rack_no }}</span></td>
		<td><span class="style5">Date Reviewed</span></td>
		<td colspan="2">{{ $worksheet->my_date_format('datereviewed')  }}</td>
    </tr>
    <tr ></tr>
	<tr >
		<td><span class="style5">Spek Kit No		</span></td>
		<td  colspan=""> <span class="style5">{{ $worksheet->spekkit_no }}</span> </td>
		<td><span class="style5">KIT EXP </span></td>
		<td><span class="style4">{{ $worksheet->my_date_format('kitexpirydate')  }}</span></td>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr class="even">
		<td><strong>Sorted By	</strong>    </td>
		<td> {{ $worksheet->sorter->full_name ?? ''  }} </td>
				
		<td><strong>Run By	</strong>    </td>
		<td> {{ $worksheet->runner->full_name ?? ''  }}</td>
	</tr>

</table>
--}}