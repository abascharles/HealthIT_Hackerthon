
<html>
<style type="text/css">
.style1 {font-family: "Courier New", Courier, monospace}
.style4 {font-size: 12}
.style5 {font-family: "Courier New", Courier, monospace; font-size: 12; }
.style8 {font-family: "Courier New", Courier, monospace; font-size: 11; }
.style6 {
	font-size: medium;
	font-weight: bold;
}
</style>
<style>

 td
 {

 }
 /*@page{
 	size: portrait;
 }*/
 .oddrow
 {
 background-color : #CCCCCC;
 }
 .evenrow
 {
 background-color : #F0F0F0;
 } 
#table1 {
border : solid 1px black;
width:1000px;
}
 /*.style7 {font-size: medium}*/
 .style7 {font-size: 13px}
.style10 {font-size: 16px}
.emph {
	font-size: 16px;
	font-weight: bold;
}
p.breakhere {page-break-before: always}
</style>

<!-- Naslogo dimensions height=64 width=80 -->
<body onLoad="JavaScript:window.print();">

	<?php $count = 0; ?>

	@foreach($samples as $key => $sample)
		@continue($sample->repeatt == 1)
		<?php $count++; ?>

		<br />
		<br />
		<br />
		<br />

		<table id="table1" align="center">

			<tr>
				<td colspan="7" align="center">
					@if(isset($print))
					<strong><img src="{{ asset('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
					@else
					<strong><img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
					@endif
					<span class="style1"><br>
					  <span class="style7">MINISTRY OF HEALTH <br />
					  PCR SARS COV-2 (COVID-19) RESULT FORM<br />
					  ISO 15189:2012 Accredited by KENAS</span>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="comment style1 style4">
					<strong> Facility.: KEMRI Nairobi Lab </strong> 
				</td>
				<td colspan="3" class="comment style1 style4" align="right">
					<strong>Testing Lab: KEMRI Nairobi Lab for Molecular Biology</strong>
				</td>
			</tr>

			<tr>
				<td colspan="7"  class="evenrow" align="center" >
					<span class="style1 style10">
						<strong> Covid-19 Test Results </strong>
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong> Unique Case Identifier</strong></td>
				<td colspan="2"> <span class="style5">{{ $sample->patient_name }}</span></td>
				<td class="style4 style1 comment" colspan="2"><strong> Name </strong></td>
				<td colspan="1" class="comment">
					<span class="style5">
						{{ $sample->patient_name }}	
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>National ID/PP No </strong></td>
				<td colspan="2"  ><span class="style5"> {{ $sample->id_passport }} </span></td>
				<td class="style4 style1 comment" colspan="2" ><strong> Phone Number </strong></td>
				<td colspan="1" class="comment"> <span class="style5"> {{ $sample->phone_no }} </span> </td>
			</tr>

			@if($sample->dob)
			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Date of Birth</strong></td>
				<td colspan="2"  ><span class="style5"> {{ $sample->dob }} </span></td>
				<td class="style4 style1 comment" colspan="2" ><strong> </strong></td>
				<td colspan="1" class="comment">
					<span class="style5">
							
					</span>
				</td>
			</tr>

			@else
			<tr>
				<td colspan="2" class="style4 style1 comment"><strong> Age (Years)</strong></td>
				<td colspan="2"  ><span class="style5"> {{ $sample->age }} </span></td>
				<td class="style4 style1 comment" colspan="2" ><strong> </strong></td>
				<td colspan="1" class="comment">
					<span class="style5">
							
					</span>
				</td>
			</tr>
			@endif

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Gender </strong></td>
				<td colspan="2"  ><span class="style5"> {{ $sample->gender }} </span></td>
				<td class="style4 style1 comment" colspan="2" ><strong> Area of Residence </strong></td>
				<td colspan="1" class="comment"> <span class="style5"> {{ $sample->residence }} </span> </td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment" ><strong>Date	Collected </strong></td>
				<td class="comment" colspan="2">
					<span class="style5">{{ $sample->my_date_format('datecollected', 'd-M-Y H:i:s') }}</span>
				</td>
				<td class="style4 style1 comment" colspan="2"><strong> Health Status at time of reporting </strong></td>
				<td colspan="1" > <span class="style5"> </span></td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Date Received </strong></td>
				<td colspan="2" class="comment" >
					<span class="style5">
						{{ $sample->my_date_format('datereceived', 'd-M-Y H:i:s') }} 
					</span>
				</td>
				<td class="style4 style1 comment" colspan="2"><strong>Sample Type </strong></td>
				<td colspan="1" >
					<span class="style5">
						Nasopharygneal & Oropharygneal swab					
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Date Test Performed </strong></td>
				<td colspan="2" class="comment" >
					<span class="style5">{{ $sample->my_date_format('datetested', 'd-M-Y H:i:s') }}</span>
				</td>
				<td class="style4 style1 comment" colspan="2"><strong>Reason for Test </strong></td>
				<td colspan="1" >
					<span class="style5"> Surveillance </span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Time Uploaded </strong></td>
				<td colspan="2" class="comment" >
					<span class="style5">{{ $sample->created_at->toDateTimeString() }}</span>
				</td>
				<!-- <td class="style4 style1 comment" colspan="2"><strong>Reason for Test </strong></td>
				<td colspan="1" >
					<span class="style5"> Surveillance </span>
				</td> -->
			</tr>

			<tr>				
				<td colspan="3" class="style4 style1 comment"><strong>Test Result</strong></td>
				<td colspan="4" class="style4 style1 comment">
					<strong> 
						PCR {{ $sample->result_name }};
						IgM {{ $sample->igm_result_name }};
						IgM Index {{ $sample->igm_index_result }};
						IgG/IgM {{ $sample->igg_igm_result_name }};
						Antigen {{ $sample->antigen_result_name }};
					</strong>
				</td>
			</tr>

			
			

			<tr>
				<td colspan="2">
				  <span class="style4 style1 comment"><strong>Comments:</strong></span>
				</td>
				<td colspan="5" class="comment" >
					<span class="style5 ">{{ $sample->comments }} &nbsp; 
						IgM & IgG tests for SARS can be inaccurate and are conducted for research purposes only

					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment">
					<strong>Date Dispatched:  </strong>
				</td>
				<td colspan="5" class="style4 style1 comment">
					{{ $sample->my_date_format('datedispatched', 'd-M-Y H:i:s') }}
				</td>
			</tr>
		
			@if($sample->result == 1)
				<tr>
					<td colspan="5" class="style4 style1 comment">
		                {!! QrCode::size(100)->generate('National ID / Passport - ' . $sample->id_passport . ', Result - ' . $sample->result_name) !!} 
					</td>
					<td colspan="2" class="style4 style1 comment">
					</td>
	            </tr>
			@endif
		

		</table>


		<div class="style8" > 

			@if(env('APP_LAB') == 1)
				If you have questions or problems regarding samples, please contact the KEMRI-NAIROBI Lab at eid-nairobi@googlegroups.com Tel +254 725 793260 <br />
			@else
				If you have questions or problems regarding samples, please contact the testing laboratory.
			@endif

		</div>


		@if (!$loop->last)
			<p class="breakhere"></p>
		@endif
	@endforeach

</body>
</html>