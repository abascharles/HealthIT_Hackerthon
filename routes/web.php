<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//file that will point to the store method of SpiceworksController.
Route::post('/spiceworks/store', [SpiceworksController::class, 'store'])->name('spiceworks.store');


// Route::get('reset_password/{token}', ['as' => 'password.reset', function($token)
// {
//     // implement your reset password route here!
// }]);

// Route::get('/', function () {
    // return view('auth.login');
    // return view('emergency');
// });

Route::get("viralsample/patient_verification/{upi_no}", "ViralpatientController@patient_verify");
Route::get("patient_cr/{upi_no}", "ViralpatientController@patient_verify");

Route::get('testtracker', function(){
	$year = date('Y', strtotime("-1 Month", strtotime(date('Y-m-d'))));
	$month = date('m', strtotime("-1 Month", strtotime(date('Y-m-d'))));
	$lab = \App\Lab::find(env('APP_LAB'));
	$data = \App\Random::__getLablogsData($year, $month);
	$path = storage_path('app/lablogs/monthlabtracker ' . $data->year .  $data->month .'.pdf');
	if(!is_dir(storage_path("app/lablogs/"))) mkdir(storage_path("app/lablogs/"), 0777, true);
    $mpdf = new Mpdf(['format' => 'A4-L']);
    $view_data = view('exports.mpdf_labtracker', ['data' => $data, 'lab' => $lab, 'download' => false])->render();
    $mpdf->WriteHTML($view_data);
    $mpdf->Output($path, \Mpdf\Output\Destination::FILE);
	// dd($data);
	return view('exports.mpdf_labtracker', ['data' => $data, 'lab' => $lab, 'download' => true]);
});

Route::redirect('/', '/login');
Route::redirect('/eid', '/login');
Route::redirect('/knh', '/login');
Route::redirect('/nyumbani', '/login');

Route::get('/eid/{param?}', 'RandomController@send_to_login')->where('param', '(.*\\.*)');
Route::get('labtrackertest', 'RandomController@testlabtracker');
Route::get('/uliza', function () {
	return view('layouts.uliza');
});
/*Route::get('/phpinfo', function () {
	return view('phpinfo');
});*/

Route::prefix('uliza')->name('uliza.')->group(function(){
	Route::get('home', 'UlizaController@home');
	Route::get('uliza', 'UlizaController@uliza');
	Route::get('ushauri', 'UlizaController@ushauri');
	Route::get('trainsmart', 'UlizaController@trainsmart');
	Route::get('echo', 'UlizaController@echo_page');
	Route::get('faqs', 'UlizaController@faqs');
	Route::get('contactus', 'UlizaController@contactus');

	Route::get('pages', 'UlizaController@pages');

	Route::get('clinicalform', 'UlizaClinicalFormController@create');
	Route::get('clinical-review', 'UlizaController@clinical_review');
});

Route::prefix('uliza-form')->name('uliza-form.')->group(function(){
	Route::post('index', 'UlizaClinicalFormController@index');
});
Route::resource('uliza-form', 'UlizaClinicalFormController');
Route::post('uliza/login', 'UlizaUserController@login');
Route::resource('uliza-additional-info', 'UlizaAdditionalInfoController');

Route::middleware(['auth'])->group(function(){

	Route::resource('uliza-twg', 'UlizaTwgController');
	Route::get('uliza-review/create/{ulizaClinicalForm}', 'UlizaTwgFeedbackController@create');
	Route::get('uliza-review/view/{ulizaClinicalForm}', 'UlizaTwgFeedbackController@create');
	Route::resource('uliza-review', 'UlizaTwgFeedbackController');
	Route::get('uliza/logout', 'UlizaUserController@logout');
	Route::get('uliza/update-password', 'UlizaUserController@change_password');
	Route::post('uliza/update-password', 'UlizaUserController@update_password');

	Route::prefix('uliza-user')->name('uliza-user.')->group(function(){
		Route::get('resend_email/{id}', 'UlizaUserController@resend_email');
		Route::put('restore/{id}', 'UlizaUserController@restore');
	});
	Route::resource('uliza-user', 'UlizaUserController');
});

// Route::get('/addsample', function () {
// 	return view('addsample');
// });

Route::get('/config', 'RandomController@config');
Route::get('/download_notice', 'RandomController@download_notice');

Route::get('login/facility', 'Auth\\LoginController@fac_login')->name('login.facility');
Route::post('login/facility', 'Auth\\LoginController@facility_login');


Auth::routes();

// Route::get('datatables', function () {
// 	return view('datatables');
// });

// Route::get('/checkboxes', function () {
// 	return view('checkbox');
// });


Route::post('facility/search/', 'FacilityController@search')->name('facility.search');

// Route::get('error', function(){
// 	return view('errors.error', ['code' => '500', 'title' => 'Internal server error', 'description' => 'Sorry, there was an internal server error that occured. Please try again later']);
// });

Route::get('/synch', 'HomeController@test');
Route::get('download_api', 'RandomController@download_api');

Route::middleware(['signed'])->group(function(){
	Route::get('dr_sample/edit/{user}/{sample}', 'DrSampleController@facility_edit')->name('dr_sample.facility_edit');
	Route::get('nhrl', 'RandomController@login_edarp')->name('nhrl');
});

Route::middleware(['auth'])->group(function(){
	Route::middleware(['consumptionsubmitted'])->group(function(){
		Route::prefix('home')->name('home.')->group(function(){
			Route::get('/', 'HomeController@index');
			Route::get('overdue/{level?}', 'HomeController@overdue')->name('overdue');
			Route::get('pending/{type?}/{sampletype?}', 'HomeController@pending')->name('pending');
			Route::get('repeat', 'HomeController@repeat')->name('repeat');
			Route::get('rejected', 'HomeController@rejected')->name('rejected');
		});
		
		Route::get('search', 'RandomController@search');

		Route::get('refresh_cache', 'RandomController@refresh_cache');
		Route::get('refresh_dashboard', 'RandomController@refresh_dashboard');
		Route::get('system_switch/{sys}', 'RandomController@sysswitch');

		Route::group(['middleware' => ['utype:5']], function () {
			Route::prefix('batch')->name('batch.')->group(function () {
				// Route::get('index/{batch_complete?}/{page?}/{date_start?}/{date_end?}', 'BatchController@index');
				Route::get('index/{batch_complete?}/{date_start?}/{date_end?}/{facility_id?}/{subcounty_id?}/{partner_id?}', 'BatchController@index');
				Route::get('to_print/{date_start?}/{date_end?}/{facility_id?}/{subcounty_id?}/{partner_id?}', 'BatchController@to_print');
				Route::get('facility/{facility_id}/{batch_complete?}/{date_start?}/{date_end?}', 'BatchController@facility_batches');
				Route::get('delayed/', 'BatchController@delayed_batches');
				Route::post('index', 'BatchController@batch_search');
				Route::get('site_approval/', 'BatchController@approve_site_entry');
				Route::get('site_approval/{batch}', 'BatchController@site_entry_approval');
				Route::get('site_approval_group/{batch}', 'BatchController@site_entry_approval_group');
				Route::put('site_approval_group/{batch}', 'BatchController@site_entry_approval_group_save');
				Route::get('labels/{batch}', 'BatchController@labels');
                Route::get('emr_approval/', 'BatchController@approve_emr_entry');

				Route::group(['middleware' => ['only_utype:1,4']], function () {
					
					Route::get('convert_from_poc/{batch}', 'BatchController@convert_to_site_entry');
					Route::post('destroy_multiple/', 'BatchController@destroy_multiple');
					
					Route::get('dispatch/', 'BatchController@batch_dispatch');
					Route::post('complete_dispatch/', 'BatchController@confirm_dispatch');

					Route::get('transfer/{batch}', 'BatchController@transfer')->name('get.transfer');
					Route::post('transfer/{batch}', 'BatchController@transfer_to_new_batch')->name('post.transfer');

					Route::get('sample_manifest', 'BatchController@sample_manifest')->name('sample_manifest');
					Route::post('sample_manifest', 'BatchController@sample_manifest')->name('post.sample_manifest');
				});

				Route::get('summary/{batch}', 'BatchController@summary');
				Route::post('summaries', 'BatchController@summaries');
				Route::get('individual/{batch}', 'BatchController@individual');
				Route::get('individual_pdf/{batch}', 'BatchController@individual_pdf');
				Route::get('envelope/{batch}', 'BatchController@envelope')->name('envelope');
				Route::get('email/{batch}', 'BatchController@email')->name('email');

				Route::post('search/', 'BatchController@search')->name('search');
			});
			Route::resource('batch', 'BatchController');
		});

		Route::post('city/search/', 'CovidSampleController@cities')->name('cities');


		Route::prefix('cancersample')->name('cancersample.')->group(function () {
			Route::get('index/{param?}', 'CancerSampleController@index')->name('list');
			Route::get('{sample}/edit_result', 'CancerSampleController@edit_result');
			Route::put('{sample}/edit_result', 'CancerSampleController@save_result');
			Route::get('list/{param?}', 'CancerSampleController@index');
			Route::get('{sample}/print', 'CancerSampleController@print');
			Route::get('facility/{facility}', 'CancerSampleController@facility')->name('facility');
			Route::post('search', 'CancerSampleController@search')->name('search');
		});
		Route::resource('cancersample', 'CancerSampleController');

		Route::prefix('cancerpatient')->name('cancerpatient.')->group(function() {
			Route::post('search/', 'CancerpatientController@search');
			// Route::post('search', 'SampleController@search');
			// Route::get('search/{patient?}', 'CancerpatientController@search_result');
		});
		Route::resource('cancerpatient', 'CancerpatientController');

		Route::prefix('cancerworksheet')->name('cancerworksheet.')->group(function () {
			Route::get('index/{state?}/{date_start?}/{date_end?}', 'CancerWorksheetController@index')->name('list');
			Route::get('find/{worksheet}', 'CancerWorksheetController@find')->name('find');
			Route::get('print/{worksheet}', 'CancerWorksheetController@print')->name('print');
			Route::get('labels/{worksheet}', 'CancerWorksheetController@labels')->name('labels');
			Route::get('cancel/{worksheet}', 'CancerWorksheetController@cancel')->name('cancel');
			Route::get('rerun_worksheet/{worksheet}', 'CancerWorksheetController@rerun_worksheet')->name('rerun_worksheet');
			Route::get('convert/{worksheet}/{machine_type}', 'CancerWorksheetController@convert_worksheet')->name('convert');
			Route::post('search/', 'CancerWorksheetController@search');

			Route::group(['middleware' => ['only_utype:1']], function () {
				Route::get('cancel_upload/{worksheet}', 'CancerWorksheetController@cancel_upload')->name('cancel_upload');
				Route::get('reverse_upload/{worksheet}', 'CancerWorksheetController@reverse_upload')->name('reverse_upload');
				Route::get('upload/{worksheet}', 'CancerWorksheetController@upload')->name('upload');
				Route::put('upload/{worksheet}', 'CancerWorksheetController@save_results')->name('save_results');
				Route::get('approve/{worksheet}', 'CancerWorksheetController@approve_results')->name('approve_results');
				Route::put('approve/{worksheet}', 'CancerWorksheetController@approve')->name('approve');
			});

			Route::get('print/{worksheet}', 'CancerWorksheetController@print')->name('print');
			Route::get('create/{machine?}/{limit?}/{entered_by?}', 'CancerWorksheetController@create')->name('create_worksheet');
		});
		Route::resource('cancerworksheet', 'CancerWorksheetController');

		Route::prefix('nat_sample')->name('covid_sample.')->group(function () {
			Route::get('index/{type?}/{date_start?}/{date_end?}/{facility_id?}/{quarantine_site_id?}/{lab_id?}', 'NatCovidSampleController@index');
			Route::post('index', 'NatCovidSampleController@sample_search');

			Route::post('print_multiple', 'NatCovidSampleController@print_multiple');
			Route::get('result/{covidSample}', 'NatCovidSampleController@result');
			
			Route::post('kem_id/', 'NatCovidSampleController@kemri_id')->name('kemri_id');
			Route::post('search/', 'NatCovidSampleController@search')->name('search');
			Route::post('new_patient/', 'NatCovidSampleController@new_patient')->name('new_patient');
			Route::post('cif_patient/', 'NatCovidSampleController@cif_patient')->name('cif_patient');
		});

		Route::prefix('covid_sample')->name('covid_sample.')->group(function () {
			Route::get('create/antigen', 'CovidSampleController@create_antigen');
			Route::get('index/{type?}/{date_start?}/{date_end?}/{facility_id?}/{quarantine_site_id?}/{lab_id?}', 'CovidSampleController@index');
			Route::post('index', 'CovidSampleController@sample_search');

			Route::post('approve_for_email', 'CovidSampleController@approve_for_email');
			Route::post('print_multiple', 'CovidSampleController@print_multiple');
			Route::get('result/{covidSample}', 'CovidSampleController@result');
			Route::get('print/{covidSample}', 'CovidSampleController@print_result');

			Route::group(['middleware' => ['only_utype:1,4,12,13,14,15']], function () {
				Route::get('cif', 'CovidSampleController@cif_samples');
				Route::get('jitenge', 'CovidSampleController@jitenge_samples');
				Route::post('cif', 'CovidSampleController@set_cif_samples');
			});

			Route::group(['middleware' => ['only_utype:1']], function () {
				Route::get('worksheet/{covidSample}/{worksheet_id?}', 'CovidSampleController@change_worksheet');
			});
			
			Route::group(['middleware' => ['only_utype:1,4,12,13,14,15']], function () {
				Route::post('receive_multiple', 'CovidSampleController@receive_multiple');
				Route::get('release/{covidSample}', 'CovidSampleController@release_redraw');
				
				Route::get('lab/upload', 'CovidSampleController@lab_sample_page');
				Route::post('lab/upload', 'CovidSampleController@upload_lab_samples');
				
				Route::post('transfer', 'CovidSampleController@transfer');
			});
			
			Route::group(['middleware' => ['only_utype:2']], function () {	
				Route::get('transfer_samples/{facility_id?}', 'CovidSampleController@transfer_samples_form');	
				Route::post('transfer_samples', 'CovidSampleController@transfer_samples');	
			});
			
			Route::get('national_sample/{national_sample_id}', 'CovidSampleController@national_sample')->name('national_sample');
			Route::post('kem_id/', 'CovidSampleController@kemri_id')->name('kemri_id');
			Route::post('search/', 'CovidSampleController@search')->name('search');
			Route::post('national_sample/search/', 'CovidSampleController@search_by_cert')->name('search_by_cert');
			Route::post('new_patient/', 'CovidSampleController@new_patient')->name('new_patient');
			Route::post('cif_patient/', 'CovidSampleController@cif_patient')->name('cif_patient');
		});
		Route::resource('covid_sample', 'CovidSampleController');

		Route::prefix('traveller')->name('traveller.')->group(function () {			
			Route::post('filter/', 'TravellerController@filter')->name('filter');
			Route::post('print_multiple/', 'TravellerController@print_multiple')->name('print_multiple');
		});
		Route::resource('traveller', 'TravellerController');

		Route::prefix('covid_patient')->name('covid_patient.')->group(function () {
			
			Route::post('search/', 'CovidPatientController@search')->name('search');
			Route::post('nat_id/', 'CovidPatientController@national_id')->name('national_id');

		});
		Route::resource('covid_patient', 'CovidPatientController');

		Route::group(['middleware' => ['only_utype:1,4,12,13,14,15']], function () {
			Route::resource('covid_kit_type', 'CovidKitTypeController');

			
			Route::prefix('covid_pool')->name('covid_pool.')->group(function () {

				Route::get('index/{state?}/{date_start?}/{date_end?}', 'CovidPoolController@index')->name('list');
				Route::get('find/{pool}', 'CovidPoolController@find')->name('find');
				Route::get('print/{pool}', 'CovidPoolController@print')->name('print');
				Route::get('cancel/{pool}', 'CovidPoolController@cancel')->name('cancel');
				Route::get('convert/{pool}/{machine_type}', 'CovidPoolController@convert_worksheet')->name('convert');
				Route::post('search/', 'CovidPoolController@search')->name('search');		
			});

			Route::resource('covid_pool', 'CovidPoolController');
			
			Route::prefix('covid_worksheet')->name('covid_worksheet.')->group(function () {
				Route::get('set_details', 'CovidWorksheetController@set_details_form')->name('set_details_form');
				Route::post('create', 'CovidWorksheetController@set_details')->name('set_details');

				Route::get('index/{state?}/{date_start?}/{date_end?}', 'CovidWorksheetController@index')->name('list');
				Route::get('create/{machine_type}/{limit}', 'CovidWorksheetController@create')->name('create_any');
				Route::get('result_file/{worksheet}', 'CovidWorksheetController@result_file')->name('result_file');
				Route::get('find/{worksheet}', 'CovidWorksheetController@find')->name('find');
				Route::get('print/{worksheet}', 'CovidWorksheetController@print')->name('print');
				Route::get('labels/{worksheet}', 'CovidWorksheetController@labels')->name('labels');
				Route::get('cancel/{worksheet}', 'CovidWorksheetController@cancel')->name('cancel');
				Route::get('rerun_worksheet/{worksheet}', 'CovidWorksheetController@rerun_worksheet')->name('rerun_worksheet');
				Route::get('convert/{worksheet}/{machine_type}', 'CovidWorksheetController@convert_worksheet')->name('convert');

				Route::group(['middleware' => ['only_utype:1,12,14']], function () {
					Route::get('cancel_upload/{worksheet}', 'CovidWorksheetController@cancel_upload')->name('cancel_upload');
					Route::get('reverse_upload/{worksheet}', 'CovidWorksheetController@reverse_upload')->name('reverse_upload');
					Route::get('upload/{worksheet}', 'CovidWorksheetController@upload')->name('upload');
					Route::put('upload/{worksheet}', 'CovidWorksheetController@save_results')->name('save_results');
					Route::get('approve/{worksheet}', 'CovidWorksheetController@approve_results')->name('approve_results');
					Route::put('approve/{worksheet}', 'CovidWorksheetController@approve')->name('approve');
				});

				Route::post('search/', 'CovidWorksheetController@search')->name('search');		
			});
			Route::resource('covid_worksheet', 'CovidWorksheetController');
		});

		Route::group(['middleware' => ['only_utype:2,12']], function () {
			Route::resource('quarantine_site', 'QuarantineSiteController');
		});

		Route::group(['middleware' => ['only_utype:1,4,12,14']], function () {
			Route::prefix('covidreports')->name('covid_reports.')->group(function () {
				Route::get('/', 'CovidReportsController@index')->name('index');
				Route::post('/', 'CovidReportsController@generate')->name('generate');
			});
		});

		Route::prefix('covidkits')->name('covidkits.')->group(function() {
			Route::get('/', 'CovidConsumptionController@index');
			Route::post('consumption', 'CovidConsumptionController@submitConsumption');
			Route::get('reports/{consumption?}', 'CovidConsumptionController@reports');

			Route::group(['middleware' => ['utype:12']], function () {
				Route::get('pending', 'CovidConsumptionController@pending');
			});
		});

		Route::prefix('cd4')->name('cd4.')->group(function(){
			Route::prefix('sample')->name('sample.')->group(function(){
				Route::get('dispatch/{state}', 'Cd4SampleController@dispatch')->name('dispatch');
				Route::get('facility/{facility}', 'Cd4SampleController@facility')->name('facility');
				Route::get('print/{sample}', 'Cd4SampleController@print')->name('print');
				Route::get('printresult/{sample}', 'Cd4SampleController@printresult')->name('printresult');
				Route::get('search/{sample}', 'Cd4SampleController@searchresult')->name('searchresult');
				Route::get('delete/{sample}', 'Cd4SampleController@destroy')->name('delete');
				Route::post('search', 'Cd4SampleController@search')->name('search');
			});
			Route::resource('sample', 'Cd4SampleController');
			Route::prefix('patient')->name('patient.')->group(function(){
				Route::post('new', 'Cd4PatientController@new_patient')->name('new');			
				Route::get('search_name/{patient_name}', 'Cd4PatientController@search_name')->name('search_name');
				Route::post('search_name', 'Cd4PatientController@search_name');
				Route::get('search_record_no/{recordno}', 'Cd4PatientController@search_record_no')->name('search_record_no');
				Route::post('search_record_no', 'Cd4PatientController@search_record_no');
			});
			Route::resource('patients', 'Cd4PatientController');

			Route::prefix('reports')->name('reports.')->group(function(){
				Route::get('/', 'ReportController@cd4reports')->name('cd4reports');
				Route::post('dateselect', 'ReportController@dateselect')->name('dateselect');
				Route::post('generate', 'ReportController@generate')->name('generate');
			});
			
			Route::prefix('worksheet')->name('worksheet.')->group(function(){
				Route::get('cancel/{worksheet}', 'Cd4WorksheetController@cancel')->name('cancel');
				Route::get('cancel_upload/{worksheet}', 'Cd4WorksheetController@cancel_upload')->name('cancel_upload');
				Route::get('confirm/{worksheet}', 'Cd4WorksheetController@confirm_upload')->name('confirm');
				Route::put('save/{worksheet}', 'Cd4WorksheetController@save_upload');
				Route::post('search', 'Cd4WorksheetController@search')->name('search');
				Route::get('state/{state}', 'Cd4WorksheetController@state')->name('state');
				Route::get('create/{limit}', 'Cd4WorksheetController@create');
				Route::get('index/{state}', 'Cd4WorksheetController@index')->name('index');
				Route::get('print/{worksheet}', 'Cd4WorksheetController@print')->name('print');
				Route::get('upload/{worksheet}', 'Cd4WorksheetController@upload')->name('upload');
				Route::put('upload/{worksheet}', 'Cd4WorksheetController@upload');
				Route::group(['middleware' => ['utype:1']], function () {
					Route::get('reverse_upload/{worksheet}', 'Cd4WorksheetController@reverse_upload')->name('reverse_upload');
				});
			});
			Route::resource('worksheet', 'Cd4WorksheetController');
		});

		Route::group(['middleware' => ['utype:5']], function () {
			Route::prefix('viralbatch')->name('viralbatch.')->group(function () {
				// Route::get('index/{batch_complete?}/{page?}/{date_start?}/{date_end?}', 'ViralbatchController@index');
				Route::get('index/{batch_complete?}/{date_start?}/{date_end?}/{facility_id?}/{subcounty_id?}/{partner_id?}', 'ViralbatchController@index');
				Route::get('to_print/{date_start?}/{date_end?}/{facility_id?}/{subcounty_id?}/{partner_id?}', 'ViralbatchController@to_print');
				Route::get('facility/{facility_id}/{batch_complete?}/{date_start?}/{date_end?}', 'ViralbatchController@facility_batches');
				Route::get('delayed/', 'ViralbatchController@delayed_batches');
				Route::post('index', 'ViralbatchController@batch_search');
				Route::get('site_approval/', 'ViralbatchController@approve_site_entry');
				Route::get('site_approval/{batch}', 'ViralbatchController@site_entry_approval');
				Route::get('site_approval_group/{batch}', 'ViralbatchController@site_entry_approval_group');
				Route::put('site_approval_group/{batch}', 'ViralbatchController@site_entry_approval_group_save');
				Route::get('labels/{batch}', 'ViralbatchController@labels');
                Route::get('emr_approval/', 'ViralbatchController@approve_emr_entry');

				Route::group(['middleware' => ['only_utype:1,4']], function () {

					Route::get('convert_from_poc/{batch}', 'ViralbatchController@convert_to_site_entry');
					Route::post('destroy_multiple/', 'ViralbatchController@destroy_multiple');

					Route::get('dispatch/', 'ViralbatchController@batch_dispatch');
					Route::post('complete_dispatch/', 'ViralbatchController@confirm_dispatch');

					Route::get('transfer/{viralbatch}', 'ViralbatchController@transfer')->name('get.transfer');
					Route::post('transfer/{batch}', 'ViralbatchController@transfer_to_new_batch')->name('post.transfer');

					Route::get('sample_manifest', 'ViralbatchController@sample_manifest')->name('sample_manifest');
					Route::post('sample_manifest', 'ViralbatchController@sample_manifest')->name('post.sample_manifest');
				});
				
				Route::get('summary/{batch}', 'ViralbatchController@summary');
				Route::post('summaries', 'ViralbatchController@summaries');
				Route::get('individual/{batch}', 'ViralbatchController@individual');
				Route::get('individual_pdf/{batch}', 'ViralbatchController@individual_pdf');
				Route::get('envelope/{batch}', 'ViralbatchController@envelope')->name('envelope');
				Route::get('email/{batch}', 'ViralbatchController@email')->name('email');

				Route::post('search/', 'ViralbatchController@search')->name('search');
			});
			Route::resource('viralbatch', 'ViralbatchController');
		});

		Route::post('county/search/', 'HomeController@countysearch')->name('county.search');
		Route::post('partner/search/', 'HomeController@partnersearch')->name('partner.search');

		Route::get('dashboard/{year?}/{month?}', 'DashboardController@index')->name('dashboard');
		Route::post('district/search/', 'DistrictController@search')->name('district.search');
		
		Route::get('downloads/{type}', 'HomeController@download')->name('downloads');

		Route::resource('district', 'DistrictController');

		// Start of Drug Resistance Routes

		Route::prefix('dr_dashboard')->name('dr_dashboard.')->group(function () {
			Route::get('/', 'DrDashboardController@index');
			Route::post('filter_any', 'DrDashboardController@filter_any');
			Route::post('filter_date', 'DrDashboardController@filter_date');
			Route::get('drug_resistance/{current_only?}', 'DrDashboardController@drug_resistance');
			Route::get('heat_map/{current_only?}', 'DrDashboardController@heat_map');
		});			

		Route::prefix('dr_testing')->name('dr_testing.')->group(function () {
			Route::get('/', 'DrDashboardTestingController@index');
			Route::get('testing', 'DrDashboardTestingController@testing');
			Route::get('rejected', 'DrDashboardTestingController@rejected');
		});				

		Route::prefix('dr_waterfall')->name('dr_waterfall.')->group(function () {
			Route::get('/', 'DrDashboardProposedController@index');
			Route::get('waterfall', 'DrDashboardProposedController@waterfall');
			Route::get('waterfall_table', 'DrDashboardProposedController@waterfall_table');
			Route::get('gender', 'DrDashboardProposedController@gender');
			Route::get('age', 'DrDashboardProposedController@age');
			Route::get('tat', 'DrDashboardProposedController@tat');
			Route::get('dr_reasons', 'DrDashboardProposedController@dr_reasons');
			Route::get('requests_table', 'DrDashboardProposedController@requests_table');
			Route::get('requests_chart', 'DrDashboardProposedController@requests_chart');
			Route::get('monthly_chart', 'DrDashboardProposedController@monthly_chart');
			Route::get('resistance_table', 'DrDashboardProposedController@resistance_table');
			Route::get('cross_resistance_table', 'DrDashboardProposedController@cross_resistance_table');
		});	

		Route::prefix('dr_formulation')->name('dr_formulation.')->group(function () {
			Route::get('/', 'DrDashboardFormulationController@index');	
			Route::get('formulation_resistance', 'DrDashboardFormulationController@formulation_resistance');	
		});	

		Route::prefix('dr_mutation')->name('dr_mutation.')->group(function () {
			Route::get('/', 'DrDashboardFormulationController@dr_mutation_index');	
			Route::get('mutation_frequency', 'DrDashboardFormulationController@mutation_frequency');	
		});		

		Route::post('dr_report', 'DrReportController@reports');

		Route::prefix('dr_sample')->name('dr_sample.')->group(function () {
			// Route::group(['middleware' => ['utype:5']], function () {
				Route::get('index/{sample_status?}/{date_start?}/{date_end?}/{facility_id?}/{subcounty_id?}/{partner_id?}', 'DrSampleController@index');
				Route::get('facility/{facility_id}', 'DrSampleController@facility')->name('facility');			
				Route::post('index', 'DrSampleController@sample_search');
				Route::post('search', 'DrSampleController@search');

				// Route::put('{drSample}', 'DrSampleController@update')->name('update');
				Route::get('vl_results/{drSample}', 'DrSampleController@vl_results')->name('vl_results');
				Route::get('results/{drSample}/{print?}', 'DrSampleController@results')->name('results');
				Route::get('download_results/{drSample}', 'DrSampleController@download_results')->name('download_results');
			// });
		});

		Route::group(['middleware' => ['utype:4']], function () {
			Route::resource('dr_patient', 'DrPatientController');

			Route::prefix('dr_sample')->name('dr_sample.')->group(function () {
				Route::get('create/{patient}', 'DrSampleController@create_from_patient');
				Route::get('create_remnant/{viralsample}', 'DrSampleController@create_from_viralsample');
				Route::get('email/{drSample}', 'DrSampleController@email');
				Route::get('report', 'DrSampleController@susceptability')->name('report');
			});
		});

		Route::resource('dr_sample', 'DrSampleController');

		Route::group(['middleware' => ['utype:4']], function () {


			Route::prefix('dr_extraction_worksheet')->name('dr_extraction_worksheet.')->group(function () {

				Route::get('index/{state?}/{date_start?}/{date_end?}', 'DrExtractionWorksheetController@index')->name('list');

				Route::get('create/{limit}', 'DrExtractionWorksheetController@create')->name('create_any');
				Route::get('gel_documentation/{drExtractionWorksheet}', 'DrExtractionWorksheetController@gel_documentation_form')->name('upload');
				Route::put('gel_documentation/{drExtractionWorksheet}', 'DrExtractionWorksheetController@gel_documentation')->name('upload');
				Route::get('download/{drExtractionWorksheet}', 'DrExtractionWorksheetController@download')->name('download');

				Route::get('print/{drExtractionWorksheet}', 'DrExtractionWorksheetController@print')->name('print');
				Route::get('cancel/{drExtractionWorksheet}', 'DrExtractionWorksheetController@cancel')->name('cancel');
				Route::get('reverse_upload/{drExtractionWorksheet}', 'DrExtractionWorksheetController@reverse_upload')->name('reverse_upload');

				Route::get('upload_worksheet_excel', 'DrExtractionWorksheetController@upload_worksheet_excel_page');
				Route::post('upload_worksheet_excel', 'DrExtractionWorksheetController@upload_worksheet_excel');
			});
			Route::resource('dr_extraction_worksheet', 'DrExtractionWorksheetController');


			Route::prefix('dr_worksheet')->name('dr_worksheet.')->group(function () {

				Route::get('index/{state?}/{date_start?}/{date_end?}', 'DrWorksheetController@index')->name('list');

				Route::get('create/{extraction_worksheet_id}', 'DrWorksheetController@create')->name('create_any');
				Route::get('upload/{worksheet}', 'DrWorksheetController@upload')->name('upload');
				Route::put('upload/{worksheet}', 'DrWorksheetController@save_results')->name('save_results');

				Route::get('approve/{worksheet}', 'DrWorksheetController@approve_results')->name('approve_results');
				Route::put('approve/{worksheet}', 'DrWorksheetController@approve')->name('approve');
				
				Route::get('create_plate/{worksheet}', 'DrWorksheetController@create_plate')->name('create_plate');
				Route::get('get_plate_result/{worksheet}', 'DrWorksheetController@get_plate_result')->name('get_plate_result');

				Route::get('print/{worksheet}', 'DrWorksheetController@print')->name('print');
				Route::get('cancel/{worksheet}', 'DrWorksheetController@cancel')->name('cancel');
				Route::get('cancel_upload/{worksheet}', 'DrWorksheetController@cancel_upload')->name('cancel_upload');

				// Download bulk template
				Route::get('download/{worksheet}', 'DrWorksheetController@download')->name('download');
				Route::get('abfiles/{worksheet}', 'DrWorksheetController@abfiles')->name('abfiles');

			});
			Route::resource('dr_worksheet', 'DrWorksheetController');
			
			Route::resource('dr_bulk_registration', 'DrBulkRegistrationController');
		});

		// End of Drug Resistance Routes


		Route::group(['middleware' => ['only_utype:2']], function () {
			Route::prefix('email')->name('email.')->group(function () {
				Route::get('preview/{email}', 'EmailController@demo')->name('demo');
				Route::post('preview/{email}', 'EmailController@demo_email')->name('demo_email');

				Route::get('download_attachment/{email}', 'EmailController@download_attachment');
				Route::get('attachment/{email}', 'EmailController@add_attachment');
				Route::put('attachment/{email}', 'EmailController@save_attachment');
				Route::delete('attachment/{attachment}', 'EmailController@delete_attachment');
			});
			Route::resource('email', 'EmailController');

			Route::get('lab', 'RandomController@labcontacts')->name('lab.edit');
			Route::put('lab', 'RandomController@savelabcontact')->name('lab.update');

			Route::get('partnercontacts', 'FacilityController@partnercontacts')->name('partnercontacts');

			Route::resource('muser', 'MuserController');
		});

		Route::group(['middleware' => ['only_utype:1']], function() {
			Route::get('lablogs/{year?}/{month?}', 'RandomController@lablogs')->name('lablogs');
			Route::post('lablogs', 'RandomController@lablogs');
			Route::get('equipmentbreakdown', 'RandomController@equipmentbreakdown')->name('equipmentbreakdown');
			Route::post('equipmentbreakdown', 'RandomController@equipmentbreakdown');
		});
		
		Route::group(['middleware' => ['utype:4']], function () {
			Route::get('facility/served', 'FacilityController@served');
			Route::get('facility/withoutemails', 'FacilityController@withoutemails')->name('withoutemails');
			Route::get('facility/withoutG4S', 'FacilityController@withoutG4S')->name('withoutG4S');
			Route::get('facility/contacts', 'FacilityController@filled_contacts')->name('facility.contacts');
			Route::get('facility/lab', 'FacilityController@lab')->name('facility.lab');
            Route::get('facility/noCode', 'FacilityController@noCode')->name('facility.noCode');
		});		
		Route::resource('facility', 'FacilityController');

		Route::get('/home', 'HomeController@index')->name('home');

		Route::get('reports', 'ReportController@index')->name('reports');
		Route::post('reports/dateselect', 'ReportController@dateselect')->name('dateselect');
		Route::post('reports', 'ReportController@generate')->name('reports');
		Route::post('reports/kitsconsumption', 'ReportController@consumption');
		Route::post('reports/kitsconsumption/update', 'ReportController@update_consumption');
		Route::get('facility/reports/{testtype?}', 'ReportController@index')->name('facility');
		Route::post('reports/worksheet', 'ReportController@worksheet')->name('worksheet');

		Route::get('reports/kits', 'KitsController@kits')->name('report.kits');
		Route::post('reports/kitdeliveries', 'KitsController@kits');
		Route::get('report/allocation/{allocation?}/{type?}/{approval?}', 'KitsController@allocation')->name('report.allocation');
		Route::put('kitallocation/{allocation}/edit', 'KitsController@editallocation');
		Route::get('printallocation/{allocation}/{testtype}', 'KitsController@printallocation');

		Route::prefix('patient')->name('patient.')->group(function () {
			Route::post('search/{facility_id?}', 'PatientController@search');
			Route::get('index/{facility_id?}', 'PatientController@index');	

			// Merging of patients	
			Route::get('{patient}/merge', 'PatientController@merge');		
			Route::put('{patient}/merge', 'PatientController@merge_patients');	

			// Transfer patient to a new facility	
			Route::get('{patient}/transfer', 'PatientController@transfer');		
			Route::put('{patient}/transfer', 'PatientController@transfer_patient');
		});
		Route::resource('patient', 'PatientController');

		Route::get('allocation', 'TaskController@allocation')->name('allocation');
		Route::post('allocation', 'TaskController@allocation')->name('post.allocation');
		Route::get('consumption/{guide?}', 'TaskController@consumption')->name('consumption');
		Route::post('consumption', 'TaskController@consumption');
		Route::get('equipmentlog', 'TaskController@equipmentlog')->name('equipmentlog');
		Route::post('equipmentlog', 'TaskController@equipmentlog');
		Route::get('/pending', 'TaskController@index')->name('pending');
		Route::get('/performancelog', 'TaskController@performancelog')->name('performancelog');
		Route::post('/performancelog', 'TaskController@performancelog');
		// Route::get('/kitsdeliveries/{platform?}', 'TaskController@addKitDeliveries')->name('kitsdeliveries');
		// Route::post('/kitsdeliveries', 'TaskController@addKitDeliveries')->name('kitsdeliveries');
		

		Route::prefix('viralpatient')->name('viralpatient.')->group(function () {
			Route::post('search/{facility_id?}/{female?}', 'ViralpatientController@search');
			Route::post('nat_id', 'ViralpatientController@nat_id');
			Route::post('cnt_id', 'ViralpatientController@cnt_id');
			Route::get('index/{facility_id?}', 'ViralpatientController@index');	
			Route::get('dr/{patient}', 'ViralpatientController@dr');	

			// Merging of patients	
			Route::get('{patient}/merge', 'ViralpatientController@merge');		
			Route::put('{patient}/merge', 'ViralpatientController@merge_patients');	

			// Transfer patient to a new facility	
			Route::get('{patient}/transfer', 'ViralpatientController@transfer');		
			Route::put('{patient}/transfer', 'ViralpatientController@transfer_patient');
		});

		Route::group(['middleware' => ['only_utype:2']], function () {
			Route::prefix('email')->name('email.')->group(function () {
				Route::get('preview/{email}', 'EmailController@demo')->name('demo');
				Route::post('preview/{email}', 'EmailController@demo_email')->name('demo_email');

				Route::get('download_attachment/{email}', 'EmailController@download_attachment');
				Route::get('attachment/{email}', 'EmailController@add_attachment');
				Route::post('attachment/{email}', 'EmailController@save_attachment');
				Route::delete('attachment/{attachment}', 'EmailController@delete_attachment');
			});
			Route::resource('email', 'EmailController');

			Route::get('lab', 'RandomController@labcontacts')->name('lab.edit');
			Route::put('lab', 'RandomController@savelabcontact')->name('lab.update');

			Route::resource('muser', 'MuserController');
		});

		Route::group(['middleware' => ['only_utype:1']], function() {
			Route::get('lablogs/{year?}/{month?}', 'RandomController@lablogs')->name('lablogs');
			Route::post('lablogs', 'RandomController@lablogs');
			Route::get('equipmentbreakdown', 'RandomController@equipmentbreakdown')->name('equipmentbreakdown');
			Route::post('equipmentbreakdown', 'RandomController@equipmentbreakdown');
		});
		
		Route::group(['middleware' => ['utype:4']], function () {
			Route::get('facility/served', 'FacilityController@served');
			Route::get('facility/withoutemails', 'FacilityController@withoutemails')->name('withoutemails');
			Route::get('facility/withoutG4S', 'FacilityController@withoutG4S')->name('withoutG4S');
			Route::get('facility/contacts', 'FacilityController@filled_contacts')->name('facility.contacts');
			Route::get('facility/lab', 'FacilityController@lab')->name('facility.lab');
		});		
		Route::resource('facility', 'FacilityController');

		Route::get('/home', 'HomeController@index')->name('home');

		Route::get('reports', 'ReportController@index')->name('reports');
		Route::post('reports/dateselect', 'ReportController@dateselect')->name('dateselect');
		Route::post('reports', 'ReportController@generate')->name('reports');
		Route::post('reports/kitsconsumption', 'ReportController@consumption');
		Route::get('facility/reports/{testtype?}', 'ReportController@index')->name('facility');

		Route::get('reports/kits', 'KitsController@kits')->name('report.kits');
		Route::post('reports/kitdeliveries', 'KitsController@kits');
		Route::get('report/allocation/{allocation?}/{type?}/{approval?}', 'KitsController@allocation')->name('report.allocation');
		Route::put('kitallocation/{allocation}/edit', 'KitsController@editallocation');
		Route::get('printallocation/{allocation}/{testtype}', 'KitsController@printallocation');

		Route::prefix('patient')->name('patient.')->group(function () {
			Route::post('search/{facility_id?}', 'PatientController@search');
			Route::get('index/{facility_id?}', 'PatientController@index');	

			// Merging of patients	
			Route::get('{patient}/merge', 'PatientController@merge');		
			Route::put('{patient}/merge', 'PatientController@merge_patients');	

			// Transfer patient to a new facility	
			Route::get('{patient}/transfer', 'PatientController@transfer');		
			Route::put('{patient}/transfer', 'PatientController@transfer_patient');
		});
		Route::resource('patient', 'PatientController');
		
		Route::post('viralpatient/search/', 'ViralpatientController@search');
		Route::prefix('viralpatient')->name('viralpatient.')->group(function () {
			Route::post('search/{facility_id?}/{female?}', 'ViralpatientController@search');
			Route::get('index/{facility_id?}', 'ViralpatientController@index');	

			// Merging of patients	
			Route::get('{patient}/merge', 'ViralpatientController@merge');		
			Route::put('{patient}/merge', 'ViralpatientController@merge_patients');	

			// Transfer patient to a new facility	
			Route::get('{patient}/transfer', 'ViralpatientController@transfer');		
			Route::put('{patient}/transfer', 'ViralpatientController@transfer_patient');
		});

		Route::resource('viralpatient', 'ViralpatientController');

		Route::group(['middleware' => ['utype:7']], function () {
			Route::prefix('sample')->name('sample.')->group(function () {
				Route::post('new_patient', 'SampleController@new_patient');
				Route::post('similar', 'SampleController@similar');
				Route::get('print/{sample}', 'SampleController@individual');
				
				Route::group(['middleware' => ['utype:4']], function () {
					Route::get('runs/{sample}', 'SampleController@runs');	
					Route::get('transfer/{sample}', 'SampleController@transfer');
					Route::get('release/{sample}', 'SampleController@release_redraw');	
					Route::get('return_for_testing/{sample}', 'SampleController@return_for_testing');	
					Route::get('unreceive/{sample}', 'SampleController@unreceive');	
				});

				Route::group(['middleware' => ['only_utype:2']], function () {	
					Route::get('transfer_samples/{facility_id?}', 'SampleController@transfer_samples_form');	
					Route::post('transfer_samples', 'SampleController@transfer_samples');	
				});

				Route::get('upload', 'SampleController@site_sample_page');
				Route::post('upload', 'SampleController@upload_site_samples');

				Route::get('sms_view', 'DatatableController@sms_view');
				Route::get('sms_log', 'SampleController@list_sms');
				Route::get('sms/{sample}', 'SampleController@send_sms');

				Route::get('create_poc', 'SampleController@create_poc');
				Route::get('list_poc/{param?}', 'SampleController@list_poc');
				Route::get('{sample}/edit_result', 'SampleController@edit_poc');
				Route::put('{sample}/edit_result', 'SampleController@save_poc');

				Route::post('search', 'SampleController@search');		
				Route::post('ord_no', 'SampleController@ord_no');		
			});
			Route::resource('sample', 'SampleController');
		});

		Route::get('user/passwordReset/{user?}', 'UserController@passwordreset')->name('passwordReset');
		Route::get('user/switch_user/{user?}', 'UserController@switch_user')->name('switch_user');
		Route::put('user/password_reset/{id?}', 'UserController@edit_password')->name('edit_password');
		// Route::put('user/password_reset/{id?}', 'UserController@edit_password')->name('edit_password');

		Route::group(['middleware' => ['only_utype:2']], function () {
			Route::get('users', 'UserController@index')->name('users');
			Route::get('user/add', 'UserController@create')->name('user.add');
			Route::get('user/status/{user}', 'UserController@delete')->name('user.delete');
			Route::get('users/activity/{user?}/{year?}/{month?}', 'UserController@activity')->name('user.activity');
			Route::get('allocationcontact/{user}', 'UserController@allocationcontact');
			Route::resource('user', 'UserController');	
		});

		Route::group(['middleware' => ['utype:9']], function () {
			Route::prefix('viralsample')->name('viralsample.')->group(function () {



				Route::get('create/{sampletype?}', 'ViralsampleController@create');

				Route::post('getPatientDetails', 'ViralsampleController@getPatientDetails');
				Route::post('getRecPatient', 'ViralsampleController@getRecPatient');

				Route::get('nhrl', 'ViralsampleController@nhrl_samples')->name('nhrl');
				Route::post('nhrl', 'ViralsampleController@approve_nhrl');

				Route::get('upload', 'ViralsampleController@site_sample_page');
				Route::post('upload', 'ViralsampleController@upload_site_samples');

				Route::get('sms_view', 'DatatableController@sms_view');
				Route::get('sms_log', 'ViralsampleController@list_sms');
				Route::get('sms/{sample}', 'ViralsampleController@send_sms');

				Route::post('new_patient', 'ViralsampleController@new_patient');
				Route::post('similar', 'ViralsampleController@similar');
				Route::get('print/{sample}', 'ViralsampleController@individual');

				Route::group(['middleware' => ['utype:4']], function () {
					Route::get('potential_dr', 'ViralsampleController@potential_dr');	

					Route::get('runs/{sample}', 'ViralsampleController@runs');		
					Route::get('transfer/{sample}', 'ViralsampleController@transfer');	
					Route::get('release/{sample}', 'ViralsampleController@release_redraw');	
					Route::get('return_for_testing/{sample}', 'ViralsampleController@return_for_testing');	
					Route::get('unreceive/{sample}', 'ViralsampleController@unreceive');	
				});

				Route::group(['middleware' => ['only_utype:2']], function () {	
					Route::get('transfer_samples/{facility_id?}', 'ViralsampleController@transfer_samples_form');	
					Route::post('transfer_samples', 'ViralsampleController@transfer_samples');	
				});

				Route::get('create_poc', 'ViralsampleController@create_poc');
				Route::get('list_poc/{param?}', 'ViralsampleController@list_poc');
				Route::get('list_vl_upi_line_list', 'ViralsampleController@list_vl_upi_line_list');
				Route::get('{sample}/edit_result', 'ViralsampleController@edit_poc');
				Route::put('{sample}/edit_result', 'ViralsampleController@save_poc');

				Route::post('search', 'ViralsampleController@search');		
				Route::post('ord_no', 'ViralsampleController@ord_no');

				Route::group(['middleware' => ['utype:0']], function(){
					Route::get('excelupload', 'ViralsampleController@excelupload');
					Route::post('excelupload', 'ViralsampleController@excelupload');
					Route::get('exceluploaddelete', 'ViralsampleController@deleteexcelupload');
					Route::post('exceluploaddelete', 'ViralsampleController@deleteexcelupload');
					Route::get('extractexcelresult', 'ViralsampleController@extract_excel_results');
					Route::post('extractexcelresult', 'ViralsampleController@extract_excel_results');
				});
			});
			Route::resource('viralsample', 'ViralsampleController');
			Route::get('Viralpatient','ViralpatientController@verify_upi_number')->name('verify_upi_number');
		});

		Route::prefix('datatable')->name('datatable.')->group(function () {	
			Route::post('sms_log/{param}', 'DatatableController@sms_log');
			Route::post('download_sms_excel/{param}', 'DatatableController@download_sms_excel');
		});


		Route::group(['middleware' => ['utype:4']], function () {

			Route::prefix('worksheet')->name('worksheet.')->group(function () {

				Route::get('index/{state?}/{date_start?}/{date_end?}', 'WorksheetController@index')->name('list');

				Route::get('set_sampletype/{machine_type}/{limit?}', 'WorksheetController@set_sampletype_form')->name('set_sampletype_form');
				Route::post('set_sampletype', 'WorksheetController@set_sampletype')->name('set_sampletype');

				Route::get('create/{machine_type}/{limit?}', 'WorksheetController@create')->name('create_any');
				Route::get('find/{worksheet}', 'WorksheetController@find')->name('find');
				Route::get('print/{worksheet}', 'WorksheetController@print')->name('print');
				Route::get('labels/{worksheet}', 'WorksheetController@labels')->name('labels');
				Route::get('cancel/{worksheet}', 'WorksheetController@cancel')->name('cancel');
				Route::get('rerun_worksheet/{worksheet}', 'WorksheetController@rerun_worksheet')->name('rerun_worksheet');
				Route::get('convert/{worksheet}/{machine_type}', 'WorksheetController@convert_worksheet')->name('convert');

				Route::group(['middleware' => ['only_utype:1']], function () {
					Route::get('cancel_upload/{worksheet}', 'WorksheetController@cancel_upload')->name('cancel_upload');
					Route::get('reverse_upload/{worksheet}', 'WorksheetController@reverse_upload')->name('reverse_upload');
					Route::get('cns/{worksheet}', 'WorksheetController@cns_worksheet')->name('cns_worksheet');
					Route::get('upload/{worksheet}', 'WorksheetController@upload')->name('upload');
					Route::put('upload/{worksheet}', 'WorksheetController@save_results')->name('save_results');
					Route::get('approve/{worksheet}', 'WorksheetController@approve_results')->name('approve_results');
					Route::put('approve/{worksheet}', 'WorksheetController@approve')->name('approve');
				});

				Route::post('search/', 'WorksheetController@search')->name('search');
			});
			Route::get('worksheetserverside/', 'WorksheetController@getworksheetserverside')->name('worksheetserverside');

			Route::resource('worksheet', 'WorksheetController');


			Route::prefix('viralworksheet')->name('viralworksheet.')->group(function () {

				Route::get('index/{state?}/{date_start?}/{date_end?}', 'ViralworksheetController@index')->name('list');

				Route::get('set_sampletype/{machine_type}/{calibration?}/{limit?}', 'ViralworksheetController@set_sampletype_form')->name('set_sampletype_form');
				Route::post('set_sampletype', 'ViralworksheetController@set_sampletype')->name('set_sampletype');

				Route::get('create/{sampletype}/{machine_type?}/{calibration?}/{limit?}/{entered_by?}', 'ViralworksheetController@create')->name('create_any');		
				Route::get('find/{worksheet}', 'ViralworksheetController@find')->name('find');
				Route::get('print/{worksheet}', 'ViralworksheetController@print')->name('print');
				Route::get('labels/{worksheet}', 'ViralworksheetController@labels')->name('labels');
				Route::get('cancel/{worksheet}', 'ViralworksheetController@cancel')->name('cancel');
				Route::get('rerun_worksheet/{worksheet}', 'ViralworksheetController@rerun_worksheet')->name('rerun_worksheet');
				Route::get('convert/{worksheet}/{machine_type}/', 'ViralworksheetController@convert_worksheet')->name('convert');

				Route::group(['middleware' => ['only_utype:1']], function () {
					Route::get('download_dump/{worksheet}', 'ViralworksheetController@download_dump')->name('download_dump');
					Route::get('cancel_upload/{worksheet}', 'ViralworksheetController@cancel_upload')->name('cancel_upload');
					Route::get('reverse_upload/{worksheet}', 'ViralworksheetController@reverse_upload')->name('reverse_upload');
					Route::get('cns/{worksheet}', 'ViralworksheetController@cns_worksheet')->name('cns_worksheet');
					Route::get('upload/{worksheet}', 'ViralworksheetController@upload')->name('upload');
					Route::put('upload/{worksheet}', 'ViralworksheetController@save_results')->name('save_results');
					Route::get('approve/{worksheet}', 'ViralworksheetController@approve_results')->name('approve_results');
					Route::put('approve/{worksheet}', 'ViralworksheetController@approve')->name('approve');
				});
				

				Route::group(['middleware' => ['utype:0']], function() {
					Route::get('exceluploadworksheet', 'ViralworksheetController@exceluploadworksheet');
					Route::post('exceluploadworksheet', 'ViralworksheetController@exceluploadworksheet');
				});

				Route::post('search/', 'ViralworksheetController@search')->name('search');

			});
			Route::resource('viralworksheet', 'ViralworksheetController');
		});


		Route::any('covidkits/deliveries', 'CovidConsumptionController@deliveries');
	});

	Route::get('allocation', 'TaskController@allocation')->name('allocation');
	Route::post('allocation', 'TaskController@allocation')->name('post.allocation');
	Route::get('postnullallocation', 'TaskController@nullallocation')->name('null.allocation');
	Route::get('consumption/{guide?}', 'ConsumptionController@consumption')->name('consumption');
	Route::post('consumption', 'ConsumptionController@consumption');
	Route::post('saveconsumption', 'ConsumptionController@saveconsumption');
	Route::get('equipmentlog', 'TaskController@equipmentlog')->name('equipmentlog');
	Route::post('equipmentlog', 'TaskController@equipmentlog');
	Route::get('/pending', 'TaskController@index')->name('pending');
	Route::get('/performancelog', 'TaskController@performancelog')->name('performancelog');
	Route::post('/performancelog', 'TaskController@performancelog');
	Route::get('/kitsdeliveries/{platform?}', 'DeliveriesController@addKitDeliveries')->name('kitsdeliveries');
	Route::post('/kitsdeliveries', 'DeliveriesController@addKitDeliveries')->name('kitsdeliveries');
	Route::post('submitkitsdeliveries', 'DeliveriesController@saveDeliveries')->name('submitkitsdeliveries');
	Route::prefix('covidkits')->name('covidkits.')->group(function() {
		Route::get('/', 'CovidConsumptionController@index');
		Route::post('consumption', 'CovidConsumptionController@submitConsumption');
		Route::get('reports', 'CovidConsumptionController@reports');
		Route::post('allocation', 'CovidConsumptionController@submitAllocation');
		Route::get('/allocation/refresh', 'CovidConsumptionController@refresh_allocations');
	});

	Route::prefix('cd4')->name('cd4.')->group(function(){
		Route::prefix('sample')->name('sample.')->group(function(){
			Route::get('dispatch/{state}', 'Cd4SampleController@dispatch')->name('dispatch');
			Route::get('facility/{facility}', 'Cd4SampleController@facility')->name('facility');
			Route::get('print/{sample}', 'Cd4SampleController@print')->name('print');
			Route::get('printresult/{sample}', 'Cd4SampleController@printresult')->name('printresult');
			Route::get('search/{sample}', 'Cd4SampleController@searchresult')->name('searchresult');
			Route::get('delete/{sample}', 'Cd4SampleController@destroy')->name('delete');
			Route::post('search', 'Cd4SampleController@search')->name('search');
		});
		Route::resource('sample', 'Cd4SampleController');
		Route::prefix('patient')->name('patient.')->group(function(){
			Route::post('new', 'Cd4PatientController@new_patient')->name('new');			
			Route::get('search_name/{patient_name}', 'Cd4PatientController@search_name')->name('search_name');
			Route::post('search_name', 'Cd4PatientController@search_name');
			Route::get('search_record_no/{recordno}', 'Cd4PatientController@search_record_no')->name('search_record_no');
			Route::post('search_record_no', 'Cd4PatientController@search_record_no');
		});
		Route::resource('patients', 'Cd4PatientController');

		Route::prefix('reports')->name('reports.')->group(function(){
			Route::get('/', 'ReportController@cd4reports')->name('cd4reports');
			Route::post('dateselect', 'ReportController@dateselect')->name('dateselect');
			Route::post('generate', 'ReportController@generate')->name('generate');
		});
		
		Route::prefix('worksheet')->name('worksheet.')->group(function(){
			Route::get('cancel/{worksheet}', 'Cd4WorksheetController@cancel')->name('cancel');
			Route::get('confirm/{worksheet}', 'Cd4WorksheetController@confirm_upload')->name('confirm');
			Route::put('save/{worksheet}', 'Cd4WorksheetController@save_upload');
			Route::post('search', 'Cd4WorksheetController@search')->name('search');
			Route::get('state/{state}', 'Cd4WorksheetController@state')->name('state');
			Route::get('create/{limit}', 'Cd4WorksheetController@create');
			Route::get('index/{state}', 'Cd4WorksheetController@index')->name('index');
			Route::get('print/{worksheet}', 'Cd4WorksheetController@print')->name('print');
			Route::get('upload/{worksheet}', 'Cd4WorksheetController@upload')->name('upload');
			Route::put('upload/{worksheet}', 'Cd4WorksheetController@upload');
		});
		Route::resource('worksheet', 'Cd4WorksheetController');
	});

	Route::group(['middleware' => ['only_utype:5']], function () {

		Route::prefix('worklist')->name('worklist.')->group(function () {
			Route::get('index/{testtype?}', 'WorklistController@index')->name('list');
			Route::get('create/{testtype}', 'WorklistController@create')->name('create_any');
			Route::get('print/{worklist}', 'WorklistController@print')->name('print');
		});
		Route::resource('worklist', 'WorklistController', ['except' => ['edit']]);
	});

	Route::group(['middleware' => ['only_utype:0']], function(){
		Route::get('abbottdeliveries/{id}/recompute', 'AbbottDeliveriesController@recompute');
		Route::get('abbottdeliveries/{id}/restore', 'AbbottDeliveriesController@restore');
		Route::get('abbottdeliveries/{id}/delete', 'AbbottDeliveriesController@delete');
		Route::resource('abbottdeliveries', 'AbbottDeliveriesController');

		Route::get('abbottprocurement/{id}/recomputeending', 'AbbottProcurementController@recomputeending');
		Route::resource('abbottprocurement', 'AbbottProcurementController');

		Route::get('taqmandeliveries/{id}/recompute', 'TaqmanDeliveriesController@recompute');
		Route::get('taqmandeliveries/{id}/restore', 'TaqmanDeliveriesController@restore');
		Route::get('taqmandeliveries/{id}/delete', 'TaqmanDeliveriesController@delete');
		Route::resource('taqmandeliveries', 'TaqmanDeliveriesController');
		
		Route::get('taqmanprocurement/{id}/recomputeending', 'TaqmanProcurementController@recomputeending');
		Route::resource('taqmanprocurement', 'TaqmanProcurementController');
	});

});
