<table  class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Negative</th>
			<th>Positive</th>
			<th>Failed</th>
			<th>Redraw</th>
			<th>No Result</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td> {{ $subtotals['neg'] }} </td>
			<td> {{ $subtotals['pos'] }} </td>
			<td> {{ $subtotals['failed'] }} </td>
			<td> {{ $subtotals['redraw'] }} </td>
			<td> {{ $subtotals['noresult'] }} </td>
			<td> {{ $subtotals['total'] }} </td>
		</tr>

	</tbody>

</table>

<br />



<table  class="table table-striped table-bordered table-hover">

	<tr class="odd">
		<td colspan="3"><strong>Worksheet Details</strong>	</td>
		<td colspan="2"><strong>Extraction Reagent</strong>	</td>
		<td colspan="3"><strong>Amplification Reagent</strong></td>
	</tr>
	<tr class="odd">
		
		<td> <strong>Worksheet/Template No</strong> </td>
		<td> {{ $worksheet->id }} </td>
		<td><strong>&nbsp;</strong>	</td>
		<td><strong>Sample Prep</strong>	</td>
		<td><strong>Bulk Lysis Buffer</strong>	</td>
		<td><strong>Control</strong>	</td>
		<td><strong>Calibrator</strong>	</td>
		<td><strong>Amplification Kit</strong>	</td>			
	</tr>
	<tr class="even">
		<td ><strong>Date Created</strong>		</td>
		<td > {{ $worksheet->my_date_format('created_at') }} </td>
		<td><strong>Lot No	</strong>	</td>
		<td> {{ $worksheet->sample_prep_lot_no }} </td>
		<td> {{ $worksheet->bulklysis_lot_no }} </td>
		<td> {{ $worksheet->control_lot_no }} </td>
		<td> {{ $worksheet->calibrator_lot_no }} </td>
		<td> {{ $worksheet->amplification_kit_lot_no }} </td>
	</tr>
	<tr class="even">
		<td><strong>Created By	</strong>    </td>
		<td> {{ $worksheet->creator->full_name ?? '' }} </td>
		<td><strong>Expiry Dates</strong>	</td>

		<td> {{ $worksheet->my_date_format('sampleprepexpirydate') }} </td>
		<td> {{ $worksheet->my_date_format('bulklysisexpirydate') }} </td>
		<td> {{ $worksheet->my_date_format('controlexpirydate') }} </td>
		<td> {{ $worksheet->my_date_format('calibratorexpirydate') }} </td>
		<td> {{ $worksheet->my_date_format('amplificationexpirydate') }} </td>
	</tr>
	<tr class="even">
		<td><strong>Sorted By	</strong>    </td>
		<td> {{ $worksheet->sorter->full_name ?? '' }} </td>
		<td><strong>Bulked By	</strong>    </td>
		<td> {{ $worksheet->bulker->full_name ?? '' }} </td>
		<td><strong>Run By	</strong>    </td>
		<td> {{ $worksheet->runner->full_name ?? '' }}</td>
	</tr>
	<tr class="even">
		<td><strong>Updated By	</strong>    </td>
		<td> {{ $worksheet->uploader->full_name ?? '' }} </td>
		<td><strong>Date Updated	</strong>    </td>
		<td> {{ $worksheet->my_date_format('dateuploaded') }}</td>
		<td><strong>Reviewed By	</strong>    </td>
		<td> {{ $worksheet->reviewer->full_name ?? '' }}</td>
		<td><strong>Date Reviewed	</strong>    </td>
		<td> {{ $worksheet->my_date_format('datereviewed') }}</td>
	</tr>
	
	@if(in_array(env('APP_LAB'), $double_approval))
		<tr class="even">
			<td>    </td>
			<td> </td>
			<td>    </td>
			<td> </td>
			<td><strong> Second Reviewer	</strong>    </td>
			<td> {{ $worksheet->reviewer2->full_name ?? '' }}</td>
			<td><strong>Date of Second Review	</strong>    </td>
			<td> {{ $worksheet->my_date_format('datereviewed2') }}</td>
		</tr>
	@endif

</table>