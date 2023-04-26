<div class="row">
    <div class="col-lg-12">
        <div class="hpanel hgreen">
            <div class="panel-heading">{{ date('d M, Y', strtotime($log->date)) }}</div>
            <div class="panel-body">
            	<table class="table table-striped table-bordered table-hover" >
            		<thead>
            			<tr>
            				<th>Samples Logged/Approved</th>
                            <th>Worksheets Sorted</th>
                            <th>Worksheets Aliquoted</th>
                            <th>Worksheets Run</th>
                            <th>Samples Dispatched</th>
            			</tr>
            		</thead>
            		<tbody>
            			<tr>
            				<td>
	                            {{ $log->samples_logged }}
	                            &nbsp;/&nbsp;
	                            {{ $log->samples_approved }}
	                        </td>
	                        <td>{{ $log->worksheets_sorted }}</td>
	                        <td>{{ $log->worksheets_aliquoted }}</td>
	                        <td>{{ $log->worksheets_run }}</td>
	                        <td>{{ $log->samples_dispatched }}</td>
            			</tr>
            		</tbody>
            	</table>
            </div>
        </div>            
    </div>
</div>