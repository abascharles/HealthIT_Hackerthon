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
						@if($worksheet->machine_type == 4)
							PANTHER
						@else
							ABBOTT M2000 SPRT TEMPLATE	
						@endif

						@if($worksheet->cdcworksheetno)
							({{ $worksheet->cdcworksheetno }})
						@endif					
					</center>	
				</td>			
			</tr>
			@if(get_class($worksheet) == "App\Viralworksheet")
				<tr class="odd">
					<td colspan="8">
						<center>
							 [{{ $worksheet->sample_type_name }}]
							 <br />
							 						
						</center>						
					</td>					
				</tr>
			@endif
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
				<td > {{ $worksheet->created_at }} </td>
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
				<td> {{ $worksheet->runner->full_name ?? '' }} </td>
			</tr>
			<tr >
				<th colspan="8" ><small> <strong> WORKSHEET SAMPLES
					@php

						if($worksheet->route_name == 'viralworksheet'){
							echo "[3 Controls]";
							$vl = true;
						}
						else if($worksheet->route_name == 'covid_worksheet' && $worksheet->machine_type == 2 && env('APP_LAB') == 2){
							echo "[4 Controls]";
							$vl = false;		
							$four_controls = true;						
						}
						else{
							echo "[2 Controls]";
							$vl = false;						
						}

					@endphp</strong></small>		</th>
			</tr>
		</table>
		<table border="0" class="data-table">

			<tr>
				<?php 
					$count = 0;
					if($vl){
						echo "<td align='center' > NC </td><td align='center' > LPC </td><td  align='center' > HPC </td>";
						$count += 3; 
						if($worksheet->calibration){
							echo "
								<td align='center' > Cal A </td> 
								<td align='center' > Cal A </td> 
								<td align='center' > Cal A </td> 
								<td align='center' > Cal A </td> 
								<td align='center' > Cal B </td>
							 </tr>
							 <tr>
								<td align='center' > Cal B </td>
								<td align='center' > Cal B </td>
								<td align='center' > Cal B </td>							 
							  ";
						}
					}
					else if(isset($four_controls)){
						echo "<td align='center' > PC </td><td  align='center' > NC </td><td  align='center' > in house PC </td><td  align='center' > in house NC  </td>";
						$count += 4; 						
					}
					else{
						echo "<td align='center' > PC </td><td  align='center' > NC </td>";
						$count += 2; 
					}
				?>


				@foreach($samples as $sample)

					@include('shared/worksheet_sample', ['sample' => $sample, 'i' => ++$i])

					@php $count++; @endphp

					@if($count % 8 == 0)
						</tr><tr><td colspan=8>&nbsp;</td></tr><tr>
					@endif

				@endforeach

				{{--@if($vl)
					<td align='center' > LPC </td><td align='center' > HPC </td><td  align='center' > NC </td>
				@else
					<td align='center' > PC </td><td  align='center' > NC </td>
				@endif--}}
			</tr>

			
			{{--@if($worksheet->calibration)
				<tr>
					<td align='center' > Cal A </td>
					<td align='center' > Cal A </td>
					<td align='center' > Cal A </td>
					<td align='center' > Cal A </td>
					<td align='center' > Cal B </td>
					<td align='center' > Cal B </td>
					<td align='center' > Cal B </td>
					<td align='center' > Cal B </td>
				</tr>

			@endif--}}
				
		</table>
	</div>
</body>
</html>