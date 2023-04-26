<td > 
	@if($sample->parentid)
		<div align='right'  style='background-color:#FAF156'> 
			<small>R ({{ $sample->parentid }})</small>
		</div>
	@endif
	{{--<span class='style7'>Sample: {{ $sample->patient->patient }}  {{$parent}}</span><br>
						<b>Facility:</b> {{ $sample->batch->facility->name }} <br />
						<b>Sample ID:</b> {{ $sample->patient->patient }} <br />
						<b>Date Collected:</b> {{ $sample->my_date_format('datecollected') }} <br />--}}
	<span class='style7'
	@if(env('APP_LAB') == 5)
		style="font-size: 12px;" 
	@endif
	>
		<?php
			if(!$sample->batch){
				unset($sample->batch);
			}
		?>

		<b>{{ $sample->batch->facility->name ??  $sample->batch->facility_id ?? $sample->patient->facility->name ?? '' }}</b> 
		@if(isset($covid) && $sample->patient->quarantine_site_id)
			<b> {{ $sample->patient->quarantine_site->name ?? '' }} </b> <br />
		@endif
		{{ $sample->patient->patient ?? $sample->patient->identifier ?? '' }}
		@if(in_array(env('APP_LAB'), [3, 5]) && get_class($worksheet) == "App\Viralworksheet")
			<br /> {{ $sample->comments }}
		@endif
		 
			@isset( $sample->recency_number)
			<br /> Rec_No - {{ $sample->recency_number ?? '' }} 	
			@endisset
		@if(env('APP_LAB') != 5 and env('APP_LAB') != 3)			
			<br /> Date Collected - {{ $sample->my_date_format('datecollected') }} 
		@endif 
		@if(env('APP_LAB') == 3 and !isset($covid)) 
		<br /> Date Received - {{ $sample->batch->my_date_format('datereceived') ?? ''}} 
		@endif 
		@if(env('APP_LAB') == 8)
			<br /> Label ID - {{ $sample->label_id }}
		@endif

		@if(env('APP_LAB') == 2 && get_class($worksheet) == "App\Worksheet")
			<br /> Date Received - {{ $sample->batch->my_date_format('datereceived') }} 
			<br /> Batch Number - {{ $sample->batch_id }} 
		@endif
		@if(env('APP_LAB') == 4 && $sample->parentid)
			<br /> Previous Worksheet - {{ $sample->prev_worksheet }}
		@endif
		@if(isset($covid) || session('testingSystem') == 'HPV')
			<br /> Name - {{ $sample->patient->patient_name }}
			@if(env('APP_LAB') == 1 && session('testingSystem') != 'HPV')
				<br /> Kemri ID - {{ $sample->kemri_id }}
			@endif
		@endif
	</span>
	<br />

		&nbsp;&nbsp;&nbsp;

		<img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($sample->barcode, 'C128') }}" alt="barcode" height="30" width="80"  />

	<br />
	{{ $sample->id }}

	@if(in_array(env('APP_LAB'), [9, 2, 1, 3, 5]))
		@if(env('APP_LAB') == 9 or env('APP_LAB') == 2 or env('APP_LAB') == 1 or env('APP_LAB') == 3 or env('APP_LAB') == 5 )
			@if(get_class($worksheet) == "App\Viralworksheet")
				- ({{ $i+3 }})
			@else
				- ({{ $i+2 }})
			@endif
		@else
			- ({{ $i }})
		@endif
	@endif

</td>