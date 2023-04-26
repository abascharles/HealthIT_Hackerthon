<p>

	Hello Edarp Team,

	<br />
	<br />

	{{-- 
	@if($data->samples->today == 0) No @else {{ $data->samples->today }} @endif
	SAMPLES WERE UPLOADED TODAY {{ date('Y-m-d') }} ON THE NASCOP STAGING ENVIRONMENT
	
	<br />
	<br />

	@if($data->samples->total == 0)
	There are also no 
	@else
	Please Note, there are still {{ $data->samples->total }} 
	@endif
	Samples previously uploaded that are pending approval on the NASCOP staging environment.
	<br />
	<br />

	------------------------------------------------------------------------------------------ 

	<br />
	<br />

	Click the link below to access and confirm these samples:

	<br />
	<br />

	<a href="{{ $data->url }}">NASCOP Staging system</a> 

	<br />
	<br />

	------------------------------------------------------------------------------------------
	--}}

	{{ $data->samples->total }} samples were uploaded on {{ date('d-M-Y', strtotime('-1 day')) }}

	<br />  
	<br />  

	This email was automatically generated. Please do not respond to this or it will be ignored.

	<br />
	<br />

	EID/VL Support Team

</p>