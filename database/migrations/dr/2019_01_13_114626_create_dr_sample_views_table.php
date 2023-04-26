<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrSampleViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW dr_samples_view AS
        (
          SELECT s.*, f.facilitycode, f.name as facilityname,
          p.national_patient_id, p.patient, p.nat, p.initiation_date, p.sex, p.dob, p.patient_name, p.patient_phone_no, p.preferred_language

          FROM dr_samples s
            LEFT JOIN viralpatients p ON p.id=s.patient_id
            LEFT JOIN facilitys f ON f.id=p.facility_id

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
        // Schema::dropIfExists('dr_sample_views');
    }
}
