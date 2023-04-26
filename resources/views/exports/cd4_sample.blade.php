<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		body {
			font-family: "Courier New", Courier, monospace;
		}
		table {
			border : solid 1px black;
		} 
		table {
			width:1000px;
		}
	</style>
</head>
<body onLoad="JavaScript:window.print();">
	<div>
		<center><img src="{{ asset('img/ampath-cd4.jpg') }}"></center>
	</div>
	<div>
		<center>
			<table>
				<tr>
					<th>PATIENT'S NAME</th>
					<td>{{ $sample->patient->patient_name ?? '' }}</td>
					<th>PHYSICIAN</th>
					<td></td>
				</tr>
				<tr>
					<th>AGE/SEX</th>
					<td>{{ $sample->patient->age ?? '' }} / {{ $sample->patient->gender ?? '' }}</td>
					<th>BARCODE DATE</th>
					<td></td>
				</tr>
				<tr>
					<th>CLINIC NAME</th>
					<td>{{ $sample->facility->name ?? '' }}</td>
					<th>REG. Date</th>
					<td>
					@isset($sample->created_at)
						{{ date('Y-m-d', strtotime($sample->created_at)) }}
					@endisset
					</td>
				</tr>
				<tr>
					<th>DATE TESTED</th>
					<td>{{ $sample->datetested }}</td>
					<th>SAMPLE DATE</th>
					<td></td>
				</tr>
				<tr>
					<th>AMPATH NO.</th>
					<td>{{ $sample->patient->medicalrecordno }}</td>
					<th>ACC NO.</th>
					<td></td>
				</tr>
				<tr>
					<th>PROVIDER IDENTIFIER</th>
					<td>{{ $sample->provider_identifier }}</td>
				</tr>
			</table>
		</center>
	</div>
	<div>
		<center>
			<table>
				<tr>
					<th>CD4</th>
				</tr>
			</table>
		</center>
	</div>
	<div>
		<center>
			<table>
				<thead>
					<tr>
						<th><center>Investigation</center></th>
						<th><center>Result</center></th>
						<th><center>Units</center></th>
						<th><center>Reference Range</center></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><center>CD3 %</center></td>
						<td><center>{{ $sample->AVGCD3percentLymph }}</center></td>
						<td><center>%</center></td>
						<td><center>55 - 85</center></td>
					</tr>
					<tr>
						<td><center>CD3 abs</center></td>
						<td><center>{{ $sample->AVGCD3AbsCnt }}</center></td>
						<td><center>cells/ul</center></td>
						<td><center>690 - 2540 </center></td>
					</tr>
					<tr>
						<td><center>CD4 %</center></td>
						<td><center>{{ $sample->AVGCD3CD4percentLymph }}</center></td>
						<td><center>%</center></td>
						<td><center>55 - 85</center></td>
					</tr>
					<tr>
						<td><center>CD4 abs</center></td>
						<td><center>{{ $sample->AVGCD3CD4AbsCnt }}</center></td>
						<td><center>cells/ul</center></td>
						<td><center>690 - 2540 </center></td>
					</tr>
					<tr>
						<td><center>Total Lymphocytes</center></td>
						<td><center>{{ $sample->CD45AbsCnt }}</center></td>
						<td><center>cells/ul</center></td>
						<td><center></center></td>
					</tr>
					<tr>
						<td><center>T HELPER/SUPPRESSOR RATIO</center></td>
						<td><center>{{ $sample->THelperSuppressorRatio }}</center></td>
						<td></td>
						<td></td>
					</tr>
				</tbody>
			</table>
		</center>
	</div>
	<div>
		<center>
			<table>
				<tr>
					<th>Tested :</th>
					<td>{{ $sample->first_approver->full_name ?? '' }}</td>
					<th>Date Tested</th>
					<td>
					@isset($sample->datetested)
						{{ date('Y-m-d', strtotime($sample->datetested)) }}
					@endisset
					</td>
				</tr>
				<tr>
					<th>Reviewed By:</th>
					<td>{{ $sample->second_approver->full_name ?? '' }}</td>
					<th>Date Approved:</th>
					<td>
					@isset($sample->dateapproved2)
						{{ date('Y-m-d', strtotime($sample->dateapproved2)) }}
					@endisset
					</td>
				</tr>
				<tr>
					<th>LAB COMMENT(S):</th>
					<td colspan="3">{{ $sample->labcomment ?? '' }}</td>
				</tr>
			</table>
		</center>
	</div>
</body>
</html>