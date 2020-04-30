<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolClasesLessonStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school_clases_lesson_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("school_id");
            $table->unsignedBigInteger("clases_id");
            $table->unsignedBigInteger("lessons_id");
            $table->unsignedBigInteger("student_id");
            $table->foreign('student_id')->references('id')->on('students')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('school_id')->references('id')->on('schools')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('lessons_id')->references('id')->on('lessons')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('clases_id')->references('id')->on('clases')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('school_clases_lesson_student');
    }
}
