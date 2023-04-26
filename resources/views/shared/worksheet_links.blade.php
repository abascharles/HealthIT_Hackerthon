@if($worksheet->status_id == 1)


	@if(!$worksheet->machine_type)
		<a href="{{ url($worksheet->route_name . '/result_file/' . $worksheet->id) }}" title="Click to Download Worksheet Result File">
			Results File
		</a> | 
	@else
		<a href="{{ url($worksheet->route_name . '/' . $worksheet->id) }}" title="Click to View Worksheet Details">
			Details
		</a> | 
		<a href="{{ url($worksheet->route_name . '/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
			Print
		</a> | 
	@endif
	
	@if($worksheet->route_name == 'covid_worksheet' && $worksheet->machine_type != 0 && session('testingSystem') != 'HPV')
		<a href="{{ url($worksheet->route_name . '/convert/' . $worksheet->id . '/0') }}" title="Convert Worksheet" >
			Convert to Manual
		</a> |
	@endif

	@if(env('APP_LAB') != 25)
	
		@if($worksheet->samples_no < 23 && $worksheet->machine_type != 1 && session('testingSystem') != 'HPV')
			<a href="{{ url($worksheet->route_name . '/convert/' . $worksheet->id . '/1') }}" title="Convert Worksheet" >
				Convert to Roche/Taqman
			</a> |
		@endif

		@if($worksheet->machine_type != 2)
		<a href="{{ url($worksheet->route_name . '/convert/' . $worksheet->id . '/2') }}" title="Convert Worksheet">
			Convert to Abbott
		</a> |
		@endif

		@if($worksheet->machine_type != 3)
		<a href="{{ url($worksheet->route_name . '/convert/' . $worksheet->id . '/3') }}" title="Convert Worksheet" >
			Convert to C8800
		</a> |
		@endif
		
		@if($worksheet->route_name == 'viralworksheet' && $worksheet->machine_type != 4 && session('testingSystem') != 'HPV')
		<a href="{{ url($worksheet->route_name . '/convert/' . $worksheet->id . '/4') }}" title="Convert Worksheet">
			Convert to Panther
		</a> |
		@endif

	@endif


	<a href="{{ url($worksheet->route_name . '/labels/' . $worksheet->id) }}" title="Click to Print Worksheet Labels">
		Print Labels
	</a> | 

	<a href="{{ url($worksheet->route_name . '/cancel/' . $worksheet->id) }}" title="Click to Cancel Worksheet" OnClick="return confirm('Are you sure you want to Cancel Worksheet {{$worksheet->id}}?');">		
		Cancel
	</a> | 

	<a href="{{ url($worksheet->route_name . '/upload/' . $worksheet->id) }}" title="Click to Update Results Worksheet" target='_blank'>
		Update
	</a>

@elseif($worksheet->status_id == 2)

	<a href="{{ url($worksheet->route_name . '/approve/' . $worksheet->id) }}" title="Click to Approve Samples Results in worksheet for Rerun or Dispatch" target='_blank'>
		Approve Worksheet Results
		@if(in_array(env('APP_LAB'), $double_approval) && $worksheet->datereviewed)
			(Second Review)
		@endif
	</a> | 
	@if($worksheet->failed)
		<a href="{{ url($worksheet->route_name . '/rerun_worksheet/' . $worksheet->id) }}" title="Click to Rerun Worksheet" target='_blank'> Rerun Worksheet </a> |
	@endif
	<a href="{{ url($worksheet->route_name . '/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a>

@elseif($worksheet->status_id == 3 || $worksheet->status_id == 5)

	@if(in_array(env('APP_LAB'), [8,9]) && $worksheet->route_name == 'viralworksheet')
		{!! $worksheet->dump_link !!}
	@endif

	@if($worksheet->route_name == 'covid_worksheet')
		<a href="{{ url($worksheet->route_name . '/result_file/' . $worksheet->id) }}" title="Click to Download Worksheet Result File">
			Results File
		</a> | 
	@endif

	<a href="{{ url($worksheet->route_name . '/approve/' . $worksheet->id) }}" title="Click to view Samples in this Worksheet" target='_blank'>
		View Results
	</a> | 
	<a href="{{ url($worksheet->route_name . '/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a>


@elseif($worksheet->status_id == 4)
	<a href="{{ url($worksheet->route_name . '/' . $worksheet->id) }}" title="Click to View Cancelled Worksheet Details">
		View Cancelled  Worksheet Details
	</a> |
	@if(session('testingSystem') != 'HPV')
	<a href="{{ url($worksheet->route_name . '/upload/' . $worksheet->id) }}" title="Click to Update Results Worksheet" target='_blank'>
		Update Results (In Case of Accidental Cancellation)
	</a> | 
	@endif
	{!! $worksheet->delete_form !!}
@elseif($worksheet->status_id == 7)
	<a href="{{ url($worksheet->route_name . '/approve/' . $worksheet->id) }}" title="Click to View Samples Results" target='_blank'>	
		View Results
	</a>
@else
@endif
