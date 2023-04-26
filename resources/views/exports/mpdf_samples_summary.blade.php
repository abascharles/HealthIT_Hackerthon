
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

		<table class="table" border="0" style="border: 0px; width: 100%;">
			<tr>
				<td colspan="9" align="center">
					<img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP">
				</td>
			</tr>
			<tr>
				<td colspan="9" align="center">
					<h5>MINISTRY OF HEALTH</h5>
					<h5>NATIONAL AIDS AND STD CONTROL PROGRAM (NASCOP)</h5>
					<h5>EARLY INFANT HIV DIAGNOSIS (DNA-PCR) RESULT FORM</h5>
				</td>
			</tr>
			<tr>
				<td colspan="5">
					<strong> Batch No.: {{ $batch->id }} &nbsp;&nbsp; {{ $batch->facility->name ?? '' }} </strong> 
				</td>
				<td colspan="4">
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

		<br />

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
				<th colspan="18" style="text-align: center;"><b>SAMPLE LOG </b></td>
			</tr>
			<tr>
				<th colspan="7" style="text-align: center;"><b> Patient Information</b></td>
				<th colspan="4" style="text-align: center;"><b>Mother Information</b></td>
				<th colspan="7" style="text-align: center;"><b>Samples Information</b></td>
				<!-- <td colspan="4"><b>Lab Information</b></td> -->
			</tr>
			<tr>
				<th><b> No</b></td>
				<th><b> Patient ID</b></td>
				<th><b> DOB & Age(M)</b></td>
				<th><b> Sex</b></td>
				<th><b> Entry Point</b></td>
				<th><b> Prophylaxis</b></td>
				<th><b> Feeding</b></td>

				<th><b> Age</b></td>
				<th><b> CCC No</b></td>
				<th><b> Regimen</b></td>
				<th><b> Last Vl</b></td>

				<th><b> Test Type</b></td>
				<th><b> Date Collected</b></td>
				<th><b> Date Received</b></td>
				<th><b> Date Tested</b></td>
				<th><b> Date Dispatched</b></td>
				<th><b> Test Result</b></td>
				<th><b> TAT</b></td>
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
					<td>{{ $sample->patient->patient }} </td>
					{{--<td>{{ $sample->patient->my_date_format('dob') }} </td>--}}
					<td>{{ $sample->patient->my_date_format('dob') }} ({{ $sample->age }}) </td>
					<td>{{ $sample->patient->gender }} </td>
	                <td>
						@foreach($entry_points as $entry_point)
	                        @if($sample->patient->entry_point == $entry_point->id)
	                            {{ $entry_point->name }}
	                        @endif
						@endforeach
	                </td>
					<td>{{ $sample->regimen }} </td>
					<td>
	                    @foreach($feedings as $feeding)
	                        @if($sample->feeding == $feeding->id)
	                            {{ $feeding->feeding }}
	                        @endif
	                    @endforeach		
	                </td>


					<td>{{ $sample->mother_age }} </td>
					<td>{{ $sample->patient->mother->ccc_no }} </td>
					<td>{{ $sample->mother_prophylaxis }} </td>
					<td>{{ $sample->mother_last_result }} </td>


					<td>
	                    @foreach($pcrtypes as $pcrtype)
	                        @if($sample->pcrtype == $pcrtype->id)
	                            {!! $pcrtype->alias !!}
	                        @endif
	                    @endforeach	

						@if($sample->redraw) 
							(redraw) 
						@endif 
					</td>
					<td>{{ $sample->my_date_format('datecollected') }} </td>
					<td>{{ $batch->my_date_format('datereceived') }} </td>
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

		@isset($rejection)
			<table>
				<tr>
					<td colspan="10" style="text-align: center;"><b> REJECTED SAMPLE(s)</b></td>
				</tr>
				<tr>
					<th><b> No</b></td>
					<th><b> Patient ID</b></td>
					<th><b> Sex</b></td>
					<th><b> DOB & Age(M)</b></td>
					<th><b> Prophylaxis</b></td>
					<th><b> Status</b></td>
					<th><b> Rejected Reason</b></td>
					<th><b> Date Collected</b></td>
					<th><b> Date Received</b></td>
					<th><b> Date Dispatched</b></td>			
				</tr>

				@foreach($batch->sample as $key => $sample)
					@continue($sample->receivedstatus != 2)
					<tr>
						<td>{{ ($key+1) }} </td>
						<td>{{ $sample->patient->patient }} </td>
						<td>{{ $sample->patient->gender }} </td>
						<td>{{ $sample->patient->my_date_format('dob') }} ({{ $sample->age }}) </td>
						<td>{{ $sample->regimen }} </td>
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

		<br />
		<br />

		<p>
			NOTE: Always provide the facility's up-to-date email address(es) and mobile number(s) on the sample requisition form so as to get alerts on the status of your samples.
			<br />
			To Access & Download your current and past results go to : http://nascop.org
		</p>

		

		<div>
			@if($batch->site_entry != 2)
				<table style="display: inline-block;">
					<th><b>LAB CONTACTS </b> </td>
					<tr><td><b> {{ $batch->lab->labname }} </b></td></tr>
					<tr><td>{{ $batch->lab->name }} </td></tr>
					<tr><td>{{ $batch->lab->lablocation }} </td></tr>
					<tr><td>{{ $batch->lab->labtel1 }} </td></tr>
					<tr><td>{{ $batch->lab->labtel2 }} </td></tr>
					<tr><td>{{ $batch->lab->email }} </td></tr>
				</table>
			@endif
				
			<table style="display: inline-block;">
				<tr><b>KEY/CODES</b></tr>
				<tr>
					<th><b>Entry Point </b> </td>
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
					<th><b>Infant Prophylaxis </b> </td>
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
					<th><b>Infant Feeding </b> </td>
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
					<th><b>PMTCT Intervention </b> </td>
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
			
			
		</div>

		@if($loop->last)
			@break
		@else
			<pagebreak sheet-size='A4-L'>
		@endif		

	@endforeach




</body>
</html>

