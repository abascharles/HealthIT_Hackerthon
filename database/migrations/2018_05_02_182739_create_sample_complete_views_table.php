<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSampleCompleteViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW sample_complete_view AS
        (
          SELECT s.*, b.national_batch_id, b.highpriority, b.datereceived, b.datedispatched, b.site_entry, b.lab_id, b.facility_id, b.user_id, b.batch_complete,
          p.national_patient_id, p.patient, p.sex, p.dob, p.mother_id. p.dateinitiatedontreatment, m.national_mother_id, m.patient_id as mother_vl_patient_id, m.ccc_no as mother_ccc_no,
          p.hei_validation, p.enrollment_ccc_no, p.enrollment_status, p.referredfromsite, p.otherreason,


           p.entry_point, g.gender_description, rs.name as receivedstatus_name, mp.name as mother_prophylaxis_name, ip.name as regimen_name, f.feeding as feeding_name, f.feeding_description,

           pcr.name as pcrtypename, ep.name as entry_point_name, r.name as result_name

          FROM samples s 
            JOIN batches b ON b.id=s.batch_id
            JOIN patients p ON p.id=s.patient_id
            LEFT JOIN mothers m on m.id=p.mother_id
            LEFT JOIN gender g on g.id=p.sex
            LEFT JOIN receivedstatus rs on rs.id=s.receivedstatus
            LEFT JOIN prophylaxis mp on mp.id=s.mother_prophylaxis
            LEFT JOIN prophylaxis ip on ip.id=s.regimen
            LEFT JOIN feedings f on f.id=s.feeding
            LEFT JOIN pcrtype pcr on pcr.id = s.pcrtype
            LEFT JOIN entry_points ep on ep.id = p.entry_point
            LEFT JOIN results r on r.id = s.result
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
        DB::statement('DROP VIEW IF EXISTS sample_complete_view');
    }
}

