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

			@foreach($samples as $row => $sample)
				<?php $i = $row + 8; ?>

				@include('shared/dr_worksheet_sample', ['sample' => $sample, 'row' => $row, 'column' => 1])

				@isset($samples[$i])
					@include('shared/dr_worksheet_sample', ['sample' => $samples[$i], 'row' => $row, 'column' => 2])
				@endisset

				@break($row == 7)

			@endforeach
				
		</table>
	</div>
	<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</body>
</html>