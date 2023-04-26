<html>
<link rel="stylesheet" type="text/css" href="{{ asset('css/worksheet_style.css') }}" media="screen" />
<style type="text/css">
<!--
.style1 {font-family: "Courier New", Courier, monospace}
.style4 {font-size: 12}
.style5 {font-family: "Courier New", Courier, monospace; font-size: 12; }
.style7 {font-size: x-small}
-->
</style>
<style>

 td
 {

 }
 .oddrow
 {
 background-color : #CCCCCC;
 }
 .evenrow
 {
 background-color : #F0F0F0;
 } #table1 {
border : solid 1px black;
width:1100px;
width:1180px;
}
 .style7 {font-size: medium}
.style10 {font-size: 16px}
</style>

<STYLE TYPE="text/css">
     P.breakhere {page-break-before: always}

}

</STYLE> 
<body
	@isset($print)
		onLoad="JavaScript:window.print();"
	@endisset
	>
	<div align="center">

		<table>
			<tr>
				<td>Worksheet No</td>
				<td>{{ $worklist->id }} </td>
				<td>Date Created</td>
				<td>{{ $worklist->my_date_format('created_at') }} </td>
			</tr>
		</table>

		<table border="0" class="data-table">
			<thead>
				<tr>
					<th>Lab ID</th>
					<th>Lab ID Barcode</th>
					<th>Sample ID</th>
					<th>Facility</th>
					<th>Date Collected</th>
					<th>Assay</th>
				</tr>
			</thead>
			<tbody>
				@foreach($samples as $sample)
					<tr>
						<td>{{ $sample->id }} </td>
						<td>
							<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($sample->id, 'C39+') }}" alt="barcode" height="30" width="100"  />
						</td>
						<td>{{ $sample->patient }} </td>
						<td>{{ $sample->facility->name }} </td>
						<td>{{ $sample->my_date_format('datecollected') }} </td>
						<td>{{ $worklist->type }} </td>
					</tr>

				@endforeach
			</tbody>
		</table>

	</div>
</body>
</html>