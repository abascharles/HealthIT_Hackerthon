<div class="table-responsive">
	<table  class="table table-striped table-bordered table-hover data-table">
		<thead>
			<tr>
				<th>#</th>
				<th>Lab ID</th>
				<th>Patient</th>
				<th>Facility</th>
				<th>Entry Type</th>
				<th>Spots</th>
				<th>Run</th>
				<th>Previous Runs</th>
				<th>Original ID</th>
				<th>Date Collected</th>
				<th>Entered By</th>
				<th>Release as Redraw</th>
				<th>Update</th>
				<th>Delete</th>
			</tr>
		</thead>
		<tbody>
			@foreach($samples as $key => $sample)
				<tr>
					<td> {{ ($key+1) }} </td>
					<td> {{ $sample->id }} </td>
					<td> {{ $sample->patient }} </td>
					<td> {{ $sample->name }} </td>
					@if($sample->site_entry == 0)
						<td> Lab Entry </td>
					@elseif($sample->site_entry == 1)
						<td> Site Entry </td>
					@endif
					<td> {{ $sample->spots }} </td>
					<td> {{ $sample->run }} </td>
					<td><a href="{{ url('sample/runs/' . $sample->id) }}" target="_blank"> Runs</a> </td>
					@if($sample->parentid)
						<td> {{ $sample->parentid ?? null }} </td>
					@else
						<td></td>
					@endif
					
					<td> {{ $sample->datecollected }} </td>

					@if($sample->site_entry == 0)
						<td> {{ $sample->surname . ' ' . $sample->oname }} </td>
					@elseif($sample->site_entry == 1)
						<td>  </td>
					@endif


	                <td> <a href="{{ url('sample/release/' . $sample->id) }}" class="confirmAction"> Release</a> </td>
	                <td> <a href="{{ url('sample/' . $sample->id . '/edit') }}"> Edit</a> </td>
	                <td> 
                        <form action="{{ url('sample/' . $sample->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the following sample?');">
                            @csrf
                            @method('DELETE')
	                        <button type="submit" class="btn btn-xs btn-primary">Delete</button>
	                    </form>
	                </td>
				</tr>
			@endforeach

		</tbody>

	</table>
</div>