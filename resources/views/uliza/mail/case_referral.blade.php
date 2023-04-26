<p>
	Dear Sir/Madam,
</p>
<p>
	The above clinical case summary {{ $uliza_clinical_form->subject_identifier ?? '' }} has been referred to you for review and feedback.
</p>
<p>
	<a href="{{ url('uliza-review/create/' . $uliza_clinical_form->id) }}">Click here</a> to provide your feedback to the Regional TWG.
</p>
<p>
	Kind Regards, <br />
	Uliza-NASCOP Admin
</p>
<p>
	<i> Please do not respond to the message, it is auto-generated. </i>
</p>