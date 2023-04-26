<html>

<head>
    <!-- <link rel="stylesheet" href="{{ public_path('vendor/bootstrap/dist/css/bootstrap.css') }}" />	 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
</head>


<body >
	<div class="container-fluid">
	<div class="row">

	<div class="col-md-12" id="my-vue-instance">
		<div class="panel panel-default border-secondary mb-4">
			<div class="text-center">
				<img class="rounded mx-auto d-block img-responsive mt-1"  height="161" width="160" src="{{ public_path('uliza_nascop/logo.jpg') }}">
			</div>
			<div class="panel-body text-secondary">
				<h5 class="card-title text-center">MINISTRY OF HEALTH</h5>
				<h5 class="card-subtitle mb-2 text-muted text-center">NATIONAL AIDS AND STI CONTROL PROGRAMME</h5>
				<p class="card-text text-center">CLINICAL SUMMARY FORM</p>
			
				<form autocomplete="off" @submit.prevent="update" novalidate="" id="myClinicalForm">
				  
					<div class="form-row mb-3">
						<div class="col-md-2 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_name">
									Facility:
								</span>
							</div>
						</div>
						<input type="text" class="form-control" value="{{ $ulizaClinicalForm->view_facility->facilitycode ?? '' }} - {{ $ulizaClinicalForm->view_facility->name ?? '' }}">
				    </div>
				  
					<div class="form-row mb-3">
						<div class="col-md-7 input-group required">
						    <div class="input-group-prepend">
								<span class="input-group-text" id="cccno">Patient’s CCC No:
									<small>(Do not write name)</small>									
								</span>
						    </div>
						    <input class="form-control" type="text" value="{{ $ulizaClinicalForm->cccno }}">
						</div>
						<div class="col-md-5 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text" id="reporting_date">
									Case Reporting Date:									
								</span>
							</div>
							<input class="form-control" required value="{{ $ulizaClinicalForm->reporting_date }}">
						</div>
				    </div>
				  
					<div class="form-row mb-3">
						<div class="col-md-2">
							Patient Details
						</div>
						<div class="col-md-10">
						  
							<div class="form-row mb-3">
								<div class="col-md-6 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" id="dob">
											Date of Birth:											
										</span>
									</div>
									<input class="form-control date initial_fields" v-model="myForm.dob" name="dob" required value="{{ $ulizaClinicalForm->dob }}">
								</div>
								<div class="col-md-6 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" id="artstart_date">
											ART Start Date:											
										</span>
									</div>
									<input class="form-control" value="{{ $ulizaClinicalForm->artstart_date }}">
								</div>
							</div>
					  
							<div class="form-row">
								<div class="col-md-4 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" for="gender">
											Gender:											
										</span>
									</div>
									<input class="form-control" value="{{ $ulizaClinicalForm->gender }}">
								</div>
						
								<div class="col-md-4 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" id="curr_weight">
											Current Weight (Kg):											
										</span>
									</div>
									<input class="form-control" type="number" value="{{ $ulizaClinicalForm->curr_weight }}">
								</div>
								<div class="col-md-4 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" id="height">
											Height (cm):											
										</span>
									</div>
									<input class="form-control" value="{{ $ulizaClinicalForm->height }}">
								</div>
							</div>
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-12 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text text-left" id="clinician_name">
									Clinician’s Name:									
								</span>
							</div>
							<input class="form-control" value="{{ $ulizaClinicalForm->clinician_name }}">
						</div>
					</div>
				  
					<div class="form-row">
						<div class="col-md-12">
							You can add three (3) email addresses by separating them with either the space bar, comma, semi-colon, tab key or return/enter key.
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-12 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_email">
									Facility Email Address:									
								</span>
							</div>
							<input class="form-control" value="{{ $ulizaClinicalForm->facility_email }}">
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-5 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_tel">
									Facility Tel No:									
								</span>
							</div>
							<input class="form-control" value="{{ $ulizaClinicalForm->facility_tel }}">
						</div>
					</div>
				  
				  
					<div class="form-row mb-3 required">
						<div class="col-md-12 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_tel">
									Primary reason for this consultation:									
								</span>
							</div>
							<input class="form-control" value="{{ $ulizaClinicalForm->get_prop_name($reasons, 'primary_reason') }}">
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Clinical Evaluation: history, physical, diagnostics, working diagnosis:(excluding the information in the table below)
						</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="3">{{ $ulizaClinicalForm->clinical_eval }}</textarea>
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-12 card">
							<div class="card-header">
								Clinical Evaluation: history, physical, diagnostics, working diagnosis (excluding the information in the table below Complete the table below chronologically, including all ART regimens and laboratory results (and any previous history available for transfer-in patients)
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-12">
										<table class="table data-table">
											<thead>
												<tr>
													<th class="index-column-header" scope="col">#</th>
													<th class="column-header" scope="col"> Date (yyyy-mm-dd) </th>
													<th class="column-header" scope="col"> CD4 </th>
													<th class="column-header" scope="col"> HB </th>
													<th class="column-header" scope="col"> CrCl/ eGFR </th>
													<th class="column-header" scope="col"> Viral Load </th>
													<th class="column-header" scope="col"> Weight </th>
													<th class="column-header" scope="col"> ARV Regimen </th>
													<th class="column-header" scope="col"> Reason for Switch </th>
													<th class="column-header" scope="col"> New OI </th>
												</tr>
											</thead>
											<tbody>
												@foreach($ulizaClinicalForm->visit as $visit)
													<tr>
														<td> {{ $loop->index+1 }} </td>
														<td> {{ $visit->clinicvisitdate }} </td>
														<td> {{ $visit->cd4 }} </td>
														<td> {{ $visit->hb }} </td>
														<td> {{ $visit->crclegfr }} </td>
														<td> {{ $visit->viral_load }} </td>
														<td> {{ $visit->weight_bmi }} </td>
														<td> {{ $visit->arv_regimen }} </td>
														<td> {{ $visit->reason_switch }} </td>
														<td> {{ $visit->new_oi }} </td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-12 input-group required">
							<p class="font-weight-bold"> Adherence and Treatment Failure Evaluation </p>
						</div>
					</div>
				  
				  
					<div class="form-group required row">
						<label class="col-md-4 col-form-label">
							Number of adherence counseling/assessment sessions done in the last 3-6 months:							
						</label>
						<div class="col-md-8">
							<input class="form-control requirable" v-model="myForm.no_adherance_counseling" name="no_adherance_counseling" required="required" type="number" value="{{ $ulizaClinicalForm->clinical_eval }}">
						</div>
					</div>
				  
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Number of home visits conducted in last 3-6 months, and findings:
						</label>
						<div class="col-md-8">
						  <input class="form-control" value="{{ $ulizaClinicalForm->no_homevisits }}">
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Support structures (e.g. treatment buddy, support group attendance, caregivers) in place for this patient?
						</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="4">{{ $ulizaClinicalForm->support_structures }}</textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Evidence of adherence concerns (e.g. missed appointments, pill counts?):
						</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="4">{{ $ulizaClinicalForm->adherence_concerns }}</textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Number of DOTS done in last 3-6 months:</label>
						<div class="col-md-8">
							<input class="form-control" value="{{ $ulizaClinicalForm->no_dotsdone }}">
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Likely root cause/s of poor adherence, for this patient (e.g. stigma, disclosure, side effects, alcohol or other drugs, mental health issues, caregiver changes, religious beliefs, inadequate preparation, etc):
						</label>
						<div class="col-md-8">
						  <textarea class="form-control" rows="4">{{ $ulizaClinicalForm->likely_rootcauses }}</textarea>
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-12 input-group required">
							<p class="font-weight-bold"> Evaluation for other causes of treatment failure, e.g. </p>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">• Inadequate dosing/dose adjustments (particularly for children)::</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="4">{{ $ulizaClinicalForm->inadequate_dosing }}</textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">• Drug-drug interactions:</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="4">{{ $ulizaClinicalForm->drug_interactions }}</textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">• Drug-food interactions:</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="4">{{ $ulizaClinicalForm->food_interactions }}</textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">• Impaired absorption (e.g. chronic severe diarrhea):</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="4">{{ $ulizaClinicalForm->impaired_absorption }}</textarea>
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-12 input-group required">
							<p class="font-weight-bold"> Other Relevant ART History. </p>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Comment on treatment interruptions, if any:</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="4">{{ $ulizaClinicalForm->treatment_interruptions }}</textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Has Drug Resistance/Sensitivity Testing been done for this patient? If yes, state date done and attach the detailed results.
						</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="4">{{ $ulizaClinicalForm->drt_testing }}</textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Has facility multidisciplinary team discussed the patient’s case?. If yes, comment on date, deliberations and recommendations.
							<br />
							(Indicate how treatment failure was established and confirmed, proposed regimen and dosage, current source of drugs if patient already on 3rd line). If yes, state date done and attach the detailed results:
						</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="6">{{ $ulizaClinicalForm->mdt_discussions }}</textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							MDT members who participated in the case discussion (names and titles)
						</label>
						<div class="col-md-8">
							<textarea class="form-control" rows="6">{{ $ulizaClinicalForm->mdt_members }}</textarea>
						</div>
					</div>
				  
					<div class="mb-3 float-centre"></div>
				</form>
			</div>
		</div>

		
	</div>
	
	</div>

	@if(isset($completed))
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default border-secondary mb-4">
					<div class="text-center">
						<h6 class="mb-0 text-white">Additional Information Form</h6>
					</div>

					<div class="panel-body text-secondary">
						<table class="table data-table">
							<thead>
								<tr>
									<th> Date </th>
									<th> Requested Info </th>
									<th> Info </th>
								</tr>
							</thead>
							<tbody>
								@foreach($ulizaClinicalForm->additional_info as $additional_info)
									<tr>
										<td> {{ $additional_info->created_at->toFormattedDateString() }} </td>
										<td> {{ $additional_info->requested_info }} </td>
										<td> {{ $additional_info->additional_info }} </td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>			
		</div>

		@foreach($ulizaClinicalForm->feedback as $clinicalFeedback)

			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default border-secondary mb-4">
						<div class="text-center">
							<h6 class="mb-0 text-white">Clinical TWG Feedback Form</h6>
						</div>

						<div class="panel-body text-secondary">
							<div class="form-row mb-3 required">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" for="review_date">Date of Review :</span>
									</div>
									<input class="form-control date" name="review_date" value="{{ $clinicalFeedback->review_date ?? '' }}" required type="text">
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<p class="font-weight-bold">A. Case Summary of consultation</p>
									<br>
									<p class="font-italic">
										(A summary of the clinical consultation and reason for consultation or description of the problem for which consultation
										is sought.) 
										<br>
										Note: this section is primarily to give the TWG reviewers a snap-shot of the patient history. This is a summary of the known information, but not an interpretation/judgment of the management:
									</p>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">Case Summary of consultation:</label>
								<div class="col-md-8">
									<textarea class="form-control" name="casesummary" rows="5">{{ $clinicalFeedback->casesummary ?? null }}</textarea>
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<p class="font-weight-bold">
										B. Observations/Interpretation on summary provided. A summary of observations about the management the patient has received.
									</p>
									<br>
									<p class="font-italic">
										(Should include interpretation of clinical parameters e.g. weight changes or clinical symptoms and presentations, interpretation of laboratory data, radiologic or other investigations, observation of how patient has been managed etc.) <br />
										Note: this section is primarily for teaching purposes for the facility staff, to show the thought process of the TWG reviewer when evaluating the patient history. <br>
									</p>

									<p class="font-weight-bold">
										Comment on what was done well and any apparent gaps in care were. Use bullet points for ease of reading.
									</p>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">Observations/Interpretation on summary provided:</label>
								<div class="col-md-8">
									<textarea class="form-control" name="observationsofsummary" rows="5">{{ $clinicalFeedback->observationsofsummary ?? null }}</textarea>
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<p class="font-weight-bold">C. Recommendations for management:</p>
								</div>
							</div>

							<div class="form-row mb-3 required">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" >Diagnosis:</span>
									</div>
									<input class="form-control date" value="{{ $ulizaClinicalForm->feedback->get_prop_name($reasons, 'diagnosis') }}" >
								</div>
							</div>



							<div class="form-group row">
								<label class="col-md-4 col-form-label">
									Supportive Management: (Includes palliative care, social, psychosocial etc.)
								</label>
								<div class="col-md-8">
									<textarea class="form-control" name="supportivemanagement"  rows="5">{{ $clinicalFeedback->supportivemanagement ?? null }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">Definative Management: (Includes recommended investigations, medicines).</label>
								<div class="col-md-8">
									<textarea class="form-control" name="definativemanagement" rows="5">{{ $clinicalFeedback->definativemanagement ?? null }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">
									Additional Information Required:(Includes recommended investigations, medicines.)
								</label>
								<div class="col-md-8">
									<textarea class="form-control" name="additionalinfo" rows="5">{{ $clinicalFeedback->additionalinfo ?? null }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">NASCOP Comments</label>
								<div class="col-md-8">
									<textarea class="form-control" name="nascop_comments"  rows="5">{{ $clinicalFeedback->nascop_comments ?? null }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">Reviewer Comments</label>
								<div class="col-md-8">
									<textarea class="form-control" name="reviewer_comments"  rows="5">{{ $clinicalFeedback->reviewer_comments ?? null }}</textarea>
								</div>
							</div>


							<div class="form-group row">
								<label class="col-md-4 col-form-label">Technical Advisors Comments</label>
								<div class="col-md-8">
									<textarea class="form-control" name="technical_reviewer_comments"  rows="5">{{ $clinicalFeedback->technical_reviewer_comments ?? null }}</textarea>
								</div>
							</div>


							<div class="form-group row">
								<div class="col-md-6">
									<label class=" col-form-label">
										Case-Summary (Recomendation feedback)
									</label>
								</div>
								<div class="col-md-6">
									<input class="form-control" value="{{ $clinicalFeedback->get_prop_name($recommendations, 'recommendation_id') }}" >
								</div>
							</div>

							{{-- <div class="form-group row" v-if="myForm.recommendation_id == 1 || myForm.recommendation_id == 5">
								<label class="col-md-4 col-form-label">Request for Info</label>
								<div class="col-md-8">
									<textarea class="form-control" name="requested_info"  rows="5"></textarea>
								</div>
							</div> --}}

							<div class="form-group row" v-if="myForm.recommendation_id == 3">
								<div class="col-md-6">
									<label class=" col-form-label">
										Facility Feedback Recomendations
									</label>
								</div>
								<div class="col-md-6">
									<input class="form-control" value="{{ $clinicalFeedback->get_prop_name($feedbacks, 'facility_recommendation_id') }}" >
								</div>
							</div>


						</div>
					</div>
				</div>			
			</div>

		@endforeach

	@endif
	</div>

</body>

<!-- <script src="{{ public_path('vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script> -->
<!-- <script src="{{ public_path('vendor/jquery/dist/jquery.min.js') }}"></script> -->

<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script> -->

</html>