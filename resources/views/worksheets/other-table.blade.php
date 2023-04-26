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
			<tr class="odd">
				<td colspan="8">
					<center>
						TAQMAN		

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
						</center>						
					</td>					
				</tr>
			@endif

		<table border="0" class="data-table">
			<tr >
				<td class="comment style1 style4"> Worksheet No		</td>
				<td class="comment"> <span class="style5"> {{ $worksheet->id }} </span></td>
				<td class="comment style1 style4">Lot No </td>
				<td><span class="comment style1 style4"> {{ $worksheet->lot_no }}78 </span></td>
				<td><span class="style5">Date Cut </span></td>
				<td colspan="2">{{ $worksheet->my_date_format('datecut') }}</td>
			</tr>
			<tr >
				<td class="comment style1 style4"> Date Created		</td>
				<td class="comment" ><span class="style5">{{ $worksheet->my_date_format('created_at') }}</span></td>
				<td class="comment style1 style4">HIQCAP Kit No</td>	
				<td><span class="comment style1 style4">{{ $worksheet->hiqcap_no }}</span></td>	
				<td><span class="style5">Reviewed By  </span></td>
				<td colspan="2">{{ $worksheet->reviewer->full_name ?? '' }}</td>
			</tr>
			<tr >
				<td class="comment style1 style4"> Created By	    </td>
				<td class="comment"  ><span class="style5"> {{ $worksheet->creator->full_name ?? '' }}</span></td>
				<td class="comment style1 style4"> Rack <strong>#</strong></td>
				<td><span class="comment style1 style4">{{ $worksheet->rack_no }}</span></td>
				<td><span class="style5">Date Reviewed</span></td>
				<td colspan="2"> {{ $worksheet->my_date_format('datereviewed') }} </td>
		    </tr>
		    <tr ></tr>
			<tr >
				<td><span class="style5">Spek Kit No</span></td>
				<td  colspan=""> <span class="style5">{{ $worksheet->spekkit_no }}</span> </td>
				<td><span class="style5">KIT EXP </span></td>
				<td><span class="style4">{{ $worksheet->my_date_format('kitexpirydate') }}</span></td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr class="even">
				<td><strong>Sorted By	</strong>    </td>
				<td colspan="2"> {{ $worksheet->sorter->full_name ?? '' }} </td>
						
				<td><strong>Run By	</strong>    </td>
				<td colspan="2"> {{ $worksheet->runner->full_name ?? '' }} </td>
			</tr>
			<tr class="even">
				<td><strong>Aliquoted By	</strong>    </td>
				<td colspan="2"> {{ $worksheet->quoter->full_name ?? '' }} </td>
						
				<td><strong>Archived By	</strong>    </td>
				<td colspan="2"> {{ $worksheet->bulker->full_name ?? '' }} </td>
			</tr>
		</table>
		<table>
					@php
						$class = get_class($worksheet);

						if($class == "App\Viralworksheet"){
							// echo "[3 Controls]";
							$vl = true;
						}
						else{
							// echo "[2 Controls]";
							$vl = false;						
						}

					@endphp

			<tr>
				<?php 
					$count = 0;
					if($vl){
						echo "<td align='center' > HPC </td><td align='center' > LPC </td><td  align='center' > NC </td>";
						$count += 3; 
					}
					else{
						echo "<td align='center' > PC </td><td  align='center' > NC </td>";
						$count += 2; 
					}
				?>


				@foreach($samples as $sample)

					@include('shared/worksheet_sample', ['sample' => $sample, 'i' => ++$i])

					@php $count++; @endphp

					@if($count % 7 == 0)
						</tr><tr><td colspan=8>&nbsp;</td></tr><tr>
					@endif

				@endforeach
			</tr>
				
		</table>

	</div>
</body>
</html>