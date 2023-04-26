@extends('uliza.uliza_layout')

@section('content')

	<div class="col-md-12">
		<div class="card my-2">
			<div class="card-header">
				<div class="row">
					<div class="col-md-4">
						<img src="{{ asset('uliza_nascop/uliza-logo.png') }}">						
					</div>
					<div class="col-md-8">
						<b>All Fields are Mandatory i.e. Don't Leave Blanks; Fill N/A where Response is not applicable! <br></b>
						<b>To Add a Clinical Visit, click the button labelled "Add Clinical Visit" then fill in the details and submit - <em>Repeat the same to add another visit</em>.</b>
						<b>In-Case you encounter any problem while filling the Clinical Summary Form Online: Call the Toll-Free Number 0800724848 for Assistance.</b>
					</div>					
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-warning" role="alert">
							To be able to save your work as a draft, you must at least fill in all values from the beginning to the variable requesting for the facility email.
						</div>
					</div>
				</div>				
			</div>			
		</div>
	</div>

	<div class="col-md-12" id="my-vue-instance">
		<div class="card border-secondary mb-4">
			<img class="rounded mx-auto d-block img-responsive mt-1" height="161" src="{{ asset('uliza_nascop/logo.jpg') }}" width="160">
			<div class="card-body text-secondary">
				<h5 class="card-title text-center">MINISTRY OF HEALTH</h5>
				<h5 class="card-subtitle mb-2 text-muted text-center">NATIONAL AIDS AND STI CONTROL PROGRAMME</h5>
				<p class="card-text text-center">CLINICAL SUMMARY FORM</p>
			
				<form autocomplete="off" @submit.prevent="update" novalidate="" id="myClinicalForm">
				  
					<div class="form-row mb-3">
						<div class="col-md-2 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_name">
									Facility:
									<div style='color: #ff0000; display: inline;'>*</div>
								</span>
							</div>
						</div>
						<div class="col-md-10">
	                        <select class="form-control initial_fields" v-model="myForm.facility_id" required name="facility_id" id="facility_id">
	                        	@isset($ulizaClinicalForm)
	                        		<option value="{{ $ulizaClinicalForm->facility_id }}" selected>
	                        			{{ $ulizaClinicalForm->view_facility->facilitycode }} - {{ $ulizaClinicalForm->view_facility->name }} 
	                        		</option>
	                        	@endisset
	                        </select>						
						</div>
				    </div>
				  
					<div class="form-row mb-3">
						<div class="col-md-7 input-group required">
						    <div class="input-group-prepend">
								<span class="input-group-text" id="cccno">Patient’s CCC No:
									<small>(Do not write name)</small>
									<div style='color: #ff0000; display: inline;'>*</div>
								</span>
						    </div>
						    <input aria-describedby="cccno" class="form-control initial_fields" v-model="myForm.cccno" maxlength="10" minlength="10" name="cccno" required type="text">
						</div>
						<div class="col-md-5 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text" id="reporting_date">
									Case Reporting Date:
									<div style='color: #ff0000; display: inline;'>*</div>
								</span>
							</div>
							<input class="form-control date initial_fields" v-model="myForm.reporting_date" name="reporting_date" required>
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
											<div style='color: #ff0000; display: inline;'>*</div>
										</span>
									</div>
									<input class="form-control date initial_fields" v-model="myForm.dob" name="dob" required>
								</div>
								<div class="col-md-6 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" id="artstart_date">
											ART Start Date:
											<div style='color: #ff0000; display: inline;'>*</div>
										</span>
									</div>
									<input class="form-control date initial_fields" v-model="myForm.artstart_date" name="artstart_date" required>
								</div>
							</div>
					  
							<div class="form-row">
								<div class="col-md-4 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" for="gender">
											Gender:
											<div style='color: #ff0000; display: inline;'>*</div>
										</span>
									</div>
									<select class="custom-select" v-model="myForm.gender" id="gender" name="gender" required="required">
										<option selected="">Choose...</option>
										<option value="Male">Male</option>
										<option value="Female">Female</option>
									</select>
								</div>
						
								<div class="col-md-4 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" id="curr_weight">
											Current Weight (Kg):
											<div style='color: #ff0000; display: inline;'>*</div>
										</span>
									</div>
									<input aria-describedby="curr_weight" v-model="myForm.curr_weight" class="form-control" maxlength="3" name="curr_weight"  type="number">
								</div>
								<div class="col-md-4 input-group required">
									<div class="input-group-prepend">
										<span class="input-group-text" id="height">
											Height (cm):
											<div style='color: #ff0000; display: inline;'>*</div>
										</span>
									</div>
									<input aria-describedby="height" class="form-control" v-model="myForm.height" maxlength="3" name="height" required="required" type="number">
								</div>
							</div>
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-12 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text text-left" id="clinician_name">
									Clinician’s Name:
									<div style='color: #ff0000; display: inline;'>*</div>
								</span>
							</div>
							<input aria-describedby="clinician_name" class="form-control initial_fields" v-model="myForm.clinician_name" maxlength="75" name="clinician_name" required="required" type="text">
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
									<div style='color: #ff0000; display: inline;'>*</div>
								</span>
							</div>
							<input aria-describedby="facility_email" class="form-control initial_fields" v-model="myForm.facility_email" maxlength="75" name="facility_email" required="required" type="text">
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-5 input-group required">
							<div class="input-group-prepend">
								<span class="input-group-text" id="facility_tel">
									Facility Tel No:
									<div style='color: #ff0000; display: inline;'>*</div>
								</span>
							</div>
							<input aria-describedby="facility_tel" class="form-control initial_fields" v-model="myForm.facility_tel" maxlength="45" name="facility_tel" required="required" type="text">
						</div>
					</div>
				  
				  
					<div class="form-row mb-3 required">
						<label class="col-md-12">
							What is the primary reason for this consultation:
							<div style='color: #ff0000; display: inline;'>*</div>
						</label>
					</div>

					<div class="form-row mb-3">
						@foreach($reasons as $reason)
							<div class="form-group col-md-4">
								<input class="form-check-input ml-1 requirable" v-model="myForm.primary_reason" name="primary_reason requirable" required="required" type="radio" id="primary_reason_A{{ $reason->id }}" value="{{ $reason->id }}">
								<label class="form-check-label ml-5" for="primary_reason_A{{ $reason->id }}">{{ $reason->name }}</label>
							</div>
						@endforeach
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Clinical Evaluation: history, physical, diagnostics, working diagnosis:(excluding the information in the table below)
						</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.clinical_eval" name="clinical_eval" rows="3"></textarea>
						</div>
					</div>
				  
					<div class="form-row mb-3">
						<div class="col-md-12 card">
							<div class="card-header">
								Clinical Evaluation: history, physical, diagnostics, working diagnosis (excluding the information in the table below Complete the table below chronologically, including all ART regimens and laboratory results (and any previous history available for transfer-in patients)
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-12 mb-3">
										<!-- <button class="btn btn-warning float-right" @click.prevent="displayModal()" type="button"> -->
										<button class="btn btn-warning float-right" data-toggle="modal" data-target="#clinical_visit_modal" type="button">
											Add Clinical Visit
										</button>
									</div>
								</div>
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
													<th class="column-header" scope="col"> Actions </th>
												</tr>
											</thead>
											<tbody>
												<tr v-for="(clinical_visit, clinical_visit_index) in myForm.clinical_visits">
													<td> @{{ clinical_visit_index+1 }} </td>
													<td> @{{ clinical_visit.clinicvisitdate }} </td>
													<td> @{{ clinical_visit.cd4 }} </td>
													<td> @{{ clinical_visit.hb }} </td>
													<td> @{{ clinical_visit.crclegfr }} </td>
													<td> @{{ clinical_visit.viral_load }} </td>
													<td> @{{ clinical_visit.weight_bmi }} </td>
													<td> @{{ clinical_visit.arv_regimen }} </td>
													<td> @{{ clinical_visit.reason_switch }} </td>
													<td> @{{ clinical_visit.new_oi }} </td>
													<td>
														<div class="btn-group" role="group">
															<button class="btn btn-sm btn-warning">Edit</button>
															<button class="btn btn-sm btn-danger" data-placement="top" data-toggle="tooltip" title="Delete selected record" @click.prevent="delVisit(clinical_visit_index)">Del</button>
															
														</div> 
													</td>
												</tr>
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
							<div style='color: #ff0000; display: inline;'>*</div>
						</label>
						<div class="col-md-8">
							<input class="form-control requirable" v-model="myForm.no_adherance_counseling" name="no_adherance_counseling" required="required" type="number">
						</div>
					</div>
				  
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Number of home visits conducted in last 3-6 months, and findings:
						</label>
						<div class="col-md-8">
						  <input class="form-control" v-model="myForm.no_homevisits" name="no_homevisits" type="number">
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Support structures (e.g. treatment buddy, support group attendance, caregivers) in place for this patient?
						</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.support_structures" name="support_structures" rows="4"></textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Evidence of adherence concerns (e.g. missed appointments, pill counts?):
						</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.adherence_concerns" name="adherence_concerns" rows="4"></textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">Number of DOTS done in last 3-6 months:</label>
						<div class="col-md-8">
							<input class="form-control" v-model="myForm.no_dotsdone" name="no_dotsdone"  type="number">
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Likely root cause/s of poor adherence, for this patient (e.g. stigma, disclosure, side effects, alcohol or other drugs, mental health issues, caregiver changes, religious beliefs, inadequate preparation, etc):
						</label>
						<div class="col-md-8">
						  <textarea class="form-control" v-model="myForm.likely_rootcauses" name="likely_rootcauses"  rows="4"></textarea>
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
							<textarea class="form-control" v-model="myForm.inadequate_dosing" name="inadequate_dosing"  rows="4"></textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">• Drug-drug interactions:</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.drug_interactions" name="drug_interactions"  rows="4"></textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">• Drug-food interactions:</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.food_interactions" name="food_interactions"  rows="4"></textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">• Impaired absorption (e.g. chronic severe diarrhea):</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.impaired_absorption" name="impaired_absorption"  rows="4"></textarea>
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
							<textarea class="form-control" v-model="myForm.treatment_interruptions" name="treatment_interruptions"  rows="4"></textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Has Drug Resistance/Sensitivity Testing been done for this patient? If yes, state date done and attach the detailed results.
						</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.drt_testing" name="drt_testing"  rows="4"></textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							Has facility multidisciplinary team discussed the patient’s case?. If yes, comment on date, deliberations and recommendations.
							<br>
							(indicate how treatment failure was established and confirmed, proposed regimen and dosage, current source of drugs if patient already on 3rd line). If yes, state date done and attach the detailed results:
						</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.mdt_discussions" name="mdt_discussions"  rows="6"></textarea>
						</div>
					</div>
				  
					<div class="form-group row">
						<label class="col-md-4 col-form-label">
							MDT members who participated in the case discussion (names and titles)
						</label>
						<div class="col-md-8">
							<textarea class="form-control" v-model="myForm.mdt_members" maxlength="255" name="mdt_members"  rows="6"></textarea>
						</div>
					</div>

					<div v-if="successful_validation" class="row alert alert-success">
						You have successfully submitted your request.
					</div>

					<div v-if="successful_validation === false" class="row alert alert-warning">
						You have validation errors.
					</div>
				  
					<div class="mb-3 float-right">
						<button class="btn btn-warning" type="submit" >Submit</button>
					</div>
				  
					<div class="mb-3 float-centre">
						<button class="btn btn-default" type="button"  @click.prevent="saveDraft()">Save As Draft</button>
					</div>
				  
					<div class="mb-3 float-centre"></div>
				</form>
			</div>
		</div>

		@include('uliza.clinical_visit')
		
	</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script type="text/javascript">
	$(document).ready(function(){

        var vm = new Vue({
        	el: "#my-vue-instance",
        	data: {
        		myForm: {
        			id: null,
        			facility_id: null,
        			cccno: null,
        			reporting_date: null,
        			dob: null,
        			artstart_date: null,
        			gender: null,
        			curr_weight: null,
        			height: null,
        			clinician_name: null,
        			facility_email: null,
        			facility_tel: null,
        			primary_reason: null,
        			clinical_eval: null,
        			clinical_visits: [],
        			no_adherance_counseling: null,
        			no_homevisits: null,
        			support_structures: null,
        			adherence_concerns: null,
        			no_dotsdone: null,
        			likely_rootcauses: null,
        			inadequate_dosing: null,
        			drug_interactions: null,
        			food_interactions: null,
        			impaired_absorption: null,
        			treatment_interruptions: null,
        			drt_testing: null,
        			mdt_discussions: null,
        			mdt_members: null, 
        			draft: null, 
        		},
        		clinicalVisit:{
        			clinicvisitdate: null,
        			cd4: null,
        			hb: null,
        			crclegfr: null,
        			viral_load: null,
        			weight_bmi: null,
        			arv_regimen: null,
        			reason_switch: null,
        			new_oi: null,        			
        		},
        		successful_validation: null,
        	},
        	mounted (){
        		@isset($ulizaClinicalForm)
        			var ulizaClinicalForm = {!! $ulizaClinicalForm->toJson() !!};
        			var tempVm = this;
        			Object.keys(this.myForm).forEach(function(key, index){
        				tempVm.myForm[key] = ulizaClinicalForm[key];
        			});
        		@endisset
        	},
        	methods: {
        		update(){
        			// console.log(this.myForm);
        			// $('.form-control').removeAttr("disabled");
        			$('requirable').attr('required', 'required');
        			var validator = $( "#myClinicalForm" ).validate();
					this.successful_validation = validator.form();
					// console.log(res);
					if(!this.successful_validation) return;
					this.myForm.draft = 0;
        			/*$("#myClinicalForm").validate({
			            errorPlacement: function (error, element)
			            {
			                element.before(error);
			            }
					});*/
        			axios.post('/uliza-form', this.myForm).then(function(response){
        				// console.log(response);
        			}).catch(function(error){
        				// console.log(error);
        			});
        		},
        		addVisit(){
        			this.myForm.clinical_visits.push({...this.clinicalVisit});

        			var tempVm = this;
        			Object.keys(this.clinicalVisit).forEach(function(key, index){
        				tempVm.clinicalVisit[key] = null;
        			});
        		},
        		editVisit(index){
        			// console.log(index);
        			this.myForm.clinical_visits.splice(index, 1);

        			this.clinicalVisit = {...this.myForm.clinical_visits[index]};
        			$('#clinical_visit_modal').modal('show')
        		},
        		delVisit(index){
        			console.log(index);
        			this.myForm.clinical_visits.splice(index, 1);
        		},
        		/*saveDraft(){
					const data = JSON.stringify(this.myForm)
					const blob = new Blob([data], {type: 'text/plain'})
					const e = document.createEvent('MouseEvents'),
					a = document.createElement('a');
					a.download = "clinicalform.json";
					a.href = window.URL.createObjectURL(blob);
					a.dataset.downloadurl = ['text/json', a.download, a.href].join(':');
					e.initEvent('click', true, false, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null);
					a.dispatchEvent(e);        			
        		},*/
        		saveDraft(){
        			// $('.form-control').attr("disabled", "disabled");
        			$('.requirable').removeAttr("required");
        			var validator = $( "#myClinicalForm" ).validate();
					this.successful_validation = validator.form();
					console.log('Saving Draft');

					if(!this.successful_validation) return;
					this.myForm.draft = 1;


        			axios.post('/uliza-form', this.myForm).then(function(response){
        				console.log(response);
        			}).catch(function(error){
        				console.log(error);
        			});
        		},
        	},
        });


        $(".form-horizontal").validate({
            errorPlacement: function (error, element)
            {
                element.before(error);
            }
        });

        $(".date").datepicker({
            startView: 2,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            // startDate: "-6m",
            // endDate: new Date(),
            format: "yyyy-mm-dd"
        });

        $("#clinicvisitdate").datepicker({
            startView: 2,
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: true,
            autoclose: true,
            // startDate: "-6m",
            // endDate: new Date(),
            format: "yyyy-mm-dd"
        });
		set_select_facility("facility_id", "{{ url('/facility/search') }}", 3, "Search for facility", false);

        $("#facility_id").change(function(){
            var val = $(this).val();
            vm.myForm.facility_id = val;
        }); 

        $(".date").change(function(){
            var val = $(this).val();
            var name = $(this).attr('name');
            vm.myForm[name] = val;
        }); 

        $("#clinicvisitdate").change(function(){
            var val = $(this).val();
            vm.clinicalVisit['clinicvisitdate'] = val;
        }); 

        @isset($ulizaClinicalForm)
	        $("#facility_id").val({{ $ulizaClinicalForm->facility_id }}).change();
	    @endisset
	    
	});
</script>
@endsection