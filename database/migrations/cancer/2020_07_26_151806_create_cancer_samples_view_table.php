<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCancerSamplesViewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW cancer_samples_view AS
        (
          SELECT s.*, `p`.`national_patient_id`,`p`.`patient`,`p`.`sex`,`p`.`dob`,`p`.`hiv_status`,`p`.`entry_point`, `p`.`patient_name`
          FROM cancer_samples s
            JOIN cancer_patients p ON p.id=s.patient_id
            LEFT JOIN facilitys f ON f.id=s.facility_id
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
        Schema::dropIfExists('DROP VIEW IF EXISTS cancer_samples_view');
    }
}
