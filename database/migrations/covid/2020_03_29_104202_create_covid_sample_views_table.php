<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCovidSampleViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        DB::statement("
        CREATE OR REPLACE VIEW covid_sample_view AS
        (
          SELECT s.*, p.national_patient_id, p.facility_id, p.quarantine_site_id, p.case_id, p.county_id, p.subcounty_id, p.identifier_type, p.identifier, p.national_id, p.patient_name, p.occupation, p.phone_no, p.email_address, p.contact_email_address, p.contact_phone_no, p.justification, p.county, p.subcounty, p.ward, p.residence, p.hospital_admitted, p.dob, p.sex, p.current_health_status, p.date_symptoms,  p.date_admission, p.date_isolation, date_death, `f`.`facilitycode`,`f`.`name` as facilityname, f.partner, f.district, qs.name as quarantine_site, c.name as countyname, d.name as subcountyname, dd.name as sub_county
          FROM covid_samples s
            JOIN covid_patients p ON p.id=s.patient_id
            LEFT JOIN facilitys f ON f.id=p.facility_id
            LEFT JOIN districts d ON d.id=f.district
            LEFT JOIN quarantine_sites qs ON qs.id=p.quarantine_site_id
            LEFT JOIN countys c ON c.id=p.county_id
            LEFT JOIN districts dd ON dd.id=p.subcounty_id
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

    }
}
