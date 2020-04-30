<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityProgramContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_program_contents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("activity_program_id");
            $table->foreign('activity_program_id')->references('id')->on('activity_program')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("activity_day_id");
            $table->foreign('activity_day_id')->references('id')->on('activity_days')->onDelete("cascade")->onUpdate("cascade");
            $table->unsignedBigInteger("activity_hour_id");
            $table->foreign('activity_hour_id')->references('id')->on('activity_hours')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('activity_program_contents');
    }
}
