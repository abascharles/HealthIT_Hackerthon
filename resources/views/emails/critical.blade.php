<p>

	Hello {{ $facility->name }},

	<br />
	<br />
	
	We would like to draw your attention to the attached {{ $tests[$type]['name'] }} Critical Results for prompt patient management. Please find attached results dispatched on {{ $datedispatched }}. 
	
	<br />
	<br />

	Any pending results are still being processed and will be sent to you once they are ready.

	<br />
	<br />

	You can also access this results and any other results via the link below on NASCOP
	
	<br />
	<br />

	------------------------------------------------------------------------------------------ 

	<br />
	<br />

	<a href="https://eiddash.nascop.org">NASCOP</a> 

	<br />
	<br />

	------------------------------------------------------------------------------------------

	<br />  
	<br />  

	This email was automatically generated. Please do not respond to this or it will be ignored.

	<br />
	<br />

	Regards, <br />
	{{ $lab->labname }} <br />
	{{ $lab->name }}  <br />
	{{ $lab->lablocation }} <br />
	{{ $lab->labtel1 }}  <br />
	{{ $lab->labtel2 }}  <br />
	{{ $lab->email }}  <br />


</p>