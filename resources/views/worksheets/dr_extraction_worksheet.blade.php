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
 /*.style7 {font-size: medium}*/
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
		<table border="0" class="data-table">
			<tr class="odd">
				<td colspan="8">
					<center>
						WORKSHEET {{ $worksheet->id }}				
					</center>	
				</td>			
			</tr>
			<tr >
				<th colspan="8" ><small> <strong> EXTRACTION WORKSHEET SAMPLES [2 Controls] </th>
			</tr>
		</table>
		<table border="0" class="data-table">
			<tr>
				@foreach($samples as $sample)

					<td>
						@if($sample->control == 1)
							Negative Control <br />
						@elseif($sample->control == 2)
							Positive Control <br />
						@else
							CCC - {{ $sample->patient->patient ?? '' }} <br />
							Nat - {{ $sample->patient->nat ?? '' }} <br />
							Date Received - {{ $sample->my_date_format('datereceived') }} <br />
						@endif

						<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG('00000' . $sample->id, 'C128') }}" alt="barcode" height="30" width="80"  />
						<br /> {{ $sample->id }}
					</td>

					@php $count++; @endphp

					@if($count % 8 == 0)
						</tr><tr><td colspan=8>&nbsp;</td></tr><tr>
					@endif

				@endforeach

			</tr>				
		</table>
	</div>
</body>
</html>