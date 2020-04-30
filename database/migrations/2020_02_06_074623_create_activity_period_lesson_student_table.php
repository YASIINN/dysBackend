<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityPeriodLessonStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_period_lesson_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("activity_id");
            $table->unsignedBigInteger("period_id");
            $table->unsignedBigInteger("lessons_id");
            $table->unsignedBigInteger("student_id");
            $table->foreign('student_id')->references('id')->on('students')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('period_id')->references('id')->on('periods')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('lessons_id')->references('id')->on('lessons')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('activity_period_lesson_student');
    }
}
