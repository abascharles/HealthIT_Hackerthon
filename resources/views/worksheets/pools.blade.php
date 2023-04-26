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
		<table class="table table-bordered" style="font-size: 10px; padding: 0px;">
			<thead>
				<tr>
					<th> Position </th>
					@foreach($covidPool->worksheet as $worksheet)
						<th> Worksheet {{ $worksheet->id }} </th>
					@endforeach
				</tr>				
			</thead>
			<tbody>
				@foreach($covidPool->pool_sample as $pool_sample)
					<tr>
						<td> {{ $pool_sample->position + 2 }} </td>
						@foreach($pool_sample->sample as $sample)
							<td> 
								S ID - {{ $sample->id }} W No - {{ $sample->worksheet_id }} <br />
								Identifier - {{ $sample->patient->identifier ?? '' }} {{ $sample->patient->patient_name ?? '' }} <br />
							</td>
						@endforeach
					</tr>
				@endforeach				
			</tbody>
		</table>
	</div>
</body>

<script src="{{ asset('vendor/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>

<script type="text/javascript">
	
	@isset($print)
    $(document).ready(function(){
    	window.print();
    });
    @endisset
</script>
</html>