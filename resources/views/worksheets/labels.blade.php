<html>
<head>
	
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/dist/css/bootstrap.css') }}" />
	<link rel="stylesheet" type="text/css" href="{{ asset('css/worksheet_style.css') }}" media="screen" />


	<style type="text/css">
	    .breakhere {page-break-before: always}
	</style> 
</head>
<!-- <body onLoad="JavaScript:window.print();"> -->
<body >
	<div class="container">
		@foreach($samples as $key => $sample)
			@if(in_array(env('APP_LAB'), [400]))
				@if(($key % 2) == 0)
				<table class="table table-borderless"><tr>
				@endif

				<td>
					<div align="left">
						<span style="font-size: 8px;"> P.ID: {{ $sample->patient->identifier ?? $sample->patient }} </span>	
						<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($sample->barcode, 'C128') }}" alt="barcode" height="30" width="80"  />
						<br />

						<span style="font-size: 11px;"> Lab ID: {{ $sample->id }} </span>		
					</div>			
				</td>


				@if(($key % 2) == 1)
				</tr></table>
				<div class="breakhere"></div>
				@endif

				@if(($key % 2) == 0 && $loop->last)

				<td>
					<div align="left">
						<span style="font-size: 8px;"> P.ID: {{ $sample->patient->identifier ?? $sample->patient }} </span>	
						<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($sample->barcode, 'C128') }}" alt="barcode" height="30" width="80"  />
						<br />
						<span style="font-size: 11px;"> Lab ID: {{ $sample->id }} </span>		
					</div>			
				</td>
				</tr></table>
				@endif



			@else
				<div class="row" align="center">
					@if(in_array(env('APP_LAB'), [2,5,9]))								
						<span style="font-size: 12px;">
							P ID: {{ $sample->patient->identifier ?? $sample->patient }} <br />
						</span>
					@endif
					<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($sample->barcode, 'C128') }}" alt="barcode" 
					@if(in_array(env('APP_LAB'), [500]))
						height="50" width="250"
					@else
						height="30" width="80"
					@endif
				   />
					<br />
					<span style="font-size: 12px;">L. ID: {{ $sample->id }} @if(in_array(env('APP_LAB'), [2,9])) ({{ ++$i }})  @endif </span>
				</div>

				@if(!$loop->last)
					<div class="breakhere"></div>
				@endif

			@endif

		@endforeach	
	</div>
</body>

<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script type="text/javascript">
	
    $(document).ready(function(){
    	window.print();
    });
</script>
</html>