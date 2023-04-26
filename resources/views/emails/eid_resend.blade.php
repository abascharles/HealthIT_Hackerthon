<p>

	Hello {{ $batch->facility->name }},

	<br />
	<br />

	Please find attached {{ $type }} Results for Batch No {{ $batch->id }} with {{ $batch->sample->count() }} samples that were received on {{ $batch->my_date_format('datereceived') }}. 

	<br />
	<br />

	Kindly ignore the previous results you received for this Batch Number. 

	<br />
	<br />

	The results were erronously released by the system before conversion to copies/ml was done by the Lab.

	<br />
	<br />

	Kindly utilize the attached copy for any decision making as pertains patient management.

	<br />
	<br />

	The correct results are also accessible via NASCOP or CPGH Facility Log In.

	<br />
	<br />

	We sincerely apologize for any inconvenience caused as a result of this.

	<br />
	<br />

	Any pending results are still being processed and will be sent to you once they are ready.
	
	<br />
	<br />

	------------------------------------------------------------------------------------------ 

	<br />
	<br />

	Many Thanks.

	<br />
	<br />

	Development Team.

	<br />
	<br />

	------------------------------------------------------------------------------------------

	<br />  
	<br />  

	This email was automatically generated. Please do not respond to this or it will be ignored.

	<br />
	<br />

	@if($batch->site_entry != 2)
		Regards, <br />
		{{ $batch->lab->labname }} <br />
		{{ $batch->lab->name }}  <br />
		{{ $batch->lab->lablocation }} <br />
		{{ $batch->lab->labtel1 }}  <br />
		{{ $batch->lab->labtel2 }}  <br />
		{{ $batch->lab->email }}  <br />
	@endif


</p>