<script type="text/javascript">
	$(document).ready(function(){

		set_select_patient("sidebar_covidpatient_search", "{{ url('/covid_patient/search') }}", 2, "Search for Covid-19 patient", true);
		set_select_patient("sidebar_covidpatient_nat_id_search", "{{ url('/covid_patient/nat_id') }}", 2, "Search for Covid-19 Nat-ID", true);

        @if(in_array(env('APP_LAB'), [1]))
			set_select_patient("sidebar_covid_kemri_id_search", "{{ url('/covid_sample/kem_id') }}", 2, "Search for KEMRI ID", true);
		@elseif(in_array(env('APP_LAB'), [25]))
			set_select_patient("sidebar_covid_kemri_id_search", "{{ url('/covid_sample/kem_id') }}", 2, "Search for AMREF ID", true);
		@endif
		set_select("sidebar_covidlabID_search", "{{ url('/covid_sample/search') }}", 1, "Search by Covid-19 Lab ID");
		set_select("sidebar_covid_cert_search", "{{ url('/covid_sample/national_sample/search') }}", 2, "Search by Covid-19 Certificate Number");
		set_select("sidebar_covid_worksheet_search", "{{ url('/covid_worksheet/search') }}", 1, "Search for Covid-19 worksheet", true);
		

		set_select("batch_search", "{{ url('/batch/search') }}", 1, "Search for batch");
		set_select("viralbatch_search", "{{ url('/viralbatch/search') }}", 1, "Search for batch");

		set_select("sidebar_batch_search", "{{ url('/batch/search') }}", 1, "Search for EID batch");
		set_select("sidebar_viralbatch_search", "{{ url('/viralbatch/search') }}", 1, "Search for VL batch");

		set_select_patient("patient_search", "{{ url('/patient/search') }}", 2, "Search for patient");
		set_select_patient("viralpatient_search", "{{ url('/viralpatient/search') }}", 2, "Search for patient");

		set_select_patient("sidebar_patient_search", "{{ url('/patient/search') }}", 2, "Search for EID patient", true);
		set_select_patient("sidebar_viralpatient_search", "{{ url('/viralpatient/search') }}", 2, "Search for VL patient", true);
		set_select("sidebar_cd4_patientname", "{{ url('/cd4/patient/search_name') }}", 2, "Search for patient name", false, true);
		set_select("sibebar_cd4medrecNo_search", "{{ url('cd4/patient/search_record_no') }}", 2, "Search Medical Record #(Ampath #)", false, true);

		set_select("worksheet_search", "{{ url('/worksheet/search') }}", 1, "Search for worksheet", true);
		set_select("viralworksheet_search", "{{ url('/viralworksheet/search') }}", 1, "Search for worksheet", true);
		set_select("sidebar_cd4worksheet_search", "{{ url('cd4/worksheet/search') }}", 1, "Search for Worksheet");

		set_select("sidebar_worksheet_search", "{{ url('/worksheet/search') }}", 1, "Search for EID worksheet", true);
		set_select("sidebar_viralworksheet_search", "{{ url('/viralworksheet/search') }}", 1, "Search for VL worksheet", true);

		set_select_facility("facility_search", "{{ url('/facility/search') }}", 3, "Search for facility", "{{ url('/batch/facility') }}");
		set_select_facility("sidebar_facility_search", "{{ url('/facility/search') }}", 3, "Search for facility batches", "{{ url('/batch/facility') }}");
		set_select_facility("sidebar_viralfacility_search", "{{ url('/facility/search') }}", 3, "Search for facility batches", "{{ url('/viralbatch/facility') }}");
		set_select_facility("sidebar_dr_facility_search", "{{ url('/facility/search') }}", 3, "Search for facility samples", "{{ url('/dr_sample/facility') }}");
		set_select_facility("sidebar_cd4facility_search", "{{ url('/facility/search') }}", 3, "Search Site Samples", "{{ url('/cd4/sample/facility') }}");
		set_select_facility("sidebar_hpv_facility_search", "{{ url('/facility/search') }}", 3, "Search for facility samples", "{{ url('/cancersample/facility') }}");

		set_select("sidebar_labID_search", "{{ url('sample/search') }}", 1, "Search by EID Lab ID");
		set_select("sidebar_virallabID_search", "{{ url('viralsample/search') }}", 1, "Search by VL Lab ID");


		set_select_orderno("sidebar_order_no_search", "{{ url('sample/ord_no') }}", 1, "Search by EID Order No");
		set_select_orderno("sidebar_viral_order_no_search", "{{ url('viralsample/ord_no') }}", 1, "Search by VL Order No");
		set_select("sidebar_cd4labID_search", "{{ url('cd4/sample/search') }}", 1, "Search by CD4 Lab ID");


		set_select_patient("dr_patient_search", "{{ url('/viralpatient/search') }}", 2, "Search for DR patient", "{{ url('/viralpatient/dr') }}");
		set_select_patient("dr_nat_id_search", "{{ url('/viralpatient/nat_id') }}", 2, "Search for DR nat ID", "{{ url('/viralpatient/dr') }}");
		set_select_patient("dr_cnt_id_search", "{{ url('/viralpatient/cnt_id') }}", 2, "Search for DR CNT ID", "{{ url('/viralpatient/dr') }}");
		set_select("dr_sample_search", "{{ url('/dr_sample/search') }}", 1, "Search for DR lab ID");

		set_select_patient("hpv_patient_search", "{{ url('/cancerpatient/search') }}", 2, "Search for HPV patient", "{{ url('/cancerpatient') }}");
		set_select("hpv_sample_search", "{{ url('/cancersample/search') }}", 1, "Search for HPV Lab ID");

	});
	
	function set_select(div_name, url, minimum_length, placeholder, worksheet=false, cd4=false) {
		div_name = '#' + div_name;		

		$(div_name).select2({
			minimumInputLength: minimum_length,
			placeholder: placeholder,
			ajax: {
				delay	: 100,
				type	: "POST",
				dataType: 'json',
				data	: function(params){
					return {
						search : params.term
					}
				},
				url		: function(params){
					params.page = params.page || 1;
					return  url + "?page=" + params.page;
				},
				processResults: function(data, params){
					return {
						results 	: $.map(data.data, function (row){
							if(cd4 == true){
								return {
									text	: row.medicalrecordno + ' - ' + row.patient_name,
									id		: row.medicalrecordno
								};
							} else {
								return {
									text	: row.id,
									id		: row.id		
								};	
							}
							
						}),
						pagination	: {
							more: data.to < data.total
						}
					};
				}
			}
		});
		if(worksheet){
			set_worksheet_change_listener(div_name, url);
		} else{
			if(cd4){
				set_cd4patient_change_listener(div_name, url);
			} else {
				set_change_listener(div_name, url);
			}			
		}	
	}
	
	function set_select_patient(div_name, url, minimum_length, placeholder, send_url=true, cd4name=false) {
		div_name = '#' + div_name;		

		$(div_name).select2({
			minimumInputLength: minimum_length,
			placeholder: placeholder,
			ajax: {
				delay	: 100,
				type	: "POST",
				dataType: 'json',
				data	: function(params){
					return {
						search : params.term
					}
				},
				url		: function(params){
					params.page = params.page || 1;
					return  url + "?page=" + params.page;
				},
				processResults: function(data, params){
					return {
						results 	: $.map(data.data, function (row){
							if (cd4name == true) {
								return {
									text	: row.patient_name + ' - ' + row.medicalrecordno,
									id		: row.id		
								};
							}else if(typeof cd4name === 'string'){
								return {
									text	: row[cd4name] + ' - ' + row.name,
									id		: row.id		
								};
							} else {
								return {
									text	: row.patient + ' - ' + row.name,
									id		: row.id		
								};
							}
						}),
						pagination	: {
							more: data.to < data.total
						}
					};
				}
			}
		});
		if(send_url != false){
			if(typeof send_url === 'string'){
				set_change_listener(div_name, send_url, false);	
			}else{
				set_change_listener(div_name, url);	
			}
		}
	}

	function set_select_facility(div_name, url, minimum_length, placeholder, send_url=false) {			
		if(!div_name.includes('#') && !div_name.includes('.')) div_name = '#' + div_name;
		// console.log(div_name);	

		$(div_name).select2({
			minimumInputLength: minimum_length,
			placeholder: placeholder,
			allowClear: true,
			ajax: {
				delay	: 100,
				type	: "POST",
				dataType: 'json',
				data	: function(params){
					return {
						search : params.term,
						div_id : div_name
					}
				},
				url		: function(params){
					params.page = params.page || 1;
					return  url + "?page=" + params.page;
				},
				processResults: function(data, params){
					return {
						results 	: $.map(data.data, function (row){
							if (row.facilitycode == undefined) {
								return {
									text	: row.name, 
									id		: row.id		
								};
							} else {
								return {
									text	: row.facilitycode + ' - ' + row.name + ' (' + row.county + ')', 
									id		: row.id
								};
							}
						}),
						pagination	: {
							more: data.to < data.total
						}
					};
				}
			}
		});

		if(send_url != false)
			set_change_listener(div_name, send_url, false);
	}
	function set_select_facility_mfl(div_name, url, minimum_length, placeholder, send_url=false) {
		if(!div_name.includes('#') && !div_name.includes('.')) div_name = '#' + div_name;
		// console.log(div_name);

		$(div_name).select2({
			minimumInputLength: minimum_length,
			placeholder: placeholder,
			allowClear: true,
			ajax: {
				delay	: 100,
				type	: "POST",
				dataType: 'json',
				data	: function(params){
					return {
						search : params.term,
						div_id : div_name
					}
				},
				url		: function(params){
					params.page = params.page || 1;
					return  url + "?page=" + params.page;
				},
				processResults: function(data, params){
					return {
						results 	: $.map(data.data, function (row){
							if (row.facilitycode == undefined) {
								return {
									text	: row.name,
									id		: row.id
								};
							} else {
								return {
									text	: row.facilitycode + ' - ' + row.name + ' (' + row.county + ')',
									id		: row.facilitycode
								};
							}
						}),
						pagination	: {
							more: data.to < data.total
						}
					};
				}
			}
		});

		if(send_url != false)
			set_change_listener(div_name, send_url, false);
	}
	
	function set_select_orderno(div_name, url, minimum_length, placeholder, worksheet=false, cd4=false) {
		div_name = '#' + div_name;		

		$(div_name).select2({
			minimumInputLength: minimum_length,
			placeholder: placeholder,
			ajax: {
				delay	: 100,
				type	: "POST",
				dataType: 'json',
				data	: function(params){
					return {
						search : params.term
					}
				},
				url		: function(params){
					params.page = params.page || 1;
					return  url + "?page=" + params.page;
				},
				processResults: function(data, params){
					return {
						results 	: $.map(data.data, function (row){
							if(cd4 == true){
								return {
									text	: row.medicalrecordno,
									id		: row.medicalrecordno
								};
							} else {
								return {
									text	: row.order_no + ' ' + row.patient,
									id		: row.id		
								};	
							}
							
						}),
						pagination	: {
							more: data.to < data.total
						}
					};
				}
			}
		});
		if(cd4){
			set_cd4patient_change_listener(div_name, url);
		} else {
			set_change_listener(div_name, url);
		}
	}

	function set_change_listener(div_name, url, not_facility=true)
	{
		if(not_facility){
			url = url.substring(0, url.length-7);
		} 	
				
		$(div_name).change(function(){
			var val = $(this).val();
			window.location.href = url + '/' + val;
		});	
	}



	function set_cd4patient_change_listener(div_name, url)
	{		
		$(div_name).change(function(){
			var val = $(this).val();
			window.location.href = url + '/' + val;
		});	
	}

	function set_worksheet_change_listener(div_name, url)
	{
		url = url.substring(0, url.length-7);	
		$(div_name).change(function(){
			var val = $(this).val();
			window.location.href = url + '/find/' + val;
		});	
	}


	{{--
		$('#batch_search').select2({
			minimumInputLength: 1,
			placeholder: 'Search for the batch',
			ajax: {
				delay	: 100,
				type	: "POST",
				dataType: 'json',
				data	: function(params){
					return {
						search : params.term
					}
				},
				url		: function(params){
					params.page = params.page || 1;
					return "{{ url('/batch/search') }}?page=" + params.page;
				},
				processResults: function(data, params){
					console.log(data);
					return {
						results 	: $.map(data.data, function (row){
							return {
								text	: row.id,
								id		: row.id		
							};
						}),
						pagination	: {
							more: data.to < data.total
						}
					};
				}
			}
		});
	--}}
</script>
