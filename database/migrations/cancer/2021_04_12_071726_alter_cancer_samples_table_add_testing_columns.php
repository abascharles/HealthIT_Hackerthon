<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCancerSamplesTableAddTestingColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cancer_samples', function (Blueprint $table) {
            $table->bigInteger('worksheet_id')->nullable()->after('national_sample_id');
            $table->string('target_1')->nullable()->after('reason_for_repeat');
            $table->string('target_2')->nullable()->after('target_1');
            $table->string('target_3')->nullable()->after('target_2');

            /*
                0. Entered at the lab and tested at the lab
                1. Entered at the facility and tested at the lab
                2. Entered at the facility and tested at the facility
            */
            $table->tinyInteger('site_entry')->default(2)->after('sample_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('cancer_samples', 'worksheet_id')) {
            Schema::table('cancer_samples', function (Blueprint $table) {
                $table->dropColumn('worksheet_id');
                $table->dropColumn('target_1');
                $table->dropColumn('target_2');
                $table->dropColumn('target_3');
                $table->dropColumn('site_entry');
                $table->dropColumn('run');
            });   
        }
    }
}
