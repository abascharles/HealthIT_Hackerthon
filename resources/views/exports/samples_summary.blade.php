
<!DOCTYPE html>
<html>
<head>

	<style type="text/css">
		table {
			border-collapse: collapse;
		}

		table, th, td {
			border: 1px solid black;
			border-style: solid;
		}

		.page-break {
			page-break-after: always;
		}

	</style>
</head>
<body>

	@foreach($batches as $batch)

		<table border="0" id='table1' align="center">
			<tr>
				<td colspan="9" align="center">

					<span class="style1"><br>
					  <span class="style7">MINISTRY OF HEALTH <br />
					  NATIONAL AIDS AND STD CONTROL PROGRAM (NASCOP)<br />
					  EARLY INFANT HIV DIAGNOSIS (DNA-PCR) RESULT FORM</span>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="5" class="comment style1 style4">
					<strong> Batch No.: {{ $batch->id }} &nbsp;&nbsp; {{ $batch->facility->name }} </strong> 
				</td>
				<td colspan="4" class="comment style1 style4" align="right">
					<strong>LAB: {{ $batch->lab->name }}</strong>
				</td>
			</tr>
			<tr>
				<td colspan="9" class="comment style1 style4">
					<strong>NOTICE:</strong> 
				</td>
			</tr>
			<tr>
				<td colspan="9" class="comment style1 style4">
					<strong>The Viral Load Test is now available in all EID testing sites. Samples can be collected in DBS form and shipped using the A/C C00339.Call the official EID lines for more information. Thank you.</strong>
				</td>
			</tr>
		</table>

		<br />

		<table>
			<tr>
				<td colspan='3'>Date Samples Were Dispatched :  {{ $batch->my_date_format('datedispatched')  }}</td>				
			</tr>
			<tr>
				<td>Facility Name: {{ $batch->facility->name }} </td>
				<td>Contact: {{ $batch->facility->contactperson }} </td>
				<td>Tel(personal): {{ $batch->facility->contacttelephone }} </td>
			</tr>
			<tr>
				<td colspan='3'>Receiving Address (via Courier): {{ $batch->facility->PostalAddress }}</td>
			</tr>
			<tr>
				<td colspan='3'>Email (optional-where provided results will be emailed and also sent by courier ):  {{ $batch->facility->email }}</td>
			</tr>
		</table>

		<br />

		<table style="width: 100%;">
			<tr>
				<td colspan="17" style="text-align: center;"><b>SAMPLE LOG</b></td>
			</tr>
			<tr>
				<td colspan="5"><b> Patient Information</b></td>
				<td colspan="4"><b>Samples Information</b></td>
				<td colspan="4"><b>Mother Information</b></td>
				<td colspan="4"><b>Lab Information</b></td>
			</tr>
			<tr>
				<td>No</td>
				<td>Patient ID</td>
				<td>Sex</td>
				<td>Age (mths)</td>
				<td>Prophylaxis</td>
				<td>Date Collected</td>
				<td>Date Received</td>
				<td>Status</td>
				<td>Test Type</td>
				<td>HIV Status</td>
				<td>PMTCT</td>
				<td>Feeding</td>
				<td>Entry Point</td>
				<td>Date Tested</td>
				<td>Date Dispatched</td>
				<td>Test Result</td>
				<td>TAT</td>
			</tr>

			@foreach($batch->sample as $key => $sample)
				@if($sample->receivedstatus == 2)
					@php  
						$rejection = true;
						continue;
					@endphp
				@endif
				<tr>
					<td>{{ ($key+1) }} </td>
					<td>{{ $sample->patient->patient }} </td>
					<td>
	                    @foreach($genders as $gender)
	                        @if($sample->patient->sex == $gender->id)
	                            {{ $gender->gender }}
	                        @endif
	                    @endforeach
					</td>
					<td>{{ $sample->age }} </td>
					<td>{{ $sample->regimen }} </td>
					<td>{{ $sample->my_date_format('datecollected') }} </td>
					<td>{{ $batch->my_date_format('datereceived') }} </td>
					<td>
	                    @foreach($received_statuses as $received_status)
	                        @if($sample->receivedstatus == $received_status->id)
	                            {{ $received_status->name }}
	                        @endif
	                    @endforeach
					</td>
					<td>{{ $sample->pcrtype }} </td>
					<td>
	                    @foreach($results as $result)
	                        @if($sample->patient->mother->hiv_status == $result->id)
	                            {{ $result->name }}
	                        @endif
	                    @endforeach
					</td>
					<td>{{ $sample->mother_prophylaxis }} </td>
					<td>
	                    @foreach($feedings as $feeding)
	                        @if($sample->feeding == $feeding->id)
	                            {{ $feeding->feeding }}
	                        @endif
	                    @endforeach		
	                </td>
	                <td>{{ $sample->patient->entry_point }} </td>
					<td>{{ $sample->my_date_format('datetested') }} </td>
					<td>{{ $batch->my_date_format('datedispatched') }} </td>
					<td>
	                    @foreach($results as $result)
	                        @if($sample->result == $result->id)
	                            {{ $result->name }}
	                        @endif
	                    @endforeach
					</td>
					<td>{{ $sample->tat($batch->datedispatched) }} </td>
				</tr>
			@endforeach		
		</table>

		Result Reviewed By: {{ $sample->approver->full_name ?? '' }}  Date Reviewed: {{ $sample->my_date_format('dateapproved') }}

		@isset($rejection)
			<table>
				<tr>
					<td colspan="10">REJECTED SAMPLE(s)</td>
				</tr>
				<tr>
					<td>No</td>
					<td>Patient ID</td>
					<td>Sex</td>
					<td>Age (mths)</td>
					<td>Prophylaxis</td>
					<td>Date Collected</td>
					<td>Date Received</td>
					<td>Status</td>
					<td>Rejected Reason</td>
					<td>Date Dispatched</td>			
				</tr>

				@foreach($batch->sample as $key => $sample)
					@continue($sample->receivedstatus != 2)
					<tr>
						<td>{{ ($key+1) }} </td>
						<td>{{ $sample->patient->patient }} </td>
						<td>
		                    @foreach($genders as $gender)
		                        @if($sample->patient->sex == $gender->id)
		                            {{ $gender->gender }}
		                        @endif
		                    @endforeach
						</td>
						<td>{{ $sample->age }} </td>
						<td>{{ $sample->regimen }} </td>
						<td>{{ $sample->my_date_format('datecollected') }} </td>
						<td>{{ $batch->my_date_format('datereceived') }} </td>
						<td>
		                    @foreach($received_statuses as $received_status)
		                        @if($sample->receivedstatus == $received_status->id)
		                            {{ $received_status->name }}
		                        @endif
		                    @endforeach
						</td>
						<td>
		                    @foreach($rejected_reasons as $rejected_reason)
		                        @if($sample->rejectedreason == $rejected_reason->id)
		                            {{ $rejected_reason->name }}
		                        @endif
		                    @endforeach
						</td>
						<td>{{ $batch->my_date_format('datedispatched') }} </td>
					</tr>
				@endforeach	
			</table>
		@endisset

		<br />
		<br />
		<br />

		<p>
			NOTE: Always provide the facility's up-to-date email address(es) and mobile number(s) on the sample requisition form so as to get alerts on the status of your samples.
			<br />
			To Access & Download your current and past results go to : http://www.nascop.org/eid/facilitylogon.php
		</p>

		<b>KEY/CODES</b>

		<table>
			<tr>
				<td><b>Test Type </b> </td>
				<td>1-1st test, &nbsp; 2-Repeat for Rejection, &nbsp; 3-Confirmatory PCR at 9mths </td>
			</tr>
			<tr>
				<td><b>Entry Point </b> </td>
				<td>
					@foreach($entry_points as $entry_point)
						{{ $entry_point->id . '-' . $entry_point->name }}

						@if($loop->last)
							@break
						@endif
						,&nbsp;
					@endforeach
				</td>
			</tr>
			<tr>
				<td><b>Infant Prophylaxis </b> </td>
				<td>
					@foreach($iprophylaxis as $iproph)
						{{ $iproph->id . '-' . $iproph->name }}

						@if($loop->last)
							@break
						@endif
						,&nbsp;
					@endforeach
				</td>				
			</tr>
			<tr>
				<td><b>Infant Feeding </b> </td>
				<td>
					@foreach($feedings as $feeding)
						{{ $feeding->feeding . ' : ' . $feeding->feeding_description }}

						@if($loop->last)
							@break
						@endif
						,&nbsp;
					@endforeach
				</td>				
			</tr>
			<tr>
				<td><b>PMTCT Intervention </b> </td>
				<td>
					@foreach($interventions as $intervention)
						{{ $intervention->id . '-' . $intervention->name }}

						@if($loop->last)
							@break
						@endif
						,&nbsp;
					@endforeach
				</td>				
			</tr>
		</table>

		@if($loop->last)
			@break
		@endif

		<div class="page-break"></div>

	@endforeach




</body>
</html>

