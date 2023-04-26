<p>
	Good day {{ $sample->patient->facility->name ?? '' }},  <br />
	The patient {{ $sample->patient->patient }} has qualified for a drug resistance test. Find attached a PDF with the form required to be filled in. Please send a plasma sample. You can also <a href="{{ $form_url }}">Click Here</a> to fill in the form online.
</p>