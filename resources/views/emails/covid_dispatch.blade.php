<p>
	@if($quarantine_site)

		Hello {{ $quarantine_site->name ?? '' }},

		<br />
		<br />

		Please find attached individual results for covid-19. 
		
		<br />
		<br />

		Any pending results are still being processed and will be sent to you once they are ready.

		<br />
		<br />

	@else	

		Hello {{ $samples[0]->patient->patient_name ?? '' }},

		<br />
		<br />

		Please find attached individual results for covid-19. 
		
		<br />
		<br />

	@endif

	------------------------------------------------------------------------------------------

	<br />  
	<br />  

	This email was automatically generated. Please do not respond to this or it will be ignored.

	<br />
	<br />

	@include('emails.lab_signature')

</p>