
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
<body onLoad="JavaScript:window.print();">
	<div align="center">
		<table>
			<tr>
				<td><strong>HIV	LAB EARLY INFANT DIAGNOSIS <br/> {{ $machine_name ?? '' }} </strong></td>
			</tr>
		</table>
	</div>

	@if($worksheet->machine_type == 1)
		@include('worksheets.other-table')
	@else
		@include('worksheets.abbot-table')
	@endif


</body>
</html>