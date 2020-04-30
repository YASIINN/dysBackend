<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sdetails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->json('illness')->nullable();           //hastalık
            $table->json('medicines')->nullable();         //ilaçlar
            $table->json('allergy')->nullable();           //alerji
            $table->json('chronic_disease')->nullable();   //kronik hastalıgı
            $table->json('scholarship')->nullable();       //bursluluk
            $table->boolean("photo_perm")->default(false); //foto izni
            $table->boolean("health_report")->default(true);//sağlık raporu
            $table->string("blood_group")->nullable();
            $table->string("s_height")->nullable();
            $table->string("s_weight")->nullable();
            $table->unsignedBigInteger("student_id");
            $table->foreign('student_id')->references('id')->on('students')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('sdetails');
    }
}
