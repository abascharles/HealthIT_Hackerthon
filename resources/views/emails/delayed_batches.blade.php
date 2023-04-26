<p>
	The following {{ $type }} batches have not been received and are in danger of being automatically rejected for being delayed:
</p>

<br />
<br />

<table>
	<thead>
		<tr>
			<th>Batch No</th>
			<th>MFL</th>
			<th>Facility</th>
			<th>Facility Email</th>
			<th>Date Created</th>
		</tr>
	</thead>
	<tbody>
		@foreach($batches as $batch)
			<tr>
				<td> {{ $batch->id }} </td>
				<td> {{ $batch->facility->facilitycode }} </td>
				<td> {{ $batch->facility->name }} </td>
				<td> {{ $batch->facility->email_string }} </td>
				<td> {{ $batch->created_at->toDayDateTimeString() }} </td>
			</tr>
		@endforeach
	</tbody>
</table>

<br />

