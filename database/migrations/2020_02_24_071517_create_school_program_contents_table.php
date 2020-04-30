<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolProgramContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_program_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("school_program_id");
            $table->foreign('school_program_id')->references('id')->on('school_program')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("school_day_id");
            $table->foreign('school_day_id')->references('id')->on('school_days')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("school_hour_id");
            $table->foreign('school_hour_id')->references('id')->on('school_hours')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("lesson_id");
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('school_program_contents');
    }
}
