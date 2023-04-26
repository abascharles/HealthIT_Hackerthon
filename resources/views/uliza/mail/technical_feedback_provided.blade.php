<p>
	Dear Sir/Madam,
</p>
<p>
	The above clinical case summary {{ $uliza_clinical_form->subject_identifier ?? '' }}  has reviewed by the technical reviewer and feedback provided.
</p>
<p>
	<a href="{{ url('uliza-review/create/' . $uliza_clinical_form->id) }}">Click here</a> to view the feedback.
</p>
<p>
	Kind Regards, <br />
	Uliza-NASCOP Admin
</p>
<p>
	<i> Please do not respond to the message, it is auto-generated. </i>
</p>