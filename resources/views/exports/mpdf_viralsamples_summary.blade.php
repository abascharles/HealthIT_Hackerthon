
<!DOCTYPE html>
<html>
<head>

	<style type="text/css">
		body {
			font-weight: 1px;
		}

		table {
			border-collapse: collapse;
			margin-bottom: .5em;
		}

		table, th, td {
			border: 1px solid black;
			border-style: solid;
     		font-size: 8px;
		}


		h5 {
			margin-top: 6px;
		    margin-bottom: 6px;
		}

		p {
			margin-top: 2px;
     		font-size: 8px;
		}
		* {
			font-size: 8px;
		}
	</style>
</head>
<body>

	@foreach($batches as $batch)
		<?php 
			if($batch->sample->count() == 0) unset($batch->sample);
		?>

		<table border="0" style="border: 0px; width: 100%;">
			<tr>
				<td colspan="9" align="center">
					<img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP">
				</td>
			</tr>
			<tr>
				<td colspan="9" align="center">
					<h5>MINISTRY OF HEALTH</h5>
					<h5>NATIONAL AIDS AND STD CONTROL PROGRAM (NASCOP)</h5>
					<h5>VIRAL LOAD TEST RESULTS SUMMARY</h5>
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<strong> Batch No.: {{ $batch->id }} &nbsp;&nbsp; {{ $batch->facility->name ?? ''}} </strong> 
				</td>
				<td colspan="3" align="right">
					@if($batch->site_entry == 2)
						<strong>Testing Facility: {{ $batch->facility_lab->name ?? '' }}</strong>
					@else
						<strong>LAB: {{ $batch->lab->name ?? '' }}</strong>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="9">
					<strong>NOTICE:</strong>
					<strong>The Viral Load Test is now available in all EID testing sites. Samples can be collected in DBS form and shipped using the A/C C00339.Call the official EID lines for more information. Thank you.</strong>
				</td>
			</tr>
		</table>

		<table style="width: 100%;">
			<tr>
				<td colspan='3'>Date Samples Were Dispatched From Facility :  {{ $batch->my_date_format('datedispatchedfromfacility')  }}</td>		
			</tr>
			<tr>
				<td>Facility Name: {{ $batch->facility->name ?? '' }} </td>
				<td>Contact: {{ $batch->facility->contactperson ?? '' }} </td>
				<td>Tel(personal): {{ $batch->facility->contacttelephone ?? '' }} </td>
			</tr>
			<tr>
				<td>MFL Code: {{ $batch->view_facility->facilitycode ?? '' }} </td>
				<td>Subcounty: {{ $batch->view_facility->subcounty ?? '' }} </td>
				<td>County: {{ $batch->view_facility->county ?? '' }} </td>
			</tr>
			<tr>
				<td colspan='3'>Receiving Address (via Courier): {{ $batch->facility->PostalAddress ?? '' }}</td>
			</tr>
			<tr>
				<td colspan='3'>Email (optional-where provided results will be emailed and also sent by courier ):  {{ $batch->facility->email_string }}</td>
			</tr>
		</table>

		<br />

		<table style="width: 100%;">
			<tr>
				<th colspan="15" style="text-align: center;"><b>SAMPLE LOG</b> </td>
			</tr>
			<tr>
				<th colspan="4"><b> Patient Information</b></td>
				<th colspan="5"><b>Samples Information</b></td>
				<th colspan="6"><b>Lab Information</b></td>
			</tr>
			<tr>
				<th><b>No </b></th>
				<th><b>Patient CCC No </b></th>
				<th><b>DOB & Age (yrs) </b></th>
				<th><b>Sex </b></th>

				<th><b>Sample Type </b></th>
				<th><b>ART Initiation Date </b></td>
				<th><b>Current Regimen </b></th>
				<th><b>Date Initiated on Current Regimen </b></th>
				<th><b>Justification </b></th>

				<th><b>Date Collected </b></th>
				<th><b>Date Received </b></th>
				<th><b>Date Tested </b></th>
				<th><b>Date Dispatched </b></th>
				<th><b>Test Result </b></th>
				<th><b>TAT</b></th>
			</tr>
			<?php $i=1; ?>
			@foreach($batch->sample as $key => $sample)
				@if($sample->receivedstatus == 2)
					@php  
						$rejection = true;
						continue;
					@endphp
				@endif
				@continue($sample->repeatt == 1)
				<tr>
					<td>{{ ($i++) }} </td>
					<td>{{ $sample->patient->patient ?? '' }} </td>
					<td>{{ $sample->patient->my_date_format('dob') }} &nbsp; ({{ $sample->age }})</td>
					<td>{{ $sample->patient->gender ?? '' }} </td>

					<td>
	                    @foreach($sample_types as $sample_type)
	                        @if($sample->sampletype == $sample_type->id)
	                            {{ $sample_type->name }}
	                        @endif
	                    @endforeach
	                </td>
					<td>{{ $sample->patient->my_date_format('initiation_date') }} </td>
					<td>
	                    @foreach($prophylaxis as $proph)
	                        @if($sample->prophylaxis == $proph->id)
	                            {{ $proph->name }}
	                        @endif
	                    @endforeach
	                </td>
					<td>{{ $sample->my_date_format('dateinitiatedonregimen') }} </td>
					<td>
	                    @foreach($justifications as $justification)
	                        @if($sample->justification == $justification->id)
	                            {{ $justification->name }}
	                        @endif
	                    @endforeach
	                </td>

					<td>{{ $sample->my_date_format('datecollected') }} </td>
					<td>{{ $batch->my_date_format('datereceived') }} </td>
					<td>{{ $sample->my_date_format('datetested') }} </td>
					<td>{{ $batch->my_date_format('datedispatched') }} </td>
					<td>{{ $sample->result }} </td>
					<td>{{ $sample->tat($batch->datedispatched) }} </td>
				</tr>
			@endforeach		
		</table>

		@isset($rejection)
			<table>
				<tr>
					<td colspan="12" style="text-align: center;"><b>REJECTED SAMPLE(s)</b></td>
				</tr>
				<tr>
					<th><b> No </b></td>
					<th><b> Patient CCC no </b></td>
					<th><b> DOB & Age (yrs) </b></td>
					<th><b> Sex </b></td>
					<th><b> ART Initiation Date </b></td>
					<th><b> Sample Type </b></td>
					<th><b> Current Regimen </b></td>
					<th><b>Date Initiated on Current Regimen </b></th>
					<th><b> Justification </b></td>
					<th><b> Rejected Reason </b></td>
					<th><b> Date Collected </b></td>
					<th><b> Date Received </b></td>
					<th><b> Date Dispatched </b></td>			
				</tr>

				@foreach($batch->sample as $key => $sample)
					@continue($sample->receivedstatus != 2)
					<tr>
						<td>{{ ($key+1) }} </td>
						<td>{{ $sample->patient->patient }} </td>
						<td>{{ $sample->patient->my_date_format('dob') }} &nbsp; ({{ $sample->age }})</td>
						<td>{{ $sample->patient->gender ?? '' }} </td>
						<td>{{ $sample->patient->my_date_format('initiation_date') }} </td>

						<td>
		                    @foreach($sample_types as $sample_type)
		                        @if($sample->sampletype == $sample_type->id)
		                            {{ $sample_type->name }}
		                        @endif
		                    @endforeach
		                </td>
						<td>
		                    @foreach($prophylaxis as $proph)
		                        @if($sample->prophylaxis == $proph->id)
		                            {{ $proph->name }}
		                        @endif
		                    @endforeach
		                </td>
						<td>{{ $sample->my_date_format('dateinitiatedonregimen') }} </td>
						<td>
		                    @foreach($justifications as $justification)
		                        @if($sample->justification == $justification->id)
		                            {{ $justification->name }}
		                        @endif
		                    @endforeach
		                </td>
						<td>
		                    @foreach($viral_rejected_reasons as $rejected_reason)
		                        @if($sample->rejectedreason == $rejected_reason->id)
		                            {{ $rejected_reason->name }}
		                        @endif
		                    @endforeach
						</td>
						<td>{{ $sample->my_date_format('datecollected') }} </td>
						<td>{{ $batch->my_date_format('datereceived') }} </td>
						<td>{{ $batch->my_date_format('datedispatched') }} </td>
					</tr>
				@endforeach	
			</table>
		@endisset
		
		<br />

			<?php 
				$sample = $batch->sample->where('receivedstatus', 1)->first();
			?>

		@if(env('APP_LAB') != 1 && $sample)


			<p>Result Reviewed By: {{ $sample->approver->full_name ?? '' }}  Date Reviewed: {{ $sample->my_date_format('dateapproved') }}</p>

		@endif

		<p>
			<strong>NOTE:</strong> Always provide the facility's up-to-date email address(es) and mobile number(s) on the sample requisition form so as to get alerts on the status of your samples.
			<br />
			To Access & Download your current and past results go to : http://nascop.org
		</p>

		@if($batch->site_entry != 2)

			<b>LAB CONTACTS </b>

			<table style="display: inline-block;">
				<th> </td>
				<tr><td><b> {{ $batch->lab->labname }} </b></td></tr>
				<tr><td>{{ $batch->lab->name }} </td></tr>
				<tr><td>{{ $batch->lab->lablocation }} </td></tr>
				<tr><td>{{ $batch->lab->labtel1 }} </td></tr>
				<tr><td>{{ $batch->lab->labtel2 }} </td></tr>
				<tr><td>{{ $batch->lab->email }} </td></tr>
			</table>

		@endif

		@if($loop->last)
			@break
		@else
			<pagebreak sheet-size='A4-L'>
		@endif

		<!-- <div class="page-break"></div> -->

		<!-- <pagebreak orientation='L' sheet-size='A4-L'> -->

	@endforeach




</body>
</html>

