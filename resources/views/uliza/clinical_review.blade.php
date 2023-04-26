@extends('uliza.main_layout')

@section('content')

@if(!auth()->user()->uliza_reviewer)
	<div class="col-md-12">
		<table class="table table-bordered table-hover table-striped">
			<thead>
				<tr>
					<th> Reviewer </th>
					<th> Date </th>
					<th> Case Summary </th>
					<th> Observations </th>
					<th> Diagnosis </th>
					<th> Supportive Management </th>
					<th> Definative Management </th>
					<th> Comments </th>
					<th> Recommendation </th>
					<th> Facility Feedback </th>
					<th> Requested Info </th>
				</tr>
			</thead>
			<tbody>
				@foreach($ulizaClinicalForm->feedback as $formFeedback)
					<tr>
						<td>{{ $formFeedback->user->full_name ?? null }}</td>
						<td>{{ $formFeedback->review_date }}</td>
						<td>{{ $formFeedback->casesummary }}</td>
						<td>{{ $formFeedback->observationsofsummary }}</td>
						<td>{{ $formFeedback->get_prop_name($reasons, 'diagnosis') }}</td>
						<td>{{ $formFeedback->supportivemanagement }}</td>
						<td>{{ $formFeedback->definativemanagement }}</td>
						<td>{{ $formFeedback->reviewer_comments }}</td>
						<td>{{ $formFeedback->get_prop_name($recommendations, 'recommendation_id') }}</td>
						<td>{{ $formFeedback->get_prop_name($feedbacks, 'facility_recommendation_id') }}</td>
						<td>{{ $formFeedback->requested_info }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endif

<div class="col-md-6">
	<div class="card mr-2">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-center p-1 text-white bg-primary rounded box-shadow">
				<div class="text-center">
					<h6 class="mb-0 text-white">Clinical Summary Form</h6>
				</div>
			</div>
			<div class="card my-1">
				<div class="card-body p-2">
					<div class="d-flex justify-content-between align-items-center w-100">
						<button class="btn btn-dark btn-sm" disabled="disabled" type="button">
							<strong>Facility: </strong>{{ $ulizaClinicalForm->facility->name ?? '' }}
						</button>
					</div>
				</div>
			</div>
			<div class="ml-0 px-3" style="max-height:73vh; overflow-y: scroll;">
				<form autocomplete="off" novalidate="" class="disabled_form">
					<div class="form-row mb-3">
						<div class="col-md-7 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_name">Facility Name:</span>
							</div>
							<input aria-describedby="facility_name" class="form-control" value="{{ $ulizaClinicalForm->facility->name ?? '' }}" name="facility_name" readonly="" type="text">
						</div>
						<div class="col-md-5 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="mfl_code">MFL Code:</span>
							</div>
							<input aria-describedby="mfl_code" class="form-control" value="{{ $ulizaClinicalForm->facility->facilitycode ?? '' }}" name="mfl_code" readonly="" type="text">
						</div>
					</div>
					<div class="form-row mb-3">
						<div class="col-md-7 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="cccno">
									CCC No:
								</span>
							</div>
							<input aria-describedby="cccno" class="form-control" value="{{ $ulizaClinicalForm->cccno ?? '' }}" name="cccno" readonly="" type="text">
						</div>
						<div class="col-md-5 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="reporting_date">Case Reporting Date:</span>
							</div>
							<input class="form-control" value="{{ $ulizaClinicalForm->reporting_date ?? '' }}"  name="reporting_date" readonly="">
						</div>
					</div>
					<div class="form-row mb-3">
						<div class="col-md-2">Patient Details</div>
						<div class="col-md-10">
							<div class="form-row mb-3">
								<div class="col-md-6 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="dob">DOB:</span>
									</div>
									<input class="form-control" value="{{ $ulizaClinicalForm->dob ?? '' }}" name="dob" readonly="">
								</div>
								<div class="col-md-6 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="artstart_date">ART Start Date:</span>
									</div>
									<input class="form-control" value="{{ $ulizaClinicalForm->artstart_date ?? '' }}" name="artstart_date" readonly="">
								</div>
							</div>
							<div class="form-row">
								<div class="col-md-4 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" for="gender">Gender:</span>
									</div>
									<input class="form-control" value="{{ $ulizaClinicalForm->gender ?? '' }}" name="gender" readonly="">
								</div>
								<div class="col-md-4 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="curr_weight">Current Weight (Kg):</span>
									</div>
									<input aria-describedby="curr_weight" value="{{ $ulizaClinicalForm->curr_weight ?? '' }}" class="form-control" name="curr_weight" readonly="" type="text">
								</div>
								<div class="col-md-4 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text" id="height">Height (cm):</span>
									</div>
									<input aria-describedby="height" value="{{ $ulizaClinicalForm->height ?? '' }}" class="form-control" name="height" readonly="" type="text">
								</div>
							</div>
						</div>
					</div>
					<div class="form-row mb-3">
						<div class="col-md-12 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text text-left" id="clinician_name">Clinician’s Name:</span>
							</div>
							<input aria-describedby="clinician_name" value="{{ $ulizaClinicalForm->clinician_name ?? '' }}" class="form-control" name="clinician_name" readonly="" type="text">
						</div>
					</div>
					<div class="form-row mb-3">
						<div class="col-md-12 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_email">Facility Email Address:</span>
							</div>
							<input aria-describedby="facility_email" value="{{ $ulizaClinicalForm->facility_email ?? '' }}" class="form-control" name="facility_email" readonly="" type="text">
						</div>
					</div>
					<div class="form-row mb-3">
						<div class="col-md-5 input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_tel">Facility Tel No:</span>
							</div>
							<input aria-describedby="facility_tel" value="{{ $ulizaClinicalForm->facility_tel ?? '' }}" class="form-control" name="facility_tel" readonly="" type="text">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">What is the primary reason for this consultation:</label>
						<div class="col-md-8">
							<textarea class="form-control" value="{{ $ulizaClinicalForm->get_prop_name($reasons, 'primary_reason') }}" name="primary_reason" readonly="" rows="3"></textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Clinical Evaluation: history, physical, diagnostics, working diagnosis:(excluding the information in the table below)
						</label>
						<div class="col-md-8">
							<textarea class="form-control" value="{{ $ulizaClinicalForm->clinical_eval ?? '' }}" name="clinical_eval" readonly="" rows="3"></textarea>
						</div>
					</div>
					<div class="form-row mb-3">

						<div class="col-md-12 card">
							<div class="card-header">
								Clinical Evaluation: history, physical, diagnostics, working diagnosis (excluding the information in the table below Complete the table below chronologically, including all ART regimens and laboratory results (and any previous history available for transfer-in patients)
							</div>
							<div class="card-body">
								<table class="table table-bordered table-hover table-sm m-0 p-0 ng-star-inserted" headertitle="Clinical Visits" id="cases-grid" indexcolumnheader="#" showtitle="false" _nghost-c10="">
									<thead>
										<tr>
											<th scope="col"> # </th>
											<th scope="col"> Date (dd/mm/yyyy) </th>
											<th scope="col"> CD4 </th>
											<th scope="col"> HB </th>
											<th scope="col"> CrCl/ eGFR </th>
											<th scope="col"> Viral Load </th>
											<th scope="col"> Weight </th>
											<th scope="col"> ARV Regimen </th>
											<th scope="col"> Reason for Switch </th>
											<th scope="col"> New OI </th>
										</tr>
									</thead>
									<tbody>
										@isset($ulizaClinicalForm)
										@foreach($ulizaClinicalForm->visit as $key => $visit)
											<tr>
												<td> {{ $key }} </td>
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
										@endisset
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="col-md-12 input-group">
							<p class="font-weight-bold">
								Adherence and Treatment Failure Evaluation
							</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Number of adherence counseling/assessment sessions done in the last 3-6 months:
						</label>
						<div class="col-md-8">
							<input class="form-control" value="{{ $ulizaClinicalForm->no_adherance_counseling ?? '' }}" name="no_adherance_counseling" readonly="" type="number">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Number of home visits conducted in last 3-6 months, and findings:</label>
						<div class="col-md-8">
							<input class="form-control" value="{{ $ulizaClinicalForm->no_homevisits ?? '' }}" name="no_homevisits" readonly="" type="number">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Support structures (e.g. treatment buddy, support group attendance, caregivers) in place for this patient?
						</label>
						<div class="col-md-8">
							<textarea class="form-control" name="support_structures" readonly="" rows="4">{{ $ulizaClinicalForm->support_structures ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Evidence of adherence concerns (e.g. missed appointments, pill counts?):</label>
						<div class="col-md-8">
							<textarea class="form-control" name="adherence_concerns" readonly="" rows="4">{{ $ulizaClinicalForm->adherence_concerns ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Number of DOTS done in last 3-6 months:</label>
						<div class="col-md-8">
							<input class="form-control" value="{{ $ulizaClinicalForm->no_dotsdone ?? '' }}" name="no_dotsdone" readonly="" type="number">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Likely root cause/s of poor adherence, for this patient (e.g. stigma, disclosure, side effects, alcohol or other drugs, mental health issues, caregiver changes, religious beliefs, inadequate preparation, etc):
						</label>
						<div class="col-md-8">
							<textarea class="form-control" name="likely_rootcauses" readonly="" rows="4">{{ $ulizaClinicalForm->likely_rootcauses ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-row mb-3">
						<div class="col-md-12 input-group">
							<p class="font-weight-bold">
								Evaluation for other causes of treatment failure, e.g.
							</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Inadequate dosing/dose adjustments (particularly for children)::</label>
						<div class="col-md-8">
							<textarea class="form-control" name="inadequate_dosing" readonly="" rows="4">{{ $ulizaClinicalForm->inadequate_dosing ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Drug-drug interactions:</label>
						<div class="col-md-8">
							<textarea class="form-control" name="drug_interactions" readonly="" rows="4">{{ $ulizaClinicalForm->drug_interactions ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Drug-food interactions:</label>
						<div class="col-md-8">
							<textarea class="form-control" name="food_interactions" readonly="" rows="4">{{ $ulizaClinicalForm->food_interactions ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Impaired absorption (e.g. chronic severe diarrhea):</label>
						<div class="col-md-8">
							<textarea class="form-control" name="impaired_absorption" readonly="" rows="4">{{ $ulizaClinicalForm->impaired_absorption ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-row mb-3">
						<div class="col-md-12 input-group">
							<p class="font-weight-bold">Other Relevant ART History.</p>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Comment on treatment interruptions, if any:</label>
						<div class="col-md-8">
							<textarea class="form-control" name="treatment_interruptions" readonly="" rows="4">{{ $ulizaClinicalForm->treatment_interruptions ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Has Drug Resistance/Sensitivity Testing been done for this patient? If yes, state date done and attach the detailed results.
						</label>
						<div class="col-md-8">
							<textarea class="form-control" name="drt_testing" readonly="" rows="4">{{ $ulizaClinicalForm->drt_testing ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Has facility multidisciplinary team discussed the patient’s case?. If yes, comment on date, deliberations and recommendations.
							<br>
							(indicate how treatment failure was established and confirmed, proposed regimen and dosage, current source of drugs if patient already on 3rd line). If yes, state date done and attach the detailed results:
						</label>
						<div class="col-md-8">
							<textarea class="form-control" name="mdt_discussions" readonly="" rows="6">{{ $ulizaClinicalForm->mdt_discussions ?? '' }}</textarea>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							MDT members who participated in the case discussion (names and titles)
						</label>
						<div class="col-md-8">
							<textarea class="form-control" name="mdt_members" readonly="" rows="4">{{ $ulizaClinicalForm->mdt_members ?? '' }}</textarea>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>







<div class="col-md-6">

	<div class="card mr-2">
		<div class="card-body">
			<div class="d-flex align-items-center justify-content-center p-1 text-white bg-success rounded box-shadow">
				<div class="text-center">
					<h6 class="mb-0 text-white">Additional Information Form</h6>
				</div>
			</div>
			<div class="card mt-1">
				<div class="card-body">
					<table class="table data-table">
						<thead>
							<tr>
								<!-- <th> (dd/mm/yyyy) Date </th> -->
								<!-- <th> Time </th> -->
								<th> Date </th>
								<th> Requested Info </th>
								<th> Info </th>
								<!-- <th> Actions </th> -->
							</tr>
						</thead>
						<tbody>
							@foreach($ulizaClinicalForm->additional_info as $additional_info)
								<tr>
									<td> {{ $additional_info->created_at->toFormattedDateString() }} </td>
									<td> {{ $additional_info->requested_info }} </td>
									<td> {{ $additional_info->additional_info }} </td>
									<!-- <td>  </td> -->
								</tr>
							@endforeach
						</tbody>
					</table>

					@isset($ulizaAdditionalInfo)
					<form autocomplete="off" class="val-form" method="POST" action="{{ url('uliza-additional-info/'.$ulizaAdditionalInfo->id) }}">
						<!-- <input type="hidden" name="uliza_clinical_form_id" value="{{ $ulizaAdditionalInfo->id }}" > -->
						@csrf
						@method('PUT')
						
						<div class="form-group row">
							<label class="col-md-4 col-form-label">Requested Information:</label>
							<div class="col-md-8">
								<textarea class="form-control" name="requested_info" rows="5" disabled>{{ $ulizaAdditionalInfo->requested_info }}</textarea>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-4 col-form-label">Additional Information:</label>
							<div class="col-md-8">
								<textarea class="form-control" name="additional_info" rows="5" required id="additional_info_field"></textarea>
							</div>
						</div>
						<div class="card my-1 ml-2">
							<div class="card-body p-2">
								<div class="d-flex justify-content-end align-items-center w-100">
									<button class="btn btn-warning btn-sm" type="submit">
										Submit Additional Information
									</button>
								</div>
							</div>
						</div>
					</form>
					@endisset
				</div>
			</div>
			<br />
			<div class="card mr-2">
				<div class="card-body">
					<form autocomplete="off" class="val-form" method="POST" action="{{ url('uliza-review') }} ">
						@csrf
						<div class="d-flex align-items-center justify-content-center p-1 text-white bg-success rounded box-shadow">
							<div class="text-center">
								<h6 class="mb-0 text-white">Clinical TWG Feedback Form</h6>
							</div>
						</div>
						@if(!$view)
						<div class="card my-1 ml-2">
							<div class="card-body p-2">
								<div class="d-flex justify-content-end align-items-center w-100">
									<button class="btn btn-warning btn-sm" type="submit">
										Submit Review
									</button>
								</div>
							</div>
						</div>
						@endif

						<div class="ml-2 px-3" style="overflow-y: scroll; max-height: 73vh;">						

							<input type="hidden" name="uliza_clinical_form_id" value="{{ $ulizaClinicalForm->id }}" >

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" id="facility_name">Facility Name:</span>
									</div>
									<input aria-describedby="facility_Name" class="form-control" value="{{ $ulizaClinicalForm->facility->name ?? '' }}" name="facility_name" readonly="" disabled type="text">
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" id="cccno">Patient's CCCNo:</span>
									</div>
									<input aria-describedby="cccno" value="{{ $ulizaClinicalForm->cccno ?? '' }}" disabled class="form-control" name="cccno" readonly="" type="text">
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" id="clinician_name">Name of Clinician Consulting:</span>
									</div>
									<input aria-describedby="clinician_name" class="form-control" value="{{ $ulizaClinicalForm->clinician_name ?? '' }}" name="clinician_name" readonly="" disabled type="text">
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" id="facility_email">Contact details of clinician (email) :</span>
									</div>
									<input aria-describedby="facility_email" value="{{ $ulizaClinicalForm->facility_email ?? '' }}" disabled class="form-control" name="facility_email" readonly="" type="text">
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" id="facility_tel">
											Contact details of clinician (telephone):
										</span>
									</div>
									<input aria-describedby="facility_tel" value="{{ $ulizaClinicalForm->facility_tel ?? '' }}" disabled class="form-control" name="facility_tel" readonly="" type="text">
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" id="artstart_date">ART Start Date :</span>
									</div>
									<input aria-describedby="artstart_date" value="{{ $ulizaClinicalForm->artstart_date ?? '' }}" disabled class="form-control" name="artstart_date" readonly="" type="text">
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" id="nat_no">NASCOP's NAT-No :</span>
									</div>
									<input aria-describedby="nat_no" value="{{ $ulizaClinicalForm->nat_no ?? '' }}" disabled class="form-control" name="nat_no" readonly="" type="text">
								</div>
							</div>

							<div class="form-row mb-3 required">
								<div class="col-md-12 input-group">
									<div class="input-group-prepend">
										<span class="input-group-text text-left" for="review_date">Date of Review :</span>
									</div>
									<input class="form-control date" name="review_date" value="{{ $userFeedback->review_date ?? '' }}" required type="text">
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
									<textarea class="form-control" name="casesummary" rows="5">{{ $userFeedback->casesummary ?? null }}</textarea>
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
									<textarea class="form-control" name="observationsofsummary" rows="5">{{ $userFeedback->observationsofsummary ?? null }}</textarea>
								</div>
							</div>

							<div class="form-row mb-3">
								<div class="col-md-12 input-group">
									<p class="font-weight-bold">C. Recommendations for management:</p>
								</div>
							</div>

							<div class="form-row mb-3 required">
								<label class="col-md-12">Diagnosis:</label>
							</div>

							<div class="form-row form-group mb-3">
								@foreach($reasons as $reason)
									<div class="col-md-6">
										<input class="form-check-input ml-1" name="diagnosis" required="required" type="radio" id="diagnosis_A{{ $reason->id }}" value="{{ $reason->id }}"  v-model="myForm.diagnosis">
										<label class="form-check-label ml-5" for="diagnosis_A{{ $reason->id }}">{{ $reason->name }}</label>
									</div>
								@endforeach
							</div>


							<div class="form-group row">
								<label class="col-md-4 col-form-label">
									Supportive Management: (Includes palliative care, social, psychosocial etc.)
								</label>
								<div class="col-md-8">
									<textarea class="form-control" name="supportivemanagement"  rows="5">{{ $userFeedback->supportivemanagement ?? null }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">Definative Management: (Includes recommended investigations, medicines).</label>
								<div class="col-md-8">
									<textarea class="form-control" name="definativemanagement" rows="5">{{ $userFeedback->definativemanagement ?? null }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">
									Additional Information Required:(Includes recommended investigations, medicines.)
								</label>
								<div class="col-md-8">
									<textarea class="form-control" name="additionalinfo" rows="5">{{ $userFeedback->additionalinfo ?? null }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">NASCOP Comments</label>
								<div class="col-md-8">
									<textarea class="form-control" name="nascop_comments"  rows="5">{{ $userFeedback->nascop_comments ?? null }}</textarea>
								</div>
							</div>

							<div class="form-group row">
								<label class="col-md-4 col-form-label">Reviewer Comments</label>
								<div class="col-md-8">
									<textarea class="form-control" name="reviewer_comments"  rows="5">{{ $userFeedback->reviewer_comments ?? null }}</textarea>
								</div>
							</div>

							@if(auth()->user()->user_type_id > 102)

							<div class="form-group row">
								<label class="col-md-4 col-form-label">Technical Advisors Comments</label>
								<div class="col-md-8">
									<textarea class="form-control" name="technical_reviewer_comments"  rows="5">{{ $userFeedback->technical_reviewer_comments ?? null }}</textarea>
								</div>
							</div>

							@endif

							<div class="form-group row">
								<div class="col-md-6">
									<label class=" col-form-label">
										Case-Summary (Recomendation feedback)
									</label>
								</div>
								<div class="col-md-6">
									<select class="custom-select" name="recommendation_id" required v-model="myForm.recommendation_id">
										<option selected="">Choose...</option>
										@foreach($recommendations as $recommendation)
											<option value="{{ $recommendation->id }}"  > {{ $recommendation->name }} </option>
										@endforeach
									</select>
								</div>
							</div>

							<div class="form-group row" v-if="myForm.recommendation_id == 1 || myForm.recommendation_id == 5">
								<label class="col-md-4 col-form-label">Request for Info</label>
								<div class="col-md-8">
									<textarea class="form-control" name="requested_info"  rows="5"></textarea>
								</div>
							</div>

							<div class="form-group row" v-if="myForm.recommendation_id == 3">
								<div class="col-md-6">
									<label class=" col-form-label">
										Facility Feedback Recomendations
									</label>
								</div>
								<div class="col-md-6">
									<select class="custom-select" name="facility_recommendation_id" v-model="myForm.facility_recommendation_id">
										<option selected="">Choose...</option>
										@foreach($feedbacks as $feedback)
											<option value="{{ $feedback->id }}"  > {{ $feedback->name }} </option>
										@endforeach
									</select>
								</div>
							</div>


							@if(!auth()->user()->uliza_reviewer)
							<div class="row ng-star-inserted" id="reviewers_row" v-if="myForm.recommendation_id == 2">
								<div class="col-md-12">
									<h4> Choose Reviewers: </h4>
								</div>

								@foreach($reviewers as $reviewer)
									<div class="col-md-6">
										<input class="form-check-input" name="reviewers[]" type="checkbox" value="{{ $reviewer->id }}" id="user_{{ $reviewer->id }}" @if(in_array($reviewer->id, $ulizaClinicalForm->reviewers)) checked  @endif >
										<label class="form-check-label" for="user_{{ $reviewer->id }}">
											{{ $reviewer->full_name }}                  
										</label>
									</div>
								@endforeach
							</div>
							@endif
						</div>
						
						@if(!$view)
						<div class="card my-1 ml-2">
							<div class="card-body p-2">
								<div class="d-flex justify-content-end align-items-center w-100">
									<button class="btn btn-warning btn-sm" type="submit">
										Submit Review
									</button>
								</div>
							</div>
						</div>
						@endif
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script type="text/javascript">
	$(document).ready(function(){

		@if($view)
			$(".form-control").attr("readonly", true);
			$(".custom-select").attr("readonly", true);
			$(".form-check-input").attr("readonly", true);

			$("#additional_info_field").removeAttr("readonly");
		@endif

        var vm = new Vue({
        	el: "#my-vue-instance",
        	data: {
        		successful_submission: null,
        		myForm: {
        			recommendation_id: "{{ $userFeedback->recommendation_id ?? null }}",
        			diagnosis: "{{ $userFeedback->diagnosis ?? null }}",
        			facility_recommendation_id: "{{ $userFeedback->facility_recommendation_id ?? null }}",
        			reviewer_id: "{{ $ulizaClinicalForm->reviewer_id ?? null }}",
        		},
        	},
        });
    });
</script>

    @component('/uliza/forms/scripts')
	@endcomponent
@endsection