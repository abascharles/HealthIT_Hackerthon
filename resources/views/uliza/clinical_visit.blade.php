<div class="modal fade" role="dialog" tabindex="-1" aria-modal="true" id="clinical_visit_modal"  aria-labelledby="clinical_visit_modal" aria-hidden="true">
	<div role="dialog" class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header text-white bg-warning">
				<h4 class="modal-title pull-left">Add Clinical Visit</h4>
				<button aria-label="Close" class="close pull-right" type="button" data-dismiss="modal">
					<span aria-hidden="true">×</span>
				</button>
			</div>
			<div class="modal-body">
				<form autocomplete="off" @submit.prevent="addVisit" novalidate="" class="">

					<div class="form-row mb-3">
						<div class="form-group col-md-6 required">
							<label for="clinicvisitdate">Clinic Visit Date:</label>
							<input class="form-control" v-model="clinicalVisit.clinicvisitdate" id="clinicvisitdate" name="clinicvisitdate" required="" type="text">
						</div>
						<div class="form-group col-md-6">
							<label for="cd4">CD4 Results(c/mm3):</label>
							<input aria-describedby="cd4" v-model="clinicalVisit.cd4" class="form-control" name="cd4" type="text">
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="form-group col-md-6">
							<label for="hb">HB Results(g/dl):</label>
							<input aria-describedby="hb" class="form-control" v-model="clinicalVisit.hb" name="hb" type="text">
						</div>
						<div class="form-group col-md-6">
							<label for="crclegfr">CrCl/eGFR Results(ml/min):</label>
							<input aria-describedby="crclegfr" class="form-control" v-model="clinicalVisit.crclegfr" name="crclegfr" type="text">
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="form-group col-md-6">
							<label for="viral_load">Viral Load Results(cp/mm3):</label>
							<input aria-describedby="viral_load" class="form-control" v-model="clinicalVisit.viral_load" name="viral_load" type="text">
						</div>
						<div class="form-group col-md-6">
							<label for="weight_bmi">Weight (z-score/BMI for children) in kgs:</label>
							<input aria-describedby="weight_bmi" class="form-control" v-model="clinicalVisit.weight_bmi" name="weight_bmi" type="text">
						</div>
					</div>

					<div class="form-row mb-3">
						<div class="form-group col-md-6">
							<label class="mr-2" for="arv_regimen">ARV Regimen:</label>
							<select class="custom-select" v-model="clinicalVisit.arv_regimen" name="arv_regimen">
								@foreach($regimens as $regimen)
									<option value="{{ $regimen->name }}">{{ $regimen->name }} </option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-md-6">
							<label for="reason_switch">Reason for Switch:</label>
							<input aria-describedby="reason_switch" class="form-control" v-model="clinicalVisit.reason_switch" name="reason_switch" type="text">
						</div>
					</div>

					<!-- <div class="form-row mb-3 ng-star-inserted">
						<div class="form-group col-md-12">
							<label for="arv_regimen_other">Specify Other ARV Regimen:</label>
							<input aria-describedby="arv_regimen_other" class="form-control" name="arv_regimen_other" type="text">
						</div>
					</div> -->

					<!---->

					<div class="form-row mb-3">
						<div class="form-group col-md-12">
							<label for="new_oi">New OI or any other clinical Event:</label>
							<input aria-describedby="new_oi" class="form-control" v-model="clinicalVisit.new_oi" name="new_oi" type="text">
						</div>
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<button class="btn btn-warning" @click="addVisit()" type="button">Submit</button>
				<button class="btn btn-default" type="button" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>