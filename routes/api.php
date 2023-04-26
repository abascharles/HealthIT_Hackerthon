<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {
      $api->group(['namespace' => 'App\\Api\\V1\\Controllers'], function(Router $api) {


        $api->group(['prefix' => 'auth'], function(Router $api) {
            $api->get('integration','\App\Http\Controllers\FacilityController@integration_update');
            $api->get('facility','\App\Http\Controllers\FacilityController@getFacilityNotInKhmfl');
            $api->post('signup', 'SignUpController@signUp');

            // $api->group(['middleware' => 'api.throttle', 'limit' => 1, 'expires' => 1], function(Router $api) {
                $api->post('login', 'LoginController@login');
            // });

            $api->post('recovery', 'ForgotPasswordController@sendResetEmail');
            $api->post('reset', 'ResetPasswordController@resetPassword');

            $api->post('logout', 'LogoutController@logout');
            $api->post('refresh', 'RefreshController@refresh');
            $api->get('me', 'UserController@me');
        });

        $api->get('hello', 'RandomController@hello');
        $api->get('hello_nascop', 'RandomController@hello_nascop');

        $api->get('covid/allocation_details/{allocation_date}', 'AllocationController@allocation_details');

        $api->post('node/result', 'NodeResultController@result');


        $api->group(['middleware' => 'jwt.auth'], function(Router $api) {
            $api->get('protected', 'RandomController@protected_route');

            $api->group(['middleware' => 'jwt.refresh'], function(Router $api) {
                $api->get('refresh', 'RandomController@refresh_route');
            });


            $api->resource('nat_covid_sample', 'NatCovidSampleController');
            
            // $api->resource('quarantine_site', 'QuarantineSiteController');

            $api->group(['prefix' => 'update'], function(Router $api) {

                $api->post('covid_samples', 'NatCovidSampleController@update_samples');
                $api->post('covid_patients', 'NatCovidSampleController@update_patients');

            });


            $api->post('email', 'RandomController@email');
            $api->post('attachment', 'RandomController@attachment');
            $api->resource('facility', 'FacilityController');

            $api->post('covid_sample/transfer', 'CovidSampleController@transfer');
            $api->resource('covid_sample', 'CovidSampleController');
            // $api->resource('covid_patient', 'CovidPatientController');

            $api->resource('batch', 'BatchController');
            $api->resource('viralbatch', 'ViralbatchController');

            $api->resource('patient', 'PatientController');
            $api->resource('viralpatient', 'ViralpatientController');

            $api->post('sample/transfer', 'SampleController@transfer');
            $api->resource('sample', 'SampleController');
            
            $api->post('viralsample/transfer', 'ViralsampleController@transfer');
            $api->resource('viralsample', 'ViralsampleController');

            $api->put('allocation', 'AllocationController@update');
            $api->resource('allocation', 'AllocationController');
        });

        
        // $api->group(['middleware' => 'jwt.auth'], function(Router $api) {

            $api->post('weekly_alerts', 'FunctionController@weekly_alerts');
            
            $api->post('eid', 'EidController@eid');    
            $api->post('eid_complete', 'EidController@complete_result');  

            $api->post('vl', 'VlController@vl');        
            $api->post('vl_complete', 'VlController@complete_result');

            $api->post('cd4', 'Cd4Controller@partial');
            $api->post('cd4_complete', 'Cd4Controller@complete_result');

            $api->post('crag', 'CragController@partial');
            // $api->post('crag_complete', 'CragController@complete_result');

            $api->group(['middleware' => 'api.throttle', 'limit' => \App\Common::api_limit(), 'expires' => 1], function(Router $api) {
                $api->get('function', 'FunctionController@data_functions');
                $api->post('function', 'FunctionController@data_functions');
                $api->post('function/eid', 'FunctionController@data_functions');
                $api->post('function/vl', 'FunctionController@data_functions');
                $api->post('function/cd4', 'FunctionController@data_functions');
            });
            
        // });

        $api->group(['middleware' => 'api.throttle', 'limit' => 1, 'expires' => 1], function(Router $api) {
            $api->post('covid/cif_samples', 'CovidController@cif_samples');
        });

        $api->group(['prefix' => 'consumption'], function (Router $api) {
            $api->post('covid', 'CovidController@ku_consumption');
        });
                
        $api->resource('covid', 'CovidController');
        
        $api->group(['prefix' => 'test'], function(Router $api) {
            $api->resource('covid', 'CovidController');
        });
    });


});