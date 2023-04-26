<a href="{{ url('dr_worksheet/download/' . $worksheet->id) }}" title="Click to View Download Bulk Template">
	Download Bulk Template
</a> | 
<br />
<a href="{{ url('dr_worksheet/abfiles/' . $worksheet->id) }}" title="Click to Download AB File Names">
	Download AB Filenames
</a> | 
<br />

@if($worksheet->status_id == 1)

	<a href="{{ url('dr_worksheet/' . $worksheet->id) }}" title="Click to View Worksheet Details">
		Details
	</a> | 
	<a href="{{ url('dr_worksheet/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a> | 
	<a href="{{ url('dr_worksheet/cancel/' . $worksheet->id) }}" title="Click to Cancel Worksheet" OnClick="return confirm('Are you sure you want to Cancel Worksheet {{$worksheet->id}}?');">		
		Cancel
	</a> | 
	<a href="{{ url('dr_worksheet/upload/' . $worksheet->id) }}" title="Click to Update Results Worksheet" target='_blank'>
		Update
	</a>

@elseif($worksheet->status_id == 2)

	<a href="{{ url('dr_worksheet/cancel_upload/' . $worksheet->id) }}" title="Click to Cancel Worksheet" OnClick="return confirm('Are you sure you want to Cancel Worksheet {{$worksheet->id}}?');">		
		Cancel Upload
	</a> | 

	<a href="{{ url('dr_worksheet/create_plate/' . $worksheet->id) }}" title="Click to Send Worksheet to Exatype">		
 		Send to Exatype
 	</a> |

	<a href="{{ url('dr_worksheet/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a>

@elseif($worksheet->status_id == 3)

	<a href="{{ url('dr_worksheet/approve/' . $worksheet->id) }}" title="Click to view Samples in this Worksheet" target='_blank'>
		View Results
	</a> | 
	<a href="{{ url('dr_worksheet/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a>


@elseif($worksheet->status_id == 4)

	<a href="{{ url('dr_worksheet/' . $worksheet->id) }}" title="Click to View Cancelled Worksheet Details">
		View Cancelled  Worksheet Details
	</a>

@elseif($worksheet->status_id == 5)

	<a href="{{ url('dr_worksheet/get_plate_result/' . $worksheet->id) }}" title="Click to Get Worksheet From Exatype">		
 		Retrieve Results
 	</a> |

	<a href="{{ url('dr_worksheet/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a>

@elseif($worksheet->status_id == 6)

	<a href="{{ url('dr_worksheet/get_plate_result/' . $worksheet->id) }}" title="Click to Get Worksheet From Exatype">		
 		Retrieve Results
 	</a> |

	<a href="{{ url('dr_worksheet/approve/' . $worksheet->id) }}" title="Click to Approve Samples Results in worksheet for Rerun or Dispatch" target='_blank'>
		Approve Worksheet Results
	</a> | 
	<a href="{{ url('dr_worksheet/print/' . $worksheet->id) }}" title="Click to Download Worksheet" target='_blank'>
		Print
	</a>

@else
@endif
