<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW samples_view AS
        (
          SELECT s.*, `b`.`national_batch_id`,`b`.`highpriority`,`b`.`datereceived`,`b`.`datedispatched`, `b`.`tat5`,`b`.`time_received`,`b`.`site_entry`,`b`.`batch_complete`,`b`.`lab_id`, `b`.`user_id`,`b`.`received_by`,`b`.`entered_by`,`f`.`facilitycode`,`f`.`name` as facilityname, `b`.`facility_id`,`b`.`input_complete`,`b`.`datedispatchedfromfacility`, `b`.`dateemailsent`,`b`.`dateindividualresultprinted`,`b`.`datebatchprinted`,
                `p`.`national_patient_id`,`p`.`patient`,`p`.`sex`,`p`.`dob`,`p`.`mother_id`,`p`.`entry_point`, `p`.`patient_name`,`p`.`patient_phone_no`,`p`.`preferred_language`,`p`.`dateinitiatedontreatment`,
            `p`.`hei_validation`,`p`.`enrollment_ccc_no`,`p`.`enrollment_status`,`p`.`referredfromsite`, `p`.`otherreason`
          FROM samples s
            JOIN batches b ON b.id=s.batch_id
            JOIN patients p ON p.id=s.patient_id
            LEFT JOIN facilitys f ON f.id=b.facility_id
        );
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW IF EXISTS samples_view');
    }
}


// (select s.*,`b`.`original_batch_id` AS `original_batch_id`,`b`.`highpriority` AS `highpriority`,`b`.`datereceived` AS `datereceived`,`b`.`datedispatched` AS `datedispatched`,`b`.`site_entry` AS `site_entry`,`b`.`lab_id` AS `lab_id`,`b`.`facility_id` AS `facility_id`,`p`.`original_patient_id` AS `original_patient_id`,`p`.`patient` AS `patient`,`p`.`patient_status` AS `patient_status`,`p`.`sex` AS `sex`,`p`.`dob` AS `dob`,`p`.`ccc_no` AS `ccc_no`,`p`.`dateinitiatedontreatment`,`p`.`mother_id` AS `mother_id`,`p`.`entry_point` AS `entry_point`,`p`.`hei_validation` AS `hei_validation`,`p`.`enrollment_ccc_no` AS `enrollment_ccc_no`,`p`.`enrollment_status` AS `enrollment_status`,`p`.`referredfromsite` AS `referredfromsite`,`p`.`otherreason` AS `otherreason` from ((`samples` `s` join `batches` `b` on((`b`.`id` = `s`.`batch_id`))) join `patients` `p` on((`p`.`id` = `s`.`patient_id`))))