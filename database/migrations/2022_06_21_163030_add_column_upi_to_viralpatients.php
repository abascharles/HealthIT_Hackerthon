<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUpiToViralpatients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('viralpatients', function (Blueprint $table) {
            $table->string('upi_no')->nullable()->after('patient');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('viralpatients', function (Blueprint $table) {
            $table->dropColumn('upi_no');
            //
        });
    }
}
