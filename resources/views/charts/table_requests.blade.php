<table id="{{ $div }}"  cellspacing="1" cellpadding="3" class="tablehead table table-striped table-bordered">
	<thead>
		<tr class="colhead">
			<th>No</th>
			<th>Name</th>
			@if($facility)
				<th>MFL Code</th>
			@endif
			<th>Requests</th>		
		</tr>
	</thead>
	<tbody>
		@foreach($rows as $key => $row)
			<tr>
				<td> {{ $key+1 }} </td>
				<td> {{ $row->name ?? $row->county ?? '' }} </td>
				@if($facility)
					<td> {{ $row->facilitycode ?? '' }} </td>
				@endif
				
				<td> {{ number_format($row->total ) }} </td>
			</tr>
		@endforeach
	</tbody>	
</table>


<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
        
        $('#{{ $div }}').dataTable({
            // dom: "<'row'<'col-sm-4'l><'col-sm-4 text-center'B><'col-sm-4'f>>tp",
            dom: '<"btn"B>lTfgtip',
            responsive: true,
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
            buttons: [
                {extend: 'copy',className: 'btn-sm'},
                {extend: 'csv',title: 'Download', className: 'btn-sm'},
                /*{extend: 'pdf', title: 'Download', className: 'btn-sm'},
                {extend: 'print',className: 'btn-sm'}*/
            ]
        });

	});
</script>
