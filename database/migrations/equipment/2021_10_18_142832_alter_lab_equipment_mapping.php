<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterLabEquipmentMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lab_equipment_mapping', function(Blueprint $table) {
            $table->integer('national_id')->nullable()->after('ID');
            $table->tinyInteger('synched')->default(0);
            $table->dateTime('datesynched')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lab_equipment_mapping', function (Blueprint $table) {
            $table->dropColumn(['national_id', 'synched', 'datesynched']);
        });
    }
}
