<p>
	Dear Sir/Madam,
</p>
<p>
	Please note that we have received your clinical summary {{ $uliza_clinical_form->subject_identifier ?? '' }} for review.
</p>
<p>
	Additional information is however required before the request can be reviewed.
</p>
<p>
	<a href="{{ url('uliza-additional-info/' . $uliza_clinical_form->id) }}">Click here</a> to fill the additional information required.
</p>
<p>
	Kind Regards, <br />
	Uliza-NASCOP Admin
</p>
<p>
	<i> Please do not respond to the message, it is auto-generated. </i>
</p>