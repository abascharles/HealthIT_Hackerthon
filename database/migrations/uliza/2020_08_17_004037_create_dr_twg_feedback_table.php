<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrTwgFeedbackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('uliza_twg_feedback', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('uliza_clinical_form_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->date('review_date')->nullable();
            $table->text('casesummary')->nullable();
            $table->text('observationsofsummary')->nullable();
            $table->tinyInteger('diagnosis')->nullable();
            $table->string('diagnosis_other')->nullable();
            $table->text('supportivemanagement')->nullable();
            $table->text('definativemanagement')->nullable();
            $table->text('additionalinfo')->nullable();
            $table->text('nascop_comments')->nullable();
            $table->text('reviewer_comments')->nullable();
            $table->tinyInteger('recommendation_id')->nullable();
            $table->tinyInteger('facility_recommendation_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dr_twg_feedback');
    }
}
