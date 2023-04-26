<p>
	Hello {{ $drSample->facility->name }},

	<br />
	<br />

	Please find attached DR Results for Patient {{ $drSample->patient->patient }} that was received on {{ $drSample->my_date_format('datereceived') }}. 
	
	<br />
	<br />

	Any pending results are still being processed and will be sent to you once they are ready.

	<br />
	<br />

	@include('emails.lab_signature')

</p>