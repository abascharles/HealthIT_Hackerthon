

<?php

$str="
<!DOCTYPE html>
<html>
<head></head>
<body>

	<table border='0' id='table1' align='center'>
		<tr>
			<td colspan='5' class='comment style1 style4'>
				<strong> Batch No.: " .  $batch->id  . " &nbsp;&nbsp; " .  $batch->facility->name  . " </strong> 
			</td>
			<td colspan='4' class='comment style1 style4' align='right'>
				<strong>LAB: " .  $batch->lab->name  . "</strong>
			</td>
		</tr>
		<tr>
			<td colspan='9' class='comment style1 style4'>
				<strong>NOTICE:</strong> 
			</td>
		</tr>
		<tr>
			<td colspan='9' class='comment style1 style4'>
				<strong>The Viral Load Test is now available in all EID testing sites. Samples can be collected in DBS form and shipped using the A/C C00339.Call the official EID lines for more information. Thank you.</strong>
			</td>
		</tr>
	</table>

	<br />

	<table>
		<tr>
			<td colspan='3'>Date Samples Were Dispatched</td>				
		</tr>
		<tr>
			<td>Facility Name: " .  $batch->facility->name  . " </td>
			<td>Contact: " .  $batch->facility->contactperson  . " </td>
			<td>Tel(personal): " .  $batch->facility->contacttelephone  . " </td>
		</tr>
		<tr>
			<td colspan='3'>Receiving Address (via Courier): " .  $batch->facility->PostalAddress  . "</td>
			<td colspan='3'>Email (optional-where provided results will be emailed and also sent by courier ):  " .  $batch->facility->email  . "</td>
		</tr>
	</table>

	<br />


	<table>
		<tr>
			<td colspan='17'>SAMPLE LOG</td>
		</tr>
		<tr>
			<td colspan='5'>Patient Information</td>
			<td colspan='4'>Samples Information</td>
			<td colspan='4'>Mother Information</td>
			<td colspan='4'>Lab Information</td>
		</tr>
		<tr>
			<td>No</td>
			<td>Patient ID</td>
			<td>Sex</td>
			<td>Age (mths)</td>
			<td>Prophylaxis</td>
			<td>Date Collected</td>
			<td>Date Received</td>
			<td>Status</td>
			<td>Test Type</td>
			<td>HIV Status</td>
			<td>PMTCT</td>
			<td>Feeding</td>
			<td>Entry Point</td>
			<td>Date Tested</td>
			<td>Date Dispatched</td>
			<td>Test Result</td>
			<td>TAT</td>
		</tr>


		";

		foreach ($samples as $key => $sample):
			if($sample->receivedstatus == 2){
				$rejection = true;
				continue;
			}
		
		$str .= "
			<tr>
				<td>" .  ($key+1)  . " </td>
				<td>" .  $sample->patient->patient  . " </td>
				<td>
					" .  $genders->where('id', $sample->patient->sex)->first()->gender . "
				</td>
				<td>" .  $sample->age  . " </td>
				<td>" .  $sample->regimen  . " </td>
				<td>" .  $sample->datecollected  . " </td>
				<td>" .  $batch->datereceived  . " </td>
				<td>
					" .  $received_statuses->where('id', $sample->receivedstatus)->first()->name . "
				</td>
				<td>" .  $sample->pcrtype  . " </td>
				<td>
					" .  $results->where('id', $sample->patient->mother->hiv_status)->first()->name . "
				</td>
				<td>" .  $sample->mother_prophylaxis  . " </td>
				<td>
					" .  $feedings->where('id', $sample->feeding)->first()->feeding . "
                </td>
                <td>" .  $sample->patient->entry_point  . " </td>
				<td>" .  $sample->datetested  . " </td>
				<td>" .  $sample->datedispatched  . " </td>
				<td>
					" .  $results->where('id', $sample->result)->first()->name . "
				</td>
				<td></td>
			</tr>
		"; 
		endforeach; 

	$str .= "</table> <br />";

		if(isset($rejection)): 
		$str .= "
		<table>
			<tr>
				<td colspan='10'>REJECTED SAMPLE(s)</td>
			</tr>
			<tr>
				<td>No</td>
				<td>Patient ID</td>
				<td>Sex</td>
				<td>Age (mths)</td>
				<td>Prophylaxis</td>
				<td>Date Collected</td>
				<td>Date Received</td>
				<td>Status</td>
				<td>Rejected Reason</td>
				<td>Date Dispatched</td>			
			</tr>

			";

			foreach ($samples as $key => $value):
				if($sample->receivedstatus != 2) continue;
			$str .= "
				<tr>
					<td>" .  ($key+1)  . " </td>
					<td>" .  $sample->patient->patient  . " </td>
					<td>
						" .  $genders->where('id', $sample->patient->sex)->first()->gender . "
					</td>
					<td>" .  $sample->age  . " </td>
					<td>" .  $sample->regimen  . " </td>
					<td>" .  $sample->datecollected  . " </td>
					<td>" .  $batch->datereceived  . " </td>
					<td>
						" .  $received_statuses->where('id', $sample->receivedstatus)->first()->name . "
					</td>
					<td>
						" .  $rejected_reasons->where('id', $sample->rejectedreason)->first()->name . "
					</td>
					<td>" .  $sample->datedispatched  . " </td>
				</tr>
			";
			endforeach;
	$str  .= "
		</table>
	";
	 endif;


	 $str . "


	Result Reviewed By: " .  $sample->approver->full_name  . "  Date Reviewed: " .  $sample->dateapproved  . "


</body>
</html>";


$conf  = config('pdf');
$mpdf = new \Mpdf\Mpdf($conf);

$mpdf->Bookmark('Start of the document');
$mpdf->WriteHTML($str);

$mpdf->Output();