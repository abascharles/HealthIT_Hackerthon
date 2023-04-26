<div class="table-responsive">
	<table  class="table table-striped table-bordered table-hover data-table">
		<thead>
			<tr>
				<th>#</th>
				@if(env('APP_LAB') == 8)
					<th>Select (to enter worksheet)</th>
				@endif
				<th>Lab ID</th>
				<th>Patient</th>
				<th>Facility</th>
				<th>Entry Type</th>
				<th>Sample Type</th>
				<th>Run</th>
				<th>Previous Runs</th>
				<th>Original ID</th>
				<th>Date Collected</th>
				<th>Entered By</th>
				<th>Release as Redraw</th>
				<th>Update</th>
				@if(env('APP_LAB') != 8)
					<th>Delete</th>
				@endif
			</tr>
		</thead>
		<tbody>
			@foreach($samples as $key => $sample)
				<tr>
					<td> {{ ($key+1) }} </td>
					@if(env('APP_LAB') == 8)
						<td>
                            <div align='center'>
                                <input name='samples[]' type='checkbox' class='checks' value='{{ $sample->id }}' checked="checked" />
                            </div>
                        </td>
					@endif
					<td> {{ $sample->id }} </td>
					<td> {{ $sample->patient }} </td>
					<td> {{ $sample->name }} </td>
					@if($sample->site_entry == 0)
						<td> Lab Entry </td>
					@elseif($sample->site_entry == 1)
						<td> Site Entry </td>
					@endif

					<td> {{ $sample->sample_type_output }} </td>
					<td> {{ $sample->run }} </td>
					<td><a href="{{ url('viralsample/runs/' . $sample->id) }}" target="_blank"> Runs</a> </td>
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

	                <td> <a href="{{ url('viralsample/release/' . $sample->id) }}" class="confirmAction"> Release</a> </td>
	                <td> <a href="{{ url('viralsample/' . $sample->id . '/edit') }}"> Edit</a> </td>
					@if(env('APP_LAB') != 8)
		                <td> 
	                        <form action="{{ url('viralsample/' . $sample->id) }}" method="POST" onSubmit="return confirm('Are you sure you want to delete the following sample?');">
	                            @csrf
	                            @method('DELETE')
		                        <button type="submit" class="btn btn-xs btn-primary">Delete</button>
		                    </form>
		                </td>
					@endif
				</tr>
			@endforeach
		</tbody>
	</table>
</div>