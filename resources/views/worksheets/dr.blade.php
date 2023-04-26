<html>
<link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.css') }}" />
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

/*#mytable{
	transform:rotate(270deg);
}*/

table td  {
	font-size: 8px;
}

* {
	font-size: 8px;
}
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
	<div>

		<table class="table table-bordered" id='mytable'>

			<?php $i=0;  ?>

			@foreach($dr_results as $key => $result)
				@if($i % 12 == 0)
					<tr>
				@endif
				<td>
					<b> {{ $result->id }} </b> <br />
					<b> {{ $result->sample_id }} - {{ $dr_primers->where('id', $result->dr_primer_id)->first()->name ?? '' }} </b> <br />

					<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($result->id, 'C39+') }}" alt="barcode" height="30" width="80"  />
				</td>

				@if($key == 77)
					<td>Positive <br /> Control</td>
					<td>Positive <br /> Control</td>
					<td>Positive <br /> Control</td>
					<td>Positive <br /> Control</td>
					<td>Positive <br /> Control</td>
					<td>Positive <br /> Control</td>
					<?php $i+=6;  ?>
				@endif

				@if($i % 12 == 11)
					</tr>
				@endif

				<?php $i++;  ?>

			@endforeach

				<td>Water</td>
				<td>Water</td>
				<td>Water</td>
				<td>Water</td>
				<td>Water</td>
				<td>pGEM</td>
			</tr>
				
		</table>
	</div>
	<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</body>
</html>