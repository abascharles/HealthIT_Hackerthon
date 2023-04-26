
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
	<table class="table" border="0" style="border: none; width: 100%;">
		<tr>
			<td colspan="13" align="center">
                <img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP LOGO">
			</td>
		</tr>
		<tr>
			<td colspan="13" align="center">
				{{-- <img src="{{ asset('img/naslogo.jpg') }}" alt="NASCOP">
				<img src="https://eiddash.nascop.org/img/naslogo.jpg" alt="NASCOP">--}}
				HIV {{ strtoupper($testtype) }} SAMPLE MANIFEST {{ $period }}
			</td>
		</tr>		
		<tr>
			<td colspan="4" align="center">Receiving Lab:</td>
			<td colspan="3" align="center">{{ $lab->name ?? ''  }}</td>
			<td colspan="3" align="center">Date Dispatched From Facility</td>
			<td colspan="3" align="center">{{ $samples->first()->datedispatchedfromfacility ?? '' }}</td>
		</tr>
		<tr>
			<td colspan="4" align="center">Hub / Facility Name:</td>
			<td colspan="3" align="center">{{ session('logged_facility')->name ?? $samples->first()->facility ?? ''  }}</td>
			<td colspan="3" align="center">MFL Code:</td>
			<td colspan="3" align="center">{{ session('logged_facility')->facilitycode ?? $samples->first()->facilitycode ?? ''  }}</td>
		</tr>
	</table>
	<br />
	<table class="table" border="0" style="border: 0px;width: 100%;">
		<thead>
			<tr>
				{{-- <th>Lab ID</th> --}}
				<th>HEI # / Patient CCC #</th>
				<th>Batch #</th>
				{{-- <th>County</th> --}}
				{{-- <th>Sub-County</th> --}}
				<th>Facility Name</th>
				<th>Facility Code</th>
				{{-- <th>Gender</th>
				<th>DOB</th> --}}
				<th>
				@if(strtoupper($testtype) == 'EID')
					PCR Type
				@else
					Sample Type
				@endif
				</th>
				{{-- <th>
				@if(strtoupper($testtype) == 'EID')
					Spots
				@else
					Justification
				@endif
				</th> --}}
				<th>Date Collected</th>
				<th>Date Entered</th>
				<th>Entered By</th>
				{{-- <th>Date Dispatched from Facility</th>
				<th>Date Received</th>
				<th>Received By</th>
				<th>Received Status</th> --}}
				{{-- <th>Date Tested</th> --}}
			</tr>
		</thead>
		<tbody>
		@foreach($samples as $sample)
			<tr>
				{{-- <td>{{ $sample->id }}</td> --}}
				<td>{{ $sample->patient }}</td>
				<td>{{ $sample->batch_id }}</td>
				{{-- <td>{{ $sample->county }}</td> --}}
				{{-- <td>{{ $sample->subcounty }}</td> --}}
				<td>{{ $sample->facility }}</td>
				<td>{{ $sample->facilitycode }}</td>
				{{-- <td>{{ $sample->gender_description }}</td>
				<td>{{ $sample->dob }}</td> --}}
				<td>
				@if(strtoupper($testtype) == 'EID')
					{{ $sample->pcrtype }}
				@else
					{{ $sample->sampletype }}
				@endif
				</td>
				{{-- <td>
				@if(strtoupper($testtype) == 'EID')
					{{ $sample->spots }}
				@else
					{{ $sample->justification }}
				@endif
				</td> --}}
				<td>{{ $sample->datecollected }}</td>
				<td>{{ date('Y-m-d', strtotime($sample->created_at)) }}</td>
				<td>{{ $sample->entered_by }}</td>
				{{-- <td>{{ $sample->datedispatchedfromfacility }}</td>
				<td>{{ $sample->datereceived }}</td>
				<td>{{ $sample->receiver }}</td>
				<td>{{ $sample->receivedstatus }}</td> --}}
				{{-- <td>{{ $sample->datetested }}</td> --}}
			</tr>
		@endforeach
		</tbody>
	</table>
	<br>
	<table class="table" border="0" style="border: none; width: 100%;">
		<tr>
			<th></th>
			<th>Name</th>
			<th>Sign</th>
			<th>Date</th>
		</tr>
		<tr>
			<td align="center">Submitting Officer:</td>
			<td align="center" style="width: 20%;"></td>
			<td align="center" style="width: 20%;"></td>
			<td align="center" style="width: 20%;"></td>
		</tr>
		<tr>
			<td align="center">Receiving Officer:</td>
			<td align="center" style="width: 20%;"></td>
			<td align="center" style="width: 20%;"></td>
			<td align="center" style="width: 20%;">{{ $sample->datereceived ?? '' }}</td>
		</tr>
	</table>
</body>
</html>