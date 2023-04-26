<html>
<link rel="stylesheet" type="text/css" href="{{ asset('css/worksheet_style.css') }}" media="screen" />
<style type="text/css">
	body {font-family: "Courier New", Courier, monospace; font-size: 12; }
	table {border: 1px solid;}
</style> 
@php
	$print = true;
@endphp
<body @isset($print) onLoad="JavaScript:window.print();" @endisset>
	<a href="{{ url('cd4/worksheet') }}">Back To Worksheets</a>
	<div>
		<center>
			<table border="1">
				<tr>
					<th rowspan="2"><br>Worksheet No</th>
					<td rowspan="2"><br>{{ $worksheet->id }}</td>
					<th>Created By</th>
					<td>{{ $worksheet->creator->full_name }}</td>
					<th>Tru Count Lot #</th>
					<td>{{ $worksheet->TruCountLotno }}</td>
					<th>Multicheck Normal Lot #	</th>
					<td>{{ $worksheet->MulticheckNormalLotno }}</td>
				</tr>
				<tr>
					<th>Date Created</th>
					<td>{{ date('d-M-Y', strtotime($worksheet->created_at)) }}</td>
					<th>Antibody Lot #</th>
					<td>{{ $worksheet->AntibodyLotno }}</td>
					<th>Multicheck Low Lot #</th>
					<td>{{ $worksheet->MulticheckLowLotno }}</td>
				</tr>
			</table>
		</center>
	</div>
	<div><center><h5>{{ $worksheet->samples->count() }} WORKSHEET SAMPLES [2 Controls]</h5></center></div>
	<div>
		<center>
			<table>
				<thead>
					<tr> 
		               <th> SR.No</th>
		               <th> Acc.No </th>
		               <th> Acc.No Bar Code</th>
		               <th> Ampath No </th>
		               <th> Study No </th>
		               <th> Patient Names </th>
		               <th> Received Dt. </th>
		               <th> Reg Dt. </th>
		               <th> Sampl Dt. </th>
		               <th> Tests </th>
		            </tr>
				</thead>
				<tbody>
				@forelse($worksheet->samples as $key => $sample)
		            <tr>
		                <td>{{ $sample->serial_no ?? '' }}</td>
		                <td>{{ $sample->id ?? '' }}</td>
		                <td>
		                	<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($sample->id, 'C128') }}" alt="barcode" height="30" width="80"  />
		                </td>
		                <td>{{ $sample->medicalrecordno ?? '' }}</td>
		                <td>{{ __(' ') }}</td>
		                <td>
		                	{{ $sample->patient_name ?? '' }} 
		                	/ {{ $sample->age ?? '' }} 
		                	/ {{ $sample->gender ?? '' }}
		                </td>
		                <td>
		                    @if($sample->datereceived) 
		                        {{ date('d-M-Y', strtotime($sample->datereceived)) }} 
		                    @endif
		                </td>
		                <td>{{ date('d-M-Y', strtotime($sample->created_at)) }}</td>
		                <td>
		                    @if($sample->datetested) 
		                        {{ date('d-M-Y', strtotime($sample->datetested)) }} 
		                    @endif
		                </td>
		                <td>{{ __('CD3/CD4') }}</td>
		            </tr>
		        @empty
		            <tr>
		                <td>No Samples available yet</td>
		            </tr>
		        @endforelse
				</tbody>
			</table>
		</center>
	</div>
</body>
</html>