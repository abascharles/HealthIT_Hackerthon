<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerFacilityContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_facility_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('county_id')->nullable();
            $table->integer('subcounty_id')->nullable();
            $table->integer('partner_id')->nullable();
            $table->string('name')->nullbale();
            $table->string('email')->nullbale();
            $table->string('telephone')->nullable();
            $table->enum('type', ['Recepient', 'Cc', 'Bcc'])->nullable();
            $table->boolean('critical_results');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_facility_contacts');
    }
}
