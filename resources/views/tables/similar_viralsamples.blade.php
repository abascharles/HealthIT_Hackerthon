
<table class="table table-striped table-bordered table-hover" >
	<thead>
	    <tr>
	        <th colspan="15"><center>Potentially Similar Samples</center></th>
	    </tr>
	    <tr>
	        <th colspan="5">Patient Information</th>
	        <th colspan="5">Sample Information</th>
	        <th colspan="5">History Information</th>
	    </tr>
	    <tr>
	    	<th>Lab ID</th>
	        <th>Patient CCC No</th>
	        <th>Sex</th>
	        <th>Age</th>
	        <th>DOB</th>

	        <th>Sample Type</th>
	        <th>Collection Date</th>
	        <th>Received Status</th>
	        <th>Batch</th>
	        <th>Worksheet</th>

	        <th>Current Regimen</th>
	        <th>ART Initiation Date</th>
	        <th>Justification</th>
	        <th>Viral Load</th>
	        <th>Task</th>
	    </tr>
	</thead>
	<tbody> 
		@foreach($samples as $sample)
		    <tr>
		    	<td> {{ $sample->id }} </td>
		        <td> {!! $sample->get_link('patient_id') !!} </td>
		        <td> {{ $sample->gender }} </td>
		        <td> {{ $sample->age }} </td>
		        <td> {{ $sample->dob }} </td>
		        <td>
		            @foreach($sample_types as $sample_type)
		                @if($sample->sampletype == $sample_type->id)
		                    {{ $sample_type->name }}
		                @endif
		            @endforeach
		        </td>
		        <td> {{ $sample->datecollected }} </td>
		        <td>
		            @foreach($received_statuses as $received_status)
		                @if($sample->receivedstatus == $received_status->id)
		                    {{ $received_status->name }}
		                @endif
		            @endforeach
		        </td>
		        <td> {!! $sample->get_link('batch_id') !!} </td>
		        <td> {!! $sample->get_link('worksheet_id') !!} </td>
		        <td>
		            @foreach($prophylaxis as $proph)
		                @if($sample->prophylaxis == $proph->id)
		                    {{ $proph->name }}
		                @endif
		            @endforeach
		        </td>
		        <td> {{ $sample->initiation_date }} </td>
		        <td>
		            @foreach($justifications as $justification)
		                @if($sample->justification == $justification->id)
		                    {{ $justification->name }}
		                @endif
		            @endforeach
		        </td>
		        <td> {{ $sample->result }} </td>
		        <td>
		            @if($sample->batch_complete == 1)
		                <a href="{{ url('/viralsample/print/' . $sample->id ) }} " target='_blank'>Print</a> |
		            @endif
		            <a href="{{ url('/viralsample/' . $sample->id . '/edit') }} ">View</a> |
		            <a href="{{ url('/viralsample/' . $sample->id . '/edit') }} ">Edit</a> |
		        </td>
		    </tr>
		@endforeach
	</tbody>
</table>