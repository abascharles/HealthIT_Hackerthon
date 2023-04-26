<html>

<head>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.css') }}" />	
</head>


<body >

	<div class="container">

		<div class="row" style="text-align: center;">
			@if(env('APP_LAB') == 7)
				<img src="{{ public_path('img/NHRL_LOGO.png') }}" alt="NHRL">
				<br />
				<!-- <h5> National Public Health Laborotories </h5>
				<h5> National HIV Reference Laboratory </h5>
				<h5> HIV DR Testing Section | Drug Resistance and Molecular Surveillance Team </h5> -->
			@else
				<img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP">
				<br />
				<b>
					MINISTRY OF HEALTH <br />
					NATIONAL AIDS AND STD CONTROL PROGRAM (NASCOP)<br />
				</b>
			@endif

			<h2>HIV-1 Drug Resistance Genotype Report</h2>
			<br />
			<br />
		</div>

		{{--<div class="row">
			<table class="table">
				<tr>			
					<td><b>Specimen ID:</b> {{ $sample->patient->patient ?? '' }} </td>
					<td><b>Collection Date:</b> {{ $sample->my_date_format('datecollected') }} </td>
					<td><b>Sample Type:</b> {{ $sample->sample_type_output }} </td>					
				</tr>
				<tr>
					<td><b>NHRL ID:</b> {{ $sample->mid }} </td>
					<td><b>Received Date:</b> {{ $sample->my_date_format('datereceived') }} </td>
					<td><b>Report Date:</b> {{ date('d-M-Y') }} </td>					
				</tr>
				<tr>
					<td><b>Tested By:</b> {{ $sample->worksheet->creator->full_name ?? '' }} </td>
					<td><b></b> </td>
					<td><b>Report ID:</b> </td>					
				</tr>
			</table>			
		</div>--}}

		<div>
			<table class="table">
				<thead>
					<tr>
						<td> Patient Details </td>
						<td> Test Details </td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td> Name: {{ $sample->patient->patient_name ?? '' }} </td>
						<td> Date of Collection: {{ $sample->my_date_format('datecollected') }} </td>
					</tr>
					<tr>
						<td> Patient CCC: {{ $sample->patient->patient ?? '' }} </td>
						<td>  </td>
					</tr>
					<tr>
						<td> NASCOP No. NAT: {{ $sample->patient->nat }} </td>
						<td> Requesting Clinician: {{ $sample->clinician_name }} </td>
					</tr>
					<tr>
						<td> Date of Birth: {{ $sample->patient->my_date_format('dob') }} </td>
						<td>  </td>
					</tr>
					<tr>
						<td> Gender: {{ $sample->patient->gender ?? '' }} </td>
						<td> Report Date: {{ $sample->my_date_format('created_at') }} </td>
					</tr>
				</tbody>
			</table>			
		</div>

		<br />
		<br />

		<div class="row">
			@if($sample->receivedstatus == 2)
				<p>
					Sample Unfit For Testing For the following reason: <br />
					{{ $dr_rejected_reasons->where('id', $sample->id)->first()->name ?? '' }}
				</p>
			@elseif($sample->collect_new_sample)
				<p>
					Sample Test Has Failed. Please collect a new sample.
				</p>
			@elseif($sample->passed_gel_documentation == 0)
				<p>
					<b>VL Results</b> &nbsp;&nbsp;&nbsp;&nbsp; {{ $sample->vl_sample->result ?? '' }}, The copies are below the sensitivity limit of the DR test assay
				</p>
			@else
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th>Drug Class</th>
							<th>Detected Mutations</th>
							<th>Drug</th>
							<th>Susceptability</th>
							<th>Susceptability Code</th>
						</tr>
					</thead>
					<tbody>
						@foreach($sample->dr_call as $dr_call)
							@foreach($dr_call->call_drug as $key => $call_drug)
								<tr>
								@if ($key)
									<td></td>
								@else
									<td>{{ $dr_call->drug_class }}  </td>
								@endif
									<td>{{ $dr_call->mutations[$key] ?? '' }}  </td>
									<td>{{ $call_drug->short_name }} </td>
									<td>{{ $call_drug->resistance }} </td>
									{!! $call_drug->resistance_cell !!}
								<tr/>	
							@endforeach

						@endforeach
				</table>

				<br />

				<h4><b> Notes on Susceptability </b> </h4>

				<table class="table table-bordered">
					<tr>
						<td> Colour </td>
						<td> Degree </td>
						<td> Explanation </td>
					</tr>
					<tr>
						<td style="background-color:{{ $resistance_colours['R']['resistance_colour'] }};" bgcolor="{{ $resistance_colours['R']['resistance_colour'] }}"> </td>
						<td>High-Level </td>
						<td>Mutations detected constitute a high level of genetic evidence for viral resistance.</td>
					</tr>	
					<tr>
						<td style="background-color:{{ $resistance_colours['I']['resistance_colour'] }};" bgcolor="{{ $resistance_colours['I']['resistance_colour'] }}"> </td>
						<td>Intermediate </td>
						<td>Mutations detected constitute an intermediate level of genetic evidence for viral resistance.</td>
					</tr>			
					<tr>
						<td style="background-color:{{ $resistance_colours['S']['resistance_colour'] }};" bgcolor="{{ $resistance_colours['S']['resistance_colour'] }}"> </td>
						<td>Susceptible </td>
						<td>Insufficient evidence for viral resistance.</td>
					</tr>			
					<tr>
						<td style="background-color:{{ $resistance_colours['LC']['resistance_colour'] }};" bgcolor="{{ $resistance_colours['LC']['resistance_colour'] }}"> </td>
						<td>Low Coverage </td>
						<td>Inconclusive.</td>
					</tr>							
				</table>

				<p>
					* Assessment of drug susceptability is based upon detected mutations and interpreted using the Stanford Genotypic Resistance Interpretation Algorithm <br />
					* The protease inhibitor (PI) interpretations estimate the expected virological response to standard doses of protease inhibitors with pharmacokinetic boosting by ritonavir. Boosted PIs are more active in the presence of resistance than non-boosted PIs.
				</p>

			@endif
			
		</div>	



		<div class="row">
			<table class="table">
				<tr>
					<td> <b>Tested By:</b> {{ $sample->worksheet->runner->full_name ?? '' }} </td>
					<td> <b>Sign:</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
					<td> <b>Date:</b> {{ $sample->my_date_format('datetested') }} </td>
				</tr>
				<tr>
					<td> <b>Verified By:</b> {{ $sample->approver->full_name ?? '' }} </td>
					<td> <b>Sign:</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
					<td> <b>Date:</b> {{ $sample->my_date_format('dateapproved') }} </td>
				</tr>							
			</table>
		</div>	
	</div>

</body>

<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>

</html>