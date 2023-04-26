<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterHcmpCovidAddRespondedColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hcmp_covid_allocations', function (Blueprint $table) {
            $table->enum('responded', ['YES', 'NO', 'POSTPONED'])->default('NO')->after('received');
            $table->integer('respond_count')->default(0)->after('responded');
            $table->date('date_responded')->nullable()->after('respond_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
