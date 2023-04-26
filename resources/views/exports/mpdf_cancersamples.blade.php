
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

	@foreach($samples as $key => $sample)
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
					  CERVICAL CANCER RESULT FORM</span>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="comment style1 style4" align="right">
					<strong>Facility: {{ $sample->facility->name ?? '' }}</strong>
				</td>
				<td colspan="3" class="comment style1 style4" align="right">
					<strong>Testing Lab: {{ $sample->testing_lab->name ?? '' }}</strong>
				</td>
			</tr>
			
			</tr>

			<tr>
				<td colspan="7"  class="evenrow" align="center" >
					<span class="style1 style10">
						<strong> CERVICAL CANCER TEST RESULTS </strong>
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="3" class="style4 style1 comment"><strong>Patient Number:</strong></td>

				<td colspan="1" class="style4 style1 comment">{{ $sample->patient->patient }}</td>
				<td colspan="1" class="style4 style1 comment"><strong>HIV Status:</strong></td>
				<td colspan="2" class="style4 style1 comment">
					@foreach($hivstatuses as $status)
                        @if($sample->patient->hiv_status == $status->id)
                        	{{ $status->name }}
                        @endif
                    @endforeach
				</td>
			</tr>
			

			<tr>
				<td colspan="3" class="style4 style1 comment"><strong>Patient Name:</strong></td>

				<td colspan="1" class="style4 style1 comment">{{ $sample->patient->patient_name }}</td>
				<td colspan="1" class="style4 style1 comment"><strong>Sample Type:</strong></td>
				<td colspan="2" class="style4 style1 comment">
					@foreach($sampletypes as $sampletype)
                        @if($sample->sample_type == $sampletype->id)
                        	{{ $sampletype->name }}
                        @endif
                    @endforeach
				</td>
			</tr>

			<tr>
				<td colspan="3" class="style4 style1 comment"><strong>DOB & Age (Months):</strong></td>

				<td colspan="1" class="style4 style1 comment">{{ $sample->patient->my_date_format('dob') }} ({{ $sample->age }})</td>
				<td colspan="1" class="style4 style1 comment"><strong>Justification:</strong></td>
				<td colspan="2" class="style4 style1 comment">
					@foreach($justifications as $justification)
                        @if($sample->justification == $justification->id)
                        	{{ $justification->name }}
                        @endif
                    @endforeach
				</td>
			</tr>

			<tr>
				<td colspan="3" class="style4 style1 comment"><strong>Entry Point:</strong></td>

				<td colspan="1" class="style4 style1 comment">
					<span class="style5">
                        {{ $sample->patient->entry_point }}
					</span>
				</td>
				<td colspan="1" class="style4 style1 comment"><strong>Target 1:</strong></td>
				<td colspan="2" class="style4 style1 comment">
					{{ $sample->target_1 }}
				</td>
			</tr>

			<tr>
				<td colspan="3" class="style4 style1 comment"><strong>Date	Collected: </strong></td>

				<td colspan="1" class="style4 style1 comment">
					<span class="style5">
                        {{ $sample->my_date_format('datecollected') }}
					</span>
				</td>
				<td colspan="1" class="style4 style1 comment"><strong>Target 2:</strong></td>
				<td colspan="2" class="style4 style1 comment">
					{{ $sample->target_2 }}
				</td>
			</tr>

			<tr>
				<td colspan="3" class="style4 style1 comment"><strong>Date Received: </strong></td>

				<td colspan="1" class="style4 style1 comment">
					<span class="style5">
                        {{ $sample->my_date_format('datereceived') }}
					</span>
				</td>
				<td colspan="1" class="style4 style1 comment"><strong>Target 3:</strong></td>
				<td colspan="2" class="style4 style1 comment">
					{{ $sample->target_3 }}
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Date Test Performed </strong></td>
				<td colspan="2" class="comment" >
					<span class="style5">{{ $sample->my_date_format('datetested') }}</span>
				</td>
			</tr>

			<tr>
				

			@if($sample->receivedstatus == 2)
				<td colspan="2" class="style4 style1 comment"><strong>Sample Rejected. Reason:</strong></td>

				<td colspan="5" class="style4 style1 comment">
					 {{ $rejectedreasons->where('id', $sample->rejectedreason)->first()->name ?? '' }}
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
				<td colspan="1" class="style4 style1 comment"><strong>Action:</strong></td>
				<td colspan="2" class="style4 style1 comment">
					<strong>
					@foreach($actions as $action)
						@if($sample->action == $action->id)
							<span>{{ $action->name }}</span>
						@endif
					@endforeach
					</strong>
				</td>
			@endif
			</tr>
			{{-- @if($sample->receivedstatus != 2)
			<tr>
				<td colspan="1" class="style4 style1 comment"><strong>Target 1:</strong></td>

				<td colspan="2" class="style4 style1 comment">
					<strong> 
	                   {{ $sample->target_1 }}
					</strong>
				</td>
				<td colspan="1" class="style4 style1 comment"><strong>Target 2:</strong></td>
				<td colspan="1" class="style4 style1 comment">
					<strong> 
	                   {{ $sample->target_2 }}
					</strong>
				</td>
				<td colspan="1" class="style4 style1 comment"><strong>Target 3:</strong></td>
				<td colspan="1" class="style4 style1 comment">
					<strong> 
	                   {{ $sample->target_3 }}
					</strong>
				</td>
			</tr>
			@endif --}}
		

			<tr>
				<td colspan="2">
				  <span class="style4 style1 comment"><strong>Comments:</strong></span>
				</td>
				<td colspan="5" class="comment" >
					<span class="style5 ">{{ $sample->comments ?? '' }} &nbsp; {{ $sample->labcomment ?? '' }}</span>
				</td>
			</tr>

			<tr>
				<td colspan="2" class="style4 style1 comment">
					<strong>Date Dispatched:  </strong>
				</td>
				<td colspan="5" class="style4 style1 comment">
					{{ $sample->my_date_format('datedispatched') }}
				</td>
			</tr>

		</table>
	{{--
		<span class="style8" > 
			<b> To Access & Download your current and past results go to : <u> https://eiddash.nascop.org</u> </b>
		</span>
--}}
		{{--@if($count % 2 == 0)
			<p class="breakhere"></p>
			<pagebreak sheet-size='A4'>
		@else--}}
			<br/> <br/> <img src="https://eiddash.nascop.org/img/but_cut.gif"> <br/><br/> 
		{{-- @endif--}}



	@endforeach

</body>
</html>