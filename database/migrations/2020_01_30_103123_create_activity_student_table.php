<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_student', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->integer("activity_id")->unsigned()->nullable()->index();
            // $table->integer("student_id")->unsigned()->nullable()->index();
            // $table->integer("period_id")->unsigned()->nullable()->index();
            // $table->integer("grade_id")->unsigned()->nullable()->index();


            $table->unsignedBigInteger("activity_id");
            $table->unsignedBigInteger("period_id");
            $table->unsignedBigInteger("grade_id");
            $table->unsignedBigInteger("student_id");
            $table->foreign('student_id')->references('id')->on('students')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('period_id')->references('id')->on('periods')->onDelete("cascade")->onUpdate("cascade");
            $table->foreign('grade_id')->references('id')->on('grades')->onDelete("cascade")->onUpdate("cascade");
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
        Schema::dropIfExists('activity_student');
    }
}
