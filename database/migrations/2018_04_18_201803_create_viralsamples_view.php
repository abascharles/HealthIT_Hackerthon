<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViralsamplesView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW viralsamples_view AS
        (
          SELECT s.*, b.national_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.tat5, b.time_received, b.site_entry, b.batch_complete, b.lab_id, b.user_id, b.received_by, b.entered_by, b.datedispatchedfromfacility,  `b`.`dateemailsent`,`b`.`dateindividualresultprinted`,`b`.`datebatchprinted`, f.facilitycode, f.name as facilityname, b.facility_id, b.input_complete,
          p.national_patient_id, p.patient, p.initiation_date, p.sex, p.dob, p.patient_name, p.patient_phone_no, p.preferred_language

          FROM viralsamples s
            JOIN viralbatches b ON b.id=s.batch_id
            JOIN viralpatients p ON p.id=s.patient_id
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
        DB::statement('DROP VIEW IF EXISTS viralsamples_view');
    }
}
