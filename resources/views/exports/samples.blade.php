<html>
	<style type="text/css">
	<!--
	.style1 {font-family: "Courier New", Courier, monospace}
	.style4 {font-size: 12}
	.style5 {font-family: "Courier New", Courier, monospace; font-size: 12; }
	.style8 {font-family: "Courier New", Courier, monospace; font-size: 11; }
	.style6 {
		font-size: medium;
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
	border : solid 1px black;
	width:1000px;
	width:900px;
	}
	 .style7 {font-size: medium}
	.style10 {font-size: 16px}
	</style>

	<STYLE TYPE="text/css">
	     P.breakhere {page-break-before: always}

	}

	</STYLE> 
<body onLoad="JavaScript:window.print();">

	@foreach($samples as $key => $sample)
		<table  border="0" id='table1' align="center">
			<tr>
				<td colspan="9" align="center">
					<span class="style6 style1">
						<!-- <strong><img src="img/naslogo.jpg" alt="NASCOP" align="absmiddle" ></strong> --> 
						<strong><img src="{{ asset('img/naslogo.jpg') }}" alt="NASCOP" align="absmiddle" ></strong> 
					</span>
					<span class="style1"><br>
					  <span class="style7">MINISTRY OF HEALTH <br />
					  NATIONAL AIDS AND STD CONTROL PROGRAM (NASCOP)<br />
					  EARLY INFANT HIV DIAGNOSIS (DNA-PCR) RESULT FORM</span>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="5" class="comment style1 style4">
					<strong> Batch No.: {{ $batch->id ?? '' }} &nbsp;&nbsp; {{ $batch->facility->name ?? '' }} </strong> 
				</td>
				<td colspan="4" class="comment style1 style4" align="right">
					<strong>LAB: {{ $batch->lab->name ?? '' }}</strong>
				</td>
			</tr>

			<tr>
				<td colspan="3"  class="evenrow" align="center" >
					<span class="style1 style10">
						<strong> DNA PCR TEST RESULTS </strong>
					</span>
				</td>
				<td colspan="4" class="evenrow" align="center">
					<span class="style1 style10">
						<strong> Mother  Information </strong>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="2" class="style4 style1 comment"><strong>Sample Code</strong></td>
				<td colspan="1"><span class="style5"> {{ $sample->patient->patient }} </span></td>
				<td colspan="3"  class="style4 style1 comment" ><strong>HIV Status </strong></td>
				<td colspan="1" >
					<span class="style5"> 
	                    @foreach($results as $result)
	                        @if($sample->patient->mother->hiv_status == $result->id)
	                            {{ $result->name }}
	                        @endif
	                    @endforeach
					</span>
				</td>
			</tr>
			<tr >
				<td colspan="2" class="style4 style1 comment" ><strong>Date	 Collected </strong></td>
				<td  class="comment" colspan="1">
				  <span class="style5">{{ $sample->datecollected }} </span>
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
			<tr >
				<td colspan="2" class="style4 style1 comment"><strong>Date Received </strong></td>
				<td colspan="1" class="comment" ><span class="style5">{{ $batch->datereceived ?? '' }}</span></td>
				<td class="style4 style1 comment" colspan="3"><strong> Infant Prophylaxis </strong></td>
				<td colspan="1" class="comment ">
					<span class="style5">
                        @foreach($iprophylaxis as $iproph)
                            @if($sample->regimen == $iproph->id)
                                {{ $iproph->name }}
                            @endif
                        @endforeach						
					</span>
				</td>
			</tr>
				<tr >
				<td colspan="2" class="style4 style1 comment" width="220px"><strong>Date Test Perfomed </strong></td>
				<td colspan="1" class="comment" ><span class="style5">{{ $sample->datetested }}</span></td>
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
			<tr >
				<td colspan="2" class="style4 style1 comment"><strong>Age (Months)</strong></td>
				<td colspan="1"  ><span class="style5">{{ $sample->age }}</span></td>
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
				<td colspan="2" class="evenrow"><span class="style1"><strong>
				Test Result </strong></span></td>
				<td colspan="5" class="evenrow"  >
					<span class="style1">
						<strong> 
		                    @foreach($results as $result)
		                        @if($sample->result == $result->id)
		                            {{ $result->name }}
		                        @endif
		                    @endforeach

						</strong>
					</span>
				</td>
			</tr>

			<tr>
				<td colspan="2">
				  <span class="style1"><strong>Comments:</strong></span>
				</td>
				<td colspan="7" class="comment" >
					<span class="style5 ">{{ $sample->comments }} <br> {{ $sample->labcomment }} </span>
				</td>
			</tr>
			<tr >
				<td colspan="12" class="style4 style1 comment">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<strong>Result Reviewed By: </strong> 
					&nbsp;&nbsp;&nbsp;&nbsp; 
					<strong> {{ $sample->approver->full_name ?? '' }}</strong> 
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<strong>Date Reviewed:  {{ $sample->dateapproved }}</strong>
				</td>
			</tr>

		</table>

		<span class="style8" > 
			If you have questions or problems regarding samples, please contact the {{ $batch->lab->name ?? '' }}  
			<br> 
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			at {{ $batch->lab->email ?? '' }}
			<br> 
			<b> To Access & Download your current and past results go to : <u> http://eid.nascop.org/login.php</u> </b>
		</span>

		<br>
		<br>
		<img src="{{ asset('img/but_cut.gif') }}">
		<br>
		<br>

		@if($key % 2 == 1)
			<p class="breakhere"></p>
		@endif

	@endforeach

</body>
</html>