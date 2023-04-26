<div class="table-responsive">
	<table  class="table table-striped table-bordered table-hover data-table">
		<thead>
			<tr>
				<th>#</th>
				@if($machine_type == 0)
					<th>Select (to enter worksheet)</th>
				@endif
				<th>Lab ID</th>
				@if(env('APP_LAB') == 1)
					<th>KEMRI ID</th>
				@elseif(env('APP_LAB') == 25)
					<th>AMREF ID</th>
				@endif
				<th>Type</th>
				<th>Patient</th>
				<th>Facility</th>
				<th>Run</th>
				<th>Original ID</th>
				<th>Date Collected</th>
				<th>Release as Redraw</th>
				<th>Update</th>
				@if($machine_type != 0)
					<th>Delete</th>
				@endif
			</tr>
		</thead>
		<tbody>
			@foreach($samples as $key => $sample)
				<tr>
					<td> {{ ($key+1) }} </td>
					@if($machine_type == 0)
						<td>
                            <div align='center'>
                                <input name='samples[]' type='checkbox' class='checks' value='{{ $sample->id }}' checked="checked" />
                            </div>
                        </td>
					@endif
					<td> {{ $sample->id }} </td>
					@if(in_array(env('APP_LAB'), [1,25]))
						<td> {{ $sample->kemri_id }} </td>
					@endif
					<td>
						@if($sample->route_name == 'covid_sample')
							Covid-19
						@elseif($sample->route_name == 'viralsample')
							Viralload
						@elseif($sample->route_name == 'sample')
							EID
						@endif
					</td>
					<td> {{ $sample->identifier ?? $sample->patient }} </td>
					<td> {{ $sample->facilityname }} </td>
					<td> {{ $sample->run }} </td>
					
					<td>
						@if($sample->parentid) 
							{{ $sample->parentid }} 
						@endif
					</td>
					
					<td> {{ $sample->my_date_format('datecollected') }} </td>
	                <td> <a href="{{ url($sample->route_name . '/release/' . $sample->id) }}" class="confirmAction"> Release</a> </td>
	                <td> {!! $sample->edit_link !!} </td>
					@if($machine_type != 0)
		                <td> {!! $sample->delete_form !!} </td>
					@endif
				</tr>
			@endforeach

		</tbody>

	</table>
</div>