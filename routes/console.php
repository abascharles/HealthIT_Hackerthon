<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('create:token', function () {
    \App\ApiToken::createToken();
})->describe('Synch to NPHL');

Artisan::command('nphl', function () {
    \App\Covid::synch_to_nphl();
})->describe('Synch to NPHL');

Artisan::command('backlog', function () {
    \App\Random::county_backlog();
    // \App\Random::backlog_report('eid');
    // \App\Random::backlog_report('vl');
})->describe('Backlog report');

Artisan::command('mysql8', function () {
    \App\AlterRegimen::mysql8_update();
})->describe('Mysql 8 update');

Artisan::command('knh:switch', function () {
    // \App\Random::knh_switch_list();
    \App\Random::knh_update_ccc();
})->describe('KNH switch list');

Artisan::command('clean:emails', function(){
    $str = \App\Synch::clean_emails();
    $this->info($str);
})->describe('Clean emails which have an issue.');

Artisan::command('dr:dispatch', function(){
    $str = \App\MiscDr::send_completed_results();
    $this->info($str);
})->describe('Email results to facilities.');

Artisan::command('dr:generate-list', function(){
    $str = \App\MiscViral::generate_dr_list();
    $this->info($str);
})->describe('Generate a list of potential dr patients.');

Artisan::command('dr:create-plates', function(){
    $str = \App\MiscDr::send_to_exatype();
    $this->info($str);
})->describe('Create plates on exatype system.');

Artisan::command('dr:fetch-results', function(){
    $str = \App\MiscDr::fetch_results();
    $this->info($str);
})->describe('Fetch results from exatype system.');

Artisan::command('dr:current-drug', function(){
    $str = \App\MiscDrOld::set_current_drug();
    $this->info($str);
})->describe('Set current drug.');

Artisan::command('dr:nhrl', function(){
    $str = \App\MiscDr::nhrl_worksheets();
    $this->info($str);
})->describe('Set current drug.');

Artisan::command('compute:tat5', function(){
    \App\Common::save_tat5('eid');
    \App\Common::save_tat5('vl');
})->describe('Compute Tat 5.');

Artisan::command('compute:dr-tat', function(){
    $my = new \App\MiscDr;
    $str = $my->save_dr_tat();
    $str .= "Completed DR tat computation at " . date('d/m/Y h:i:s a', time()). "\n";
    $this->info($str);
})->describe('Compute DR Tat.');

Artisan::command('compute:eid-tat', function(){
    $my = new \App\Misc;
    $str = $my->compute_tat(\App\SampleView::class, \App\Sample::class);
    $str .= "Completed eid tat computation at " . date('d/m/Y h:i:s a', time()). "\n";
    $this->info($str);
})->describe('Compute Eid Tat.');

Artisan::command('compute:vl-tat', function(){
    $my = new \App\MiscViral;
    $str = $my->compute_tat(\App\ViralsampleView::class, \App\Viralsample::class);
    $str .= "Completed vl tat computation at " . date('d/m/Y h:i:s a', time()). "\n";
    $this->info($str);
})->describe('Compute Vl Tat.');

Artisan::command('compute:vl-stat {sample_id}', function($sample_id){
    $my = new \App\MiscViral;
    $str = $my->compute_tat_sample(\App\ViralsampleView::class, \App\Viralsample::class, $sample_id);
    $str .= "Completed vl at " . date('d/m/Y h:i:s a', time()). "\n";
    $this->info($str);
})->describe('Compute Vl Tat.');

Artisan::command('dispatch:results', function(){
    $str = \App\Common::dispatch_results('eid');
    $str = \App\Common::dispatch_results('vl');
    $this->info($str);
})->describe('Send emails for dispatched batches.');

Artisan::command('dispatch:critical', function(){
 //   $str = \App\Synch::synchPartnerFacilities();
    $str = \App\Common::critical_results('eid');
    $str = \App\Common::critical_results('vl');
    $this->info($str);
})->describe('Send emails for critical results.');

Artisan::command('dispatch:mlab', function(){
    $str = \App\Misc::send_to_mlab();
    $str .= \App\MiscViral::send_to_mlab();
    $this->info($str);
})->describe('Post dispatched results to mlab.');

Artisan::command('dispatch:nhrl', function(){
    \App\Common::nhrl('eid');
    \App\Common::nhrl('vl');
})->describe('Set NHRL & Edarp samples to be dispatched.');

Artisan::command('dispatch:covid', function(){
    \App\MiscCovid::dispatch_covid();
})->describe('Email Covid Results.');

Artisan::command('input-complete', function(){
    $str = \App\Common::input_complete_batches('eid');
    $str = \App\Common::input_complete_batches('vl');
    $this->info($str);
})->describe('Mark batches as input completed.');

Artisan::command('batch-complete', function(){
    $str = \App\Common::check_batches('eid');
    $str = \App\Common::check_batches('vl');
    $this->info($str);
})->describe('Check if batch is ready for dispatch.');

Artisan::command('fix:noage', function(){
    $str = \App\Common::fix_no_age('eid');
    $str = \App\Common::fix_no_age('vl');
    $this->info($str);
})->describe('Fix no age.');

Artisan::command('delete:delayed-batches', function(){
    \App\Common::delete_delayed_batches('eid');
    \App\Common::delete_delayed_batches('vl');
})->describe('Delete batches that have not been received after 2 weeks.');

Artisan::command('transfer:missing-samples', function(){
    $str = \App\Common::transfer_delayed_samples('eid');
    $str .= \App\Common::transfer_delayed_samples('vl');
    $this->info($str);
})->describe('Transfer samples delaying batches to new batches.');

Artisan::command('transfer:delayed-samples', function(){
    $str = \App\Common::transfer_delayed_samples('eid', false);
    $str .= \App\Common::transfer_delayed_samples('vl', false);
    $this->info($str);
})->describe('Transfer samples delaying batches to new batches.');

Artisan::command('notify:missing-samples', function(){
    $str = \App\Common::delayed_samples_notification('eid');
    $str .= \App\Common::delayed_samples_notification('vl');
    $this->info($str);
})->describe('Notify lab of batches that have not been received despite a long duration.');

Artisan::command('reject:missing-samples', function(){
    $str = \App\Common::reject_delayed_samples('eid');
    $str .= \App\Common::reject_delayed_samples('vl');
    $this->info($str);
})->describe('Reject samples that have not been received despite a long duration.');

Artisan::command('delete:empty-batches', function(){
    \App\Misc::delete_empty_batches();
    \App\MiscViral::delete_empty_batches();
})->describe('Delete empty batches.');

Artisan::command('delete:pdfs', function(){
    $str = \App\Common::delete_folder(storage_path('app/batches'));
    $str = \App\Common::delete_folder(storage_path('app/public/results'));
    $this->info($str);
})->describe('Delete pdfs and excel sheets from hard drive.');

Artisan::command('lablog', function(){
    $str = \App\Synch::labactivity('eid');
	$str = \App\Synch::labactivity('vl');

    /*if(env('APP_LAB') == 2){
        $str = \App\Synch::labactivity('eid', 7);
        $str = \App\Synch::labactivity('vl', 7);

        $str = \App\Synch::labactivity('eid', 10);
        $str = \App\Synch::labactivity('vl', 10);
    }*/
    $this->info($str);
})->describe('Send lablog data to national.');

// Artisan::command('synch:vl-patients', function(){
// 	$str = \App\Synch::synch_vl_patients();
//     $this->info($str);
// })->describe('Synch vl patients to the national database.');

Artisan::command('send:worksheet_data', function(){
    $str = \App\Random::worksheets_data();
    $this->info($str);
})->describe('Send worksheets data.');

Artisan::command('send:nodata', function(){
    $str = \App\Common::no_data_report('eid');
    $str = \App\Common::no_data_report('vl');
    $this->info($str);
})->describe('Send no data report.');

Artisan::command('send:gender', function(){
    $str = \App\Nat::save_gender_results();
    $str = \App\Nat::save_gender_ordering_results();
    $this->info($str);
})->describe('Send gender suppression data.');

Artisan::command('send:communication', function(){
    $str = \App\Common::send_communication();
    $this->info($str);
})->describe('Send any pending emails.');

Artisan::command('send:sms', function(){
    $str = \App\Misc::patient_sms();
    $str .= \App\MiscViral::patient_sms();
    $this->info($str);
})->describe('Send result sms.');

Artisan::command('create:weekly-backlog', function(){
    $str = \App\Console::create_alert_row();
    $this->info($str);
})->describe('Create edarp weekly backlog row.');

Artisan::command('send:weekly-activity', function(){
    $str = \App\Console::send_weekly_activity();
    if(env('APP_LAB') == 1) $str .= \App\Console::edarp_weekly_activity();
    $this->info($str);
})->describe('Send out weekly activity sms alert.');

Artisan::command('send:weekly-backlog', function(){
    $str = \App\Console::send_weekly_backlog();
    $this->info($str);
})->describe('Send out weekly backlog sms alert.');

Artisan::command('send:labtrackermail', function() {
    $lastmonth = date('Y-m-d', strtotime("-1 Month", strtotime(date('Y-m-d'))));
    $year = date('Y', strtotime($lastmonth));
    $month = date('m', strtotime($lastmonth));
    $str = \App\Common::send_lab_tracker($year, $month);
    $this->info($str);
})->describe('Send out monthly labtracker report email');

Artisan::command('synch:covid', function(){
    $str = \App\Synch::synch_covid();
    $str .= \App\Synch::synch_updates('covid');
    $this->info($str);
})->describe('Synch covid samples to the national database.');

Artisan::command('synch:covidconsumptions', function(){
    $str =  \App\Synch::synchCovidConsumption();
    $this->info($str);
})->describe('Sync covid consumptions to the national database.');


Artisan::command('synch:patients', function(){
    // if($type == 'eid') $str = \App\Synch::synch_eid_patients();
    // else { $str = \App\Synch::synch_vl_patients(); }  
    $str = \App\Synch::synch_eid_patients();  
    $str .= \App\Synch::synch_vl_patients();  
    $this->info($str);
})->describe('Synch patients to the national database.');

Artisan::command('synch:batches', function(){
    $str = \App\Synch::synch_batches('eid');
    $str = \App\Synch::synch_batches('vl');
    
    $str = \App\Synch::synch_batches_odd('eid');
	$str = \App\Synch::synch_batches_odd('vl');
    $this->info($str);
})->describe('Synch batches to the national database.');

Artisan::command('synch:worksheets', function(){
    $str = \App\Synch::synch_worksheets('eid');
	$str = \App\Synch::synch_worksheets('vl');
    $this->info($str);
})->describe('Synch worksheets to the national database.');

Artisan::command('synch:updates', function(){
    $str = \App\Synch::synch_updates('eid');
    $str = \App\Synch::synch_updates('vl');
    $this->info($str);
})->describe('Synch updates to the national database.');

Artisan::command('synch:deletes', function(){
    $str = \App\Synch::synch_deletes('eid');
	$str = \App\Synch::synch_deletes('vl');
    $this->info($str);
})->describe('Synch deletes to the national database.');

Artisan::command('synch:allocations', function(){
    $str = \App\Synch::synch_allocations();
    $this->info($str);
})->describe('Synch allocations from lab to national database');

Artisan::command('synch:allocationsupdates', function(){
    $str = \App\Synch::synch_allocations_updates();
    $this->insteadOf($str);
})->describe('Synch Allocation updates');

Artisan::command('synch:covidallocations', function(){
	$model = new \App\HCMPCovidAllocations;
	$str = $model->pullAllocations();
	$this->info($str->message);
})->describe('Synch COVID Allocations from KEMSA');

Artisan::command('synch:consumptions', function(){
    $str = \App\Synch::synch_consumptions();
    $this->info($str);
})->describe('Synch consumptions from lab to national databases');

Artisan::command('synch:equipmentlogs', function() {
    $str = \App\Synch::synchMachineMapping();
    $str = \App\Synch::synchLabPerformanceTrackers();
    $str = \App\Synch::synchLabEquipmentTrackers();
    $this->info($str);
})->describe('Synch equipment tracker and log from the labs to national database.');

Artisan::command('synch:deliveries', function(){
    $str = \App\Synch::synch_deliveries();
    $this->info($str);
})->describe('Synch deliveries from lab to national database');

Artisan::command('synch:facilities', function(){
    $str = \App\Synch::synch_facilities();
    $this->info($str);
})->describe('Synch facilities from lab to national database');

Artisan::command('synch:facilities-updates', function(){
    $str = \App\Synch::synch_updates_facilities();
    $this->info($str);
})->describe('Synch updates for facilities from national database to lab');




Artisan::command('copy:eid', function(){
	$str = \App\Copier::copy_eid();
    $this->info($str);
})->describe('Copy eid data from old database to new database.');

Artisan::command('copy:vl', function(){
	$str = \App\Copier::copy_vl();
    $this->info($str);
})->describe('Copy vl data from old database to new database.');

Artisan::command('copy:worksheet', function(){
	$str = \App\Copier::copy_worksheet();
    $this->info($str);
})->describe('Copy worksheet data from old database to new database.');

Artisan::command('copy:worklist', function(){
    $str = \App\Copier::copy_worklist();
    $this->info($str);
})->describe('Copy worklist data from old database to new database.');

Artisan::command('copy:deliveries', function(){
    $str = \App\Copier::copy_deliveries();
    $this->info($str);
})->describe('Copy deliveries data from old database to new database.');

Artisan::command('copy:facility-contacts', function(){
    $str = \App\Copier::copy_facility_contacts();
    $this->info($str);
})->describe('Copy facility contacts from old database to new database.');

Artisan::command('copy:facility-missing', function(){
    $str = \App\Copier::copy_missing_facilities();
    $this->info($str);
})->describe('Copy missing facilities from old database to new database.');

Artisan::command('copy:cd4', function(){
    $str = \App\Copier::cd4();
    $this->info($str);
})->describe('Copy cd4 data from old database to new database.');



Artisan::command('match:eid-patients', function(){
    $str = \App\Synch::match_eid_patients();
    $this->info($str);
})->describe('Copy facility contacts from old database to new database.');

Artisan::command('match:patients {type}', function($type){
    if($type == 'eid') $str = \App\Synch::match_eid_patients();
    else { $str = \App\Synch::match_vl_patients(); }    
    $this->info($str);
})->describe('Match patients with records on the national database.');

Artisan::command('match:batches {type}', function($type){
    $str = \App\Synch::match_batches($type);
    $this->info($str);
})->describe('Match batches with records on the national database.');

Artisan::command('match:samples {type}', function($type){
    $str = \App\Synch::match_samples($type);
    $this->info($str);
})->describe('Match samples with records on the national database.');

Artisan::command('match:poc', function(){
    $str = \App\Copier::match_eid_poc_batches();
    $str = \App\Copier::match_vl_poc_batches();
    $this->info($str);
})->describe('Match POC records.');

Artisan::command('test:email', function(){
	$str = \App\Common::test_email();
    $this->info($str);
})->describe('Send test email.');

Artisan::command('test:sms', function(){
    $str = \App\Misc::sms_test();
    $this->info($str);
})->describe('Send test sms.');

Artisan::command('test:connection', function(){
    $str = \App\Synch::test_connection();
    $this->info($str);
})->describe('Check connection to lab-2.test.nascop.org.');

Artisan::command('set:time', function(){
    $str = \App\Synch::synch_time();
    $this->info($str);
})->describe('Check time at lab-2.test.nascop.org and set the time to that.');

Artisan::command('send:labtracker {year} {month}', function($year, $month){
    $str = \App\Common::send_lab_tracker($year, $month);
    $this->info($str);
})->describe('Send labtracker email to the program people');

Artisan::command('edarp:approvesamples', function(){
    $str = \App\MiscViral::edarpsamplesforapproval();
    $this->info($str);
})->describe('Send email of the EDARP samples that need approval');

Artisan::command('transfer:consumptions', function(){
    $str = \App\Common::transferconsumptions();
    $this->info($str);
})->describe('Migrate from the old procurements tables');

Artisan::command('transfer:deliveries', function(){
    $str = \App\Common::transferdeliveries();
    $this->info($str);
})->describe('Migrate from the old procurements tables');

// Artisan::command('verify:list', function(){
//     $str = \App\Misc::check_patients_list();
//     $this->info($str);
// })->describe('Checking for Chege');

// Quick fixes
Artisan::command('kisumu:approval', function(){
    $str = \App\Common::fix_kisumu_approval('eid');
    $str = \App\Common::fix_kisumu_approval('vl');
    $this->info($str);
})->describe('Fix results that have first approval but not second approval.');
Artisan::command('correct:repeats', function(){
    $str = \App\Random::temp_correct_repeats();
    $this->info($str);
})->describe('Adjust repeats');
// Quick fix for deliveries
// Quick fix for deliveries
Artisan::command('adjust:deliveries {platform} {id} {quantity} {damaged}', function($platform, $id, $quantity, $damaged){
    $str = \App\Random::adjust_deliveries($platform, $id, $quantity, $damaged);
    $this->info($str);
})->describe('Adjust deliveries');
// Quick fix for deliveries
// Quick fix for consumptions
Artisan::command('adjust:consumptions {id} {ending?} {wasted?} {issued?} {request?} {pos?}', function($platform, $id, $ending = null, $wasted = null, $issued = null, $request = null, $pos = null) {
    $str = \App\Random::adjust_procurement($id, $ending, $wasted, $issued, $request, $pos);
    $this->info($str);
})->describe('Adjust Consumptions');


Artisan::command('backdate:consumption {plartform} {testtype} {month} {year} {used} {wasted} {posAdj} {negAdj} {requested}', function($plartform, $testtype, $month, $year, $used, $wasted, $posAdj, $negAdj, $requested){
    $str = \App\Random::backdateprocurement($plartform, $testtype, $month, $year, $used, $wasted, $posAdj, $negAdj, $requested);
    $this->info($str);
})->describe('Add consumptions that were left intentionally');
// Quick fix for consumptions

//Quick fix add EDARP samples to KEMRI
Artisan::command('edarp:upload {received_by}', function($received_by) {
    $str = \App\Random::import_edarp_samples_excel($received_by);
    $this->info($str);
})->describe('Move EDARP samples to Lab');

Artisan::command('edarp:lab', function(){
    $str = \App\Random::export_edarp_results();
    $this->info($str);
})->describe('Extract Moved samples');

Artisan::command('edarp:labwks', function(){
    $str = \App\Random::export_edarp_results_worksheet();
    $this->info($str);
})->describe('Extract Moved samples');

Artisan::command('edarp:labdelete', function(){
    $str = \App\Random::delete_edarp_imported_batches();
    $this->info($str);
})->describe('Delete Moved samples');

Artisan::command('edarp:labdeleteduplicates', function(){
    $str = \App\Random::delete_duplicates();
    $this->info($str);
})->describe('Delete duplicated Moved samples');

Artisan::command('edarp:confirm  {received_by}', function($received_by){
    $str = \App\Random::confirm_edarp_upload($received_by);
    $this->info($str);
})->describe('Check EDARP request');

Artisan::command('edarp:delete', function(){
    $str = \App\Random::delete_uploads();
    $this->info($str);
})->describe('Delete Transfered');
//Quick fix add EDARP samples to KEMRI

Artisan::command('edarp:machakos  {batch_id?}', function($batch_id=null){
    $str = \App\Console::machakos_edarp($batch_id);
    $this->info($str);
})->describe('Send Machakos Samples to Edarp');

Artisan::command('edarp:machakos-failed', function(){
    $str = \App\Console::send_failed_edarp_samples();
    $this->info($str);
})->describe('Email Edarp about failed samples.');

Artisan::command('edarp:machakos-delayed', function(){
    $str = \App\Console::send_edarp_delayed();
    $this->info($str);
})->describe('Email Edarp about failed samples.');

Artisan::command('check:maryland', function(){
    $str = \App\Random::getElvis();
    $this->info($str);
})->describe('Get MB No');

Artisan::command('missing', function(){
    $str = \App\Random::consolidate();
    $this->info($str);
})->describe('merge Missing Kemri Results');


Artisan::command('get:ken', function(){
    $str = \App\Random::run_ken_request();
    $this->info($str);
});

Artisan::command('get:linelist', function(){
    $str = \App\Random::linelist();
    $this->info($str);
});
//Quick fixes

Artisan::command('alloc', function(){
    $str = \App\Synch::sendAllocationReviewEmail();
    $this->info($str);
});

Artisan::command('rectify', function(){
    $str = \App\Random::rectify();
    $this->info($str);
});

