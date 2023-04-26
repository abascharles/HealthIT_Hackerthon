<p>
	Dear Sir/Madam,
</p>
<p>
	Clinical summary {{ $uliza_clinical_form->subject_identifier ?? '' }} has been submitted for review.
</p>
<p>
	<a href="{{ url('/uliza-review/create/' . ($uliza_clinical_form->id ?? null)) }}">Click here</a> to review the case.
</p>
<p>
	Kind Regards, <br />
	Uliza-NASCOP Admin
</p>
<p>
	<i> Please do not respond to the message, it is auto-generated. </i>
</p>