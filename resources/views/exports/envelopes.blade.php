
<html>
<style type="text/css">
<!--
.style4 {font-family: "Arial Bold"; font-size: 18; }
.style5 {font-family: "Arial Bold"; font-size: 14; }
.style4 {font-family: "Arial Bold"; font-size: 18; }
.style8 {font-family: "Courier New", Courier, monospace; font-size: 11; }
.style4 {
	
	font-weight: bold;
}
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
border : solid 0px black;


}#table2 {
border : solid 1px black;
margin-top: 30px;

width:1200px;
}#table4 {
border : solid 0px black;
margin-top: 0px;


}
 .style7 {font-size: medium}
.style10 {font-size: 16px}
p.breakhere {page-break-before: always}
</style>


<br/>
<br/>
<br/>
<br/>
<br/>


<body onLoad="JavaScript:window.print();">

	<?php

		$labss = env('APP_LAB');

		if ($labss ==1 ) // nairobi
		{
			$labaddress="KEMRI HIV-P3 Lab";
			$labtelno="0725793260 / 0725796842";
		}
		elseif  ( $labss ==2  ) //kisumu
		{
			$labaddress="CDC HIV/R Lab";
			$labtelno="057 2053017/8  / 0722204614";
		}
		elseif  ( $labss ==3  ) //busia
		{
			$labaddress="KEMRI CIPDCR -Alupe ";
			$labtelno="(055) 22410; 0726 156679";

		}
		elseif ( $labss ==4  ) //kericho
		{
			$labaddress="Walter Reed CRC Lab";
			$labtelno="052 30388/21064";
		}
		elseif ( $labss ==5  ) //ampath
		{
			$labaddress="AMPATH LAB, AMPATH";
			$labtelno="";
		}
		elseif ( $labss ==6  ) //cpgh
		{
			$labaddress="CPGH Molecular Lab";
			$labtelno="0722207868 Ext. Lab";
		}

		if(in_array($labss, [1, 3])) $envelope_logo = 'img/envelope_logos/kemri_nairobi_and_busia.jpg';
		else if(in_array($labss, [5, 6, 9])) $envelope_logo = 'img/envelope_logos/ampath_cpgh_knh.jpg';
		else if(in_array($labss, [2])) $envelope_logo = 'img/envelope_logos/cdc_kisumu.jpg';
		else if(in_array($labss, [5])) $envelope_logo = 'img/envelope_logos/walter_reed.jpg';
		else $envelope_logo = '';

	?>

@foreach($batches as $batch)

<table border="0" id='table2' align="left" >

	<tr>
		<td>
			<table border="0" id='table1' >
				<tr>
					<td class="style4 style1 comment" colspan="8" ><strong> EARLY INFANT DIAGNOSIS PROGRAM</strong></td>
				</tr>

				<tr>
					<td class="style4 style1 comment" ><strong>{{ $batch->lab->name }} </strong></td>
					{{-- <td rowspan="4" ><img src="{{ asset(env('ENVELOPE_LOGO') ) }} " alt="" width="60" height="60" /></td> --}}
					<td rowspan="4" ><img src="{{ asset($envelope_logo) }} " alt="" width="60" height="60" /></td>
				</tr>

				<tr></tr>

				<tr>
					<td  class="style4 style1 comment" ><strong> {{ $labaddress ?? null }} </strong></td>	
				</tr>
				<tr>
					<td  class="style4 style1 comment" ><strong>&nbsp; </strong></td>
				</tr>
				<tr>
					<td  class="style4 style1 comment" ><strong> TEL NO: {{ $labtelno ?? null }}</strong> </td></td>
				</tr>
			</table>
		</td>
		<td>
			<table border="0" id='table1'>
				<tr>
					<td  class="style5 style1 comment" ><strong> ACCOUNT # C00339</strong></td>
				</tr>
				<tr>
					<td  class="style5 style1 comment" ><strong> &nbsp; </strong></td>
				</tr>
				<tr>
					<td  class="style5 style1 comment" >BATCH #: <strong> {{ $batch->id }} </strong></td>
				</tr>
			</table>
		</td>		
	</tr>
	<tr>
		<td colspan="10" class="style4 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr>
		<td colspan="10" class="style4 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr>
		<td colspan="10" class="style4 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr>
		<td colspan="10" class="style4 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>

	@if($batch->view_facility->province_id != 5 && $batch->facility->branch_location && $batch->facility->branch_phones)
		<tr>
			<td colspan="10" class="style4 style1 comment" >
				<strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TO:</strong>
			</td>
		</tr>
		<tr>
			<td colspan="10" class="style4 style1 comment" >
				<strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				{{ $batch->facility->branch_location }}
				</strong>
			</td>
		</tr>
		<tr>
			<td colspan="10" class="style4 style1 comment" >
				<strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				{{ $batch->facility->branch_phones }}
				</strong>
			</td>
		</tr>

	@endif

	<tr>
		<td colspan="10" class="style4 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>

	<tr>
		<td  class="style4 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>
	<tr>
		<td>
			<table border="0">
				<tr>
		
					<td  class="style5 style1 comment" colspan="1" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;FOR:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
					<td  class="style5 style1 comment" ><strong> {{ $batch->facility->name }} </strong></td>
				</tr>

	
				<tr>
					<td  class="style5 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
					<td  class="style5 style1 comment" ><strong> {{ $batch->facility->PostalAddress }} </strong></td>
				</tr>

				@if($batch->facility->facility_contacts)

					<tr>
						<td  class="style5 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
						<td  class="style5 style1 comment" ><strong> {{ $batch->facility->facility_contacts }}</strong></td>
					</tr>
				@endif


				<tr>
					<td  class="style5 style1 comment" ><strong> &nbsp;</strong></td>
					<td  class="style5 style1 comment" ><strong> {{ $batch->view_facility->subcounty }}</strong></td>
				</tr>


				<tr>
					<td  class="style5 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				</tr>

				<tr>
					<td  class="style5 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				</tr>

				<tr>
					<td  class="style5 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				</tr>

				<tr>
					<td  class="style5 style1 comment" colspan="1" >
						<strong> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ATTENTION:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
					</td>
					<td  class="style5 style1 comment" ><strong> {{ strtoupper($batch->facility->contactperson) }} </strong></td>
				</tr>

				<tr>
					<td  class="style5 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>

					<td  class="style5 style1 comment" ><strong> {{ $batch->facility->contacts }} </strong></td>
				</tr>

				<tr>
					<td  class="style4 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
				</tr>

				<tr>
					<td  class="style4 style1 comment" colspan="2" >
						<strong> &nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  USE  G4S  ACCOUNT # C00339&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong>
					</td>
				</tr>

			</table>
		</td>
	</tr>
	<tr>
		<td  class="style4 style1 comment" ><strong> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></td>
	</tr>

</table>

@if (!$loop->last)
	<p class="breakhere"></p>
@endif

@endforeach

</body>
</html>