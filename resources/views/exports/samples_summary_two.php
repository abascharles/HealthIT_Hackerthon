<?php
$conf  = config('pdf');
$mpdf = new \Mpdf\Mpdf($conf);
$mpdf->simpleTables = true;
ob_start();

?>

<?php

// echo '<div>Generate your content</div>';

// $html = ob_get_contents();
// ob_end_clean();

// // send the captured HTML from the output buffer to the mPDF class for processing
// $mpdf->WriteHTML($html);
// $mpdf->Output();

// die();



?>

<html>
<head></head>
<body>

	<table border='0' id='table1' align='center'>
		<tr>
			<td colspan='9' align='center'>
				<span class='style6 style1'>
					<strong><img src='<?php echo asset('img/naslogo.jpg') ; ?>' alt='NASCOP' align='absmiddle'></strong> 
				</span>
				<span class='style1'><br>
				  <span class='style7'>MINISTRY OF HEALTH <br />
				  NATIONAL AIDS AND STD CONTROL PROGRAM (NASCOP)<br />
				  EARLY INFANT HIV DIAGNOSIS (DNA-PCR) RESULT FORM</span>
				</span>
			</td>
		</tr>
		<tr>
			<td colspan='5' class='comment style1 style4'>
				<strong> Batch No.: <?php echo $batch->id ; ?> &nbsp;&nbsp; <?php echo $batch->facility->name ; ?> </strong> 
			</td>
			<td colspan='4' class='comment style1 style4' align='right'>
				<strong>LAB: <?php echo $batch->lab->name ; ?></strong>
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
			<td>Facility Name: <?php echo $batch->facility->name ; ?> </td>
			<td>Contact: <?php echo $batch->facility->contactperson ; ?> </td>
			<td>Tel(personal): <?php echo $batch->facility->contacttelephone ; ?> </td>
		</tr>
		<tr>
			<td colspan='3'>Receiving Address (via Courier): <?php echo $batch->facility->PostalAddress ; ?></td>
			<td colspan='3'>Email (optional-where provided results will be emailed and also sent by courier ):  <?php echo $batch->facility->email ; ?></td>
		</tr>
	</table>

	<br />



<?php

$html = ob_get_contents();
ob_end_clean();

// send the captured HTML from the output buffer to the mPDF class for processing
$mpdf->WriteHTML($html);
$mpdf->Output();

die();

?>

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


		<?php
		foreach ($samples as $key => $sample):
			if($sample->receivedstatus == 2){
				$rejection = true;
				continue;
			}
		?>
			<tr>
				<td><?php echo ($key+1) ; ?> </td>
				<td><?php echo $sample->patient->patient ; ?> </td>
				<td>
					<?php echo $genders->where('id', $sample->patient->sex)->first()->gender; ?>
				</td>
				<td><?php echo $sample->age ; ?> </td>
				<td><?php echo $sample->regimen ; ?> </td>
				<td><?php echo $sample->datecollected ; ?> </td>
				<td><?php echo $batch->datereceived ; ?> </td>
				<td>
					<?php echo $received_statuses->where('id', $sample->receivedstatus)->first()->name; ?>
				</td>
				<td><?php echo $sample->pcrtype ; ?> </td>
				<td>
					<?php echo $results->where('id', $sample->patient->mother->hiv_status)->first()->name; ?>
				</td>
				<td><?php echo $sample->mother_prophylaxis ; ?> </td>
				<td>
					<?php echo $feedings->where('id', $sample->feeding)->first()->feeding; ?>
                </td>
                <td><?php echo $sample->patient->entry_point ; ?> </td>
				<td><?php echo $sample->datetested ; ?> </td>
				<td><?php echo $sample->datedispatched ; ?> </td>
				<td>
					<?php echo $results->where('id', $sample->result)->first()->name; ?>
				</td>
				<td></td>
			</tr>
		<?php endforeach;  ?>
	</table>

	<?php if(isset($rejection)): ?>
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

			<?php
			foreach ($samples as $key => $value):
				if($sample->receivedstatus != 2) continue;
			?>
				<tr>
					<td><?php echo ($key+1) ; ?> </td>
					<td><?php echo $sample->patient->patient ; ?> </td>
					<td>
						<?php echo $genders->where('id', $sample->patient->sex)->first()->gender; ?>
					</td>
					<td><?php echo $sample->age ; ?> </td>
					<td><?php echo $sample->regimen ; ?> </td>
					<td><?php echo $sample->datecollected ; ?> </td>
					<td><?php echo $batch->datereceived ; ?> </td>
					<td>
						<?php echo $received_statuses->where('id', $sample->receivedstatus)->first()->name; ?>
					</td>
					<td>
						<?php echo $rejected_reasons->where('id', $sample->rejectedreason)->first()->name; ?>
					</td>
					<td><?php echo $sample->datedispatched ; ?> </td>
				</tr>
			<?php endforeach;  ?>
		</table>
	<?php endif; ?>


	Result Reviewed By: <?php echo $sample->approver->full_name ; ?>  Date Reviewed: <?php echo $sample->dateapproved ; ?>


</body>
</html>

<?php

$html = ob_get_contents();
ob_end_clean();

// send the captured HTML from the output buffer to the mPDF class for processing
$mpdf->WriteHTML($html);
$mpdf->Output();

?>