
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
 .style7 {font-size: medium}
.style10 {font-size: 16px}
</style>

<STYLE TYPE="text/css">
     P.breakhere {page-break-before: always}

}

</STYLE> 
<body onLoad="JavaScript:window.print();">
<div align="center">
<table>
<tr>
<td><strong>HIV	LAB EARLY INFANT DIAGNOSIS<br/>

ABBOTT M2000 SPRT TEMPLATE </strong>
</td>
</tr>
</table>
</div>
<table border="0" class="data-table">
		<tr class="odd">
				<td colspan="3"><strong>WorkSheet Details</strong>	</td>
				
				<td colspan="2"><strong>Extraction Reagent</strong>	</td>
				<td colspan="3"><strong>Amplification Reagent</strong></td>
								
			</tr>
			<tr class="odd">
				
				<td >
				<strong>Worksheet/Template No</strong>		</td>
				<td >
<?php echo $wno; ?></td>
	<td><strong>&nbsp;</strong>	</td>
				<td><strong>Sample Prep</strong>	</td>
				<td><strong>Bulk Lysis Buffer</strong>	</td>
				<td><strong>Control</strong>	</td>
				<td><strong>Calibrator</strong>	</td>
				<td><strong>Amplificatio Kit</strong>	</td>			
			</tr>
			<tr class="even">
						
				<td ><strong>Date Created</strong>		</td>
				<td ><?php $currentdate=date('d-M-Y'); echo  '<strong>'.$currentdate.'</strong>' ; //get current date ?></td>
				
				<td><strong>Lot No	</strong>	</td>
<td>
				<?php echo $samplepreplotno; ?></td>
<td>
				<?php echo $bulklysislotno; ?></td>

<td  >
				<?php echo $controllotno; ?></td>
<td  >
				<?php echo $calibratorlotno; ?></td>
<td  >
				<?php echo $amplificationkitlotno; ?></td>
</tr>
<tr class="even">
<td><strong>Created By	</strong>    </td>
				<td><?php  echo $creator ?>		</td>
				
<td><strong>Expiry Dates</strong>	</td>
<td><?php echo $sampleprepexpirydate; ?></td>
<td><?php echo $bulklysisexpirydate; ?></td>
<td><?php echo $controlexpirydate; ?></td>
<td><?php echo $calibratorexpirydate; ?></td>
<td><?php echo $amplificationexpirydate; ?></td>	
</tr>
<tr class="even">
<td><strong>Sorted By	</strong>    </td>
				<td>_____________________________	</td>
				<td><strong>Bulked By	</strong>    </td>
				<td>_____________________________	</td>
					<td><strong>Run By	</strong>    </td>
				<td>_____________________________	</td>
				</tr>
<tr >
<th colspan="8" ><small> <strong><?php echo $no; ?> WORKSHEET SAMPLES [2 Controls]</strong></small>		</th>
</tr>
			
<tr >
	 <?php
	 $qury = "SELECT ID,patient
         FROM samples
		WHERE worksheet='$wno'  and flag=1 ORDER BY parentid DESC,ID ASC";			
			$result = $objDB->query($qury) or die(mysqli_error());

	 $i = 0;
	$samplesPerRow = 8;
	while(list($ID,$patient) = mysqli_fetch_array($result))
	{  
	
	$paroid=getParentID($ID,$labss);//get parent id
	
if ($paroid ==0)
{
$paroid="";
$RR="";
}
else
{
$paroid=" - ". $paroid;
$RR=" <div align='right'>
	 <table><tr><td style='background-color:#FAF156'><small>
	 R </small>
	 </td></tr></table></div>";
}

     
        if ($i % $samplesPerRow == 0) {
            echo '<tr>';
        }

  
        
      echo "<td > $RR <span class='style7'>Sample:  $patient   $paroid</span>   <br> <img src=../html/image.php?code=code128&o=2&dpi=50&t=50&r=1&rot=0&text=$ID&f1=Arial.ttf&f2=8&a1=&a2=B&a3=   />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; </td>
			
";
	
       
   
    
        if ($i % $samplesPerRow == $samplesPerRow - 1) {
            echo '</tr>
			<tr><td colspan=8>&nbsp;</td></tr>
						';
        }
        
        $i += 1;

	


	}
	 echo"<td  align=center > PC </td><td  align=center > NC </td>";
	?>
</tr>
</table>

</body>
</html>