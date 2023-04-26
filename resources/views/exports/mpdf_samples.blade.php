
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
		<?php 
			$count++;
			if(!isset($current_batch)) $current_batch = $sample->batch_id;
			if(!$sample->batch) unset($sample->batch);
			if($sample->batch_id != $current_batch){
				echo "<p class='breakhere'></p> <pagebreak sheet-size='A4'>";
				$current_batch = $sample->batch_id;
			}
		 ?>
		<table id="table1" align="center">

			<tr>
				<td colspan="7" align="center">
					@if(isset($to_pdf))
					<strong><img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
					@else
					<strong><img src="{{ asset('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
					@endif
					
					<span class="style1"><br>
					  <span class="style7">MINISTRY OF HEALTH <br />
					  NATIONAL AIDS AND STD CONTROL PROGRAM (NASCOP)<br />
					  EARLY INFANT HIV DIAGNOSIS (DNA-PCR) RESULT FORM</span>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="comment style1 style4">
					<strong> Batch No.: {{ $sample->batch->id }} &nbsp;&nbsp; {{ $sample->batch->facility->name ?? '' }} </strong> 
				</td>
				<td colspan="3" class="comment style1 style4" align="right">
					@if($sample->batch->site_entry == 2)
						<strong>Testing Facility: {{ $sample->batch->facility_lab->name ?? '' }}</strong>
					@else
						<strong>Testing Lab: {{ $sample->batch->lab->name ?? '' }}</strong>
					@endif
				</td>
			</tr>

			@if(env('APP_LAB') == 1)

				<tr>
					<td colspan="7" class="style4 style1 comment">
						<strong>Contact/Facility Telephone:</strong>
						{{ $sample->batch->facility->contacts }} &nbsp;&nbsp;
						{{ $sample->batch->facility->facility_contacts }}
					</td>		
				</tr>			

				<tr>
					<td colspan="7" class="style4 style1 comment">
						<strong>Contact/Facility Email:</strong> &nbsp; {{ $sample->batch->facility->email_string }}
					</td>					
				</tr>

			@endif

			@if(env('APP_LAB') == 5 && $sample->amrs_location)			

				<tr>
					<td colspan="7" class="style4 style1 comment">
						<strong>AMRS Location:</strong> &nbsp; {{ $amrs_locations->where('id', $sample->amrs_location)->first()->name ?? ''  }}
					</td>					
				</tr>

			@endif

			<tr>
				<td colspan="3"  class="evenrow" align="center" >
					<span class="style1 style10">
						<strong> DNA PCR TEST RESULTS </strong>
					</span>
				</td>
				<td colspan="4" class="evenrow" align="center">
					<span class="style1 style10">
						<strong> Mother & Infant Information </strong>
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong> HEI Number</strong></td>
				<td colspan="1"> <span class="style5">{{ $sample->patient->patient }}</span></td>
				<td class="style4 style1 comment" colspan="3"><strong> Infant Prophylaxis </strong></td>
				<td colspan="1" class="comment">
					<span class="style5">
                        @foreach($iprophylaxis as $iproph)
                            @if($sample->regimen == $iproph->id)
                                {{ $iproph->name }}
                            @endif
                        @endforeach						
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong> DOB & Age (Months)</strong></td>
				<td colspan="1"  ><span class="style5">{{ $sample->patient->my_date_format('dob') }} ({{ $sample->age }})</span></td>
				<td class="style4 style1 comment" colspan="3" ><strong>Infant Feeding </strong></td>
				<td colspan="1" class="comment">
					<span class="style5">
                        @foreach($feedings as $feeding)
                            @if($sample->feeding == $feeding->id)
                                {{ $feeding->feeding }}
                            @endif
                        @endforeach					
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong> Gender</strong></td>
				<td colspan="1"  ><span class="style5"> {{ $sample->patient->gender }} </span></td>
				<td class="style4 style1 comment" colspan="3" ><strong> Entry Point	</strong></td>
				<td colspan="1" class="comment">
					<span class="style5">
                        @foreach($entry_points as $entry_point)
                            @if($sample->patient->entry_point == $entry_point->id)
                                {{ $entry_point->name }}
                            @endif
                        @endforeach
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong> PCR Type</strong></td>
				<td colspan="2">
					<span class="style5">
                        @foreach($pcrtypes as $pcrtype)
                            @if($sample->pcrtype == $pcrtype->id)
                                {!! $pcrtype->alias !!}
                            @endif
                        @endforeach	
					</span>
				</td>
				<td class="style4 style1 comment" colspan="2" ><strong> Mother CCC #</strong></td>
				<td colspan="1" class="comment">
					<span class="style5"> {{ $sample->patient->mother->ccc_no ?? '' }} </span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment" ><strong>Date	Collected </strong></td>
				<td class="comment" colspan="1">
					<span class="style5">{{ $sample->my_date_format('datecollected') }}</span>
				</td>
				<td class="style4 style1 comment" colspan="3"><strong> Age (Yrs) </strong></td>
				<td colspan="1" > <span class="style5">{{ $sample->mother_age }}</span></td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Date Received </strong></td>
				<td colspan="1" class="comment" >
					<span class="style5">
						{{ $sample->batch->my_date_format('datereceived') }} 
					</span>
				</td>
				<td class="style4 style1 comment" colspan="3"><strong>PMTCT Intervention </strong></td>
				<td colspan="1" >
					<span class="style5">
	                    @foreach($interventions as $intervention)
	                        @if($sample->mother_prophylaxis == $intervention->id)
	                            {{ $intervention->name }}
	                        @endif
	                    @endforeach
						
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Date Test Performed </strong></td>
				<td colspan="1" class="comment" >
					<span class="style5">{{ $sample->my_date_format('datetested') }}</span>
				</td>
				<td class="style4 style1 comment" colspan="3"><strong> Mother Last VL </strong></td>
				<td colspan="1" ><span class="style5">{{ $sample->mother_last_result }}
					@if($sample->mother_last_result && is_integer($sample->mother_last_result))
						cp/ml
					@endif
				</span></td>
			</tr>

			<tr>
				

				@if($sample->receivedstatus == 2)
					<td colspan="2" class="style4 style1 comment"><strong>Sample Rejected. Reason:</strong></td>

					<td colspan="4" class="style4 style1 comment">
						 {{ $rejected_reasons->where('id', $sample->rejectedreason)->first()->name ?? '' }}
					</td>


				@else
					<td colspan="3" class="style4 style1 comment"><strong>Test Result</strong></td>

					<td colspan="1" class="style4 style1 comment">
						<strong> 
		                    @foreach($results as $result)
		                        @if($sample->result == $result->id)
		                        	<span
		                        		@if($result->id == 2)
		                        			class="emph"
		                        		@endif

		                        	> {{ $result->name }} </span>
		                            
		                        @endif
		                    @endforeach
						</strong>
					</td>
					<td colspan="3"></td>

				@endif
			</tr>

			
			@if($sample->worksheet)
				<tr>
					<td colspan="2"></td>
					<td colspan="5" class="style4 style1 comment">					
						@if($sample->worksheet->machine_type == 1)
							HIV-1 DNA qualitative  assay on Roche CAP/CTM system
						@elseif($sample->worksheet->machine_type == 2)
							HIV-1 DNA qualitative  assay on Abbott M2000 system
						@endif					
					</td>				
				</tr>
			@endif
			

			<tr>
				<td colspan="2">
				  <span class="style4 style1 comment"><strong> Lab Comments:</strong></span>
				</td>
				<td colspan="5" class="comment" >
					<span class="style5 ">{{ $sample->comments }} &nbsp; {{ $sample->labcomment }}
						@if($sample->result == 2 && $sample->pcrtype < 4)
							&nbsp; Initiate on ART, Collect samples for Confirmatory Testing & Baseline Viral Load
						@endif

					</span>
				</td>
			</tr>

			@if(env('APP_LAB') != 1)

				<tr>
					<td colspan="7" class="style4 style1 comment">
						<center>
							<strong>Result Reviewed By: </strong>
							&nbsp;&nbsp;
							<strong> {{ $sample->approver->full_name ?? '' }}</strong> 
						</center>					
					</td>
				</tr>
				<tr>
					<td colspan="3" class="style4 style1 comment">
						<strong>Date Reviewed:  {{ $sample->my_date_format('dateapproved') }}</strong>
					</td>
					<td colspan="4" class="style4 style1 comment">
						<strong>Date Dispatched:  {{ $sample->batch->my_date_format('datedispatched') }}</strong>
					</td>
				</tr>

			@else

				<tr>
					<td colspan="2" class="style4 style1 comment">
						<strong>Date Dispatched:  </strong>
					</td>
					<td colspan="5" class="style4 style1 comment">
						{{ $sample->batch->my_date_format('datedispatched') }}
					</td>
				</tr>

			@endif

			<?php $sample->prev_tests();  ?>

			@if($sample->previous_tests->count() > 0)
				@foreach($sample->previous_tests as $prev)

					<tr class="evenrow">
						<td colspan="2"> <span class="style4 style1 comment">Previous EID Results</span></td>
						<td colspan="1" class="comment style1 style5" >
							<strong>
								 
			                    @foreach($results as $result)
			                        @if($prev->result == $result->id)
			                            {{ $result->name }}
			                        @endif
			                    @endforeach

							</strong> 
						</td>
						<td colspan="2"> <span class="style4 style1 comment">Date Test Performed</span></td>
						<td colspan="2" class="comment style1 style5"> {{ $prev->my_date_format('datetested') }} </td>

					</tr>

				@endforeach

			@else
				<tr class="evenrow">
					<td colspan="2">
						<span class="style4 style1 comment"><strong>Previous EID Results</strong></span>
					</td>
					<td colspan="5" class="style4 style1 comment" ><span class="style5 "> N/A </span></td>
				</tr>
			@endif

		</table>

		@if($sample->batch->site_entry != 2)

			<span class="style8" > 

				@if(env('APP_LAB') == 1)
					If you have questions or problems regarding samples, please contact the KEMRI-NAIROBI Lab at eid-nairobi@googlegroups.com <br />
				@elseif(env('APP_LAB') == 3)
					If you have questions or problems regarding samples, please contact the KEMRI ALUPE HIV Laboratory through 0726156679 or eid-alupe@googlegroups.com <br />
				@else
					If you have questions or problems regarding samples, please contact the {{ $sample->batch->lab->name }} at {{ $sample->batch->lab->email }}
				@endif

				<b> To Access & Download your current and past results go to : <u> https://eiddash.nascop.org</u> </b>
			</span>

		@else
			<span class="style8" > 
				<b> To Access & Download your current and past results go to : <u> https://eiddash.nascop.org</u> </b>
			</span>

		@endif

		@if($count % 2 == 0)
			<p class="breakhere"></p>
			<pagebreak sheet-size='A4'>
		@else
			<br/> <br/> <img src="https://eiddash.nascop.org/img/but_cut.gif"> <br/><br/> 
		@endif



	@endforeach

</body>
</html>