<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacilityContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('facility_contacts', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->integer('facility_id')->unsigned()->index();
            
        //     $table->string('telephone', 20)->nullable()->comment("Facility Telephone 1");
        //     $table->string('telephone2', 20)->nullable()->comment("Facility Telephone 2");
        //     $table->string('fax', 30)->nullable();
        //     $table->string('email', 30)->nullable()->comment("Facility email Address");
        //     $table->string('PostalAddress', 40)->nullable()->comment("Facility Contact Address");
        //     $table->string('contactperson', 30)->nullable()->comment("Facility Contact Name");
        //     $table->string('contacttelephone', 20)->nullable()->comment("Contact Person's Telephone 1");
        //     $table->string('contacttelephone2', 20)->nullable()->comment("Contact Person's Telephone 2");
        //     $table->string('physicaladdress', 40)->nullable();
        //     $table->string('ContactEmail', 40)->nullable();
        //     $table->string('G4Sbranchname', 100)->nullable();
        //     $table->string('G4Slocation', 100)->nullable();
        //     $table->string('G4Sphone1', 100)->nullable();
        //     $table->string('G4Sphone2', 100)->nullable();
        //     $table->string('G4Sphone3', 100)->nullable();
        //     $table->string('G4Sfax', 100)->nullable();
            
        //     $table->string('sms_printer_phoneno', 100)->nullable();

        //     $table->tinyInteger('synched')->default(0)->nullable();
        //     $table->date('datesynched')->nullable();
            
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('facility_contacts');
    }
}
