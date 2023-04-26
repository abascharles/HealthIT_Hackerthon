<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDrGenotypeViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE VIEW dr_genotypes_views AS
        (
          SELECT r.*, g.sample_id, g.locus, g.locus_id, 
          s.patient_id, s.facility_id 

          FROM dr_genotypes g
            LEFT JOIN dr_residues r ON g.id=r.genotype_id
            LEFT JOIN dr_samples s ON g.sample_id=s.id

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
        // Schema::dropIfExists('dr_genotype_views');
    }
}
