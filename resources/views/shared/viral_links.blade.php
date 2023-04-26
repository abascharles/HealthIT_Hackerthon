@if($worksheet_status == 1)

	<a href="{{ url('viralworksheet/' . $worksheet->id) }}" title="Click to View Worksheet Details">
		Details
	</a> | 
	<a href="{{ url('viralworksheet/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a> | 
	<a href="{{ url('viralworksheet/cancel/' . $worksheet->id) }}" title="Click to Cancel Worksheet" OnClick="return confirm('Are you sure you want to Cancel Worksheet {{$worksheet->id}}?');">		
		Cancel
	</a> | 
	@if(in_array(env('APP_LAB'), [1, 4, 5]))
		<a href="{{ url('viralworksheet/labels/' . $worksheet->id) }}" title="Click to Print Worksheet Labels">
			Print Labels
		</a> | 
	@endif
	
	<a href="{{ url('viralworksheet/convert/' . $worksheet->id . '/1') }}" title="Convert Worksheet" >
		Convert to Roche/Taqman
	</a> |
	<a href="{{ url('viralworksheet/convert/' . $worksheet->id . '/2') }}" title="Convert Worksheet" >
		Convert to Abbott
	</a> |
	<a href="{{ url('viralworksheet/convert/' . $worksheet->id . '/3') }}" title="Convert Worksheet" >
		Convert to C8800
	</a> |
	<a href="{{ url('viralworksheet/convert/' . $worksheet->id . '/4') }}" title="Convert Worksheet" >
		Convert to Panther
	</a> |

	<a href="{{ url('viralworksheet/upload/' . $worksheet->id) }}" title="Click to Update Results Worksheet" target='_blank'>
		Update
	</a>

@elseif($worksheet_status == 2)

	<a href="{{ url('viralworksheet/approve/' . $worksheet->id) }}" title="Click to Approve Samples Results in worksheet for Rerun or Dispatch" target='_blank'>
		Approve Worksheet Results
		@if(in_array(env('APP_LAB'), $double_approval) && $worksheet->datereviewed)
			(Second Review)
		@endif
	</a> | 
	@if($worksheet->failed)
		<a href="{{ url($worksheet->route_name . '/rerun_worksheet/' . $worksheet->id) }}" title="Click to Rerun Worksheet" target='_blank'> Rerun Worksheet </a> |
	@endif
	<a href="{{ url('viralworksheet/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a>

@elseif($worksheet_status == 3 || $worksheet_status == 5)

	@if(env('APP_LAB') == 9 || env('APP_LAB') == 8)
		{!! $worksheet->dump_link !!}
	@endif

	<a href="{{ url('viralworksheet/approve/' . $worksheet->id) }}" title="Click to view Samples in this Worksheet" target='_blank'>
		View Results
	</a> | 
	<a href="{{ url('viralworksheet/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a>


@elseif($worksheet_status == 4)
	<a href="{{ url('viralworksheet/' . $worksheet->id) }}" title="Click to View Cancelled Worksheet Details">
		View Cancelled  Worksheet Details
	</a> |

	<a href="{{ url('viralworksheet/upload/' . $worksheet->id) }}" title="Click to Update Results Worksheet" target='_blank'>
		Update (In Case of Accidental Cancellation)
	</a> |

    <form action="{{ url('viralworksheet/' . $worksheet->id) }}" method="POST" onSubmit="return confirm('Are you sure you want to delete the following worksheet?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-xs btn-primary">Delete</button>
    </form>

@else
@endif
