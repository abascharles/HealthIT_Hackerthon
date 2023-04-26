<p>
	Batch Number {{ $batch->id }} that was created on {{ $batch->my_date_format('created_at') }} has not yet been received at the lab and thus has been deleted. Below are the samples that have been deleted:
</p>

<br />
<br />

<table>
	<thead>
		<tr>
			<th>Patient Identifier</th>
			<th>Gender</th>
			<th>Date Collected</th>
		</tr>
	</thead>
	<tbody>
		@foreach($batch->sample as $sample)
			<tr>
				<td> {{ $sample->patient->patient }} </td>
				<td> {{ $sample->patient->gender }} </td>
				<td> {{ $sample->datecollected }} </td>
			</tr>
		@endforeach
	</tbody>
</table>

<br />

@include('emails.lab_signature') 
