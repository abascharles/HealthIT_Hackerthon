
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

			@if(env('APP_LAB') == 23)
				<tr>
					<td colspan="3">
						@if(isset($print))
						<strong><img src="{{ asset('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
						@else
						<strong><img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
						@endif					
					</td>
					<td colspan="4" align="center">
						@if(isset($print))
						<strong><img src="{{ asset('img/ku_result_logo.png') }}" alt="KUTRRH" width="90" height="48"></strong>
						@else
						<strong><img src="{{ public_path('img/ku_result_logo.png') }}" alt="KUTRRH" width="90" height="48"></strong>
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="7" align="center">
						<span class="style1 style7"><br>
						  	KENYATTA UNIVERSITY TEACHING, REFERRAL & RESEARCH HOSPITAL <br />
							P.O. BOX 7674-00100, GPO, NAIROBI <br />
							<b> Tel: </b> 0710642513/0780900519  <b> Website: </b> www.kutrrh.go.ke <b> Email: </b> info@kutrrh.go.ke <br />
							@if($sample->antigen)
								Antigen SARS COV-2 (COVID-19) RESULT FORM
							@else
								PCR SARS COV-2 (COVID-19) RESULT FORM
							@endif
						</span>
					</td>					
				</tr>

			@elseif(env('APP_LAB') == 25)
				<tr>
					<td colspan="3">
						@if(isset($print))
						<strong><img src="{{ asset('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
						@else
						<strong><img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
						@endif						
					</td>
					<td colspan="4" align="center">
						@if(isset($print))
						<strong><img src="{{ asset('img/amref_logo.jpg') }}" alt="AMREF" width="90" height="48"></strong> 
						@else
						<strong><img src="{{ public_path('img/amref_logo.jpg') }}" alt="AMREF" width="90" height="48"></strong> 
						@endif
					</td>
				</tr>
				<tr>
					<td colspan="7" align="center">
						<span class="style1 style7"><br>
						  <span class="style7">AMREF <br />
						  PCR SARS COV-2 (COVID-19) RESULT FORM</span>
						</span>
					</td>					
				</tr>
			@else
				<tr>
					<td colspan="7" align="center">
						@if(isset($print))
						<strong><img src="{{ asset('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
						@else
						<strong><img src="{{ public_path('img/naslogo.jpg') }}" alt="NASCOP"></strong> 
						@endif
						<span class="style1"><br>
						  <span class="style7">MINISTRY OF HEALTH <br />
						  PCR SARS COV-2 (COVID-19) RESULT FORM</span>
						</span>
					</td>
				</tr>
			@endif
			<tr>
				<td colspan="4" class="comment style1 style4">
					<strong> Facility.: {{ $sample->patient->facility->county ?? '' }} &nbsp;&nbsp; {{ $sample->patient->facility->name ?? $sample->patient->quarantine_site->name ?? '' }} </strong> 
				</td>
				<td colspan="3" class="comment style1 style4" align="right">
					<strong>Testing Lab: 
						@if(env('APP_LAB') == 5)
							Moi Teaching & Referral Hospital
						@else
							{{ $sample->lab->name ?? '' }}
						@endif
					</strong>
				</td>
			</tr>

			@if(env('APP_LAB') == 1 && $sample->facility)

				<tr>
					<td colspan="7" class="style4 style1 comment">
						<strong>Contact/Facility Telephone:</strong>
						{{ $sample->facility->contacts }} &nbsp;&nbsp;
						{{ $sample->facility->facility_contacts }}
					</td>		
				</tr>	

				<tr>
					<td colspan="7" class="style4 style1 comment">
						<strong>Contact/Facility Email:</strong> &nbsp; {{ $sample->facility->email_string }}
					</td>					
				</tr>

			@endif

			<tr>
				<td colspan="7"  class="evenrow" align="center" >
					<span class="style1 style10">
						<strong> Covid-19 Test Results </strong>
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong> Unique Case Identifier</strong></td>
				<td colspan="2"> <span class="style5">{{ $sample->patient->identifier }}</span></td>
				<td class="style4 style1 comment" colspan="2"><strong> Name </strong></td>
				<td colspan="1" class="comment">
					<span class="style5">
						{{ $sample->patient->patient_name }}	
					</span>
				</td>
			</tr>

			@if(env('APP_LAB') == 25)

				<tr>
					<td colspan="2" class="style4 style1 comment"><strong>  </strong></td>
					<td colspan="2"> <span class="style5"> </span></td>
					<td class="style4 style1 comment" colspan="2"><strong> Kemri ID </strong></td>
					<td colspan="1" class="comment">
						<span class="style5">
							{{ $sample->kemri_id }}	
						</span>
					</td>
				</tr>

			@endif

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>National ID </strong></td>
				<td colspan="2"  ><span class="style5"> {{ $sample->patient->national_id }} </span></td>
				<td class="style4 style1 comment" colspan="2" ><strong> Phone Number </strong></td>
				<td colspan="1" class="comment"> <span class="style5"> {{ $sample->patient->phone_no }} </span> </td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong> DOB & Age (Years)</strong></td>
				<td colspan="2"  ><span class="style5">{{ $sample->patient->my_date_format('dob') }} ({{ $sample->age }})</span></td>
				<td class="style4 style1 comment" colspan="2" ><strong> </strong></td>
				<td colspan="1" class="comment">
					<span class="style5">
							
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Gender </strong></td>
				<td colspan="2"  ><span class="style5"> {{ $sample->patient->gender }} </span></td>
				<td class="style4 style1 comment" colspan="2" ><strong> Area of Residence </strong></td>
				<td colspan="1" class="comment"> <span class="style5"> {{ $sample->patient->residence }} </span> </td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment" ><strong>Date	Collected </strong></td>
				<td class="comment" colspan="2">
					<span class="style5">{{ $sample->my_date_format('datecollected') }}</span>
				</td>
				<td class="style4 style1 comment" colspan="2"><strong> Health Status at time of reporting </strong></td>
				<td colspan="1" > <span class="style5">{{ $sample->get_prop_name($health_statuses, 'health_status') }}</span></td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Date Received </strong></td>
				<td colspan="2" class="comment" >
					<span class="style5">
						{{ $sample->my_date_format('datereceived') }} 
					</span>
				</td>
				<td class="style4 style1 comment" colspan="2"><strong>Sample Type </strong></td>
				<td colspan="1" >
					<span class="style5">
						{{ $sample->get_prop_name($covid_sample_types, 'sample_type') }}					
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Date Test Performed </strong></td>
				<td colspan="2" class="comment" >
					<span class="style5">{{ $sample->my_date_format('datetested') }}</span>
				</td>
				<td class="style4 style1 comment" colspan="2"><strong>Reason for Test </strong></td>
				<td colspan="1" ><span class="style5"> 
						{{ $sample->patient->get_prop_name($covid_justifications, 'justification') }}					
					</span></td>
			</tr>

			<tr>
				

				@if($sample->receivedstatus == 2)
					<td colspan="2" class="style4 style1 comment"><strong>Sample Rejected. </strong></td>

					<td colspan="4" class="style4 style1 comment">
						$sample->get_prop_name($rejected_reasons, 'rejectedreason') <br />
						The sample was not fit for testing. Kindly collect a new sample.
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

			
			

			<tr>
				<td colspan="2">
				  <span class="style4 style1 comment"><strong>Comments:</strong></span>
				</td>
				<td colspan="5" class="comment" >
					<span class="style5 ">{{ $sample->comments }} &nbsp; {{ $sample->labcomment }}
						@if($sample->result == 2)
							&nbsp; Kindly carry out a follow up test in due time
						@endif
						@if($sample->result == 8)
							&nbsp; The patient should be presumed to be positive but the results were not conclusive. Carry out a follow up test as soon as possible.
						@endif

					</span>
				</td>
			</tr>

			@if(env('APP_LAB') != 1)

				@if(env('APP_LAB') == 5)
				<tr>
					<td colspan="7" class="style4 style1 comment">
						<center>
							<strong>Result Reviewed By: </strong>
							&nbsp;&nbsp;
							<strong> Kadima S.</strong> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						</center>					
					</td>
				</tr>
				@elseif(in_array(env('APP_LAB'), [23, 25]))
				<tr>
					<td colspan="7" class="style4 style1 comment">
						<center>
							<strong>Test Performed By: </strong>
							&nbsp;&nbsp;
							<strong> {{ $sample->worksheet->runner->full_name ?? '' }} </strong> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<img src="{{ $sample->worksheet->runner->user_signature ?? null }}" height="30" width="60" alt="SIGNATURE">
						</center>					
					</td>
				</tr>			
				<tr>
					<td colspan="7" class="style4 style1 comment">
						<center>
							<strong>Result Reviewed By: </strong>
							&nbsp;&nbsp;
							<strong> {{ $sample->final_approver->full_name ?? $sample->approver->full_name ?? '' }} </strong> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<img src="{{ $sample->final_approver->user_signature ?? $sample->approver->user_signature ?? null }}" height="30" width="60" alt="SIGNATURE">
						</center>					
					</td>
				</tr>

				@elseif(env('APP_LAB') == 2)			
				<tr>
					<td colspan="7" class="style4 style1 comment">
						<center>
							<strong>Tested By: </strong>
							&nbsp;&nbsp;
							<strong> {{ $sample->worksheet->uploader->full_name ?? '' }} </strong> 
						</center>					
					</td>
				</tr>
				<tr>
					<td colspan="7" class="style4 style1 comment">
						<center>
							<strong>Reviewed By: </strong>
							&nbsp;&nbsp;
							<strong> {{ $sample->final_approver->full_name ??  $sample->approver->full_name ?? '' }}</strong> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<strong>Signature: </strong>
							&nbsp;&nbsp;
						</center>					
					</td>
				</tr>	
				@else
				<tr>
					<td colspan="7" class="style4 style1 comment">
						<center>
							<strong>Result Reviewed By: </strong>
							&nbsp;&nbsp;
							<strong> {{ $sample->final_approver->full_name ??  $sample->approver->full_name ?? '' }}</strong> 
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<strong>Signature: </strong>
							&nbsp;&nbsp;
						</center>					
					</td>
				</tr>
				@endif
				<tr>
					<td colspan="3" class="style4 style1 comment">
						<strong>Date Reviewed:  {{ $sample->my_date_format('dateapproved') }}</strong>
					</td>
					<td colspan="4" class="style4 style1 comment">
						<strong>Date Dispatched:  {{ $sample->my_date_format('datedispatched') }}</strong>
					</td>
				</tr>

			@else

				<tr>
					<td colspan="2" class="style4 style1 comment">
						<strong>Date Dispatched:  </strong>
					</td>
					<td colspan="5" class="style4 style1 comment">
						{{ $sample->my_date_format('datedispatched') }}
					</td>
				</tr>

			@endif

			@if(env('APP_LAB') == 600)

				<tr>
					<td colspan="5" class="style4 style1 comment">
						<!-- <img height="90" width="240" src="{{ asset('john_sig.jpg') }} " alt="SIGNATURE"> -->
						<img src="{{ $sample->lab->lab_signature }}" height="90" width="240" alt="SIGNATURE">
					</td>
					<td colspan="2" class="style4 style1 comment">
					</td>
				</tr>
			@endif
		
			@if($sample->national_sample_id && $sample->result == 1)
				<tr>
					<td colspan="5" class="style4 style1 comment">
		                <b> Attestation Certificate Number (Scan with any QR Code Scanner to Verify): </b> &nbsp;&nbsp;&nbsp; {{ $sample->national_sample_id }} <br />
		                {!! QrCode::size(100)->generate('National ID / Passport - ' . $sample->patient->national_id . ', Name - ' . utf8_decode($sample->patient->patient_name) . ', Result - ' . $sample->result_name . ', Attestation Number - ' . $sample->national_sample_id) !!}
					</td>
					<td colspan="2" class="style4 style1 comment">
					</td>
	            </tr>
			@endif
		
			@if(Str::contains(get_class($sample), 'CovidModel') && $sample->result == 1)
				<tr>
					<td colspan="5" class="style4 style1 comment">
		                <b> Attestation Certificate Number: </b> &nbsp;&nbsp;&nbsp; {{ $sample->id }} <br />
		                {!! QrCode::size(100)->generate($sample->id) !!}
					</td>
					<td colspan="2" class="style4 style1 comment">
					</td>
	            </tr>
			@endif


		</table>

		@if($sample->site_entry != 2)

			<span class="style8" > 

				@if(env('APP_LAB') == 1)
					If you have questions or problems regarding samples, please contact the KEMRI-NAIROBI Lab at eid-nairobi@googlegroups.com <br />
				@elseif(env('APP_LAB') == 3)
					If you have questions or problems regarding samples, please contact the KEMRI ALUPE HIV Laboratory through {{ $sample->lab->labtel1 ?? '' }} / {{ $sample->lab->labtel2 ?? '' }} or {{ $sample->lab->email ?? '' }} <br />
				@else
					If you have questions or problems regarding samples, please contact the testing laboratory.
				@endif

			</span>

		@endif

		@if (!$loop->last)
			<p class="breakhere"></p>
		@endif
	@endforeach

</body>
</html>
