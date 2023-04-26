<p>
	Dear Sir/Madam,
</p>
<p>
	Please note that we have received your clinical summary {{ $uliza_clinical_form->subject_identifier ?? '' }} with additional information requested has been submitted for review.
</p>
<p>
	<a href="{{ url('uliza-review/create/' . $uliza_clinical_form->id . '/edit') }}">Click here</a> to review the case.
</p>
<p>
	Kind Regards, <br />
	Uliza-NASCOP Admin
</p>
<p>
	<i> Please do not respond to the message, it is auto-generated. </i>
</p>