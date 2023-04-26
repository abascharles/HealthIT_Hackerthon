<table  class="table table-striped table-bordered table-hover data-table">
	<thead>
		<tr>
			<th>#</th>
			<th>Patient</th>
			<th>Facility</th>
			<th>Spots</th>
			<th>Run</th>
			<th>Date Collected</th>

		</tr>
	</thead>
	<tbody>
		@foreach($samples as $key => $sample)
			<tr>
				<td>{{ ($key+1) }} </td>
				<td> {{ $sample->patient }} </td>
				<td> {{ $sample->name }} </td>
				<td> {{ $sample->spots }} </td>
				<td> {{ $sample->run }} </td>
				<td> {{ $sample->datecollected }} </td>
			</tr>
		@endforeach

	</tbody>

</table>